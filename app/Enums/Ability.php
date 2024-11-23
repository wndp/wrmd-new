<?php

namespace App\Enums;

enum Ability: string
{
    case VIEW_WRMD_ADMIN = 'VIEW_WRMD_ADMIN';
    case VIEW_ACCOUNT_SETTINGS = 'VIEW_ACCOUNT_SETTINGS';
    case VIEW_DANGER_ZONE = 'VIEW_DANGER_ZONE';
    case VIEW_EXTENSION_SETTINGS = 'VIEW_EXTENSION_SETTINGS';
    case CREATE_PEOPLE = 'CREATE_PEOPLE';
    case COMBINE_PEOPLE = 'COMBINE_PEOPLE';
    case EXPORT_PEOPLE = 'EXPORT_PEOPLE';
    case SEARCH_RESCUERS = 'SEARCH_RESCUERS';
    case VIEW_RESCUER = 'VIEW_RESCUER';
    case VIEW_PEOPLE = 'VIEW_PEOPLE';
    case DELETE_PEOPLE = 'DELETE_PEOPLE';
    case UN_VOID_PATIENT = 'UN_VOID_PATIENT';
    case CREATE_PATIENTS = 'CREATE_PATIENTS';
    case UPDATE_CAGE_CARD = 'UPDATE_CAGE_CARD';
    case UPDATE_RESCUER = 'UPDATE_RESCUER';
    case UPDATE_PATIENT_CARE = 'UPDATE_PATIENT_CARE';
    case UPDATE_PATIENT_META = 'UPDATE_PATIENT_META';
    case VIEW_LOCATIONS = 'VIEW_LOCATIONS';
    case MANAGE_LOCATIONS = 'MANAGE_LOCATIONS';
    case VIEW_DAILY_TASKS = 'VIEW_DAILY_TASKS';
    case MANAGE_DAILY_TASKS = 'MANAGE_DAILY_TASKS';
    case VIEW_CARE_LOGS = 'VIEW_CARE_LOGS';
    case MANAGE_CARE_LOGS = 'MANAGE_CARE_LOGS';
    case VIEW_ATTACHMENTS = 'VIEW_ATTACHMENTS';
    case MANAGE_ATTACHMENTS = 'MANAGE_ATTACHMENTS';
    case VIEW_LABS = 'VIEW_LABS';
    case MANAGE_LABS = 'MANAGE_LABS';
    case VIEW_EXPENSES = 'VIEW_EXPENSES';
    case MANAGE_EXPENSES = 'MANAGE_EXPENSES';
    case UPDATE_NECROPSY = 'UPDATE_NECROPSY';
    case UPDATE_BANDING_AND_MORPHOMETRICS = 'UPDATE_BANDING_AND_MORPHOMETRICS';
    case DETACH_RESCUERS = 'DETACH_RESCUERS';
    case SHARE_PATIENTS = 'SHARE_PATIENTS';
    case VIEW_REPORTS = 'VIEW_REPORTS';
    case SEARCH_PATIENTS = 'SEARCH_PATIENTS';
    case BATCH_UPDATE = 'BATCH_UPDATE';
    case VIEW_STABILIZATION = 'VIEW_STABILIZATION';
    case VIEW_OWCN_REPORTS = 'VIEW_OWCN_REPORTS';
    case MANAGE_OWCN_VITALS = 'MANAGE_OWCN_VITALS';
    case VIEW_OWCN_EXPRESS = 'VIEW_OWCN_EXPRESS';
    case VIEW_EXAMS = 'VIEW_EXAMS';
    case MANAGE_EXAMS = 'MANAGE_EXAMS';
    case MANAGE_PATIENT_SETTINGS = 'MANAGE_PATIENT_SETTINGS';
    case MANAGE_HOTLINE = 'MANAGE_HOTLINE';
    case VIEW_ANALYTICS = 'VIEW_ANALYTICS';
    case VIEW_PATIENTS = 'VIEW_PATIENTS';
    case VIEW_HOTLINE = 'VIEW_HOTLINE';
    case VIEW_OIL_PROCESSING = 'VIEW_OIL_PROCESSING';
    case MANAGE_OIL_PROCESSING = 'MANAGE_OIL_PROCESSING';
    // case VIEW_OIL_PREWASH = 'VIEW_OIL_PREWASH';
    // case MANAGE_OIL_PREWASH = 'MANAGE_OIL_PREWASH';
    case VIEW_OIL_WASH = 'VIEW_OIL_WASH';
    case MANAGE_OIL_WASH = 'MANAGE_OIL_WASH';
    case VIEW_OIL_CONDITIONING = 'VIEW_OIL_CONDITIONING';
    case MANAGE_OIL_CONDITIONING = 'MANAGE_OIL_CONDITIONING';

