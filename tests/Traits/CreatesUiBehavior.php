<?php

namespace Tests\Traits;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\AttributeOption;
use App\Models\AttributeOptionUiBehavior as AttributeOptionUiBehaviorModel;

trait CreatesUiBehavior
{
    public function createUiBehavior(AttributeOptionName|int $attributeOptionName, AttributeOptionUiBehavior $behavior): int
    {
        $attributeOptionId = is_int($attributeOptionName)
            ? $attributeOptionName
            : AttributeOption::factory()->create(['name' => $attributeOptionName])->id;

        AttributeOptionUiBehaviorModel::factory()->create([
            'attribute_option_id' => $attributeOptionId,
            'behavior' => $behavior->value,
        ]);

        return $attributeOptionId;
    }

    public function pendingDispositionId(): int
    {
        return $this->createUiBehavior(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING
        );
    }

    public function weightUnits()
    {
        $kgWeightId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::EXAM_WEIGHT_UNITS,
            'value' => 'Kg',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $kgWeightId,
            'behavior' => AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_KG,
        ]);

        $gWeightId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::EXAM_WEIGHT_UNITS,
            'value' => 'g',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $gWeightId,
            'behavior' => AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_G,
        ]);

        $lbWeightId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::EXAM_WEIGHT_UNITS,
            'value' => 'lb',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $lbWeightId,
            'behavior' => AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_LB,
        ]);

        $ozWeightId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::EXAM_WEIGHT_UNITS,
            'value' => 'oz',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $ozWeightId,
            'behavior' => AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_OZ,
        ]);

        return [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId];
    }

    public function temperatureUnits()
    {
        $cTemperatureId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::EXAM_TEMPERATURE_UNITS,
            'value' => 'C',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $cTemperatureId,
            'behavior' => AttributeOptionUiBehavior::EXAM_WEIGHT_TEMPERATURE_IS_C,
        ]);

        $fTemperatureId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::EXAM_TEMPERATURE_UNITS,
            'value' => 'F',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $fTemperatureId,
            'behavior' => AttributeOptionUiBehavior::EXAM_WEIGHT_TEMPERATURE_IS_F,
        ]);

        $kTemperatureId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::EXAM_TEMPERATURE_UNITS,
            'value' => 'K',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $kTemperatureId,
            'behavior' => AttributeOptionUiBehavior::EXAM_WEIGHT_TEMPERATURE_IS_K,
        ]);

        return [$cTemperatureId, $fTemperatureId, $kTemperatureId];
    }

    public function patientLocationFacilitiesIds()
    {
        $clinicId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::PATIENT_LOCATION_FACILITIES,
            'value' => 'Clinic',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $clinicId,
            'behavior' => AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_CLINIC,
        ]);

        $homeCareId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::PATIENT_LOCATION_FACILITIES,
            'value' => 'Homecare',
        ])->id;

        \App\Models\AttributeOptionUiBehavior::factory()->create([
            'attribute_option_id' => $homeCareId,
            'behavior' => AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_HOMECARE,
        ]);

        return [$clinicId, $homeCareId];
    }
}
