<?php

namespace App\Http\Controllers;

use App\Http\Clients\PasswordClientInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class FacilityController extends Controller
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
     * Show facilities.
     *
     * @param \Illuminate\http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (! auth_can('facilities', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('facilities', [
            'query' => [
                'paginate' => true,
                'limit' => 10,
                'page' => $request->page,
            ],
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        $facilities = paginate($request, $body->facilities);

        return view('facilities.index', ['facilities' => $facilities]);
    }

    /**
     * Show facilities via datatables.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function showDatatables()
    {
        if (! auth_can('facilities', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        return view('facilities.index-dt');
    }

    /**
     * Load facilities via datatables.
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
        if (! auth_can('facilities', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get('facilities/dt', [
            'query' => $request->query(),
        ]);

        $body = json_decode($apiResponse->getBody(), false);

        return response()->json($body);
    }

    /**
     * Show facility.
     *
     * @param string $facilityId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function show($facilityId)
    {
        if (! auth_can('facilities', 'view')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("facilities/{$facilityId}");

        $facility = json_decode($apiResponse->getBody(), false);

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Show create facility.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! auth_can('facilities', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        return view('facilities.create');
    }

    /**
     * Create facility.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        if (! auth_can('facilities', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->post('facilities', [
            'json' => $request->all(),
        ]);

        $facility = json_decode($apiResponse->getBody(), false);

        flash("{$facility->name} created.")->success();

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Show edit facility.
     *
     * @param string $facilityId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function edit($facilityId)
    {
        if (! auth_can('facilities', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("facilities/{$facilityId}");

        $facility = json_decode($apiResponse->getBody(), false);

        return view('facilities.edit', ['facility' => $facility]);
    }

    /**
     * Update facility.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $facilityId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function update(Request $request, $facilityId)
    {
        if (! auth_can('facilities', 'update')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("facilities/{$facilityId}", [
            'json' => $request->all(),
        ]);

        $facility = json_decode($apiResponse->getBody(), false);

        flash("{$facility->name} updated.")->success();

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Revoke facility.
     *
     * @param string $facilityId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function revoke($facilityId)
    {
        if (! auth_can('facilities', 'soft-delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("facilities/{$facilityId}/revoke");

        $facility = json_decode($apiResponse->getBody(), false);

        flash("{$facility->name} revoked.")->warning();

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Restore facility.
     *
     * @param string $facilityId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function restore($facilityId)
    {
        if (! auth_can('facilities', 'restore')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->put("facilities/{$facilityId}/restore");

        $facility = json_decode($apiResponse->getBody(), false);

        flash("{$facility->name} restored.")->success();

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Delete facility.
     *
     * @param string $facilityId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($facilityId)
    {
        if (! auth_can('facilities', 'force-delete')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $this->passwordClient->delete("facilities/{$facilityId}");

        flash('Facility deleted.')->error();

        return redirect()->route('facilities.index');
    }
}
