<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A provisioned service delivered to a customer after payment.
 *
 * This is the equivalent of WHMCS's tblhosting record: it holds the
 * activation code / IPTV credentials the customer "bought".
 */
class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_number',
        'user_id',
        'product_id',
        'order_id',
        'status',
        'provider',
        'username',
        'password',
        'activation_code',
        'access_url',
        'provision_data',
        'starts_at',
        'expires_at',
        'delivered_at',
        'delivery_channels',
        'notes',
    ];

    protected $casts = [
        'status' => ServiceStatus::class,
        'provision_data' => 'array',
        'delivery_channels' => 'array',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'delivered_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->service_number)) {
                $service->service_number = self::generateServiceNumber();
            }
        });
    }

    public static function generateServiceNumber(): string
    {
        $year = now()->year;
        $last = self::withTrashed()->whereYear('created_at', $year)->latest('id')->first();
        $number = $last ? ((int) substr($last->service_number, -6)) + 1 : 1;

        return "SVC-{$year}-".str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

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

    public function markActive(): void
    {
        $this->update([
            'status' => ServiceStatus::ACTIVE,
            'starts_at' => $this->starts_at ?? now(),
        ]);
    }

    public function markFailed(?string $reason = null): void
    {
        $this->update([
            'status' => ServiceStatus::FAILED,
            'notes' => $reason,
        ]);
    }

    public function markDelivered(array $channels): void
    {
        $this->update([
            'delivered_at' => now(),
            'delivery_channels' => $channels,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ServiceStatus::ACTIVE);
    }
}
