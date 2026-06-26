<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'order_id',
        'status',
        'subtotal',
        'tax',
        'discount',
        'total',
        'due_date',
        'paid_at',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastInvoice = self::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->latest('id')
            ->first();

        $number = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;

        return "INV-{$year}{$month}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function markAsPaid(string $paymentMethod = null): void
    {
        $this->update([
            'status' => InvoiceStatus::PAID,
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
        ]);
    }

    public function markAsOverdue(): void
    {
        if ($this->status === InvoiceStatus::UNPAID && $this->due_date->isPast()) {
            $this->update(['status' => InvoiceStatus::OVERDUE]);
        }
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', InvoiceStatus::UNPAID);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', InvoiceStatus::OVERDUE)
            ->orWhere(function ($q) {
                $q->where('status', InvoiceStatus::UNPAID)
                    ->where('due_date', '<', now());
            });
    }

    public function calculateTotal(): void
    {
        $subtotal = $this->items()->sum('amount');
        $total = $subtotal + $this->tax - $this->discount;

        $this->update([
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }
}
