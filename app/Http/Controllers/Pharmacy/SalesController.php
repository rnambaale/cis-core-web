<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Clients\PasswordClientInterface;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class SalesController extends Controller
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
     * Show store sales.
     *
     * @param string  $storeId
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $storeId)
    {
        if (! auth_can('pharm-sales', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $from = $request->query('from', date('Y-m-d'));
        $to = $request->query('to', date('Y-m-d'));

        $apiResponse = $this->passwordClient->get("pharmacy/stores/{$storeId}", [
            'query' => [
                'paginate' => false,
            ],
        ]);

        $store = json_decode($apiResponse->getBody(), false);

        $salesApiResponse = $this->passwordClient->get('pharmacy/sales', [
            'query' => [
                'paginate' => false,
                'store_id' => $storeId,
            ],
        ]);

        $salesBody = json_decode($salesApiResponse->getBody(), false);

        return view('pharmacy.sales.index', [
            'storeId' => $storeId,
            'storeName' => $store->name,
            'sales' => $salesBody->data,
            'section' => 'sales',
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * Show sales create.
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
        if (! auth_can('pharm-stores', 'view-any')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("pharmacy/stores/{$storeId}", [
            'query' => [
                'paginate' => false,
            ],
        ]);

        $store = json_decode($apiResponse->getBody(), false);

        $cartItems = [];

        \Cart::session(auth()->id())->getContent()->each(function ($item) use (&$cartItems) {
            $cartItems[] = $item;
        });

        return view('pharmacy.sales.create', [
            // 'inventories' => $body->data,
            'storeId' => $storeId,
            'storeName' => $store->name,
            'cartItems' => $cartItems,
            'cartTotal' => \Cart::session(auth()->id())->getTotal(),
            'section' => 'sales',
        ]);
    }

    /**
     * Submit sale.
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
        if (! auth_can('pharm-sales', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $requestData = $request->all();
        $requestData['store_id'] = $storeId;

        $apiResponse = $this->passwordClient->post('pharmacy/sales', [
            'json' => $requestData,
        ]);

        // $response_body = json_decode($apiResponse->getBody(), false);

        \Cart::clear();
        \Cart::session(auth()->id())->clear();

        flash('Debited inventory items')->success();

        return redirect(route('pharmacy.sales.create', $storeId));
    }

    /**
     * Show sale.
     *
     * @param mixed $storeId
     * @param mixed $saleId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View
     */
    public function show($storeId, $saleId)
    {
        if (! auth_can('pharm-sales', 'view')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $apiResponse = $this->passwordClient->get("pharmacy/sales/{$saleId}");

        $saleData = json_decode($apiResponse->getBody(), false);

        $store = $saleData->store;
        $products = $saleData->products;
        $cashier = $saleData->user;
        $sale = [
            'id' => $saleData->id,
            'patient_id' => $saleData->patient_id,
            'tax_rate' => $saleData->tax_rate,
            'total' =>  $saleData->total,
            'created_at' => $saleData->created_at,
        ];

        return view('pharmacy.sales.show', [
            'storeId' => $storeId,
            'storeName' => $store->name,
            'products' => $products,
            'cashier' => $cashier,
            'sale' => $sale,
            'section' => 'sales',
        ]);
    }
}
