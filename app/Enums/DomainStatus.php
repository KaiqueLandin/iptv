<?php

namespace App\Enums;

enum DomainStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case TRANSFERRED = 'transferred';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::ACTIVE => 'Ativo',
            self::EXPIRED => 'Expirado',
            self::CANCELLED => 'Cancelado',
            self::TRANSFERRED => 'Transferido',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::ACTIVE => 'green',
            self::EXPIRED => 'red',
            self::CANCELLED => 'gray',
            self::TRANSFERRED => 'blue',
        };
    }
}
