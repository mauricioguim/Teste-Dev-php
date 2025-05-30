<?php

namespace Feature\Client;

use App\Adapters\BrasilApi;
use App\Models\Address;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateTest extends ClientTest
{
    use RefreshDatabase;

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

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_update_client_validation_name_max_length(): void
    {
        $client = Client::factory()->create();
        $data = ['name' => str_repeat('a', 256)];

        $response = $this->putJson(route(self::ROUTES['update'], $client->id), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_update_client_validation_cpf_size(): void
    {
        $client = Client::factory()->create();
        $data = ['cpf' => '1234567890'];

        $response = $this->putJson(route(self::ROUTES['update'], $client->id), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_update_client_validation_email_format(): void
    {
        $client = Client::factory()->create();
        $data = ['email' => 'invalid-email'];

        $response = $this->putJson(route(self::ROUTES['update'], $client->id), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_update_client_validation_phone_max_length(): void
    {
        $client = Client::factory()->create();
        $data = ['phone' => str_repeat('1', 21)];

        $response = $this->putJson(route(self::ROUTES['update'], $client->id), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_update_client_validation_address_cep_format(): void
    {
        $client = Client::factory()->create();
        $data = [
            'address' => [
                'cep' => '1234567'
            ]
        ];

        $response = $this->putJson(route(self::ROUTES['update'], $client->id), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address.cep']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_update_client_validation_address_required_fields_when_provided(): void
    {
        $client = Client::factory()->create();
        $data = [
            'address' => [
                'cep' => '89010025'
            ]
        ];

        $response = $this->putJson(route(self::ROUTES['update'], $client->id), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'address.street',
                'address.neighborhood',
                'address.city',
                'address.state'
            ]);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_update_client_validation_address_max_lengths(): void
    {
        $client = Client::factory()->create();
        $data = [
            'address' => [
                'cep' => '89010025',
                'street' => str_repeat('a', 256),
                'neighborhood' => str_repeat('a', 256),
                'city' => str_repeat('a', 256),
                'state' => str_repeat('a', 101)
            ]
        ];

        $response = $this->putJson(route(self::ROUTES['update'], $client->id), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'address.street',
                'address.neighborhood',
                'address.city',
                'address.state'
            ]);
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
}
