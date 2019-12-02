<?php

namespace Tests\Feature;

use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FacilityController
 */
class FacilityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_facilities_unauthenticated()
    {
        $this->get(route('facilities.index'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_facilities()
    {
        $fakeApiResponseBody = [
            'facilities' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
                        'name' => 'Mulago Hospital',
                        'description' => 'Regional Referral Hospital',
                        'address' => 'Mulago Hill',
                        'email' => 'info@mulago.com',
                        'website' => 'https://mulago.ug',
                        'phone' => '+256754954852',
                        'created_at' => '2019-10-15 16:50:47',
                        'updated_at' => '2019-10-15 16:50:47',
                        'deleted_at' => null,
                    ],
                ],
                'first_page_url' => 'http://api.test/v1/facilities?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://api.test/v1/facilities?page=1',
                'next_page_url' => null,
                'path' => 'http://api.test/v1/facilities',
                'per_page' => 15,
                'prev_page_url' => null,
                'to' => 1,
                'total' => 1,
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.index'));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.index');

        // $response->assertViewHas('facilities', ((object) $fakeApiResponseBody)->facilities);
    }

    public function test_can_show_facility()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $facilityId,
            'name' => 'Mulago Hospital',
            'description' => 'Regional Referral Hospital',
            'address' => 'Mulago Hill',
            'email' => 'info@mulago.com',
            'website' => 'https://mulago.ug',
            'phone' => '+256754954852',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.show', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.show');

        $response->assertViewHas('facility', (object) $fakeApiResponseBody);
    }

    public function test_can_show_create_facility()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.create'));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.create');
    }

    public function test_can_create_facility()
    {
        $fakeApiResponseBody = [
            'id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
            'name' => 'Mulago Hospital',
            'description' => 'Regional Referral Hospital',
            'address' => 'Mulago Hill',
            'email' => 'info@mulago.com',
            'website' => 'https://mulago.ug',
            'phone' => '+256754954852',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(201, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from(route('facilities.create'))->post(route('facilities.store'), [
            'name' => 'Mulago Hospital',
            'description' => 'Regional Referral Hospital',
            'address' => 'Mulago Hill',
            'email' => 'info@mulago.com',
            'website' => 'https://mulago.ug',
            'phone' => '+256754954852',
        ]);

        $response->assertStatus(200);

        $response->assertViewIs('facilities.show');

        $response->assertViewHas('facility', (object) $fakeApiResponseBody);
    }

    public function test_can_show_edit_facility()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $facilityId,
            'name' => 'Mulago Hospital',
            'description' => 'Regional Referral Hospital',
            'address' => 'Mulago Hill',
            'email' => 'info@mulago.com',
            'website' => 'https://mulago.ug',
            'phone' => '+256754954852',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.edit', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.edit');

        $response->assertViewHas('facility', (object) $fakeApiResponseBody);
    }

    public function test_can_edit_facility()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $facilityId,
            'name' => 'Mulago Hospital',
            'description' => 'Regional Referral Hospital',
            'address' => 'Mulago Hill',
            'email' => 'info@mulago.com',
            'website' => 'https://mulago.ug',
            'phone' => '+256754954852',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('facilities.edit', $facilityId))
            ->put(route('facilities.update', $facilityId), [
                'name' => 'Mengo Hospital',
                'description' => '',
                'address' => 'Mengo',
                'email' => 'info@mengo.com',
                'website' => 'https://mengo.ug',
                'phone' => '+256754954852',
            ]);

        $response->assertStatus(200);

        $response->assertViewIs('facilities.show');

        $response->assertViewHas('facility', (object) $fakeApiResponseBody);
    }

    public function test_can_revoke_facility()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $facilityId,
            'name' => 'Mulago Hospital',
            'description' => 'Regional Referral Hospital',
            'address' => 'Mulago Hill',
            'email' => 'info@mulago.com',
            'website' => 'https://mulago.ug',
            'phone' => '+256754954852',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => '2019-10-16 09:02:58',
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('facilities.show', $facilityId))
            ->put(route('facilities.revoke', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.show');

        $response->assertViewHas('facility', (object) $fakeApiResponseBody);
    }

    public function test_can_restore_facility()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $facilityId,
            'name' => 'Mulago Hospital',
            'description' => 'Regional Referral Hospital',
            'address' => 'Mulago Hill',
            'email' => 'info@mulago.com',
            'website' => 'https://mulago.ug',
            'phone' => '+256754954852',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('facilities.show', $facilityId))
            ->put(route('facilities.restore', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.show');

        $response->assertViewHas('facility', (object) $fakeApiResponseBody);
    }

    public function test_can_delete_facility()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeResponse = new Response(204, [], null);

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('facilities.show', $facilityId))
            ->delete(route('facilities.destroy', $facilityId));

        $response->assertRedirect(route('facilities.index'));
    }
}
