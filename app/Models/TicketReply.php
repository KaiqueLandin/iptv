<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_staff',
        'attachments',
    ];

    protected $casts = [
        'is_staff' => 'boolean',
        'attachments' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->ticket->update([
                'last_reply_at' => now(),
                'status' => $reply->is_staff 
                    ? \App\Enums\TicketStatus::WAITING_CUSTOMER 
                    : \App\Enums\TicketStatus::WAITING_STAFF,
            ]);
        });
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
