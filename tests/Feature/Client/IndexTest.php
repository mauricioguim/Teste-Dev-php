<?php

namespace Feature\Client;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTest extends ClientTest
{
    use RefreshDatabase;

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_can_list_clients(): void
    {
        Client::factory()->count(3)->create();

        $response = $this->getJson(route(self::ROUTES['index']));

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_can_filter_clients_by_name(): void
    {
        Client::factory()->create(['name' => 'John Doe']);
        Client::factory()->create(['name' => 'Jane Smith']);

        $response = $this->getJson(route(self::ROUTES['index'], ['name' => 'John Doe']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'John Doe');
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_can_filter_clients_by_cpf(): void
    {
        Client::factory()->create(['cpf' => '12345678909']);
        Client::factory()->create(['cpf' => '98765432109']);

        $response = $this->getJson(route(self::ROUTES['index'], ['cpf' => '12345678909']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.cpf', '12345678909');
    }
}
