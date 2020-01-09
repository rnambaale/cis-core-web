<?php

namespace Tests\Feature\Auth;

use App\Http\Clients\ClientCredentialsClientInterface;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Auth\ResetPasswordController
 */
class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function getResetToken($user)
    {
        return Password::broker()->createToken($user);
    }

    public function test_cant_visit_reset_password_when_authenticated()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('password.reset', $this->getResetToken($user)));

        $response->assertRedirect(route('home'));
    }

    public function test_can_visit_reset_password_when_unauthenticated()
    {
        $user = factory(User::class)->create();

        $reset_token = $this->getResetToken($user);

        $response = $this->get(route('password.reset', $reset_token));

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.reset');
        $response->assertViewHas('token', $reset_token);
    }

    public function test_cant_reset_password_with_invalid_token()
    {
        $user = factory(User::class)->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->from(route('password.reset', 'unknown_token'))->post(route('password.update'), [
            'token' => 'unknown_token',
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect(route('password.reset', 'unknown_token'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function test_cant_reset_password_with_expired_token()
    {
        $user = factory(User::class)->create([
            'password' => Hash::make('old-password'),
        ]);

        $token = $this->getResetToken($user);

        DB::table('password_resets')->where('email', $user->email)->update(['created_at' => '1970-01-01 00:00:00']);

        $response = $this->from(route('password.reset', 'expired_token'))->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect(route('password.reset', 'expired_token'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function test_cant_reset_password_with_invalid_email()
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

        $user = factory(User::class)->create([
            'password' => Hash::make('old-password'),
        ]);

        $reset_token = $this->getResetToken($user);

        $response = $this->from(route('password.reset', $reset_token))->post(route('password.update'), [
            'token' => $reset_token,
            'email' => '',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect(route('password.reset', $reset_token));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function test_cant_reset_password_with_invalid_password()
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

        $user = factory(User::class)->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->from(route('password.reset', $token = $this->getResetToken($user)))->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('password.reset', $token));
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    /**
     * @see https://stackoverflow.com/a/54817274/2732184
     */
    public function test_can_reset_password_with_valid_token()
    {
        $fakeResponse = new Response(204, [], null);

        $fakeMachineClient = $this->mockMachineClient($fakeResponse);

        $this->app->instance(ClientCredentialsClientInterface::class, $fakeMachineClient);

        // ...

        $user = factory(User::class)->create();

        Event::fake();

        $response = $this->post(route('password.update'), [
            'token' => $this->getResetToken($user),
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertEquals($user->email, $user->fresh()->email);

        $this->assertGuest();

        // Event::assertDispatched(PasswordReset::class, function ($e) use ($user) {
        //     return $e->user->id === $user->id;
        // });
    }
}
