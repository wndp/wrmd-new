<?php

namespace App\Enums;

enum Role: string
{
    case WNDP_SUPER_ADMIN = 'WNDP_SUPER_ADMIN';
    case ADMIN = 'ADMIN';
    case USER = 'USER';
    case VIEWER = 'VIEWER';

    public function label(): string
    {
        return match ($this) {
            self::WNDP_SUPER_ADMIN => 'WNDP Super Admin',
            self::ADMIN => 'Admin',
            self::USER => 'User',
            self::VIEWER => 'Viewer',
        };
    }

    public static function publicRoles(): array
    {
        return [
            self::ADMIN,
            self::USER,
            self::VIEWER,
        ];
    }
}
