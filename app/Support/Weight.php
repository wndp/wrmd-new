<?php

namespace App\Support;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;

class Weight
{
    /**
     * Convert a weight into kilograms.
     */
    public static function toKilograms(float $weight, int $unitId): float
    {
        [
            $unitIsPerKgId,
            $unitIsGId,
            $unitIsLbId,
            $unitIsOzId,
        ] = static::weightUnitIds();

        $weight = match ($unitId) {
            $unitIsGId => $weight = $weight / 1000,
            $unitIsLbId => $weight = $weight / 2.205,
            $unitIsOzId => $weight = $weight / 35.274,
            default => $weight
        };

        return round($weight, 4);
    }

    /**
     * Convert a weight into grams.
     */
    public static function toGrams(float $weight, int $unitId): float
    {
        [
            $unitIsPerKgId,
            $unitIsGId,
            $unitIsLbId,
            $unitIsOzId,
        ] = static::weightUnitIds();

        $weight = match ($unitId) {
            $unitIsPerKgId => $weight = $weight * 1000,
            $unitIsLbId => $weight = $weight * 453.592,
            $unitIsOzId => $weight = $weight * 28.35,
            default => $weight
        };

        return round($weight, 4);
    }

    /**
     * Convert a weight into pounds.
     */
    public static function toPounds(float $weight, int $unitId): float
    {
        [
            $unitIsPerKgId,
            $unitIsGId,
            $unitIsLbId,
            $unitIsOzId,
        ] = static::weightUnitIds();

        $weight = match ($unitId) {
            $unitIsPerKgId => $weight = $weight * 2.205,
            $unitIsGId => $weight = $weight / 453.592,
            $unitIsOzId => $weight = $weight / 16,
            default => $weight
        };

        return round($weight, 4);
    }

    /**
     * Convert a weight into ounces.
     */
    public static function toOunces(float $weight, int $unitId): float
    {
        [
            $unitIsPerKgId,
            $unitIsGId,
            $unitIsLbId,
            $unitIsOzId,
        ] = static::weightUnitIds();

        $weight = match ($unitId) {
            $unitIsPerKgId => $weight = $weight * 35.274,
            $unitIsGId => $weight = $weight / 28.35,
            $unitIsLbId => $weight = $weight * 16,
            default => $weight
        };

        return round($weight, 4);
    }

    private static function weightUnitIds()
    {
        return \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::EXAM_WEIGHT_UNITS->value, AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_KG->value],
            [AttributeOptionName::EXAM_WEIGHT_UNITS->value, AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_G->value],
            [AttributeOptionName::EXAM_WEIGHT_UNITS->value, AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_LB->value],
            [AttributeOptionName::EXAM_WEIGHT_UNITS->value, AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_OZ->value],
        ]);
    }
}
