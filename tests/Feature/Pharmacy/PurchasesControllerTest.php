<?php

namespace Tests\Feature\Pharmacy;

use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Pharmacy\PurchasesController
 */
class PurchasesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_store_purchases_unauthenticated()
    {
        $this->get(route('pharmacy.purchases.create', '0ac99f1b-482c-4af1-be28-ddace07eff2e'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_page_for_adding_a_store_purchase()
    {
        $storeId = '0ac99f1b-482c-4af1-be28-ddace07eff20';

        $fakeApiResponseBody = [
            'id' => '0ac99f1b-482c-4af1-be28-ddace07eff20',
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

        $response = $this->actingAs($user)->get(route('pharmacy.purchases.create', $storeId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('pharm-purchases', 'create');

        $response = $this->actingAs($user)->get(route('pharmacy.purchases.create', $storeId));

        $response->assertStatus(200);

        $response->assertViewIs('pharmacy.purchases.create');
    }

    public function test_can_submit_purchase_items_to_increament_the_inventory()
    {
        $storeId = '0ac99f1b-482c-4af1-be28-ddace07eff20';

        $fakeApiResponseBody = [
            'message' => 'Credited inventory items.',
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('pharmacy.purchases.store', $storeId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('pharm-purchases', 'create');

        $response = $this->actingAs($user)->from(route('pharmacy.purchases.create', $storeId))->post(route('pharmacy.purchases.store', $storeId), [
            'products' => [
                [
                    'id'        => '8060a978-0352-4094-a156-aabe532bc66d',
                    'quantity'  => 2,
                    'cost_price' => '100.00',
                    'unit_retail_price' => '25.00',
                ],
                [
                    'id'        => '8060a978-0352-4094-a156-aabe532bc66f',
                    'quantity'  => 3,
                    'cost_price' => '100.00',
                    'unit_retail_price' => '25.00',
                ],
            ],
        ]);

        $response->assertRedirect(route('pharmacy.purchases.create', $storeId));
    }
}
