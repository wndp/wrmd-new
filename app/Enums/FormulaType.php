<?php

namespace App\Enums;

enum FormulaType: string
{
    case PRESCRIPTION = 'PRESCRIPTION';
    case NUTRITION = 'NUTRITION';

    public function label(): string
    {
        return match ($this) {
            self::PRESCRIPTION => 'Prescription',
            self::NUTRITION => 'Nutrition',
        };
    }
}
