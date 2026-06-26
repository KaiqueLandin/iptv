<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_CUSTOMER = 'waiting_customer';
    case WAITING_STAFF = 'waiting_staff';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match($this) {
            self::OPEN => 'Aberto',
            self::IN_PROGRESS => 'Em Progresso',
            self::WAITING_CUSTOMER => 'Aguardando Cliente',
            self::WAITING_STAFF => 'Aguardando Equipe',
            self::CLOSED => 'Fechado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::OPEN => 'blue',
            self::IN_PROGRESS => 'yellow',
            self::WAITING_CUSTOMER => 'orange',
            self::WAITING_STAFF => 'purple',
            self::CLOSED => 'gray',
        };
    }
}
