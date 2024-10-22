<?php

namespace App\Enums;

enum ForumGroupRole: string
{
    case ADMIN = 'ADMIN';
    case PARTICIPANT = 'PARTICIPANT';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PARTICIPANT => 'Participant',
        };
    }
}
