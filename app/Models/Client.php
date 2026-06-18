<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'password',
        'user_id',
        'first_name_hash',
        'first_name_encrypted',
        'last_name_hash',
        'last_name_encrypted',
        'display_name',
        'email_hash',
        'email_encrypted',
        'name',
        'email',
        'document_type',
        'document_number',
        'document_hash',
        'document_encrypted',
        'phone1',
        'phone1_hash',
        'phone1_encrypted',
        'contact1',
        'phone2',
        'phone2_hash',
        'phone2_encrypted',
        'contact2',
        'state_registration',
        'municipal_registration',
        'contributor_type',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'first_name_hash',
        'first_name_encrypted',
        'last_name_hash',
        'last_name_encrypted',
        'email_hash',
        'email_encrypted',
        'document_hash',
        'document_encrypted',
        'phone1_hash',
        'phone1_encrypted',
        'phone2_hash',
        'phone2_encrypted',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function shoppingCartItems(): HasMany
    {
        return $this->hasMany(ShoppingCart::class, 'user_id', 'user_id');
    }

    public function getDecryptedFirstNameAttribute(): ?string
    {
        if ($this->first_name_encrypted) {
            return Crypt::decryptString($this->first_name_encrypted);
        }
        return null;
    }

    public function getDecryptedLastNameAttribute(): ?string
    {
        if ($this->last_name_encrypted) {
            return Crypt::decryptString($this->last_name_encrypted);
        }
        return null;
    }

    public function getDecryptedEmailAttribute(): ?string
    {
        if ($this->email_encrypted) {
            return Crypt::decryptString($this->email_encrypted);
        }
        return null;
    }

    public function getDecryptedDocumentAttribute(): ?string
    {
        if ($this->document_encrypted) {
            return Crypt::decryptString($this->document_encrypted);
        }
        return $this->document_number;
    }

    public function getDecryptedPhone1Attribute(): ?string
    {
        if ($this->phone1_encrypted) {
            return Crypt::decryptString($this->phone1_encrypted);
        }
        return $this->phone1;
    }

    public function getDecryptedPhone2Attribute(): ?string
    {
        if ($this->phone2_encrypted) {
            return Crypt::decryptString($this->phone2_encrypted);
        }
        return $this->phone2;
    }

    public function getDeliveryAddressAttribute()
    {
        return $this->addresses()->where('is_delivery_address', true)->first();
    }

    public function isCPF(): bool
    {
        return $this->document_type === 'CPF' && strlen($this->document_number ?? '') === 11;
    }

    public function isCNPJ(): bool
    {
        return $this->document_type === 'CNPJ' && strlen($this->document_number ?? '') === 14;
    }

    public function getNameAttribute($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getContact1Attribute($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getPhone2Attribute($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getContact2Attribute($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getStateRegistrationAttribute($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getMunicipalRegistrationAttribute($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getFormattedDocumentAttribute(): string
    {
        $doc = $this->decrypted_document ?? $this->document_number;
        if ($this->isCPF()) {
            $doc = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        } elseif ($this->isCNPJ()) {
            $doc = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }
        return htmlspecialchars($doc, ENT_QUOTES, 'UTF-8');
    }

    public function getContributorTypeDescriptionAttribute(): string
    {
        return match($this->contributor_type) {
            1 => 'Contribuinte ICMS',
            2 => 'Contribuinte Isento',
            9 => 'Não Contribuinte',
            default => 'Não definido'
        };
    }

    public function isICMSContributor(): bool
    {
        return $this->contributor_type === 1;
    }

    public function isICMSExempt(): bool
    {
        return $this->contributor_type === 2;
    }

    public function isNonContributor(): bool
    {
        return $this->contributor_type === 9;
    }
}