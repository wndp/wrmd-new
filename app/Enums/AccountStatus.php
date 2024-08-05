<?php

namespace App\Enums;

enum AccountStatus: string
{
    case ACTIVE = 'ACTIVE';
    case STALE = 'STALE';
    case BANNED = 'BANNED';
}
