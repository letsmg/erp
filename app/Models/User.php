<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\AccessLevel;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'password',
        'first_name_hash',
        'first_name_encrypted',
        'last_name_hash',
        'last_name_encrypted',
        'display_name',
        'email_hash',
        'email_encrypted',
        'access_level',
        'is_active',
        'last_login_ip',
        'email_verified_at',
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
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'access_level' => AccessLevel::class,
    ];

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

    public function getEmailForVerification(): string
    {
        return $this->decrypted_email ?? '';
    }

    public function isAdmin(): bool
    {
        return $this->access_level?->isAdmin() ?? false;
    }

    public function isOperator(): bool
    {
        return $this->access_level?->isOperator() ?? false;
    }

    public function isClient(): bool
    {
        return $this->access_level?->isClient() ?? false;
    }

    public function isStaff(): bool
    {
        return $this->access_level?->isStaff() ?? false;
    }

    public function canManageProducts(): bool
    {
        return $this->access_level?->canManageProducts() ?? false;
    }

    public function canDelete(): bool
    {
        return $this->access_level?->canDelete() ?? false;
    }
}