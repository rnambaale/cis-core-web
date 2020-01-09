<?php

namespace App\Http\Controllers\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

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
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        // $request->validate(['email' => 'required|email']);

        $this->remoteEmailValidation($request);
    }

    /**
     * Remote email validation.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return int
     */
    protected function remoteEmailValidation($request)
    {
        $response = $this->machineClient->post('users/email', [
            'form_params' => $request->input(),
        ]);

        return $response->getStatusCode();
    }
}
