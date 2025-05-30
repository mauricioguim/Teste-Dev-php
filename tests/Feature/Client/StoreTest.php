<?php

namespace Feature\Client;

use App\Adapters\BrasilApi;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

class StoreTest extends ClientTest
{
    use RefreshDatabase;

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_store_client_validation_required_fields(): void
    {
        $response = $this->postJson(route(self::ROUTES['store']), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'cpf', 'email', 'address']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_cannot_store_client_with_duplicate_cpf(): void
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
    public function test_cannot_store_client_with_duplicate_email(): void
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
    public function test_store_client_validation_name_max_length(): void
    {
        $data = $this->clientData;
        $data['name'] = str_repeat('a', 256);

        $response = $this->postJson(route(self::ROUTES['store']), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_store_client_validation_cpf_size(): void
    {
        $data = $this->clientData;
        $data['cpf'] = '1234567890';

        $response = $this->postJson(route(self::ROUTES['store']), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_store_client_validation_email_format(): void
    {
        $data = $this->clientData;
        $data['email'] = 'invalid-email';

        $response = $this->postJson(route(self::ROUTES['store']), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_store_client_validation_phone_max_length(): void
    {
        $data = $this->clientData;
        $data['phone'] = str_repeat('1', 21);

        $response = $this->postJson(route(self::ROUTES['store']), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    /**
     * @test
     * @group clients
     * @return void
     */
    public function test_store_client_validation_address_required_fields(): void
    {
        $data = $this->clientData;
        $data['address'] = [];

        $response = $this->postJson(route(self::ROUTES['store']), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'address.cep',
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
    public function test_store_client_validation_address_cep_format(): void
    {
        $data = $this->clientData;
        $data['address']['cep'] = '1234567';

        $response = $this->postJson(route(self::ROUTES['store']), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address.cep']);
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
}
