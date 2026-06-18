<?php

namespace App\Repositories;

use App\Models\Client;

class SelfClientRepository
{
    public function findByUserId(int $userId): ?Client
    {
        return Client::where('user_id', $userId)->first();
    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client->fresh();
    }

    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    public function deactivate(Client $client): Client
    {
        $client->update(['is_active' => false]);
        return $client->fresh();
    }

    public function findByDocument(string $document): ?Client
    {
        $cleanDocument = preg_replace('/[^0-9]/', '', $document);
        return Client::where('document_number', $cleanDocument)->first();
    }

    /**
     * Verifica se email já está em uso (busca por email_hash)
     */
    public function emailExists(string $email, ?int $excludeClientId = null): bool
    {
        $emailHash = hash('sha256', $email);
        $query = Client::where('email_hash', $emailHash);

        if ($excludeClientId) {
            $query->where('id', '!=', $excludeClientId);
        }

        return $query->exists();
    }

    public function getAddresses(int $clientId)
    {
        return Client::find($clientId)->addresses()
            ->orderBy('is_delivery_address', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addAddress(int $clientId, array $addressData)
    {
        $client = Client::find($clientId);
        return $client->addresses()->create($addressData);
    }

    public function updateAddress(int $addressId, array $addressData)
    {
        $address = \App\Models\Address::findOrFail($addressId);
        $address->update($addressData);
        return $address->fresh();
    }

    public function removeAddress(int $addressId): bool
    {
        return \App\Models\Address::findOrFail($addressId)->delete();
    }

    public function setDeliveryAddress(int $clientId, int $addressId)
    {
        $client = Client::find($clientId);
        $client->addresses()->update(['is_delivery_address' => false]);
        $client->addresses()->where('id', $addressId)->update(['is_delivery_address' => true]);
    }
}