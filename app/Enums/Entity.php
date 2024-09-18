<?php

namespace App\Enums;

enum Entity: string
{
    case CARE_LOGS = 'care_logs';

    public function label(): string
    {
        return match ($this) {
            self::CARE_LOGS => __('Care Log'),
        };
    }
}
