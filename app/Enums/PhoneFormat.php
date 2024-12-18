<?php

namespace App\Enums;

enum PhoneFormat: string
{
    case E164 = 'E164';
    case NATIONAL = 'NATIONAL';
    case NORMALIZED = 'NORMALIZED';
}
