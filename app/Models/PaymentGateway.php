<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gateway_key',
        'description',
        'is_active',
        'is_sandbox',
        'credentials',
        'settings',
        'fee_fixed',
        'fee_percentage',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'fee_fixed' => 'decimal:2',
        'fee_percentage' => 'decimal:2',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function calculateFee(float $amount): float
    {
        return $this->fee_fixed + (($amount * $this->fee_percentage) / 100);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
