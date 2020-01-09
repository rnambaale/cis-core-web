<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Auth\ConfirmPasswordController
 */
class ConfirmPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_confirm_password_when_unauthenticated()
    {
        $response = $this->get(route('password.confirm'));

        $response->assertRedirect(route('login'));
    }

    public function test_can_visit_confirm_password_when_authenticated()
    {
        $user = factory(User::class)->create([]);

        $response = $this->actingAs($user)->get(route('password.confirm'));

        $response->assertStatus(200);

        $response->assertViewIs('auth.passwords.confirm');
    }

    public function test_cant_confirm_password_with_an_invalid_one()
    {
        $user = factory(User::class)->create([
            'password' => Hash::make('user-password'),
        ]);

        $response = $this->actingAs($user)->from(route('password.confirm'))->post(route('password.confirm'), [
            'password' => 'invalid_password',
        ]);

        $response->assertRedirect(route('password.confirm'));
        $response->assertSessionMissing('auth.password_confirmed_at');
    }

    public function test_can_confirm_password_with_a_valid_one()
    {
        $user = factory(User::class)->create([
            'password' => Hash::make('user-password'),
        ]);

        $response = $this->actingAs($user)->from(route('password.confirm'))->post(route('password.confirm'), [
            'password' => 'user-password',
        ]);

        //$response->assertRedirect(route('password.confirm'));
        $response->assertSessionHas('auth.password_confirmed_at');
    }
}
