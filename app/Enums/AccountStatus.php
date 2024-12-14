<?php

namespace App\Enums;

enum AccountStatus: string
{
    case ACTIVE = 'ACTIVE';
    case STALE = 'STALE';
    case SUSPENDED = 'SUSPENDED';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::STALE => 'Stale',
            self::SUSPENDED => 'Suspended',
        };
    }
}
