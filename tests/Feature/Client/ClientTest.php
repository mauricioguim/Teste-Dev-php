<?php

namespace Feature\Client;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The base URL for the API.
     *
     * @var array
     */
    protected const ROUTES = [
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
    protected array $clientData = [
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
    protected array $payload = [
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
}
