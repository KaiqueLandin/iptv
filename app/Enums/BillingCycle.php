<?php

namespace App\Enums;

enum BillingCycle: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case SEMIANNUALLY = 'semiannually';
    case ANNUALLY = 'annually';
    case BIENNIALLY = 'biennially';
    case TRIENNIALLY = 'triennially';
    case ONETIME = 'onetime';

    public function label(): string
    {
        return match($this) {
            self::MONTHLY => 'Mensal',
            self::QUARTERLY => 'Trimestral',
            self::SEMIANNUALLY => 'Semestral',
            self::ANNUALLY => 'Anual',
            self::BIENNIALLY => 'Bienal',
            self::TRIENNIALLY => 'Trienal',
            self::ONETIME => 'Pagamento Único',
        };
    }

    public function months(): int
    {
        return match($this) {
            self::MONTHLY => 1,
            self::QUARTERLY => 3,
            self::SEMIANNUALLY => 6,
            self::ANNUALLY => 12,
            self::BIENNIALLY => 24,
            self::TRIENNIALLY => 36,
            self::ONETIME => 0,
        };
    }
}
