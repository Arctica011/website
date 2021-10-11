<?php

namespace Tests\Feature\User;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserCrudTest extends TestCase
{

    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function testList(): void
    {
        User::factory()->count(100)->create();

        $response = $this->json('GET', '/api/user');
        $response->assertStatus(200);
        $response->assertJsonCount(10, "data.items");
        $response->assertJsonPath('data.totalItems', 101);
        $response->assertJsonPath('data.pageIndex', 1);
        $response->assertJsonPath('data.currentItemCount', 10);
    }

    public function testListPage2(): void
    {
        User::factory()->count(100)->create();

        $response = $this->json('GET', '/api/user?page=2');

        $response->assertStatus(200);
        $response->assertJsonCount(10, "data.items");
        $response->assertJsonPath('data.totalItems', 101);
        $response->assertJsonPath('data.pageIndex', 2);
        $response->assertJsonPath('data.currentItemCount', 10);
        $response->assertJsonPath('data.startIndex', 11);
    }

    public function testEqualsFilter(): void
    {
        User::factory()->count(2)->create(['name' => "i_like_a_good_search_result"]);
        User::factory()->count(100)->create();

        $response = $this->json('GET', '/api/user?filter[name]=i_like_a_good_search_result');

        $response->assertStatus(200);
        $response->assertJsonCount(2, "data.items");
    }

    public function testCreate(): void
    {
        $response = $this->json('POST', '/api/user', [
            'email' => "foo@bar.com",
            'name' => "John Cena",
            'password' => "my_epic_password",
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => [
            'id',
            'name',
            'email'
        ]]);
    }

    public function testRetrieve(): void
    {
        $toGet = User::factory()->create();
        $expected = json_decode(json_encode(new UserResource($toGet)), true);

        $response = $this->json('GET', "/api/user/{$toGet->id}");

        $response->assertStatus(200);
        $response->assertJson(['data' => $expected]);
    }

    public function testRetrieveNotFound(): void
    {
        $response = $this->json('GET', "/api/user/300");
        $response->assertNotFound();
    }

    public function testDelete(): void
    {
        $toDelete = User::factory()->create();

        $response = $this->json('DELETE', "/api/user/{$toDelete->id}");

        $response->assertStatus(200);
        $response->assertJson(['data' => [
            'deleted' => true,
            'id' => $toDelete->id,
        ]]);

        $this->assertDeleted($toDelete);
    }

    public function testDeleteNotFound(): void
    {
        $response = $this->json('DELETE', "/api/user/300");
        $response->assertNotFound();
    }
}
