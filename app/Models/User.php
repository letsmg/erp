<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\AccessLevel;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'display_name',
        'email',
        'password',
        'access_level',
        'is_active',
        'last_login_ip',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'access_level' => AccessLevel::class,
    ];

    // ─── Helpers ───

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