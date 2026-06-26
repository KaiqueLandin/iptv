<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'response',
        'status_code',
        'status',
        'error_message',
        'attempt',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'status_code' => 'integer',
        'attempt' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    public function markAsSuccess(array $response, int $statusCode): void
    {
        $this->update([
            'status' => 'success',
            'response' => $response,
            'status_code' => $statusCode,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $error, int $statusCode = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'status_code' => $statusCode,
        ]);
    }
}
