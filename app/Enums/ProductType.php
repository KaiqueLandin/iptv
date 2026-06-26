<?php

namespace App\Enums;

enum ProductType: string
{
    case HOSTING = 'hosting';
    case VPS = 'vps';
    case DEDICATED = 'dedicated';
    case DOMAIN = 'domain';
    case SSL = 'ssl';
    case ADDON = 'addon';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::HOSTING => 'Hospedagem',
            self::VPS => 'VPS',
            self::DEDICATED => 'Servidor Dedicado',
            self::DOMAIN => 'Domínio',
            self::SSL => 'Certificado SSL',
            self::ADDON => 'Addon',
            self::OTHER => 'Outro',
        };
    }
}
