<?php

namespace App\Http\Controllers\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
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

        if (! $passwordReset || ! Hash::check($request->token, $passwordReset->token)) {
            $validator = Validator::make([], []);

            $validator->errors()->add('email', 'The password reset token is invalid.');

            throw new ValidationException($validator);
        }

        if ($this->tokenExpired($passwordReset->created_at)) {
            $validator = Validator::make([], []);

            $validator->errors()->add('email', 'The password reset token is expired.');

            throw new ValidationException($validator);
        }

        $this->remoteResetPassword($request);

        DB::table('password_resets')->where('email')->delete();

        // Because the user is not logged in yet...
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
     * @return int
     */
    protected function remoteResetPassword(Request $request)
    {
        $response = $this->machineClient->put('users/password/reset', [
            'form_params' => $request->input(),
        ]);

        return $response->getStatusCode();
    }
}
