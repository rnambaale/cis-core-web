<?php

namespace App\Http\Controllers\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Client credentials client.
     *
     * @var \App\Http\Clients\ClientCredentialsClientInterface
     */
    protected $machineClient;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ClientCredentialsClientInterface $clientCredentialsClient)
    {
        $this->machineClient = $clientCredentialsClient;
        $this->middleware('auth');
    }

    /**
     * Confirm the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $this->remotePasswordConfirmation($request);

        $this->resetPasswordConfirmationTimeout($request);

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the password confirmation validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'password' => 'required',
        ];
    }

    /**
     * Get the password confirmation validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Remote password confirmation.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return void
     */
    protected function remotePasswordConfirmation($request)
    {
        try {
            $this->machineClient->post('users/password', [
                'form_params' => $request->input(),
            ]);
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
