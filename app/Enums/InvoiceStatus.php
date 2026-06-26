<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Rascunho',
            self::UNPAID => 'Não Paga',
            self::PAID => 'Paga',
            self::CANCELLED => 'Cancelada',
            self::REFUNDED => 'Reembolsada',
            self::OVERDUE => 'Vencida',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::UNPAID => 'yellow',
            self::PAID => 'green',
            self::CANCELLED => 'red',
            self::REFUNDED => 'blue',
            self::OVERDUE => 'red',
        };
    }
}
