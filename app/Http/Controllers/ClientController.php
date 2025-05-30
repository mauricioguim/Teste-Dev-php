<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $clientService
    ) {}

    /**
     * Display a listing of the clients.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'cpf', 'cep']);
        $clients = $this->clientService->list($filters);

        return response()->json(ClientResource::collection($clients));
    }

    /**
     * Store a new client in storage.
     *
     * @param StoreClientRequest $request
     * @return JsonResponse
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->clientService->store($request->validated());

        return response()->json(new ClientResource($client), 201);
    }

    /**
     * Update the specified client in storage.
     *
     * @param UpdateClientRequest $request
     * @param Client $client
     * @return JsonResponse
     */
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $client = $this->clientService->update($client->id, $request->validated());

        return response()->json(new ClientResource($client));
    }

    /**
     * Remove the specified client from storage.
     *
     * @param Client $client
     * @return JsonResponse
     */
    public function destroy(Client $client): JsonResponse
    {
        $this->clientService->delete($client->id);

        return response()->json(null, 204);
    }
}
