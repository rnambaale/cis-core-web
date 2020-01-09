<?php

namespace App\Http\Controllers\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;

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
     * @param ClientCredentialsClientInterface $machineClient
     *
     * @return void
     */
    public function __construct(ClientCredentialsClientInterface $machineClient)
    {
        $this->machineClient = $machineClient;
        $this->middleware('auth');
    }

    /**
     * Confirm the given user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request)
    {
        $this->remotePasswordConfirmation($request);

        $this->resetPasswordConfirmationTimeout($request);

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Remote password confirmation.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return int
     */
    protected function remotePasswordConfirmation(Request $request)
    {
        $response = $this->machineClient->post('users/password', [
            'form_params' => $request->input(),
        ]);

        return $response->getStatusCode();
    }
}
