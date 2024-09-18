<?php

namespace App\Macros;

class PercentageOf
{
    public function __invoke()
    {
        return function ($numerator, $denominator, $decimals = 2) {
            if ($denominator == 0) {
                return 0;
            }

            return round($numerator / $denominator * 100, $decimals);
        };
    }
}
