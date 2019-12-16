<?php

namespace Tests\Feature;

use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_modules_unauthenticated()
    {
        $this->get(route('modules.index'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_modules()
    {
        $fakeApiResponseBody = [
            'modules' => [
                'current_page' => 1,
                'data' => [
                    [
                        'name' => 'users',
                        'description' => 'Users module.',
                        'category'      => 'uncategorized',
                        'created_at' => '2019-10-15 16:50:47',
                        'updated_at' => '2019-10-15 16:50:47',
                        'deleted_at' => null,
                    ],
                ],
                'first_page_url' => 'http://api.test/v1/modules?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://api.test/v1/modules?page=1',
                'next_page_url' => null,
                'path' => 'http://api.test/v1/modules',
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

        $response = $this->actingAs($user)->get(route('modules.index'));

        $response->assertStatus(200);

        $response->assertViewIs('modules.index');
    }

    public function test_can_show_module()
    {
        $moduleName = 'users';

        $fakeApiResponseBody = [
            'name'          => $moduleName,
            'description'   => 'Users module.',
            'category'      => 'uncategorized',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('modules.show', $moduleName));

        $response->assertStatus(200);

        $response->assertViewIs('modules.show');

        $response->assertViewHas('module', (object) $fakeApiResponseBody);
    }

    public function test_can_show_create_module()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('modules.create'));

        $response->assertStatus(200);

        $response->assertViewIs('modules.create');
    }

    public function test_can_create_module()
    {
        $fakeApiResponseBody = [
            'name' => 'users',
            'description' => 'Users module.',
            'category'      => 'uncategorized',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(201, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from(route('modules.create'))->post(route('modules.store'), [
            'name' => 'users',
            'description' => 'Users module.',
        ]);

        $response->assertStatus(200);

        $response->assertViewIs('modules.show');

        $response->assertViewHas('module', (object) $fakeApiResponseBody);
    }

    public function test_can_show_edit_module()
    {
        $moduleName = 'users';

        $fakeApiResponseBody = [
            'name' => $moduleName,
            'description' => 'Users module.',
            'category'      => 'uncategorized',
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('modules.edit', $moduleName));

        $response->assertStatus(200);

        $response->assertViewIs('modules.edit');

        $response->assertViewHas('module', (object) $fakeApiResponseBody);
    }

    public function test_can_edit_module()
    {
        $moduleName = 'users';

        $fakeApiResponseBody = [
            'name'          => $moduleName,
            'description'   => 'Some Description',
            'category'      => 'uncategorized',
            'created_at'    => '2019-10-15 16:50:47',
            'updated_at'    => '2019-10-15 16:50:47',
            'deleted_at'    => null,
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('modules.edit', $moduleName))
            ->put(route('modules.update', $moduleName), [
                'description'   => 'Some Description',
            ]);

        $response->assertStatus(200);

        $response->assertViewIs('modules.show');

        $response->assertViewHas('module', (object) $fakeApiResponseBody);
    }

    public function test_can_revoke_module()
    {
        $moduleName = 'users';

        $fakeApiResponseBody = [
            'name'          => $moduleName,
            'description'   => 'Users module.',
            'category'      => 'uncategorized',
            'created_at'    => '2019-10-15 16:50:47',
            'updated_at'    => '2019-10-15 16:50:47',
            'deleted_at'    => '2019-10-16 09:02:58',
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('modules.show', $moduleName))
            ->put(route('modules.revoke', $moduleName));

        $response->assertStatus(200);

        $response->assertViewIs('modules.show');

        $response->assertViewHas('module', (object) $fakeApiResponseBody);
    }

    public function test_can_restore_module()
    {
        $moduleName = 'users';

        $fakeApiResponseBody = [
            'name'          => $moduleName,
            'description'   => 'Users module.',
            'category'      => 'uncategorized',
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
            ->from(route('modules.show', $moduleName))
            ->put(route('modules.restore', $moduleName));

        $response->assertStatus(200);

        $response->assertViewIs('modules.show');

        $response->assertViewHas('module', (object) $fakeApiResponseBody);
    }

    public function test_can_delete_module()
    {
        $moduleName = 'users';

        $fakeResponse = new Response(204, [], null);

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('modules.show', $moduleName))
            ->delete(route('modules.destroy', $moduleName));

        $response->assertRedirect(route('modules.index'));
    }
}