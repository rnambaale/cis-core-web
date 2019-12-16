<?php

namespace Tests\Feature;

use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_users_unauthenticated()
    {
        $this->get(route('users.index'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_show_users()
    {
        $fakeApiResponseBody = [
            'users' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
                        'facility_id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
                        'role_id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
                        'name' => 'John Doe',
                        'alias' => 'Jdoe',
                        'email' => 'Jdoe@example.com',
                        'email_verified_at' => '2018-09-30 17:06:12',
                        'created_at' => '2019-10-15 16:50:47',
                        'updated_at' => '2019-10-15 16:50:47',
                        'deleted_at' => null,
                    ],
                ],
                'first_page_url' => 'http://api.test/v1/users?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://api.test/v1/users?page=1',
                'next_page_url' => null,
                'path' => 'http://api.test/v1/users',
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

        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertStatus(200);

        $response->assertViewIs('users.index');
    }

    public function test_can_show_user()
    {
        $userId = $facilityId = $roleId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $userId,
            'facility_id' => $facilityId,
            'role_id' => $roleId,
            'alias' => 'jdoe',
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'email_verified_at' => '2018-09-30 17:06:12',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
            'deleted_at' => null,
            'role' => [
                'id' => $roleId,
                'facility_id' => $userId,
                'name' => 'Sys Admin',
                'description' => 'System Administrator',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
            'facility' => [
                'id' => $facilityId,
                'name' => 'Mulago Hospital',
                'description' => 'Mulago Hospital',
                'address' => 'Mulago Hill',
                'email' => 'cis@mulago.Hospital',
                'website' => 'https://mulago.Hospital',
                'phone' => '+256392741586',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('users.show', $userId));

        $response->assertStatus(200);

        $response->assertViewIs('users.show');

        $response->assertViewHas('user', (object) $fakeApiResponseBody);
    }

    public function test_can_show_create_user()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('users.create'));

        $response->assertStatus(200);

        $response->assertViewIs('users.create');
    }

    public function test_can_create_user()
    {
        $userId = $facilityId = $roleId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $userId,
            'facility_id' => $facilityId,
            'role_id' => $roleId,
            'alias' => 'jdoe',
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'email_verified_at' => '2018-09-30 17:06:12',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
            'deleted_at' => null,
            'role' => [
                'id' => $roleId,
                'facility_id' => $userId,
                'name' => 'Sys Admin',
                'description' => 'System Administrator',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
            'facility' => [
                'id' => $facilityId,
                'name' => 'Mulago Hospital',
                'description' => 'Mulago Hospital',
                'address' => 'Mulago Hill',
                'email' => 'cis@mulago.Hospital',
                'website' => 'https://mulago.Hospital',
                'phone' => '+256392741586',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
        ];

        $fakeResponse = new Response(201, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from(route('users.create'))->post(route('users.store'), [
            'name' => 'John Doe',
            'alias' => 'jdoe',
            'email' => 'jdoe@example.com',
            'role_id' => $roleId,
        ]);

        $response->assertStatus(200);

        $response->assertViewIs('users.show');

        $response->assertViewHas('user', (object) $fakeApiResponseBody);
    }

    public function test_can_show_edit_user()
    {
        $userId = $facilityId = $roleId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $userId,
            'facility_id' => $facilityId,
            'role_id' => $roleId,
            'alias' => 'jdoe',
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'email_verified_at' => '2018-09-30 17:06:12',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
            'deleted_at' => null,
            'role' => [
                'id' => $roleId,
                'facility_id' => $userId,
                'name' => 'Sys Admin',
                'description' => 'System Administrator',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
            'facility' => [
                'id' => $facilityId,
                'name' => 'Mulago Hospital',
                'description' => 'Mulago Hospital',
                'address' => 'Mulago Hill',
                'email' => 'cis@mulago.Hospital',
                'website' => 'https://mulago.Hospital',
                'phone' => '+256392741586',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('users.edit', $userId));

        $response->assertStatus(200);

        $response->assertViewIs('users.edit');

        $response->assertViewHas('user', (object) $fakeApiResponseBody);
    }

    public function test_can_edit_user()
    {
        $userId = $facilityId = $roleId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $userId,
            'facility_id' => $facilityId,
            'role_id' => $roleId,
            'alias' => 'jdoe',
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'email_verified_at' => '2018-09-30 17:06:12',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
            'deleted_at' => null,
            'role' => [
                'id' => $roleId,
                'facility_id' => $userId,
                'name' => 'Sys Admin',
                'description' => 'System Administrator',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
            'facility' => [
                'id' => $facilityId,
                'name' => 'Mulago Hospital',
                'description' => 'Mulago Hospital',
                'address' => 'Mulago Hill',
                'email' => 'cis@mulago.Hospital',
                'website' => 'https://mulago.Hospital',
                'phone' => '+256392741586',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('users.edit', $userId))
            ->put(route('users.update', $userId), [
                'name' => 'John Doe',
            ]);

        $response->assertStatus(200);

        $response->assertViewIs('users.show');

        $response->assertViewHas('user', (object) $fakeApiResponseBody);
    }

    public function test_can_revoke_user()
    {
        $userId = $facilityId = $roleId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $userId,
            'facility_id' => $facilityId,
            'role_id' => $roleId,
            'alias' => 'jdoe',
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'email_verified_at' => '2018-09-30 17:06:12',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
            'deleted_at'    => '2019-10-16 09:02:58',
            'role' => [
                'id' => $roleId,
                'facility_id' => $userId,
                'name' => 'Sys Admin',
                'description' => 'System Administrator',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
            'facility' => [
                'id' => $facilityId,
                'name' => 'Mulago Hospital',
                'description' => 'Mulago Hospital',
                'address' => 'Mulago Hill',
                'email' => 'cis@mulago.Hospital',
                'website' => 'https://mulago.Hospital',
                'phone' => '+256392741586',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('users.show', $userId))
            ->put(route('users.revoke', $userId));

        $response->assertStatus(200);

        $response->assertViewIs('users.show');

        $response->assertViewHas('user', (object) $fakeApiResponseBody);
    }

    public function test_can_restore_user()
    {
        $userId = $facilityId = $roleId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeApiResponseBody = [
            'id' => $userId,
            'facility_id' => $facilityId,
            'role_id' => $roleId,
            'alias' => 'jdoe',
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'email_verified_at' => '2018-09-30 17:06:12',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
            'deleted_at' => null,
            'role' => [
                'id' => $roleId,
                'facility_id' => $userId,
                'name' => 'Sys Admin',
                'description' => 'System Administrator',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
            'facility' => [
                'id' => $facilityId,
                'name' => 'Mulago Hospital',
                'description' => 'Mulago Hospital',
                'address' => 'Mulago Hill',
                'email' => 'cis@mulago.Hospital',
                'website' => 'https://mulago.Hospital',
                'phone' => '+256392741586',
                'created_at' => '2018-09-30 09:42:23',
                'updated_at' => '2018-10-02 14:27:09',
                'deleted_at' => null,
            ],
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('users.show', $userId))
            ->put(route('users.restore', $userId));

        $response->assertStatus(200);

        $response->assertViewIs('users.show');

        $response->assertViewHas('user', (object) $fakeApiResponseBody);
    }

    public function test_can_delete_user()
    {
        $userId = 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929';

        $fakeResponse = new Response(204, [], null);

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('users.show', $userId))
            ->delete(route('users.destroy', $userId));

        $response->assertRedirect(route('users.index'));
    }
}
