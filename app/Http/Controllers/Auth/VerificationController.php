<?php

namespace App\Http\Controllers\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
     * @param \App\Http\Clients\ClientCredentialsClientInterface $clientCredentialsClient
     *
     * @return void
     */
    public function __construct(ClientCredentialsClientInterface $clientCredentialsClient)
    {
        $this->machineClient = $clientCredentialsClient;
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        // ...

        $this->remoteVerifyEmail($request->user()->email);

        // ...

        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    /**
     * Remote email verification.
     *
     * @param string $email Authenticated user email
     *
     * @return int
     */
    protected function remoteVerifyEmail(string $email)
    {
        $response = $this->machineClient->put('users/email', [
            'form_params' => [
                'email' => $email,
            ],
        ]);

        return $response->getStatusCode();
    }
}
