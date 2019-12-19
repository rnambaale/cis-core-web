<?php

namespace App\Http\Controllers;

use App\Http\Clients\PasswordClientInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Password client.
     *
     * @var \App\Http\Clients\PasswordClientInterface
     */
    protected $passwordClient;

    /**
     * Create a new controller instance.
     *
     * @param \App\Http\Clients\PasswordClientInterface $passwordClient
     *
     * @return void
     */
    public function __construct(PasswordClientInterface $passwordClient)
    {
        $this->middleware('auth');
        $this->passwordClient = $passwordClient;
    }

    /**
     * Show users.
     *
     * @param \Illuminate\http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (! auth_can('users', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('users', [
            'query' => [
                'paginate' => true,
                'limit' => 10,
                'page' => $request->page,
            ],
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        $users = paginate($request, $body->users);

        return view('users.index', ['users' => $users]);
    }

    /**
     * Show users via datatables.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function showDatatables()
    {
        if (! auth_can('users', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        return view('users.index-dt');
    }

    /**
     * Load users via datatables.
     *
     * @see http://docs.guzzlephp.org/en/5.3/quickstart.html#query-string-parameters Empty string vs. Null
     *
     * @param \Illuminate\http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatables(Request $request)
    {
        if (! auth_can('users', 'view-any')) {
            // return response()->json(['error' => 'Unauthorized access.'], 403);
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('users/dt', [
            'query' => $request->query(),
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        return response()->json($body);
    }

    /**
     * Show user.
     *
     * @param string $userId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function show($userId)
    {
        if (! auth_can('users', 'view')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("users/{$userId}");

        $user = json_decode($apiResponse->getBody(), false);

        return view('users.show', ['user' => $user]);
    }

    /**
     * Show create user.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! auth_can('users', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('roles', [
            'query' => [
                'paginate' => false,
            ],
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        return view('users.create', ['roles' => $body->roles]);
    }

    /**
     * Create user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        if (! auth_can('users', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->post('users', [
            'json' => $request->all(),
        ]);

        $user = json_decode($apiResponse->getBody(), false);

        flash("{$user->name} created.")->success();

        return view('users.show', ['user' => $user]);
    }

    /**
     * Show edit user.
     *
     * @param string $userId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function edit($userId)
    {
        if (! auth_can('users', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("users/{$userId}");

        $user = json_decode($apiResponse->getBody(), false);

        // ...

        $rolesApiResponse = $this->passwordClient->get('roles', [
            'query' => [
                'paginate' => false,
            ],
        ]);

        $roles = json_decode($rolesApiResponse->getBody(), false)->roles;

        return view('users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update user.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $userId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function update(Request $request, $userId)
    {
        if (! auth_can('users', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("users/{$userId}", [
            'json' => $request->all(),
        ]);

        $user = json_decode($apiResponse->getBody(), false);

        flash("{$user->name} updated.")->success();

        return view('users.show', ['user' => $user]);
    }

    /**
     * Revoke user.
     *
     * @param string $userId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function revoke($userId)
    {
        if (! auth_can('users', 'soft-delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("users/{$userId}/revoke");

        $user = json_decode($apiResponse->getBody(), false);

        flash("{$user->name} revoked.")->warning();

        return view('users.show', ['user' => $user]);
    }

    /**
     * Restore user.
     *
     * @param string $userId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function restore($userId)
    {
        if (! auth_can('users', 'restore')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("users/{$userId}/restore");

        $user = json_decode($apiResponse->getBody(), false);

        flash("{$user->name} restored.")->success();

        return view('users.show', ['user' => $user]);
    }

    /**
     * Delete user.
     *
     * @param string $userId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($userId)
    {
        if (! auth_can('users', 'force-delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $this->passwordClient->delete("users/{$userId}");

        flash('user deleted.')->error();

        return redirect()->route('users.index');
    }
}
