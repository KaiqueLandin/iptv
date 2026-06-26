<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'department_id',
        'assigned_to',
        'subject',
        'priority',
        'status',
        'last_reply_at',
        'closed_at',
    ];

    protected $casts = [
        'priority' => TicketPriority::class,
        'status' => TicketStatus::class,
        'last_reply_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    public static function generateTicketNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;
            $lastTicket = self::whereYear('created_at', $year)
                ->lockForUpdate()
                ->latest('id')
                ->first();
            $number = $lastTicket
                ? ((int) substr($lastTicket->ticket_number, -6)) + 1
                : 1;

            return 'TKT-'.$year.'-'.str_pad($number, 6, '0', STR_PAD_LEFT);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(TicketDepartment::class, 'department_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    public function close(): void
    {
        $this->update([
            'status' => TicketStatus::CLOSED,
            'closed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => TicketStatus::OPEN,
            'closed_at' => null,
        ]);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [
            TicketStatus::OPEN,
            TicketStatus::IN_PROGRESS,
            TicketStatus::WAITING_CUSTOMER,
            TicketStatus::WAITING_STAFF,
        ]);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', TicketStatus::CLOSED);
    }
}
