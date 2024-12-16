<?php

namespace App\Enums;

use App\Models\AttributeOption;
use App\Support\AttributeOptionsCollection;

enum Attribute: string
{
    case INCIDENT_STATUS_ID = 'incidents.incident_status_id';
    case INCIDENT_CATEGORY_ID = 'incidents.category_id';
    case INCIDENT_SUBDIVISION = 'incidents.incident_subdivision';
    case INCIDENT_REPORTED_AT = 'incidents.reported_at';
    case INCIDENT_OCCURRED_AT = 'incidents.occurred_at';

    case PATIENTS_DATE_ADMITTED_AT = 'patients.date_admitted_at';
    case PATIENTS_NAME = 'patients.name';
    case PATIENTS_BAND = 'patients.band';
    case PATIENTS_COMMON_NAME = 'patients.common_name';
    case PATIENTS_DISPOSITION_ID = 'patients.disposition_id';
    case PATIENTS_TRANSFER_TYPE_ID = 'patients.transfer_type_id';
    case PATIENTS_RELEASE_TYPE_ID = 'patients.release_type_id';
    case PATIENTS_IS_CARCASS_SAVED = 'patients.is_carcass_saved';
    case PATIENTS_DISPOSITIONED_AT = 'patients.dispositioned_at';

    case PATIENT_LOCATIONS_FACILITY_ID = 'patient_locations.facility_id';

    case PEOPLE_ENTITY_ID = 'people.entity_id';
    case PEOPLE_ORGANIZATION = 'people.organization';
    case PEOPLE_FIRST_NAME = 'people.first_name';
    case PEOPLE_LAST_NAME = 'people.last_name';
    case PEOPLE_PHONE = 'people.phone';
    case PEOPLE_ALTERNATE_PHONE = 'people.alternate_phone';
    case PEOPLE_EMAIL = 'people.email';
    case PEOPLE_COUNTRY = 'people.country';
    case PEOPLE_SUBDIVISION = 'people.subdivision';
    case PEOPLE_CITY = 'people.city';
    case PEOPLE_ADDRESS = 'people.address';
    case PEOPLE_POSTAL_CODE = 'people.postal_code';
    case PEOPLE_COUNTY = 'people.county';
    case PEOPLE_NOTES = 'people.notes';
    case PEOPLE_NO_SOLICITATIONS = 'people.no_solicitations';
    case PEOPLE_IS_VOLUNTEER = 'people.is_volunteer';
    case PEOPLE_IS_MEMBER = 'people.is_member';

    case EXAMS_SEX_ID = 'exams.sex_id';
    case EXAMS_WEIGHT_UNIT_ID = 'exams.weight_unit_id';
    case EXAMS_BODY_CONDITION_ID = 'exams.body_condition_id';
    case EXAMS_ATTITUDE_ID = 'exams.attitude_id';
    case EXAMS_DEHYDRATION_ID = 'exams.dehydration_id';
    case EXAMS_MUCOUS_MEMBRANE_COLOR_ID = 'exams.mucous_membrane_color_id';
    case EXAMS_MUCOUS_MEMBRANE_TEXTURE_ID = 'exams.mucous_membrane_texture_id';

    public function label(): string
    {
        return match ($this) {
            self::INCIDENT_STATUS_ID => '',
            self::INCIDENT_CATEGORY_ID => '',
            self::INCIDENT_SUBDIVISION => '',
            self::INCIDENT_REPORTED_AT => '',
            self::INCIDENT_OCCURRED_AT => '',

            self::PATIENTS_DATE_ADMITTED_AT => __('Date Admitted'),
            self::PATIENTS_NAME => __('Name'),
            self::PATIENTS_BAND => __('Band'),
            self::PATIENTS_DISPOSITION_ID => __('Disposition'),
            self::PATIENTS_COMMON_NAME => __('Common Name'),
            self::PATIENTS_TRANSFER_TYPE_ID => __('Transfer Type'),
            self::PATIENTS_RELEASE_TYPE_ID => __('Release Type'),
            self::PATIENTS_IS_CARCASS_SAVED => __('Is_Carcass_Saved'),
            self::PATIENTS_DISPOSITIONED_AT => __('Disposition Date'),

            self::PATIENT_LOCATIONS_FACILITY_ID => __('Facility'),

            self::PEOPLE_ENTITY_ID => __('Entity Type'),
            self::PEOPLE_ORGANIZATION => __('Organization'),
            self::PEOPLE_FIRST_NAME => __('First Name'),
            self::PEOPLE_LAST_NAME => __('Last Name'),
            self::PEOPLE_PHONE => __('Phone'),
            self::PEOPLE_ALTERNATE_PHONE => __('Alternate Phone'),
            self::PEOPLE_EMAIL => __('Email'),
            self::PEOPLE_COUNTRY => __('Country'),
            self::PEOPLE_SUBDIVISION => __('Subdivision'),
            self::PEOPLE_CITY => __('City'),
            self::PEOPLE_ADDRESS => __('Address'),
            self::PEOPLE_POSTAL_CODE => __('Postal Code'),
            self::PEOPLE_COUNTY => __('County'),
            self::PEOPLE_NOTES => __('Notes'),
            self::PEOPLE_NO_SOLICITATIONS => __('No Solicitations'),
            self::PEOPLE_IS_VOLUNTEER => __('Is a Volunteer'),
            self::PEOPLE_IS_MEMBER => __('Is a Member'),

            self::EXAMS_SEX_ID => __('Sex'),
            self::EXAMS_WEIGHT_UNIT_ID => __('Weight Unit'),
            self::EXAMS_BODY_CONDITION_ID => __('Bcs'),
            self::EXAMS_ATTITUDE_ID => __('Attitude'),
            self::EXAMS_DEHYDRATION_ID => __('Dehydration'),
            self::EXAMS_MUCOUS_MEMBRANE_COLOR_ID => __('Mucous Membrane Color'),
            self::EXAMS_MUCOUS_MEMBRANE_TEXTURE_ID => __('Mucous Membrane Texture'),

        };
    }

