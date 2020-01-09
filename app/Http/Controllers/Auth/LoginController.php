<?php

namespace App\Http\Controllers\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Http\Clients\PasswordClientInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Bmatovu\OAuthNegotiator\Repositories\TokenRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
     * OAuth token repository.
     *
     * @var \Bmatovu\OAuthNegotiator\Repositories\TokenRepositoryInterface
     */
    protected $tokenRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Http\Clients\ClientCredentialsClientInterface $clientCredentialsClient
     * @param \App\Http\Clients\PasswordClientInterface          $passwordClient
     * @param TokenRepositoryInterface                           $tokenRepository
     *
     * @return void
     */
    public function __construct(
        ClientCredentialsClientInterface $clientCredentialsClient,
        PasswordClientInterface $passwordClient,
        TokenRepositoryInterface $tokenRepository
    ) {
        $this->machineClient = $clientCredentialsClient;
        $this->passwordClient = $passwordClient;
        $this->tokenRepository = $tokenRepository;
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\Response
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
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $response = $alien = $this->remoteLogin($request);

        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }

        $this->syncRemoteUser($alien, $request->password);

        // Persist password-grant token
        $this->tokenRepository->create((array) $alien->token);

        // Attempt local login:
        // Should never fail since auth is handled by remote API
        // add a correct user has been syncd to local persistent storage.
        $this->attemptLogin($request);

        // Sync user permissions
        $role = $this->remoteQueryPermissionsAvailable($alien->role_id);

        $permissions = $this->processPermissions($role->permissions);

        $request->session()->put('modules', $permissions->modules);

        $request->session()->put('categories', $permissions->categories);

        return $this->sendLoginResponse($request);
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
        $this->remoteLogout($request);

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    /**
     * Remote deauthentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return int HTTP status code
     */
    protected function remoteLogout(Request $request)
    {
        $response = $this->passwordClient->post('users/deauth');

        return $response->getStatusCode();
    }

    /**
     * Remote authentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return object|\Symfony\Component\HttpFoundation\Response Remote user or http response.
     */
    protected function remoteLogin(Request $request)
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
        } catch (ClientException $ex) {
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            $statusCode = $ex->getResponse()->getStatusCode();

            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();

            if ($statusCode !== 422) {
                return redirect()->back();
            }

            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors($body['errors']);
        }
    }

    /**
     * Remote; query permissions granted to user.
     *
     * @param string $roleId
     *
     * @return object
     */
    protected function remoteQueryPermissionsAvailable(string $roleId): object
    {
        $response = $this->passwordClient->get("roles/{$roleId}/permissions/available");

        return json_decode($response->getBody(), false);
    }

    /**
     * Group permissions by module.
     *
     * @param array $rawPermissions
     *
     * @return object
     */
    protected function processPermissions(array $rawPermissions): object
    {
        $categories = $modules = [];

        foreach ($rawPermissions as $permission) {
            if (! $permission->granted) {
                continue;
            }

            $categories[] = $permission->module->category;

            $module = $permission->module->name;
            $modules[$module][] = $permission->name;
        }

        return (object) [
            'categories' => array_unique($categories),
            'modules' => $modules,
        ];
    }

    /**
     * Sync remote user to local storage.
     *
     * @param object $alien
     * @param string $secret
     *
     * @return \App\Models\User
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
