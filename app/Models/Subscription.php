<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'status',
        'billing_cycle',
        'amount',
        'started_at',
        'next_billing_date',
        'expires_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'status' => SubscriptionStatus::class,
        'amount' => 'decimal:2',
        'started_at' => 'date',
        'next_billing_date' => 'date',
        'expires_at' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function pause(): void
    {
        $this->update(['status' => SubscriptionStatus::PAUSED]);
    }

    public function resume(): void
    {
        $this->update(['status' => SubscriptionStatus::ACTIVE]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => SubscriptionStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function renew(): void
    {
        if ($this->billing_cycle && $this->next_billing_date) {
            $billingCycle = \App\Enums\BillingCycle::from($this->billing_cycle);
            $months = $billingCycle->months();
            
            $this->update([
                'next_billing_date' => $this->next_billing_date->addMonths($months),
            ]);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::ACTIVE);
    }

    public function scopeDueForRenewal($query)
    {
        return $query->where('status', SubscriptionStatus::ACTIVE)
            ->where('next_billing_date', '<=', now());
    }
}
