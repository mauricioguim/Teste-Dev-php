<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientService
{
    /**
     * List clients with optional filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Client::query()->with('address');

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['cpf'])) {
            $query->where('cpf', $filters['cpf']);
        }

        if (isset($filters['cep'])) {
            $query->whereHas('address', function ($q) use ($filters) {
                $q->where('addresses.cep', $filters['cep']);
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Find a client by ID
     *
     * @param int $id
     * @return Model
     * @throws ModelNotFoundException
     */
    public function find(int $id): Model
    {
        $client = Client::query()
            ->with('address')
            ->find($id);

        if (!$client) {
            throw new ModelNotFoundException("Client not found.");
        }

        return $client;
    }

    /**
     * Store a new client
     *
     * @param array $data
     * @return Model
     */
    public function store(array $data): Model
    {
        $client = Client::query()->create($data['client'] ?? $data);

        if (isset($data['address'])) {
            $client->address()->create($data['address']);
        }

        return $client->load('address');
    }

    /**
     * Update an existing client
     *
     * @param int $id
     * @param array $data
     * @return Model
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $client = $this->find($id);

        $client->update($data['client'] ?? $data);

        if (isset($data['address'])) {
            $client->address()->update($data['address']);
        }

        return $client->load('address');
    }

    /**
     * Delete a client
     *
     * @param int $id
     * @return void
     * @throws ModelNotFoundException
     */
    public function delete(int $id): void
    {
        $client = $this->find($id);
        $client->delete();
    }
}
