<?php

namespace Tests\Feature\Pharmacy;

use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Pharmacy\StoreUserController
 */
class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_store_inventory_unauthenticated()
    {
        $this->get(route('pharmacy.inventories.index', '0ac99f1b-482c-4af1-be28-ddace07eff20'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_store_inventory_page_for_datatables()
    {
        $storeId = '0ac99f1b-482c-4af1-be28-ddace07eff20';

        $fakeApiResponseBody = [
            'id' => 'e68b75bdf25',
            'facility_id' => '0ac99f1b-482c-4af1-be28-ddace07eff20',
            'name' => 'OPD Pharmacy',
            'created_at' => '2020-04-04T17:23:08Z',
            'updated_at' => '2020-04-04T17:23:08Z',
            'deleted_at' => null,
            'facility' => [
                'id' => '90cae4ff-6f86-4ba3-8651-134b48fd7a2a',
                'name' => 'Mulago Hospital',
                'description' => 'National Referral Hospital',
                'address' => 'Mulago Hill, Kampala',
                'email' => 'cis@mulago.Hospital',
                'website' => 'https://mulago.Hospital',
                'phone' => '+256392741586',
                'created_at' => '2020-04-04T17:23:08Z',
                'updated_at' => '2020-04-04T17:23:08Z',
                'deleted_at' => null,
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('pharmacy.inventories.index', $storeId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('pharm-stores', 'view-any');

        $response = $this->actingAs($user)->get(route('pharmacy.inventories.index', $storeId));

        $response->assertStatus(200);

        $response->assertViewIs('pharmacy.inventories.index');
    }

    public function test_can_load_store_inventories_via_datatables()
    {
        $storeId = '0ac99f1b-482c-4af1-be28-ddace07eff20';

        $fakeApiResponseBody = [
            'draw' => 1,
            'recordsTotal' => 1,
            'recordsFiltered' => 1,
            'data' => [
                [
                    'id' => '90cae4ff-6f86-4ba3-8651-134b48fd7a2a',
                    'product_id' => '90cae4ff-6f86-4ba3-8651-134b48fd7a2a',
                    'quantity' => 12,
                    'unit_price' => 2000,
                ],
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('pharmacy.inventories.index.dt', $storeId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('pharm-stores', 'view-any');

        $response = $this->actingAs($user)->get(route('pharmacy.inventories.index.dt', $storeId));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                '*' => [
                    'id',
                    'product_id',
                    'quantity',
                    'unit_price',
                ],
            ],
        ]);
    }

    public function test_can_load_suggestions_for_store_inventories()
    {
        $storeId = '0ac99f1b-482c-4af1-be28-ddace07eff20';

        $fakeApiResponseBody = [
            'current_page' => 1,
            'data' => [
                [
                    'id' => 'e6c28b40a1e',
                    'quantity' => 10,
                    'unit_price' => '2000',
                ],
            ],
            'first_page_url' => 'http://cis-core-api.test/api/v1/pharmacy/inventories?page=1',
            'from' => 1,
            'last_page' => 1,
            'last_page_url' => 'http://cis-core-api.test/api/v1/pharmacy/inventories?page=1',
            'next_page_url' => null,
            'path' => 'http://cis-core-api.test/api/v1/pharmacy/inventories',
            'per_page' => 15,
            'prev_page_url' => null,
            'to' => 1,
            'total' => 1,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('pharmacy.inventories.index_suggest', $storeId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('pharm-products', 'view-any');

        $response = $this->actingAs($user)->get(route('pharmacy.inventories.index_suggest', $storeId));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'quantity',
                    'unit_price',
                ],
            ],
        ]);
    }
}
