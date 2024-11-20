<?php

namespace App\Enums;

enum AccountStatus: string
{
    case ACTIVE = 'ACTIVE';
    case STALE = 'STALE';
    case BANNED = 'BANNED';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::STALE => 'Stale',
            self::BANNED => 'Banned',
        };
    }
}
