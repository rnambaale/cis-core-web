<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\HomeController
 */
class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_visit_home_unauthenticated()
    {
        $this->get('/')->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_can_visit_home()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }
}
