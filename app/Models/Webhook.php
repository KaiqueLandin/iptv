<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'url',
        'secret',
        'is_active',
        'headers',
        'timeout',
        'max_retries',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'headers' => 'array',
        'timeout' => 'integer',
        'max_retries' => 'integer',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->where('event', $event);
    }
}
