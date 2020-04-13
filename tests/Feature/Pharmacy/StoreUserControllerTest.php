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
class StoreUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_user_stores_unauthenticated()
    {
        $this->get(route('pharmacy.stores.index'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_user_stores()
    {
        $fakeApiResponseBody = [
            'id' => '819b948f-a0bd-4ae6-b0ea-1c47a6f3c6b2',
            'facility_id' => '437870eb-ed7c-45ad-a4f2-274499eec0f2',
            'role_id' => '6c3ab5fd-5f64-401a-8ac0-2999ba597ae1',
            'alias' => 'jdoe',
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'email_verified_at' => '2020-04-01 19:43:34',
            'created_at' => '2020-04-01 19:43:34',
            'updated_at' => '2020-04-01 19:43:34',
            'deleted_at' => null,
            'pharm_stores' => [
                [
                    'id' => 'e6c28b404ce',
                    'facility_id' => '437870eb-ed7c-45ad-a4f2-274499eec0f2',
                    'name' => 'OPD Store',
                    'created_at' => '2020-04-01 19:43:34',
                    'updated_at' => '2020-04-01 19:43:34',
                    'deleted_at' => null,
                    'pivot' => [
                        'user_id' => '819b948f-a0bd-4ae6-b0ea-1c47a6f3c6b2',
                        'store_id' => 'e6c28b404ce',
                    ],
                ],
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('pharmacy.stores.index'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('pharm-stores', 'view-any');

        $response = $this->actingAs($user)->get(route('pharmacy.stores.index'));

        $response->assertStatus(200);

        $response->assertViewIs('pharmacy.stores.index');
    }
}
