<?php

namespace Tests\Feature\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Http\Clients\PasswordClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Auth\LoginController
 */
class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $fakeApiUserResponseBody = [
        'id' => '215bf10c-acd6-4643-aaa7-ec120df74cc3',
        'facility_id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
        'role_id' => '30edc518-0368-4d88-95fe-95bb765d31c2',
        'alias' => 'jdoe',
        'name' => 'John Doe',
        'email' => 'jdoe@example.com',
        'email_verified_at' => '2019-10-19 14:15:47',
        'created_at' => '2019-10-15 16:50:47',
        'updated_at' => '2019-10-19 14:16:59',
        'deleted_at' => null,
        'facility' => [
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
        'role' => [
            'id' => '30edc518-0368-4d88-95fe-95bb765d31c2',
            'facility_id' => 'bc6d2fb7-caa9-40ae-b29e-fab51aeea929',
            'name' => 'Developer',
            'description' => null,
            'created_at' => '2019-10-15 16:50:47',
            'updated_at' => '2019-10-15 16:50:47',
            'deleted_at' => null,
        ],
        'token' => [
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGcNGY4ODNhMzRmMTM0NTdmMTkyMGNlY...',
            'refresh_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijk4OGM1...',
        ],
    ];

    public function test_cant_visit_login_when_authenticated()
    {
        $user = factory(User::class)->make();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('home'));
    }

    public function test_can_visit_login_if_not_authenticated()
    {
        $response = $this->get(route('login'));

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    public function test_cant_login_with_invalid_email()
    {
        $fakeApiResponseHeaders = [
            'Content-Type' => 'application/json',
        ];

        $fakeApiResponseBody = [
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [
                    'Wrong email or password.',
                ],
            ],
        ];

        $fakeResponse = new Response(422, $fakeApiResponseHeaders, json_encode($fakeApiResponseBody));

        $fakeMachineClient = $this->mockMachineClient($fakeResponse);

        $this->app->instance(ClientCredentialsClientInterface::class, $fakeMachineClient);

        // ...

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'unknown@example.com',
            'password' => 'gJrFhC2B-!Y!4CTk',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_cant_login_with_invalid_password()
    {
        $fakeApiResponseBody = [
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [
                    'Wrong email or password.',
                ],
            ],
        ];

        $fakeResponse = new Response(422, [], json_encode($fakeApiResponseBody));

        $fakeMachineClient = $this->mockMachineClient($fakeResponse);

        $this->app->instance(ClientCredentialsClientInterface::class, $fakeMachineClient);

        // ...

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'jdoe@example.com',
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_redirects_on_unknown_bad_request()
    {
        $fakeApiResponseBody = [
            'message' => 'Some random 4xx error.',
        ];

        $fakeResponse = new Response(400, [], json_encode($fakeApiResponseBody));

        $fakeMachineClient = $this->mockMachineClient($fakeResponse);

        $this->app->instance(ClientCredentialsClientInterface::class, $fakeMachineClient);

        // ...

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'jdoe@example.com',
            'password' => 'gJrFhC2B-!Y!4CTk',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('flash_notification.0.level', 'warning');
        $response->assertSessionHas('flash_notification.0.message', 'Some random 4xx error.');
        $this->assertGuest();
    }

    public function test_cant_make_more_than_five_failed_login_attempts_a_minute()
    {
        $fakeApiResponseBody = [
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [
                    'Wrong email or password.',
                ],
            ],
        ];

        $fakeResponse = new Response(422, [], json_encode($fakeApiResponseBody));

        $fakeResponses = array_fill(0, 5, $fakeResponse);

        $fakeMachineClient = $this->mockMachineClient($fakeResponses);

        $this->app->instance(ClientCredentialsClientInterface::class, $fakeMachineClient);

        // ...

        foreach (range(0, 5) as $_) {
            $response = $this->from(route('login'))->post(route('login'), [
                'email' => 'jdoe@example.com',
                'password' => 'invalid-password',
            ]);
        }

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'Too many login attempts.',
            collect($response->baseResponse->getSession()->get('errors')->getBag('default')->get('email'))->first()
        );
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_can_login_with_correct_credentials()
    {
        $fakeResponse = new Response(200, [], json_encode($this->fakeApiUserResponseBody));

        $fakeMachineClient = $this->mockMachineClient($fakeResponse);

        $this->app->instance(ClientCredentialsClientInterface::class, $fakeMachineClient);

        // ...

        $fakeResponse = new Response(200, [], json_encode([
            'id' => 'f0f95c23-6ba1-4348-b1d9-bfb5e4bb1e3f',
            'facility_id' => '0ac99f1b-482c-4af1-be28-ddace07eff20',
            'name' => 'Sys Admin',
            'description' => 'System Administrator',
            'created_at' => '2018-09-30 09:42:23',
            'updated_at' => '2018-10-02 14:27:09',
            'deleted_at' => null,
            'permissions' => [
                [
                    'id' => 1,
                    'name' => 'view-any',
                    'granted' => true,
                    'module' => [
                        'name' => 'users',
                        'category' => 'uncategorized',
                    ],
                ],
                [
                    'id' => 2,
                    'name' => 'force-delete',
                    'granted' => false,
                    'module' => [
                        'name' => 'users',
                        'category' => 'uncategorized',
                    ],
                ],
            ],
        ]));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $response = $this->post(route('login'), [
            'email' => 'jdoe@example.com',
            'password' => 'gJrFhC2B-!Y!4CTk',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('token');
        $response->assertSessionHas('categories');
        $response->assertSessionHas('modules');

        $user = User::find('215bf10c-acd6-4643-aaa7-ec120df74cc3');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * @throws \Exception
     */
    public function test_can_be_remembered()
    {
        $fakeResponse = new Response(200, [], json_encode($this->fakeApiUserResponseBody));

        $fakeMachineClient = $this->mockMachineClient($fakeResponse);

        $this->app->instance(ClientCredentialsClientInterface::class, $fakeMachineClient);

        // ...

        $fakeResponse = new Response(200, [], json_encode(['permissions' => []]));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $response = $this->post(route('login'), [
            'email' => 'jdoe@example.com',
            'password' => 'gJrFhC2B-!Y!4CTk',
            'remember' => 'on',
        ]);

        $user = User::find('215bf10c-acd6-4643-aaa7-ec120df74cc3');

        $response->assertRedirect(route('home'));
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        $response->assertSessionHas('token');
        $response->assertSessionHas('categories');
        $response->assertSessionHas('modules');

        $this->assertAuthenticatedAs($user);
    }

    public function test_cant_logout_if_not_authenticated()
    {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_can_logout_if_authenticated()
    {
        $fakeResponse = new Response(204, [], null);

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $this->be(factory(User::class)->create());

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
