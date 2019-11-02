<?php

namespace App\Http\Controllers;

use App\Http\Clients\PasswordClientInterface;
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
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $apiResponse = $this->passwordClient->get('facilities');

        $body = json_decode($apiResponse->getBody(), false);

        return view('facilities.index', ['facilities' => $body->facilities]);
    }

    /**
     * Show facility.
     *
     * @param string $facilityId
     *
     * @return \Illuminate\View\View
     */
    public function show($facilityId)
    {
        $apiResponse = $this->passwordClient->get("facilities/{$facilityId}");

        $facility = json_decode($apiResponse->getBody(), false);

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Show create facility.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('facilities.create');
    }

    /**
     * Create facility.
     *
     * @param \Illuminate\Http\Request
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
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
     * @return \Illuminate\View\View
     */
    public function edit($facilityId)
    {
        $apiResponse = $this->passwordClient->get("facilities/{$facilityId}");

        $facility = json_decode($apiResponse->getBody(), false);

        return view('facilities.edit', ['facility' => $facility]);
    }

    /**
     * Update facility.
     *
     * @param \Illuminate\Http\Request $requests
     * @param string                   $facilityId
     * @param Request                  $request
     *
     * @return \Illuminate\View\View
     */
    public function update(Request $request, $facilityId)
    {
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
     * @param \Illuminate\Http\Request $requests
     * @param string                   $facilityId
     * @param Request                  $request
     *
     * @return \Illuminate\View\View
     */
    public function revoke(Request $request, $facilityId)
    {
        $apiResponse = $this->passwordClient->put("facilities/{$facilityId}/revoke");

        $facility = json_decode($apiResponse->getBody(), false);

        flash("{$facility->name} revoked.")->warning();

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Restore facility.
     *
     * @param \Illuminate\Http\Request $requests
     * @param string                   $facilityId
     * @param Request                  $request
     *
     * @return \Illuminate\View\View
     */
    public function restore(Request $request, $facilityId)
    {
        $apiResponse = $this->passwordClient->put("facilities/{$facilityId}/restore");

        $facility = json_decode($apiResponse->getBody(), false);

        flash("{$facility->name} restored.")->success();

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Delete facility.
     *
     * @param \Illuminate\Http\Request $requests
     * @param string                   $facilityId
     * @param Request                  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $facilityId)
    {
        $this->passwordClient->delete("facilities/{$facilityId}");

        flash('Facility deleted.')->error();

        return redirect()->route('facilities.index');
    }
}
