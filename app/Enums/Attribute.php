<?php

namespace App\Enums;

enum Attribute: string
{
    case PATIENTS_NAME = 'patients.name';
    case PATIENTS_BAND = 'patients.band';
    case PATIENT_LOCATIONS_FACILITY_ID = 'patient_locations.facility_id';
    case PATIENTS_DISPOSITION_ID = 'patients.disposition_id';
    case PATIENTS_DISPOSITIONED_AT = 'patients.dispositioned_at';

    public function label(): string
    {
        return match ($this) {
            self::PATIENTS_NAME => __('Name'),
            self::PATIENTS_BAND => __('Band'),
            self::PATIENT_LOCATIONS_FACILITY_ID => __('Facility'),
            self::PATIENTS_DISPOSITION_ID => __('Disposition'),
            self::PATIENTS_DISPOSITIONED_AT => __('Disposition Date'),
        };
    }

    public function hasAttributeOptions(): bool
    {
        return match ($this) {
            self::PATIENT_LOCATIONS_FACILITY_ID,
            self::PATIENTS_DISPOSITION_ID => true,
            default => false,
        };
    }

    public function attributeOptionOwningModelRelationship(): string
    {
        return match ($this) {
            self::PATIENT_LOCATIONS_FACILITY_ID => 'facility',
            self::PATIENTS_DISPOSITION_ID => 'disposition',
            default => null,
        };
    }
}
