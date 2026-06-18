<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'address_id',
        'total_amount',
        'sale_date',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function getFormattedTotal(): string
    {
        return 'R$ ' . number_format($this->total_amount, 2, ',', '.');
    }
}