<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Clients\PasswordClientInterface;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class PurchasesCartController extends Controller
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

    public function store($storeId)
    {
        if (! auth_can('pharm-purchases', 'create')) {
            throw new AuthorizationException('Unauthorized access', 403);
        }

        $userId = auth()->id();
        $productId = request('product_id');

        //  This is temporary, we might need an end point for fetch a single inventory item
        $apiResponse = $this->passwordClient->get("pharmacy/stores/{$storeId}/products", [
            'query' => [
                'paginate'  => false,
                'limit'     => 1,
                'filters'   => [
                    'select' => ['unit_price', 'product.id', 'product.name'],
                    'where' => [
                        [
                            'column' => 'product.id',
                            'value' => $productId,
                            'operator' => '=',
                            'boolean' => 'and',
                        ],
                    ],
                ],
            ],
        ]);

        $inventory = json_decode($apiResponse->getBody(), false);

        $inventoryItem = $inventory->data[0];

        $name = $inventoryItem->product->name;
        $price = $inventoryItem->unit_price;
        $qty = 1;

        $customAttributes = [
            'unit_retail_price' =>  $inventoryItem->unit_price,
        ];

        $purchaseCart = app('purchaseCart');
        $purchaseCart->session($userId)->add($productId, $name, $price, $qty, $customAttributes);

        return redirect(route('pharmacy.purchases.create', $storeId));
    }

    public function update($storeId, $itemId)
    {
        $purchaseCart = app('purchaseCart');
        $userId = auth()->id();

        // update the item on cart
        $purchaseCart->session($userId)->update($itemId, [
            'quantity' => [
                'relative' => false,
                'value' => request('quantity'),
            ],
            'price' => request('price'),
            'attributes' => [
                'unit_retail_price' => request('unit_retail_price'),
            ],
        ]);

        return redirect(route('pharmacy.purchases.create', $storeId));
    }

    public function destroy($storeId, $itemId)
    {
        $purchaseCart = app('purchaseCart');
        $userId = auth()->id();

        $purchaseCart->session($userId)->remove($itemId);

        return redirect(route('pharmacy.purchases.create', $storeId));
    }
}
