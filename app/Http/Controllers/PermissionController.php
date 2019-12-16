<?php

namespace App\Http\Controllers;

use App\Http\Clients\PasswordClientInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class PermissionController extends Controller
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
     * Show permissions.
     *
     * @param \Illuminate\http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (! auth_can('permissions', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('permissions', [
            'query' => [
                'paginate' => true,
                'limit' => 10,
                'page' => $request->page,
            ],
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        $permissions = paginate($request, $body->permissions);

        return view('permissions.index', ['permissions' => $permissions]);
    }

    /**
     * Show perimssions via datatables.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function showDatatables()
    {
        if (! auth_can('perimssions', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        return view('perimssions.index-dt');
    }

    /**
     * Load permissions via datatables.
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
        if (! auth_can('permissions', 'view-any')) {
            // return response()->json(['error' => 'Unauthorized access.'], 403);
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('permissions/dt', [
            'query' => $request->query(),
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        return response()->json($body);
    }

    /**
     * Show permission.
     *
     * @param string $permissionId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function show($permissionId)
    {
        if (! auth_can('permissions', 'view')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("permissions/{$permissionId}");

        $permission = json_decode($apiResponse->getBody(), false);

        return view('permissions.show', ['permission' => $permission]);
    }

    /**
     * Show create permission.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! auth_can('permissions', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $modulesApiResponse = $this->passwordClient->get('modules', [
            'query' => [
                'paginate' => false,
            ],
        ]);

        $modules = json_decode($modulesApiResponse->getBody(), false)->modules;

        return view('permissions.create', ['modules' => $modules]);
    }

    /**
     * Create permission.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        if (! auth_can('permissions', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->post('permissions', [
            'json' => $request->all(),
        ]);

        $permission = json_decode($apiResponse->getBody(), false);

        flash("{$permission->name} created.")->success();

        return view('permissions.show', ['permission' => $permission]);
    }

    /**
     * Show edit permission.
     *
     * @param string $permissionId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function edit($permissionId)
    {
        if (! auth_can('permissions', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("permissions/{$permissionId}");

        $permission = json_decode($apiResponse->getBody(), false);

        $modulesApiResponse = $this->passwordClient->get('modules', [
            'query' => [
                'paginate' => false,
            ],
        ]);

        $modules = json_decode($modulesApiResponse->getBody(), false)->modules;

        return view('permissions.edit', ['permission' => $permission, 'modules' => $modules]);
    }

    /**
     * Update permission.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $permissionId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function update(Request $request, $permissionId)
    {
        if (! auth_can('permissions', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("permissions/{$permissionId}", [
            'json' => $request->all(),
        ]);

        $permission = json_decode($apiResponse->getBody(), false);

        flash("{$permission->name} updated.")->success();

        return view('permissions.show', ['permission' => $permission]);
    }

    /**
     * Delete permission.
     *
     * @param string $permissionId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($permissionId)
    {
        if (! auth_can('permissions', 'delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $this->passwordClient->delete("permissions/{$permissionId}");

        flash('permission deleted.')->error();

        return redirect()->route('permissions.index');
    }
}
