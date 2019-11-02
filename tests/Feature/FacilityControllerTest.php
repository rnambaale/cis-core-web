<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use App\Http\Clients\PasswordClientInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    public function test_can_see_facilities()
    {
        $fakeApiResponseBody = [
            'facilities' => [
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
        ];

        $fakeResponse = new Response(200, [], json_encode($fakeApiResponseBody));

        $fakePasswordClient = $this->mockPasswordClient($fakeResponse);

        $this->app->instance(PasswordClientInterface::class, $fakePasswordClient);

        // ...

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('facilities.index'));

        $response->assertStatus(200);

        $response->assertViewIs('facilities.index');
    }
}
