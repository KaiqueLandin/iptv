<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case SUPERADMIN = 'superadmin';

    public function label(): string
    {
        return match($this) {
            self::USER => 'Usuário',
            self::ADMIN => 'Administrador',
            self::SUPERADMIN => 'Super Administrador',
        };
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::ADMIN, self::SUPERADMIN]);
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SUPERADMIN;
    }
}
