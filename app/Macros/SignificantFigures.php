<?php

namespace App\Macros;

class SignificantFigures
{
    public function __invoke()
    {
        return function ($number, $significantFigures) {
            // May be negative.
            $decimalPlaces = intval(floor($significantFigures - log10(abs($number))));

            // Round as a regular number.
            $number = round($number, $decimalPlaces);

            // Leave the formatting to number_format(), but always format 0 to 0 decimal places.
            return (float) number_format($number, $number == 0 ? 0 : $decimalPlaces);
        };
    }
}
