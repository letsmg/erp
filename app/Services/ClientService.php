<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Repositories\ClientRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientService
{
    public function __construct(private readonly ClientRepository $repository) {}

    public function createClientWithUser(array $clientData, array $userData): array
    {
        return DB::transaction(function () use ($clientData, $userData) {
            $userData['access_level'] = 2;
            $userData['is_active'] = true;
            $user = User::create($userData);

            $clientData['user_id'] = $user->id;
            $client = $this->repository->create($clientData);

            return [
                'client' => $client,
                'user' => $user,
            ];
        });
    }

    public function createClientOnly(array $data): Client
    {
        return $this->repository->create($data);
    }

    public function updateClientWithUser(Client $client, array $clientData, ?array $userData = null): Client
    {
        return DB::transaction(function () use ($client, $clientData, $userData) {
            $this->repository->update($client, $clientData);

            if ($userData && $client->user) {
                $client->user->update($userData);
            }

            return $client->fresh();
        });
    }

    public function findByUserId(int $userId): ?Client
    {
        return $this->repository->findByUserId($userId);
    }

    public function validateDocument(string $document, ?int $excludeId = null): array
    {
        $cleanDocument = preg_replace('/[^0-9]/', '', $document);

        if (strlen($cleanDocument) === 11) {
            if (!$this->isValidCPF($cleanDocument)) {
                return [
                    'valid' => false,
                    'message' => 'CPF inválido',
                    'type' => 'CPF',
                ];
            }
        }

        if (strlen($cleanDocument) === 14) {
            if (!$this->isValidCNPJ($cleanDocument)) {
                return [
                    'valid' => false,
                    'message' => 'CNPJ inválido',
                    'type' => 'CNPJ',
                ];
            }
        }

        if ($this->repository->documentExists($cleanDocument, $excludeId)) {
            return [
                'valid' => false,
                'message' => 'Documento já cadastrado',
                'type' => 'duplicate',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Documento válido',
            'type' => $this->getDocumentType($cleanDocument),
            'clean_document' => $cleanDocument,
        ];
    }

    private function isValidCPF(string $cpf): bool
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    public function isValidCNPJ(string $cnpj): bool
    {
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $sum = 0;
        $weight = 5;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weight;
            $weight = $weight == 2 ? 9 : $weight - 1;
        }
        $rest = $sum % 11;
        $digit1 = $rest < 2 ? 0 : 11 - $rest;

        $sum = 0;
        $weight = 6;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weight;
            $weight = $weight == 2 ? 9 : $weight - 1;
        }
        $rest = $sum % 11;
        $digit2 = $rest < 2 ? 0 : 11 - $rest;

        return $cnpj[12] == $digit1 && $cnpj[13] == $digit2;
    }

    public function getDocumentType(string $document): string
    {
        return strlen($document) === 11 ? 'CPF' : 'CNPJ';
    }

    public function prepareClientData(array $data, string $documentType): array
    {
        $cleanDocument = preg_replace('/[^0-9]/', '', $data['document_number']);

        return array_merge($data, [
            'document_type' => $documentType,
            'document_number' => $cleanDocument,
            'state_registration' => $documentType === 'CPF' ? null : ($data['state_registration'] ?? null),
            'municipal_registration' => $documentType === 'CPF' ? null : ($data['municipal_registration'] ?? null),
            'contributor_type' => $documentType === 'CPF'
                ? ($data['contributor_type'] ?? 9)
                : ($data['contributor_type'] ?? 1),
        ]);
    }

    /**
     * Busca cliente por documento ou email (busca por email_hash)
     */
    public function searchClient(string $search): ?Client
    {
        if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
            $emailHash = hash('sha256', $search);
            $user = User::where('email_hash', $emailHash)->first();
            if (!$user) {
                // Try by email_hash
                $user = User::where('email_hash', hash('sha256', $search))->first();
            }
            return $user ? $this->repository->findByUserId($user->id) : null;
        }

        $cleanDocument = preg_replace('/[^0-9]/', '', $search);
        return $this->repository->findByDocument($cleanDocument);
    }

    public function hasRecentPurchases(int $clientId, int $years = 5): bool
    {
        return \App\Models\Sale::where('client_id', $clientId)
            ->where('created_at', '>=', now()->subYears($years))
            ->exists();
    }
}