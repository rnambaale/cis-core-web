<?php

namespace App\Http\Controllers\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Client credentials client.
     *
     * @var \App\Http\Clients\ClientCredentialsClientInterface
     */
    protected $machineClient;

    /**
     * Create a new controller instance.
     *
     * @param \App\Http\Clients\ClientCredentialsClientInterface $clientCredentialsClient
     *
     * @return void
     */
    public function __construct(
        ClientCredentialsClientInterface $clientCredentialsClient
    ) {
        $this->machineClient = $clientCredentialsClient;
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function reset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $passwordReset = DB::table('password_resets')->where('email', $request->email)->first();

        if (! $passwordReset) {
            $validator = Validator::make([], []);

            $validator->errors()->add('email', 'The password reset token is unknown.');

            throw new ValidationException($validator);
        }

        if (! Hash::check($request->token, $passwordReset->token)) {
            $validator = Validator::make([], []);

            $validator->errors()->add('email', 'The password reset token is invalid.');

            throw new ValidationException($validator);
        }

        if ($this->tokenExpired($passwordReset->created_at)) {
            $validator = Validator::make([], []);

            $validator->errors()->add('email', 'The password reset token is expired.');

            throw new ValidationException($validator);
        }

        $response = $this->remoteResetPassword($request);

        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }

        DB::table('password_resets')->where('email')->delete();

        // Because the use is not logged in yet...
        // event(new PasswordReset($user));

        $message = trans('Password reset successful, login to access your account.');

        flash($message)->success()->important();

        return redirect($this->redirectPath());
    }

    /**
     * Determine if the token has expired.
     *
     * @param string $createdAt
     *
     * @return bool
     */
    protected function tokenExpired(string $createdAt): bool
    {
        return Carbon::parse($createdAt)->addSeconds(config('password.users.expire', 60))->isPast();
    }

    /**
     * Remote password reset.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return int|\Symfony\Component\HttpFoundation\Response Remote user or http response.
     */
    protected function remoteResetPassword(Request $request)
    {
        try {
            $response = $this->machineClient->put('users/password/reset', [
                'form_params' => $request->input(),
            ]);

            return $response->getStatusCode();
        } catch (ConnectException $ex) {
            flash('Error connecting to remote service.')->error()->important();
        } catch (ClientException $ex) {
            $statusCode = $ex->getResponse()->getStatusCode();

            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();

            if ($statusCode === 422) {
                $validator = Validator::make([], []);

                $validator->errors()->merge($body['errors']);

                throw new ValidationException($validator);
            }
        } catch (RequestException $ex) {
            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();
        }

        return redirect()->back();
    }
}