    public function hasAttributeOptions(): bool
    {
        return match ($this) {
            self::INCIDENT_STATUS_ID,
            self::INCIDENT_CATEGORY_ID,
            self::INCIDENT_SUBDIVISION,
            self::PATIENT_LOCATIONS_FACILITY_ID,
            self::PATIENTS_DISPOSITION_ID,
            self::EXAMS_SEX_ID,
            self::EXAMS_WEIGHT_UNIT_ID,
            self::EXAMS_BODY_CONDITION_ID,
            self::EXAMS_ATTITUDE_ID,
            self::EXAMS_DEHYDRATION_ID,
            self::EXAMS_MUCOUS_MEMBRANE_COLOR_ID,
            self::EXAMS_MUCOUS_MEMBRANE_TEXTURE_ID => true,
            default => false,
        };
    }

    public function isDateAttribute(): bool
    {
        return match ($this) {
            self::INCIDENT_REPORTED_AT,
            self::INCIDENT_OCCURRED_AT,
            self::PATIENTS_DATE_ADMITTED_AT,
            self::PATIENTS_DISPOSITIONED_AT => true,
            default => false,
        };
    }

    public function isNumberAttribute(): bool
    {
        return match ($this) {

            default => false,
        };
    }

    public function options(): AttributeOptionsCollection
    {
        return match ($this) {
            self::INCIDENT_STATUS_ID => AttributeOption::getDropdownOptions(AttributeOptionName::HOTLINE_STATUSES),
            self::INCIDENT_CATEGORY_ID => AttributeOption::getDropdownOptions([
                AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES,
                AttributeOptionName::HOTLINE_ADMINISTRATIVE_CATEGORIES,
                AttributeOptionName::HOTLINE_OTHER_CATEGORIES,
            ]),
            self::INCIDENT_SUBDIVISION => new AttributeOptionsCollection(),
            self::PATIENT_LOCATIONS_FACILITY_ID => AttributeOption::getDropdownOptions(AttributeOptionName::PATIENT_LOCATION_FACILITIES),
            self::PATIENTS_DISPOSITION_ID => AttributeOption::getDropdownOptions(AttributeOptionName::PATIENT_DISPOSITIONS),
            self::EXAMS_SEX_ID => AttributeOption::getDropdownOptions(AttributeOptionName::EXAM_SEXES),
            self::EXAMS_WEIGHT_UNIT_ID => AttributeOption::getDropdownOptions(AttributeOptionName::EXAM_WEIGHT_UNITS),
            self::EXAMS_BODY_CONDITION_ID => AttributeOption::getDropdownOptions(AttributeOptionName::EXAM_BODY_CONDITIONS),
            self::EXAMS_ATTITUDE_ID => AttributeOption::getDropdownOptions(AttributeOptionName::EXAM_ATTITUDES),
            self::EXAMS_DEHYDRATION_ID => AttributeOption::getDropdownOptions(AttributeOptionName::EXAM_DEHYDRATIONS),
            self::EXAMS_MUCOUS_MEMBRANE_COLOR_ID => AttributeOption::getDropdownOptions(AttributeOptionName::EXAM_MUCUS_MEMBRANE_COLORS),
            self::EXAMS_MUCOUS_MEMBRANE_TEXTURE_ID => AttributeOption::getDropdownOptions(AttributeOptionName::EXAM_MUCUS_MEMBRANE_TEXTURES),
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
