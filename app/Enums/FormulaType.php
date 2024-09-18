<?php

namespace App\Enums;

enum FormulaType: string
{
    case PRESCRIPTION = 'PRESCRIPTION';

    public function label(): string
    {
        return match ($this) {
            self::PRESCRIPTION => 'Prescription',
        };
    }
}
