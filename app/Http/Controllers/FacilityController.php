<?php

namespace App\Http\Controllers;

use App\Http\Clients\PasswordClientInterface;

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
        $response = $this->passwordClient->get('facilities');

        $apiResponse = json_decode($response->getBody(), false);

        return view('facilities.index', ['facilities' => $apiResponse->facilities]);
    }
}
