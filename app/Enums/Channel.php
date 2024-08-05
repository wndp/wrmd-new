<?php

namespace App\Enums;

enum Channel: string
{
    case WELCOME = 'WELCOME';
    case GENERAL = 'GENERAL';

    public function label(): string
    {
        return match ($this) {
            self::WELCOME => 'Welcome',
            self::GENERAL => 'General',
        };
    }

    public function slug(): string
    {
        return match ($this) {
            self::WELCOME => 'welcome',
            self::GENERAL => 'general',
        };
    }
}
