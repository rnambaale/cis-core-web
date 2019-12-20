<?php

namespace Tests\Feature;

use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_roles_unauthenticated()
    {
        $this->get(route('roles.index'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_roles()
    {
        $fakeApiResponseBody = [
            'roles' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
                        'facility_id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
                        'name' => 'Developer',
                        'description' => 'Role Description',
                        'created_at' => '2019-10-15 16:50:47',
                        'updated_at' => '2019-10-15 16:50:47',
                        'deleted_at' => null,
                    ],
                ],
                'first_page_url' => 'http://api.test/v1/roles?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://api.test/v1/roles?page=1',
                'next_page_url' => null,
                'path' => 'http://api.test/v1/roles',
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

        $response = $this->actingAs($user)->get(route('roles.index'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'view-any');

        $response = $this->actingAs($user)->get(route('roles.index'));

        $response->assertStatus(200);

        $response->assertViewIs('roles.index');
    }

    public function test_can_show_roles_via_datatables()
    {
        $fakeApiResponseBody = [
            'draw' => 1,
            'recordsTotal' => 1,
            'recordsFiltered' => 1,
            'data' => [
                [
                    'id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
                    'facility_id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
                    'name' => 'Developer',
                    'description' => 'Role Description',
                    'created_at' => '2019-10-15 16:50:47',
                    'updated_at' => '2019-10-15 16:50:47',
                    'deleted_at' => null,
                ],
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('roles.dt.show'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'view-any');

        $response = $this->actingAs($user)->get(route('roles.dt.show'));

        $response->assertStatus(200);

        $response->assertViewIs('roles.index-dt');
    }

    public function test_can_show_role()
    {
        $roleId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';
        $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $roleId,
            'facility_id' => $facilityId,
            'name' => 'Developer',
            'description' => 'Role Description.',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('roles.show', $roleId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'view');

        $response = $this->actingAs($user)->get(route('roles.show', $roleId));

        $response->assertStatus(200);

        $response->assertViewIs('roles.show');

        $response->assertViewHas('role', objectify($fakeApiResponseBody));
    }

    public function test_can_show_create_role()
    {
        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('roles.create'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'create');

        $response = $this->actingAs($user)->get(route('roles.create'));

        $response->assertStatus(200);

        $response->assertViewIs('roles.create');
    }

    public function test_can_create_role()
    {
        $fakeApiResponseBody = [
            'id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
            'facility_id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
            'name' => 'Manager Role',
            'description' => 'Role Description',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(201, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('roles.store'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'create');

        $response = $this->actingAs($user)->from(route('roles.create'))->post(route('roles.store'), [
            'name' => 'Manager Role',
            'description' => 'Role Description',
        ]);

        $response->assertStatus(200);

        $response->assertViewIs('roles.show');

        $response->assertViewHas('role', objectify($fakeApiResponseBody));
    }

    public function test_can_show_edit_role()
    {
        $roleId = $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $roleId,
            'facility_id' => $facilityId,
            'name' => 'Manager Role',
            'description' => 'Role Description',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('roles.edit', $roleId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'update');

        $response = $this->actingAs($user)->get(route('roles.edit', $roleId));

        $response->assertStatus(200);

        $response->assertViewIs('roles.edit');

        $response->assertViewHas('role', objectify($fakeApiResponseBody));
    }

    public function test_can_edit_role()
    {
        $roleId = $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id'            => $roleId,
            'facility_id'   => $facilityId,
            'name'          => 'Supervisor Role',
            'description'   => 'Role Description',
            'created_at'    => '2019-10-15 16:50:47',
            'updated_at'    => '2019-10-15 16:50:47',
            'deleted_at'    => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->put(route('roles.update', $roleId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'update');

        $response = $this->actingAs($user)
            ->from(route('roles.edit', $roleId))
            ->put(route('roles.update', $roleId), [
                'name' => 'Supervisor Role',
                'description' => '',
            ]);

        $response->assertStatus(200);

        $response->assertViewIs('roles.show');

        $response->assertViewHas('role', objectify($fakeApiResponseBody));
    }

    public function test_can_revoke_role()
    {
        $roleId = $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id'            => $roleId,
            'facility_id'   => $facilityId,
            'name'          => 'Developer',
            'description'   => 'Role Description',
            'created_at'    => '2019-10-15 16:50:47',
            'updated_at'    => '2019-10-15 16:50:47',
            'deleted_at'    => '2019-10-16 09:02:58',
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->put(route('roles.revoke', $roleId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'soft-delete');

        $response = $this->actingAs($user)
            ->from(route('roles.show', $roleId))
            ->put(route('roles.revoke', $roleId));

        $response->assertStatus(200);

        $response->assertViewIs('roles.show');

        $response->assertViewHas('role', objectify($fakeApiResponseBody));
    }

    public function test_can_restore_role()
    {
        $roleId = $facilityId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $roleId,
            'facility_id' => $facilityId,
            'name'          => 'Developer',
            'description'   => 'Role Description',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->put(route('roles.restore', $roleId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'restore');

        $response = $this->actingAs($user)
            ->from(route('roles.show', $roleId))
            ->put(route('roles.restore', $roleId));

        $response->assertStatus(200);

        $response->assertViewIs('roles.show');

        $response->assertViewHas('role', objectify($fakeApiResponseBody));
    }

    public function test_can_delete_role()
    {
        $roleId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeResponse = new Response(204, [], null);

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->delete(route('roles.destroy', $roleId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('roles', 'force-delete');

        $response = $this->actingAs($user)
            ->from(route('roles.show', $roleId))
            ->delete(route('roles.destroy', $roleId));

        $response->assertRedirect(route('roles.index'));
    }
}
