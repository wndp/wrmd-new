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
            $dosageUnitIsPerKgId,
            $dosageUnitIsGId,
            $dosageUnitIsLbId,
            $dosageUnitIsOzId,
        ] = static::weightUnitIds();

        $weight = match ($unitId) {
            $dosageUnitIsGId => $weight = $weight / 1000,
            $dosageUnitIsLbId => $weight = $weight / 2.205,
            $dosageUnitIsOzId => $weight = $weight / 35.274,
        };

        return round($weight, 4);
    }

    /**
     * Convert a weight into grams.
     */
    public static function toGrams(float $weight, int $unitId): float
    {
        [
            $dosageUnitIsPerKgId,
            $dosageUnitIsGId,
            $dosageUnitIsLbId,
            $dosageUnitIsOzId,
        ] = static::weightUnitIds();

        $weight = match ($unitId) {
            $dosageUnitIsPerKgId => $weight = $weight * 1000,
            $dosageUnitIsLbId => $weight = $weight * 453.592,
            $dosageUnitIsOzId => $weight = $weight * 28.35,
        };

        return round($weight, 4);
    }

    /**
     * Convert a weight into pounds.
     */
    public static function toPounds(float $weight, int $unitId): float
    {
        [
            $dosageUnitIsPerKgId,
            $dosageUnitIsGId,
            $dosageUnitIsLbId,
            $dosageUnitIsOzId,
        ] = static::weightUnitIds();

        $weight = match ($unitId) {
            $dosageUnitIsPerKgId => $weight = $weight * 2.205,
            $dosageUnitIsGId => $weight = $weight / 453.592,
            $dosageUnitIsOzId => $weight = $weight / 16,
        };

        return round($weight, 4);
    }

    /**
     * Convert a weight into ounces.
     */
    public static function toOunces(float $weight, int $unitId): float
    {
        [
            $dosageUnitIsPerKgId,
            $dosageUnitIsGId,
            $dosageUnitIsLbId,
            $dosageUnitIsOzId,
        ] = static::weightUnitIds();

        $weight = match ($unitId) {
            $dosageUnitIsPerKgId => $weight = $weight * 35.274,
            $dosageUnitIsGId => $weight = $weight / 28.35,
            $dosageUnitIsLbId => $weight = $weight * 16,
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
