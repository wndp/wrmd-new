<?php

namespace App\Enums;

enum SettingKey: string
{
    case TIMEZONE = 'TIMEZONE';
    case LANGUAGE = 'LANGUAGE';

    case FULL_PEOPLE_ACCESS = 'FULL_PEOPLE_ACCESS';
    case REMOTE_RESTRICTED = 'REMOTE_RESTRICTED';
    case CLINIC_IP = 'CLINIC_IP';
    case USER_REMOTE_PERMISSION = 'USER_REMOTE_PERMISSION';
    case ROLE_REMOTE_PERMISSION = 'ROLE_REMOTE_PERMISSION';
    case REQUIRE_TWO_FACTOR = 'REQUIRE_TWO_FACTOR';

    case LOG_ORDER = 'LOG_ORDER';
    case LOG_ALLOW_AUTHOR_EDIT = 'LOG_ALLOW_AUTHOR_EDIT';
    case LOG_ALLOW_EDIT = 'LOG_ALLOW_EDIT';
    case LOG_ALLOW_DELETE = 'LOG_ALLOW_DELETE';
    case LOG_SHARES = 'LOG_SHARES';

    case SHOW_LOOKUP_RESCUER = 'SHOW_LOOKUP_RESCUER';
    case SHOW_GEOLOCATION_FIELDS = 'SHOW_GEOLOCATION_FIELDS';
    case AREAS = 'AREAS';
    case ENCLOSURES = 'ENCLOSURES';
    case LIST_FIELDS = 'LIST_FIELDS';

    case SHOW_TAGS = 'SHOW_TAGS';
    case WILD_ALERT_SHARING = 'WILD_ALERT_SHARING';
    case EXPORT_SHARING = 'EXPORT_SHARING';

    case SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS = 'SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS';
    case SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS = 'SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS';

    case OSPR_SPILL_ID = 'OSPR_SPILL_ID';
}
