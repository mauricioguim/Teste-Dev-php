<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Address;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory()
            ->count(50)
            ->create()
            ->each(function ($client) {
                Address::factory()->create([
                    'client_id' => $client->id
                ]);
            });
    }
}
