<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Clients\PasswordClientInterface;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class PurchasesController extends Controller
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
     * Show purchases create.
     *
     * @param \Illuminate\http\Request $request
     * @param mixed                    $storeId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $storeId)
    {
        if (! auth_can('pharm-purchases', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("pharmacy/stores/{$storeId}", [
            'query' => [
                'paginate' => false,
            ],
        ]);

        $store = json_decode($apiResponse->getBody(), false);

        $purchaseCart = app('purchaseCart');

        $cartItems = [];

        $purchaseCart->session(auth()->id())->getContent()->each(function ($item) use (&$cartItems) {
            $cartItems[] = $item;
        });

        return view('pharmacy.purchases.create', [
            'storeId' => $storeId,
            'storeName' => $store->name,
            'cartItems' => $cartItems,
            'cartTotal' => $purchaseCart->getTotal(),
            'section' => 'purchases',
        ]);
    }

    /**
     * Submit purchase.
     *
     * @param \Illuminate\http\Request $request
     * @param mixed                    $storeId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $storeId)
    {
        if (! auth_can('pharm-purchases', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $requestData = $request->all();
        $requestData['store_id'] = $storeId;

        $apiResponse = $this->passwordClient->post('pharmacy/purchases', [
            'json' => $requestData,
        ]);

        // $response_body = json_decode($apiResponse->getBody(), false);

        $purchaseCart = app('purchaseCart');

        $purchaseCart->clear();
        $purchaseCart->session(auth()->id())->clear();

        flash('Credited inventory items')->success();

        return redirect(route('pharmacy.purchases.create', $storeId));
    }
}
