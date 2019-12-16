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

        $response->assertStatus(200);

        $response->assertViewIs('permissions.index');
    }

    public function test_can_show_permission()
    {
        $fakeApiResponseBody = [
            'id' => 1,
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

        $response = $this->actingAs($user)->get(route('permissions.show', 1));

        $response->assertStatus(200);

        $response->assertViewIs('permissions.show');

        $response->assertViewHas('permission', (object) $fakeApiResponseBody);
    }

    public function test_can_show_create_permission()
    {
        $user = factory(User::class)->create();

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
        $fakeApiResponseBody = [
            'id' => 1,
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

        $response = $this->actingAs($user)->get(route('permissions.edit', 1));

        $response->assertStatus(200);

        $response->assertViewIs('permissions.edit');

        $response->assertViewHas('permission', (object) $fakeApiResponseBody);
    }

    public function test_can_edit_permission()
    {
        $fakeApiResponseBody = [
            'id' => 1,
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

        $response = $this->actingAs($user)
            ->from(route('permissions.edit', 1))
            ->put(route('permissions.update', 1), [
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
        $fakeResponse = new Response(204, [], null);

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('permissions.show', 1))
            ->delete(route('permissions.destroy', 1));

        $response->assertRedirect(route('permissions.index'));
    }
}
