<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Clients\PasswordClientInterface;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class SalesCartController extends Controller
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
        if (! auth_can('pharm-products', 'view-any')) {
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

        $item = \Cart::session($userId)->add($productId, $name, $price, $qty, []);

        return redirect(route('pharmacy.sales.create', $storeId));
    }

    public function update($storeId, $itemId)
    {
        $userId = auth()->id();

        // update the item on cart
        \Cart::session($userId)->update($itemId, [
            'quantity' => [
                'relative' => false,
                'value' => request('quantity'),
            ],
            // 'price' => request('price'),
        ]);

        return redirect(route('pharmacy.sales.create', $storeId));
    }

    public function destroy($storeId, $itemId)
    {
        $userId = auth()->id();

        \Cart::session($userId)->remove($itemId);

        return redirect(route('pharmacy.sales.create', $storeId));
    }
}
