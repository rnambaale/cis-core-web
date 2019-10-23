<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Repositories\TokenRepository;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use App\Http\Clients\PasswordClientInterface;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Clients\ClientCredentialsClientInterface;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
     * Password client.
     *
     * @var \App\Http\Clients\PasswordClientInterface
     */
    protected $passwordClient;

    /**
     * Create a new controller instance.
     *
     * @param \App\Http\Clients\ClientCredentialsClientInterface $clientCredentialsClient
     * @param \App\Http\Clients\PasswordClientInterface          $passwordClient
     *
     * @return void
     */
    public function __construct(
        ClientCredentialsClientInterface $clientCredentialsClient,
        PasswordClientInterface $passwordClient
    ) {
        $this->machineClient = $clientCredentialsClient;
        $this->passwordClient = $passwordClient;
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $response = $this->remoteLogin($request);

        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }

        $this->syncRemoteUser($response, $request->password);

        // Backup password-grant token

        $tokenRepository = new TokenRepository(); // DI

        $tokenRepository->create((array) $response->token);

        // ...

        // Attempt local login

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $response = $this->remoteLogout($request);

        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    /**
     * Remote deauthentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return int|\Symfony\Component\HttpFoundation\Response Remote user or http response.
     */
    protected function remoteLogout($request)
    {
        try {
            $response = $this->passwordClient->post('users/deauth');

            return $response->getStatusCode();
        } catch (ConnectException $ex) {
            flash('Error connecting to remote service.')->error()->important();
        } catch (ClientException $ex) {
            $statusCode = $ex->getResponse()->getStatusCode();

            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();
        } catch (RequestException $ex) {
            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();
        } catch (ServerException $ex) {
            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->error()->important();
        }

        return redirect()->back();
    }

    /**
     * Remote authentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return object|\Symfony\Component\HttpFoundation\Response Remote user or http response.
     */
    protected function remoteLogin($request)
    {
        try {
            $response = $this->machineClient->post('users/auth', [
                'form_params' => [
                    'email' => $request->email,
                    'password' => $request->password,
                ],
            ]);

            $api_response = json_decode($response->getBody());

            return $api_response;
        } catch (ConnectException $ex) {
            flash('Error connecting to remote service.')->error()->important();
        } catch (ClientException $ex) {
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            $statusCode = $ex->getResponse()->getStatusCode();

            $body = json_decode($ex->getResponse()->getBody(), true);

            if ($statusCode == 422) {
                flash($body['message'])->warning()->important();

                return redirect()->back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors($body['errors']);
            }

            flash($body['message'])->warning()->important();
        } catch (RequestException $ex) {
            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();
        } catch (ServerException $ex) {
            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->error()->important();
        }

        return redirect()->back();
    }

    /**
     * Sync remote user to local storage.
     *
     * @param object $alien
     * @param string $secret
     *
     * @return App\Models\User
     */
    protected function syncRemoteUser(object $alien, string $secret): User
    {
        // Prevent duplicate user accounts
        User::query()
            ->withTrashed()
            ->where(['id' => $alien->id])
            ->orWhere(['email' => $alien->email])
            ->orWhere(['alias' => $alien->alias])
            ->forceDelete();

        // Mirror remote user.
        $user = new User();
        $user->id = $alien->id;
        $user->facility_id = $alien->facility_id;
        $user->role_id = $alien->role_id;
        $user->alias = $alien->alias;
        $user->name = $alien->name;
        $user->email = $alien->email;
        $user->email_verified_at = $alien->email_verified_at;
        $user->password = Hash::make($secret);
        $user->remember_token = null;
        $user->created_at = $alien->created_at;
        $user->updated_at = $alien->updated_at;
        $user->deleted_at = $alien->deleted_at;
        $user->save();

        return $user;
    }
}
