<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativa',
            self::PAUSED => 'Pausada',
            self::CANCELLED => 'Cancelada',
            self::EXPIRED => 'Expirada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::PAUSED => 'yellow',
            self::CANCELLED => 'red',
            self::EXPIRED => 'gray',
        };
    }
}
