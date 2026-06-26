<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referral_code',
        'commission_rate',
        'total_earned',
        'pending_balance',
        'paid_balance',
        'total_referrals',
        'payment_method',
        'payment_details',
        'is_active',
        'approved_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'paid_balance' => 'decimal:2',
        'payment_details' => 'array',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($affiliate) {
            if (empty($affiliate->referral_code)) {
                $affiliate->referral_code = self::generateReferralCode();
            }
        });
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    public function addCommission(AffiliateCommission $commission): void
    {
        $this->increment('total_earned', $commission->commission_amount);
        $this->increment('pending_balance', $commission->commission_amount);
    }

    public function markCommissionAsPaid(AffiliateCommission $commission): void
    {
        $this->decrement('pending_balance', $commission->commission_amount);
        $this->increment('paid_balance', $commission->commission_amount);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('approved_at');
    }
}
