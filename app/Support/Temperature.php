<?php

namespace App\Support;

class Temperature
{
    /**
     * Convert a temperature into Celsius.
     */
    public static function toCelsius(float $temperature, string $unit): float
    {
        switch ($unit) {
            case 'F': $temperature = ($temperature - 32) * (5 / 9);
                break;
            case 'K': $temperature = $temperature - 273.15;
                break;
        }

        return round($temperature, 2);
    }

    /**
     * Convert a temperature into Fahrenheit.
     */
    public static function toFahrenheit(float $temperature, string $unit): float
    {
        switch ($unit) {
            case 'C': $temperature = ($temperature * (9 / 5)) + 32;
                break;
            case 'K': $temperature = ($temperature - 273.15) * (9 / 5) + 32;
                break;
        }

        return round($temperature, 2);
    }

    /**
     * Convert a temperature into Kelvin.
     */
    public static function toKelvin(float $temperature, string $unit): float
    {
        switch ($unit) {
            case 'C': $temperature = $temperature + 273.15;
                break;
            case 'F': $temperature = (($temperature - 32) * (5 / 9)) + 273.15;
                break;
        }

        return round($temperature, 2);
    }
}
