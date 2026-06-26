<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'invoice_number',
        'nfe_key',
        'nfe_series',
        'nfe_protocol',
        'nfe_xml',
        'pdf_url',
        'status',
        'error_message',
        'issued_at',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issue(): void
    {
        $this->update([
            'status' => 'issued',
            'issued_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function markAsError(string $error): void
    {
        $this->update([
            'status' => 'error',
            'error_message' => $error,
        ]);
    }
}
