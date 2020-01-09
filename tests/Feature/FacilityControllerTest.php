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
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.index'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'view-any');

        $response = $this->actingAs($user)->get(route('facilities.index'));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.index');
    }

    public function test_can_show_facilities_via_datatables()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.dt.show'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'view-any');

        $response = $this->actingAs($user)->get(route('facilities.dt.show'));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.index-dt');
    }

    public function test_can_load_facilities_via_datatables()
    {
        $fakeApiResponseBody = [
            'draw' => 1,
            'recordsTotal' => 1,
            'recordsFiltered' => 1,
            'data' => [
                [
                    'id' => '90cae4ff-6f86-4ba3-8651-134b48fd7a2a',
                    'name' => 'Mulago Hospital',
                    'description' => 'National Referral Hospital',
                    'address' => 'Mulago Hill, Kampala',
                    'email' => 'cis@mulago.Hospital',
                    'website' => 'https://mulago.Hospital',
                    'phone' => '+256392741586',
                    'created_at' => '2020-01-09T05:32:50Z',
                    'updated_at' => '2020-01-09T05:32:50Z',
                    'deleted_at' => null,
                ],
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.dt'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'view-any');

        $response = $this->actingAs($user)->get(route('facilities.dt'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'website',
                    'deleted_at',
                ],
            ],
        ]);
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

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'view');

        $response = $this->actingAs($user)->get(route('facilities.show', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.show');

        $response->assertViewHas('facility', objectify($fakeApiResponseBody));
    }

    public function test_can_show_create_facility()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.create'));

        $response->assertStatus(403);

        $this->fakeUserPermission('facilities', 'create');

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

        $response = $this->actingAs($user)->post(route('facilities.store'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'create');

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

        $response->assertViewHas('facility', objectify($fakeApiResponseBody));
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

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'update');

        $response = $this->actingAs($user)->get(route('facilities.edit', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.edit');

        $response->assertViewHas('facility', objectify($fakeApiResponseBody));
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

        $response = $this->actingAs($user)->put(route('facilities.update', $facilityId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'update');

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

        $response->assertViewHas('facility', objectify($fakeApiResponseBody));
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

        $response = $this->actingAs($user)->put(route('facilities.revoke', $facilityId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'soft-delete');

        $response = $this->actingAs($user)
            ->from(route('facilities.show', $facilityId))
            ->put(route('facilities.revoke', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.show');

        $response->assertViewHas('facility', objectify($fakeApiResponseBody));
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

        $response = $this->actingAs($user)->put(route('facilities.restore', $facilityId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'restore');

        $response = $this->actingAs($user)
            ->from(route('facilities.show', $facilityId))
            ->put(route('facilities.restore', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.show');

        $response->assertViewHas('facility', objectify($fakeApiResponseBody));
    }

    public function test_can_delete_facility()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeResponse = new Response(204, [], null);

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->delete(route('facilities.destroy', $facilityId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('facilities', 'force-delete');

        $response = $this->actingAs($user)
            ->from(route('facilities.show', $facilityId))
            ->delete(route('facilities.destroy', $facilityId));

        $response->assertRedirect(route('facilities.index'));
    }

    public function test_can_show_facility_modules()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $facilityId,
            'name' => 'Mulago Hospital',
            'description' => 'National Referral Hospital',
            'address' => 'Mulago Hill, Kampala',
            'email' => 'cis@mulago.Hospital',
            'website' => 'https://mulago.Hospital',
            'phone' => '+256392741586',
            'created_at' => '2020-01-09T07:27:43Z',
            'updated_at' => '2020-01-09T07:27:43Z',
            'deleted_at' => null,
            'modules' => [
                [
                    'name' => 'users',
                    'category' => 'uncategorized',
                    'granted' => true,
                ],
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.modules.show', $facilityId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('modules', 'assign-modules');

        $response = $this->actingAs($user)->get(route('facilities.modules.show', $facilityId));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.modules');

        $facility = objectify($fakeApiResponseBody);

        $facility->modules = collect($facility->modules)->groupBy('category');

        $response->assertViewHas('facility', $facility);
    }

    public function test_can_update_facility_modules()
    {
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';
        $facilityName = 'Mulago Hospital';

        $fakeApiResponseBody = [
            'id' => $facilityId,
            'name' => $facilityName,
            'description' => 'National Referral Hospital',
            'address' => 'Mulago Hill, Kampala',
            'email' => 'cis@mulago.Hospital',
            'website' => 'https://mulago.Hospital',
            'phone' => '+256392741586',
            'created_at' => '2020-01-09T07:27:43Z',
            'updated_at' => '2020-01-09T07:27:43Z',
            'deleted_at' => null,
            'modules' => [
                [
                    'name' => 'users',
                    'category' => 'uncategorized',
                    'granted' => true,
                ],
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->put(route('facilities.modules.update', $facilityId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('modules', 'assign-modules');

        $response = $this->actingAs($user)
            ->from(route('facilities.modules.show', $facilityId))
            ->put(route('facilities.modules.update', $facilityId), []);

        $response->assertRedirect(route('facilities.modules.show', $facilityId));
        $response->assertSessionHas('flash_notification.0.level', 'success');
        $response->assertSessionHas('flash_notification.0.message', "{$facilityName} modules updated.");
    }
}
