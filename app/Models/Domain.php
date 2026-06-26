<?php

namespace App\Models;

use App\Enums\DomainStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'domain_name',
        'registrar',
        'status',
        'registration_date',
        'expiry_date',
        'auto_renew',
        'nameservers',
        'dns_records',
        'privacy_protection',
        'whois_data',
    ];

    protected $casts = [
        'status' => DomainStatus::class,
        'registration_date' => 'date',
        'expiry_date' => 'date',
        'auto_renew' => 'boolean',
        'nameservers' => 'array',
        'dns_records' => 'array',
        'privacy_protection' => 'boolean',
        'whois_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function renew(int $years = 1): void
    {
        $this->update([
            'expiry_date' => $this->expiry_date->addYears($years),
            'status' => DomainStatus::ACTIVE,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', DomainStatus::ACTIVE);
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->where('status', DomainStatus::ACTIVE)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days));
    }
}
