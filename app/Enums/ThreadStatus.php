<?php

namespace App\Enums;

enum ThreadStatus: string
{
    case UNSOLVED = 'UNSOLVED';
    case SOLVED = 'SOLVED';

    public function label(): string
    {
        return match ($this) {
            self::UNSOLVED => 'Unsolved',
            self::SOLVED => 'Solved',
        };
    }
}
