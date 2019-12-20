<?php

namespace Tests\Feature;

use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_permissions_unauthenticated()
    {
        $this->get(route('permissions.index'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_permissions()
    {
        $fakeApiResponseBody = [
            'permissions' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => 1,
                        'module_name' => 'users',
                        'name' => 'view-any',
                        'description' => 'View any user',
                        'created_at' => '2018-09-30 09:42:23',
                        'updated_at' => '2018-10-02 14:27:09',
                    ],
                ],
                'first_page_url' => 'http://api.test/v1/permissions?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://api.test/v1/permissions?page=1',
                'next_page_url' => null,
                'path' => 'http://api.test/v1/permissions',
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

        $response = $this->actingAs($user)->get(route('permissions.index'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('permissions', 'view-any');

        $response = $this->actingAs($user)->get(route('permissions.index'));

        $response->assertStatus(200);

        $response->assertViewIs('permissions.index');
    }

    public function test_can_show_permissions_via_datatables()
    {
        $fakeApiResponseBody = [
            'draw' => 1,
            'recordsTotal' => 1,
            'recordsFiltered' => 1,
            'data' => [
                [
                    'id' => 1,
                    'module_name' => 'users',
                    'name' => 'view-any',
                    'description' => 'View any user',
                    'created_at' => '2018-09-30 09:42:23',
                    'updated_at' => '2018-10-02 14:27:09',
                ],
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('permissions.dt.show'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('permissions', 'view-any');

        $response = $this->actingAs($user)->get(route('permissions.dt.show'));

        $response->assertStatus(200);

        $response->assertViewIs('permissions.index-dt');
    }

    public function test_can_show_permission()
    {
        $permissionId = 1;

        $fakeApiResponseBody = [
            'id' => $permissionId,
            'module_name' => 'users',
            'name' => 'view-any',
            'description' => 'View any user',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('permissions.show', $permissionId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('permissions', 'view');

        $response = $this->actingAs($user)->get(route('permissions.show', $permissionId));

        $response->assertStatus(200);

        $response->assertViewIs('permissions.show');

        $response->assertViewHas('permission', (object) $fakeApiResponseBody);
    }

    public function test_can_show_create_permission()
    {
        $fakeApiResponseBody = [
            'modules' => [
                [
                    'name' => 'users',
                    'description' => 'Users module.',
                    'category'      => 'uncategorized',
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

        $response = $this->actingAs($user)->get(route('permissions.create'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('permissions', 'create');

        $response = $this->actingAs($user)->get(route('permissions.create'));

        $response->assertStatus(200);

        $response->assertViewIs('permissions.create');
    }

    public function test_can_create_permission()
    {
        $fakeApiResponseBody = [
            'id' => 1,
            'module_name' => 'users',
            'name' => 'view-any',
            'description' => 'View any user',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
        ];

        $fakeResponse = new Response(201, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('permissions.store'));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('permissions', 'create');

        $response = $this->actingAs($user)->from(route('permissions.create'))->post(route('permissions.store'), [
            'module_name' => 'users',
            'name' => 'view-any',
            'description' => 'View any user',
        ]);

        $response->assertStatus(200);

        $response->assertViewIs('permissions.show');

        $response->assertViewHas('permission', (object) $fakeApiResponseBody);
    }

    public function test_can_show_edit_permission()
    {
        $permissionId = 1;

        $fakeApiPermissionResponseBody = [
            'id' => $permissionId,
            'module_name' => 'users',
            'name' => 'view-any',
            'description' => 'View any user',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
        ];

        $fakePermissionResponse = new Response(200, [], json_encode($fakeApiPermissionResponseBody));

        $responses[] = $fakePermissionResponse;

        $fakeApiModulesResponseBody = [
            'modules' => [
                [
                    'name' => 'users',
                    'description' => 'Users module.',
                    'category'      => 'uncategorized',
                    'created_at' => '2019-10-15 16:50:47',
                    'updated_at' => '2019-10-15 16:50:47',
                    'deleted_at' => null,
                ],
            ],
        ];

        $fakeModulesResponse = new Response(200, [], json_encode($fakeApiModulesResponseBody));

        $responses[] = $fakeModulesResponse;

        $fakePasswordClient = $this->mockPasswordClient($responses);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('permissions.edit', $permissionId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('permissions', 'update');

        $response = $this->actingAs($user)->get(route('permissions.edit', $permissionId));

        $response->assertStatus(200);

        $response->assertViewIs('permissions.edit');

        $response->assertViewHas('permission', objectify($fakeApiPermissionResponseBody));

        $response->assertViewHas('modules', objectify($fakeApiModulesResponseBody)->modules);
    }

    public function test_can_edit_permission()
    {
        $permissionId = 1;

        $fakeApiResponseBody = [
            'id' => $permissionId,
            'module_name' => 'users',
            'name' => 'view-any',
            'description' => 'View any user',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('permissions.update', $permissionId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('permissions', 'update');

        $response = $this->actingAs($user)
            ->from(route('permissions.edit', $permissionId))
            ->put(route('permissions.update', $permissionId), [
                'module_name' => 'users',
                'name' => 'view-any',
                'description' => 'View any user',
            ]);

        $response->assertStatus(200);

        $response->assertViewIs('permissions.show');

        $response->assertViewHas('permission', (object) $fakeApiResponseBody);
    }

    public function test_can_delete_permission()
    {
        $permissionId = 1;

        $fakeResponse = new Response(204, [], null);

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->delete(route('permissions.destroy', $permissionId));

        $response->assertStatus(403);

        // ...

        $this->fakeUserPermission('permissions', 'delete');

        $response = $this->actingAs($user)
            ->from(route('permissions.show', $permissionId))
            ->delete(route('permissions.destroy', $permissionId));

        $response->assertRedirect(route('permissions.index'));
    }
}
