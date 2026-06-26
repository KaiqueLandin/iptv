<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'product_id',
        'status',
        'amount',
        'billing_cycle',
        'next_due_date',
        'activated_at',
        'cancelled_at',
        'cancellation_reason',
        'metadata',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'amount' => 'decimal:2',
        'next_due_date' => 'date',
        'activated_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $year = now()->year;
        $lastOrder = self::whereYear('created_at', $year)->latest('id')->first();
        $number = $lastOrder ? ((int) substr($lastOrder->order_number, -6)) + 1 : 1;

        return "ORD-{$year}-" . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class);
    }

    public function activate(): void
    {
        $this->update([
            'status' => OrderStatus::ACTIVE,
            'activated_at' => now(),
        ]);
    }

    public function suspend(): void
    {
        $this->update(['status' => OrderStatus::SUSPENDED]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', OrderStatus::ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::PENDING);
    }
}
