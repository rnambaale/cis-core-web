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
}
