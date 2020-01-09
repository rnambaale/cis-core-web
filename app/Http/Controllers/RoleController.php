<?php

namespace App\Http\Controllers;

use App\Http\Clients\PasswordClientInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class RoleController extends Controller
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
     * Show roles.
     *
     * @param \Illuminate\http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (! auth_can('roles', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('roles', [
            'query' => [
                'paginate' => true,
                'limit' => 10,
                'page' => $request->page,
            ],
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        $roles = paginate($request, $body);

        return view('roles.index', ['roles' => $roles]);
    }

    /**
     * Show roles via datatables.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function showDatatables()
    {
        if (! auth_can('roles', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        return view('roles.index-dt');
    }

    /**
     * Load roles via datatables.
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
        if (! auth_can('roles', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('roles/datatables', [
            'query' => $request->query(),
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        return response()->json($body);
    }

    /**
     * Show role.
     *
     * @param string $roleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function show($roleId)
    {
        if (! auth_can('roles', 'view')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("roles/{$roleId}");

        $role = json_decode($apiResponse->getBody(), false);

        return view('roles.show', ['role' => $role]);
    }

    /**
     * Show create role.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! auth_can('roles', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        return view('roles.create');
    }

    /**
     * Create role.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        if (! auth_can('roles', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->post('roles', [
            'json' => $request->all(),
        ]);

        $role = json_decode($apiResponse->getBody(), false);

        flash("{$role->name} created.")->success();

        return view('roles.show', ['role' => $role]);
        //return redirect(route('roles.show', $role->id));
    }

    /**
     * Show edit role.
     *
     * @param string $roleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function edit($roleId)
    {
        if (! auth_can('roles', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("roles/{$roleId}");

        $role = json_decode($apiResponse->getBody(), false);

        return view('roles.edit', ['role' => $role]);
    }

    /**
     * Update role.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $roleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function update(Request $request, $roleId)
    {
        if (! auth_can('roles', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("roles/{$roleId}", [
            'json' => $request->all(),
        ]);

        $role = json_decode($apiResponse->getBody(), false);

        flash("{$role->name} updated.")->success();

        return view('roles.show', ['role' => $role]);
        //return redirect(route('roles.show', $roleId));
    }

    /**
     * Revoke role.
     *
     * @param string $roleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function revoke($roleId)
    {
        if (! auth_can('roles', 'soft-delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("roles/{$roleId}/revoke");

        $role = json_decode($apiResponse->getBody(), false);

        flash("{$role->name} revoked.")->warning();

        return view('roles.show', ['role' => $role]);
        //return redirect(route('roles.show', $roleId));
    }

    /**
     * Restore role.
     *
     * @param string $roleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function restore($roleId)
    {
        if (! auth_can('roles', 'restore')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("roles/{$roleId}/restore");

        $role = json_decode($apiResponse->getBody(), false);

        flash("{$role->name} restored.")->success();

        return view('roles.show', ['role' => $role]);
    }

    /**
     * Delete role.
     *
     * @param string $roleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($roleId)
    {
        if (! auth_can('roles', 'force-delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $this->passwordClient->delete("roles/{$roleId}");

        flash('role deleted.')->error();

        return redirect()->route('roles.index');
    }

    /**
     * Show role permissions.
     *
     * @param string $roleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function showPermissions($roleId)
    {
        if (! auth_can('permissions', 'assign-permissions')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("roles/{$roleId}/permissions/granted");

        $role = json_decode($apiResponse->getBody(), false);

        $role->permissions = collect($role->permissions)->groupBy('module.name');

        return view('roles.permissions', [
            'role' => $role,
        ]);
    }

    /**
     * Update role permissions.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $roleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function syncPermissions(Request $request, $roleId)
    {
        if (! auth_can('permissions', 'assign-permissions')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("roles/{$roleId}/permissions", [
            'json' => $request->all(),
        ]);

        $role = json_decode($apiResponse->getBody(), false);

        flash("{$role->name} permissions updated.")->success();

        return redirect(route('roles.permissions.show', $roleId));
    }
}
