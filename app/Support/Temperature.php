<?php

namespace App\Support;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;

class Temperature
{
    /**
     * Convert a temperature into Celsius.
     */
    public static function toCelsius(float $temperature, int $unitId): float
    {
        [
            $unitIsC,
            $unitIsF,
            $unitIsK,
        ] = static::temperatureUnitIds();

        $temperature = match ($unitId) {
            $unitIsF => $temperature = ($temperature - 32) * (5 / 9),
            $unitIsK => $temperature = $temperature - 273.15,
            default => $temperature
        };

        return round($temperature, 2);
    }

    /**
     * Convert a temperature into Fahrenheit.
     */
    public static function toFahrenheit(float $temperature, int $unitId): float
    {
        [
            $unitIsC,
            $unitIsF,
            $unitIsK,
        ] = static::temperatureUnitIds();

        $temperature = match ($unitId) {
            $unitIsC => $temperature = ($temperature * (9 / 5)) + 32,
            $unitIsK => $temperature = ($temperature - 273.15) * (9 / 5) + 32,
            default => $temperature
        };

        return round($temperature, 2);
    }

    /**
     * Convert a temperature into Kelvin.
     */
    public static function toKelvin(float $temperature, int $unitId): float
    {
        [
            $unitIsC,
            $unitIsF
        ] = static::temperatureUnitIds();

        $temperature = match ($unitId) {
            $unitIsC => $temperature = $temperature + 273.15,
            $unitIsF => $temperature = (($temperature - 32) * (5 / 9)) + 273.15,
            default => $temperature
        };

        return round($temperature, 2);
    }

    private static function temperatureUnitIds()
    {
        return \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::EXAM_TEMPERATURE_UNITS->value, AttributeOptionUiBehavior::EXAM_WEIGHT_TEMPERATURE_IS_C->value],
            [AttributeOptionName::EXAM_TEMPERATURE_UNITS->value, AttributeOptionUiBehavior::EXAM_WEIGHT_TEMPERATURE_IS_F->value],
            [AttributeOptionName::EXAM_TEMPERATURE_UNITS->value, AttributeOptionUiBehavior::EXAM_WEIGHT_TEMPERATURE_IS_K->value],
        ]);
    }
}
