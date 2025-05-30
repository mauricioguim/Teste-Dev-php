<?php

namespace Feature\Client;

use App\Models\Address;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends ClientTest
{
    use RefreshDatabase;

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_cannot_delete_non_existent_client(): void
    {
        $response = $this->deleteJson(route(self::ROUTES['destroy'], 9999));

        $response->assertStatus(404)
                    ->assertJson([
                        'message' => 'Resource not found.',
                    ]);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_can_delete_client(): void
    {
        $client = Client::factory()->create();
        $address = Address::factory()->create(['client_id' => $client->id]);

        $response = $this->deleteJson(route(self::ROUTES['destroy'], $client->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
        $this->assertDatabaseMissing('addresses', ['client_id' => $client->id]);
    }
}
