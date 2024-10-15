<?php

namespace App\Enums;

enum Attribute: string
{
    case PATIENTS_NAME = 'patients.name';
    case PATIENTS_BAND = 'patients.band';
    case PATIENTS_DISPOSITION_ID = 'patients.disposition_id';
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

    public function label(): string
    {
        return match ($this) {
            self::PATIENTS_NAME => __('Name'),
            self::PATIENTS_BAND => __('Band'),
            self::PATIENTS_DISPOSITION_ID => __('Disposition'),
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
