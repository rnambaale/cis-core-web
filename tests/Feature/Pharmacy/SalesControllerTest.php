<?php

namespace Tests\Feature\Pharmacy;

use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Pharmacy\SalesController
 */
class SalesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_store_sales_unauthenticated()
    {
        $this->get(route('pharmacy.sales.create', '0ac99f1b-482c-4af1-be28-ddace07eff2e'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_page_for_adding_a_store_sale()
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

        $response = $this->actingAs($user)->get(route('pharmacy.sales.create', $storeId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('pharm-stores', 'view-any');

        $response = $this->actingAs($user)->get(route('pharmacy.sales.create', $storeId));

        $response->assertStatus(200);

        $response->assertViewIs('pharmacy.sales.create');
    }

    public function test_can_submit_sales_items_for_deduction_in_inventory()
    {
        $storeId = '0ac99f1b-482c-4af1-be28-ddace07eff20';

        $fakeApiResponseBody = [
            'message' => 'Debited inventory items.',
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('pharmacy.sales.store', $storeId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('pharm-sales', 'create');

        $response = $this->actingAs($user)->from(route('pharmacy.sales.create', $storeId))->post(route('pharmacy.sales.store', $storeId), [
            'products' => [
                [
                    'id'        => '8060a978-0352-4094-a156-aabe532bc66d',
                    'quantity'  => 2,
                ],
                [
                    'id'        => '8060a978-0352-4094-a156-aabe532bc66f',
                    'quantity'  => 3,
                ],
            ],
        ]);

        $response->assertRedirect(route('pharmacy.sales.create', $storeId));
    }

    // public function test_can_show_store_sales()
    // {

    //     $storeId = '0ac99f1b-482c-4af1-be28-ddace07eff20';

    //     $fakeApiResponseBody = [
    //         "current_page" => 1,
    //         "data" => [
    //             [
    //                 "id" => "8060a978-0352-4094-a156-aabe532bc66d",
    //                 "store_id" => "783125fc-b061-4f04-ae46-64d2dac93fb8",
    //                 "patient_id" => null,
    //                 "tax_rate" => "0.00",
    //                 "total" => "2500.00",
    //                 "created_at" => "2020-04-14T14:22:20Z",
    //                 "updated_at" => "2020-04-14T14:22:20Z"
    //             ]
    //         ],
    //         "first_page_url" => "http://sandbox-api.example.com/v1/pharmacy/sales?page=1",
    //         "from" => 1,
    //         "last_page" => 1,
    //         "last_page_url" => 0,
    //         "next_page_url" => null,
    //         "path" => "http://sandbox-api.example.com/v1/pharmacy/sales",
    //         "per_page" => 10,
    //         "prev_page_url" => null,
    //         "to" => 1,
    //         "total" => 1
    //     ];

    //     $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

    //     $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

    //     $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

    //     // ...

    //     $user = factory(User::class)->create();

    //     $response = $this->actingAs($user)->get(route('pharmacy.sales.index', $storeId));

    //     $response->assertStatus(403);

    //     // ...

    //     $this->fakeUserPermission('pharm-sales', 'view-any');

    //     $response = $this->actingAs($user)->get(route('pharmacy.sales.index', $storeId));

    //     dd($response->body);

    //     $response->assertStatus(200);

    //     $response->assertViewIs('pharmacy.sales.index');
    // }
}
