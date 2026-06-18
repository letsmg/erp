<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'zip_code',
        'zip_code_hash',
        'zip_code_encrypted',
        'street',
        'street_hash',
        'street_encrypted',
        'number',
        'neighborhood',
        'neighborhood_hash',
        'neighborhood_encrypted',
        'city',
        'city_hash',
        'city_encrypted',
        'state',
        'complement',
        'is_delivery_address',
    ];

    protected $casts = [
        'is_delivery_address' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─── Relationships ───

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // ─── Accessors ───

    /**
     * Get decrypted street
     */
    public function getDecryptedStreetAttribute(): ?string
    {
        if ($this->street_encrypted) {
            return Crypt::decryptString($this->street_encrypted);
        }
        return $this->street;
    }

    /**
     * Get decrypted neighborhood
     */
    public function getDecryptedNeighborhoodAttribute(): ?string
    {
        if ($this->neighborhood_encrypted) {
            return Crypt::decryptString($this->neighborhood_encrypted);
        }
        return $this->neighborhood;
    }

    /**
     * Get decrypted city
     */
    public function getDecryptedCityAttribute(): ?string
    {
        if ($this->city_encrypted) {
            return Crypt::decryptString($this->city_encrypted);
        }
        return $this->city;
    }

    /**
     * Get decrypted zip code
     */
    public function getDecryptedZipCodeAttribute(): ?string
    {
        if ($this->zip_code_encrypted) {
            return Crypt::decryptString($this->zip_code_encrypted);
        }
        return $this->zip_code;
    }

    /**
     * Retorna o endereço completo formatado (usando dados decriptados)
     */
    public function getFullAddressAttribute(): string
    {
        $address = "{$this->decrypted_street}, {$this->number}";

        if ($this->complement) {
            $address .= " - {$this->complement}";
        }

        $address .= ", {$this->decrypted_neighborhood}";
        $address .= ", {$this->decrypted_city}/{$this->state}";
        $address .= " - CEP: {$this->decrypted_zip_code}";

        return $address;
    }

    // ─── Scopes ───

    public function scopeDeliveryAddresses($query)
    {
        return $query->where('is_delivery_address', true);
    }

    public function scopeMainDelivery($query)
    {
        return $query->where('is_delivery_address', true)->first();
    }
}