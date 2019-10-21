<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Clients\ClientCredentialsClientInterface;

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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return int
     */
    protected function remoteVerifyEmail(string $email)
    {
        try {
            $response = $this->machineClient->put('users/email', [
                'form_params' => [
                    'email' => $email,
                ],
            ]);

            return $response->getStatusCode();
        } catch (ConnectException $ex) {
            flash('Error connecting to remote service.')->error()->important();
        } catch (ClientException $ex) {
            $statusCode = $ex->getResponse()->getStatusCode();

            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();

            if ($statusCode === 422) {
                throw new AuthorizationException($body['errors']['email'][0]);
            }
        } catch (RequestException $ex) {
            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();
        }

        throw new AuthorizationException();
    }
}