    public function label(): string
    {
        return match ($this) {
            self::VIEW_WRMD_ADMIN => 'View WRMD Admin',
            self::VIEW_ACCOUNT_SETTINGS => 'View Account Settings',
            self::VIEW_DANGER_ZONE => 'View Danger Zone',
            self::VIEW_EXTENSION_SETTINGS => 'View Extension Settings',
            self::CREATE_PEOPLE => 'Create People',
            self::COMBINE_PEOPLE => 'Combine People',
            self::EXPORT_PEOPLE => 'Export People',
            self::SEARCH_RESCUERS => 'Search Rescuers',
            self::VIEW_RESCUER => 'View Rescuer',
            self::VIEW_PEOPLE => 'View People',
            self::DELETE_PEOPLE => 'Delete People',
            self::UN_VOID_PATIENT => 'Un-void Patients',
            self::CREATE_PATIENTS => 'Create Patients',
            self::UPDATE_CAGE_CARD => 'Update Cage Card',
            self::UPDATE_RESCUER => 'Update Rescuer',
            self::UPDATE_PATIENT_CARE => 'Update Patient Care',
            self::UPDATE_PATIENT_META => 'Update Patient Metadata',
            self::VIEW_LOCATIONS => 'View Locations',
            self::MANAGE_LOCATIONS => 'Manage Locations',
            self::VIEW_DAILY_TASKS => 'View Daily Tasks',
            self::MANAGE_DAILY_TASKS => 'Manage Daily Tasks',
            self::VIEW_CARE_LOGS => 'View Care Logs',
            self::MANAGE_CARE_LOGS => 'Manage Care Logs',
            self::VIEW_ATTACHMENTS => 'View Attachments',
            self::MANAGE_ATTACHMENTS => 'Manage Attachments',
            self::VIEW_LABS => 'View Labs',
            self::MANAGE_LABS => 'Manage Labs',
            self::VIEW_EXPENSES => 'View Expenses',
            self::MANAGE_EXPENSES => 'Manage Expenses',
            self::UPDATE_NECROPSY => 'Update Necropsy',
            self::UPDATE_BANDING_AND_MORPHOMETRICS => 'Update Banding and Morphometrics',
            self::DETACH_RESCUERS => 'Detach Rescuers',
            self::SHARE_PATIENTS => 'Share Patients',
            self::VIEW_REPORTS => 'View Reports',
            self::SEARCH_PATIENTS => 'Search Patients',
            self::BATCH_UPDATE => 'Batch Update',
            self::VIEW_STABILIZATION => 'View_Stabilization',
            self::VIEW_OWCN_REPORTS => 'View OWCN Reports',
            self::MANAGE_OWCN_VITALS => 'Manage OWCN Vitals',
            self::VIEW_OWCN_EXPRESS => 'View OWCN Express',
            self::VIEW_EXAMS => 'View Exams',
            self::MANAGE_EXAMS => 'Manage Exams',
            self::MANAGE_PATIENT_SETTINGS => 'Manage Patient Settings',
            self::MANAGE_HOTLINE => 'Manage Hotline',
            self::VIEW_ANALYTICS => 'View Analytics',
            self::VIEW_PATIENTS => 'View Patients',
            self::VIEW_HOTLINE => 'View Hotline',
            self::VIEW_EVENT_PROCESSING => 'View Event Processing',
            self::MANAGE_EVENT_PROCESSING => 'Manage Event Processing',
            self::VIEW_EVENT_PREWASH => 'View Event Prewash',
            self::MANAGE_EVENT_PREWASH => 'Manage Event Prewash',
            self::VIEW_EVENT_WASH => 'View Event Wash',
            self::MANAGE_EVENT_WASH => 'Manage Event Wash',
            self::VIEW_EVENT_CONDITIONING => 'View Event Conditioning',
            self::MANAGE_EVENT_CONDITIONING => 'Manage Event Conditioning'
        };
    }

    public static function publicAbilities(): array
    {
        return [
            self::VIEW_ACCOUNT_SETTINGS,
            self::VIEW_DANGER_ZONE,
            self::VIEW_EXTENSION_SETTINGS,
            self::CREATE_PEOPLE,
            self::COMBINE_PEOPLE,
            self::EXPORT_PEOPLE,
            self::SEARCH_RESCUERS,
            self::VIEW_RESCUER,
            self::VIEW_PEOPLE,
            self::DELETE_PEOPLE,
            self::UN_VOID_PATIENT,
            self::CREATE_PATIENTS,
            self::UPDATE_CAGE_CARD,
            self::UPDATE_RESCUER,
            self::UPDATE_PATIENT_CARE,
            self::UPDATE_PATIENT_META,
            self::VIEW_LOCATIONS,
            self::MANAGE_LOCATIONS,
            self::VIEW_DAILY_TASKS,
            self::MANAGE_DAILY_TASKS,
            self::VIEW_CARE_LOGS,
            self::MANAGE_CARE_LOGS,
            self::VIEW_ATTACHMENTS,
            self::MANAGE_ATTACHMENTS,
            self::VIEW_LABS,
            self::MANAGE_LABS,
            self::UPDATE_NECROPSY,
            self::UPDATE_BANDING_AND_MORPHOMETRICS,
            self::DETACH_RESCUERS,
            self::SHARE_PATIENTS,
            self::VIEW_REPORTS,
            self::SEARCH_PATIENTS,
            self::BATCH_UPDATE,
            self::VIEW_STABILIZATION,
            self::VIEW_OWCN_REPORTS,
            self::MANAGE_OWCN_VITALS,
            self::VIEW_OWCN_EXPRESS,
            self::VIEW_EXAMS,
            self::MANAGE_EXAMS,
            self::MANAGE_PATIENT_SETTINGS,
            self::MANAGE_HOTLINE,
            self::VIEW_ANALYTICS,
            self::VIEW_PATIENTS,
            self::VIEW_HOTLINE,
            self::VIEW_EVENT_PROCESSING,
            self::MANAGE_EVENT_PROCESSING,
            self::VIEW_EVENT_PREWASH,
            self::MANAGE_EVENT_PREWASH,
            self::VIEW_EVENT_WASH,
            self::MANAGE_EVENT_WASH,
            self::VIEW_EVENT_CONDITIONING,
            self::MANAGE_EVENT_CONDITIONING,
        ];
    }
}
