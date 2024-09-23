<?php

namespace App\Enums;

enum Entity: string
{
    case PATIENT = 'patients';
    case CARE_LOGS = 'care_logs';
    case PATIENT_LOCATIONS = 'patient_locations';

    public function label(): string
    {
        return match ($this) {
            self::PATIENT => 'Patients',
            self::CARE_LOGS => __('Care Log'),
            self::PATIENT_LOCATIONS => __('Patient Location'),
        };
    }

    public function patientRelationshipName(): string
    {
        return match ($this) {
            self::PATIENT => '',
            self::CARE_LOGS => 'careLogs',
            self::PATIENT_LOCATIONS => 'locations',
        };
    }

    public function shouldDisplayLatestInLists()
    {
        return match ($this) {
            self::PATIENT => false,
            self::CARE_LOGS,
            self::PATIENT_LOCATIONS => true,
        };
    }
}
