<?php

namespace Feature\Client;

use App\Models\Client;
use App\Models\Address;
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
            ->assertJsonCount(3);
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
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'John Doe');
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
            ->assertJsonCount(1)
            ->assertJsonPath('0.cpf', '12345678909');
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_can_filter_clients_by_cep(): void
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        
        Address::factory()->create([
            'client_id' => $client1->id,
            'cep' => '12345678'
        ]);
        
        Address::factory()->create([
            'client_id' => $client2->id,
            'cep' => '87654321'
        ]);

        $response = $this->getJson(route(self::ROUTES['index'], ['cep' => '12345678']));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.address.cep', '12345678');
    }
}
