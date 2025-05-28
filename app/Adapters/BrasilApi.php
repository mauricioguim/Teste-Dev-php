<?php

namespace App\Adapters;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;

class BrasilApi
{
    /**
     * Guzzle HTTP client instance
     *
     * @var Client
     */
    private Client $client;

    /**
     * BrasilApi constructor.
     * Initializes the Guzzle HTTP client with configuration settings
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => Config::get('cep.base_uri'),
        ]);
    }

    /**
     * Get CEP information from Brasil API
     *
     * @param string $cep
     * @return array|null
     * @throws GuzzleException
     */
    public function getCep(string $cep): ?array
    {
        try {
            $response = $this->client->get("/api/cep/v2/{$cep}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() !== 200) {
                return null;
            }
            throw $e;
        }
    }
}
