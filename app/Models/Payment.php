<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'invoice_id',
        'user_id',
        'payment_gateway_id',
        'amount',
        'fee',
        'net_amount',
        'status',
        'gateway_transaction_id',
        'gateway_response',
        'payment_method',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'status' => PaymentStatus::class,
        'gateway_response' => 'array',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = self::generateTransactionId();
            }
        });
    }

    public static function generateTransactionId(): string
    {
        do {
            $id = 'TXN-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));
        } while (self::where('transaction_id', $id)->exists());

        return $id;
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => PaymentStatus::COMPLETED,
            'completed_at' => now(),
        ]);

        $this->invoice->markAsPaid($this->gateway?->name ?? $this->payment_method);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => PaymentStatus::FAILED,
            'notes' => $reason,
        ]);
    }

    public function refund(): void
    {
        $this->update([
            'status' => PaymentStatus::REFUNDED,
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', PaymentStatus::COMPLETED);
    }
}
