<?php

namespace Tests\Feature\Profile;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{

    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testRetrieve(): void
    {
        Sanctum::actingAs($this->user);

        $expected = json_decode(json_encode(new UserResource($this->user)), true);

        $response = $this->json('GET', "/api/profile");

        $response->assertStatus(200);
        $response->assertJson(['data' => $expected]);
    }

    public function testRetrieveFailed(): void
    {
        $response = $this->json('GET', "/api/profile");
        $response->assertStatus(401);
    }
}
