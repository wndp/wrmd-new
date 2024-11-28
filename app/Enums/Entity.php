<?php

namespace App\Enums;

enum Entity: string
{
    // Entity enums should be defined as CASE::{database table name}
    case PATIENT = 'patients';
    case CARE_LOGS = 'care_logs';
    case PATIENT_LOCATIONS = 'patient_locations';
    case PRESCRIPTIONS = 'prescriptions';
    case EXAMS = 'exams';
    case MORPHOMETRIC = 'morphometrics';
    case NECROPSY = 'necropsies';
    case LAB_REPORT = 'lab_reports';
    case NUTRITION_PLAN = 'nutrition_plans';
    case NUTRITION_PLAN_INGREDIENT = 'nutrition_plan_ingredient';

    public function label(): string
    {
        return match ($this) {
            self::PATIENT => 'Patients',
            self::CARE_LOGS => __('Care Log'),
            self::PATIENT_LOCATIONS => __('Patient Location'),
            self::PRESCRIPTIONS => __('Prescription'),
            self::EXAMS => __('Exam'),
            self::MORPHOMETRIC => __('Morphometrics'),
            self::NECROPSY => __('Necropsy'),
            self::LAB_REPORT => __('Lab Reports'),
            self::NUTRITION_PLAN => __('Nutrition Plan'),
            self::NUTRITION_PLAN_INGREDIENT => __('Nutrition Plan Ingredient'),
        };
    }

    public function patientRelationshipName(): string
    {
        return match ($this) {
            self::PATIENT => '',
            self::CARE_LOGS => 'careLogs',
            self::PATIENT_LOCATIONS => 'locations',
            self::PRESCRIPTIONS => 'prescriptions',
            self::EXAMS => 'exams',
            self::MORPHOMETRIC => 'morphometric',
            self::NECROPSY => 'necropsy',
            self::LAB_REPORT => 'labReports',
            self::NUTRITION_PLAN => 'nutritionPlans',
            self::NUTRITION_PLAN_INGREDIENT => null
        };
    }

    public function shouldDisplayLatestInLists()
    {
        return match ($this) {
            self::PATIENT,
            self::MORPHOMETRIC,
            self::NECROPSY,
            self::NUTRITION_PLAN_INGREDIENT => false,
            self::CARE_LOGS,
            self::PATIENT_LOCATIONS,
            self::PRESCRIPTIONS,
            self::NUTRITION_PLAN,
            self::EXAMS,
            self::LAB_REPORT => true,
        };
    }
}
