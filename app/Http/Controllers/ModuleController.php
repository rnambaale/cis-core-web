<?php

namespace App\Http\Controllers;

use App\Http\Clients\PasswordClientInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class ModuleController extends Controller
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
     * Show modules.
     *
     * @param \Illuminate\http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (! auth_can('modules', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('modules', [
            'query' => [
                'paginate' => true,
                'limit' => 10,
                'page' => $request->page,
            ],
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        $modules = paginate($request, $body->modules);

        return view('modules.index', ['modules' => $modules]);
    }

    /**
     * Show modules via datatables.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function showDatatables()
    {
        if (! auth_can('modules', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        return view('modules.index-dt');
    }

    /**
     * Load modules via datatables.
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
        if (! auth_can('modules', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('modules/dt', [
            'query' => $request->query(),
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        return response()->json($body);
    }

    /**
     * Show module.
     *
     * @param string $name
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function show($name)
    {
        if (! auth_can('modules', 'view')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("modules/{$name}");

        $module = json_decode($apiResponse->getBody(), false);

        return view('modules.show', ['module' => $module]);
    }

    /**
     * Show create module.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! auth_can('modules', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        return view('modules.create');
    }

    /**
     * Create module.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        if (! auth_can('modules', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->post('modules', [
            'json' => $request->all(),
        ]);

        $module = json_decode($apiResponse->getBody(), false);

        flash("{$module->name} created.")->success();

        return view('modules.show', ['module' => $module]);
    }

    /**
     * Show edit module.
     *
     * @param string $name
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function edit($name)
    {
        if (! auth_can('modules', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("modules/{$name}");

        $module = json_decode($apiResponse->getBody(), false);

        return view('modules.edit', ['module' => $module]);
    }

    /**
     * Update module.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $name
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function update(Request $request, $name)
    {
        if (! auth_can('modules', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("modules/{$name}", [
            'json' => $request->all(),
        ]);

        $module = json_decode($apiResponse->getBody(), false);

        flash("{$module->name} updated.")->success();

        return view('modules.show', ['module' => $module]);
    }

    /**
     * Revoke module.
     *
     * @param string $name
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function revoke($name)
    {
        if (! auth_can('modules', 'soft-delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("modules/{$name}/revoke");

        $module = json_decode($apiResponse->getBody(), false);

        flash("{$module->name} revoked.")->warning();

        return view('modules.show', ['module' => $module]);
    }

    /**
     * Restore module.
     *
     * @param string $name
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function restore($name)
    {
        if (! auth_can('modules', 'restore')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("modules/{$name}/restore");

        $module = json_decode($apiResponse->getBody(), false);

        flash("{$module->name} restored.")->success();

        return view('modules.show', ['module' => $module]);
    }

    /**
     * Delete module.
     *
     * @param string $name
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($name)
    {
        if (! auth_can('modules', 'force-delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $this->passwordClient->delete("modules/{$name}");

        flash('module deleted.')->error();

        return redirect()->route('modules.index');
    }
}
