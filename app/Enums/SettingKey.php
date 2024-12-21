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

    case FAVORITE_REPORTS = 'FAVORITE_REPORTS';
    case PAPER_FORM_TEMPLATES = 'PAPER_FORM_TEMPLATES';

    case SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS = 'SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS';
    case SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS = 'SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS';

    case OSPR_SPILL_ID = 'OSPR_SPILL_ID';

    public function label(): string
    {
        return match ($this) {
            self::TIMEZONE => 'Timezone',
            self::LANGUAGE => 'Language',
            self::FULL_PEOPLE_ACCESS => 'Full people access',
            self::REMOTE_RESTRICTED => 'Remote access restricted',
            self::CLINIC_IP => 'Clinic IP address',
            self::USER_REMOTE_PERMISSION => 'User remote permission',
            self::ROLE_REMOTE_PERMISSION => 'Role remote permission',
            self::REQUIRE_TWO_FACTOR => 'Require two factor authentication',
            self::LOG_ORDER => 'Care log order',
            self::LOG_ALLOW_AUTHOR_EDIT => 'Care log allow author edit',
            self::LOG_ALLOW_EDIT => 'Care log allow edit',
            self::LOG_ALLOW_DELETE => 'Care log allow delete',
            self::LOG_SHARES => 'Record when patient data is shared',
            self::SHOW_LOOKUP_RESCUER => 'Show lookup rescuer by default',
            self::SHOW_GEOLOCATION_FIELDS => 'Show geolocation fields',
            self::AREAS => 'Areas',
            self::ENCLOSURES => 'Enclosures',
            self::LIST_FIELDS => 'List Fields',
            self::SHOW_TAGS => 'Show Tags',
            self::WILD_ALERT_SHARING => 'Wild Alert Sharing',
            self::EXPORT_SHARING => 'Export Sharing',
            self::FAVORITE_REPORTS => 'Favorite Reports',
            self::PAPER_FORM_TEMPLATES => 'Paper Form Templates',
            self::SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS => 'Sub Account Allow Manage Settings',
            self::SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS => 'Sub Account Allow Transfer Patients',
            self::OSPR_SPILL_ID => 'OSPR Spill Id',
        };
    }
}
