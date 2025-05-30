<?php

namespace Tests\Feature;

use App\Adapters\BrasilApi;
use App\Models\Client;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The base URL for the API.
     *
     * @var array
     */
    private const ROUTES = [
        'index'   => 'clients.index',
        'store'   => 'clients.store',
        'update'  => 'clients.update',
        'destroy' => 'clients.destroy',
    ];

    /**
     * The data used for creating a client.
     *
     * @var array
     */
    private array $clientData = [
        'name' => 'John Doe',
        'cpf' => '12345678909',
        'email' => 'mauricio@allstrategy.com',
        'phone' => '27999999999',
        'address' => [
            'cep' => '89010025',
            'state' => 'SC',
            'city' => 'Blumenau',
            'neighborhood' => 'Centro',
            'street' => 'Rua Doutor Luiz de Freitas Melro',
        ]
    ];

    /**
     * The payload used for testing address.
     *
     * @var array
     */
    private array $payload = [
        'cep' => '89010025',
        'state' => 'SC',
        'city' => 'Blumenau',
        'neighborhood' => 'Centro',
        'street' => 'Rua Doutor Luiz de Freitas Melro',
        'service' => 'viacep',
        'location' => [
            'type' => 'Point',
            'coordinates' => [
                'longitude' => '-49.0629788',
                'latitude'  => '-26.9244749'
            ],
        ],
    ];

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

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_can_create_client(): void
    {
        $this->mock(BrasilApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getCep')
                ->once()
                ->with($this->clientData['address']['cep'])
                ->andReturn($this->payload);
        });

        $response = $this->postJson(route(self::ROUTES['store']), $this->clientData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => $this->clientData['name'],
                'cpf' => $this->clientData['cpf'],
                'email' => $this->clientData['email'],
                'phone' => $this->clientData['phone']
            ]);

        $this->assertDatabaseHas('clients', [
            'name' => $this->clientData['name'],
            'cpf' => $this->clientData['cpf'],
            'email' => $this->clientData['email']
        ]);

        $this->assertDatabaseHas('addresses', [
            'cep' => $this->clientData['address']['cep'],
            'street' => $this->clientData['address']['street'],
            'neighborhood' => $this->clientData['address']['neighborhood'],
            'city' => $this->clientData['address']['city'],
            'state' => $this->clientData['address']['state']
        ]);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_cannot_create_client_with_duplicate_cpf(): void
    {
        Client::factory()->create(['cpf' => $this->clientData['cpf']]);

        $response = $this->postJson(route(self::ROUTES['store']), $this->clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_cannot_create_client_with_duplicate_email(): void
    {
        Client::factory()->create(['email' => $this->clientData['email']]);

        $response = $this->postJson(route(self::ROUTES['store']), $this->clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_can_update_client(): void
    {
        $client = Client::factory()->create();
        $address = Address::factory()->create(['client_id' => $client->id]);

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '11988888888',
            'address' => [
                'cep' => '89010025',
                'state' => 'SC',
                'city' => 'Blumenau',
                'neighborhood' => 'Centro',
                'street' => 'Rua Doutor Luiz de Freitas Melro',
            ]
        ];

        $this->mock(BrasilApi::class, function (MockInterface $mock) use ($updateData) {
            $mock->shouldReceive('getCep')
                ->once()
                ->with($updateData['address']['cep'])
                ->andReturn($this->payload);
        });

        $response = $this->putJson(route(self::ROUTES['update'], $client->id), $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'name' => $updateData['name'],
                'phone' => $updateData['phone']
            ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => $updateData['name'],
            'phone' => $updateData['phone']
        ]);

        $this->assertDatabaseHas('addresses', [
            'client_id' => $client->id,
            'cep' => $updateData['address']['cep'],
            'street' => $updateData['address']['street'],
            'neighborhood' => $updateData['address']['neighborhood'],
            'city' => $updateData['address']['city'],
            'state' => $updateData['address']['state']
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

        $response = $this->deleteJson("/api/clients/{$client->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
        $this->assertDatabaseMissing('addresses', ['client_id' => $client->id]);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_returns_404_when_client_not_found(): void
    {
        $response = $this->putJson(route(self::ROUTES['update'], 999));

        $response->assertStatus(404);
    }
}
