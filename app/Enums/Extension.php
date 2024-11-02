<?php

namespace App\Enums;

enum Extension: string
{
    case ATTACHMENTS = 'ATTACHMENTS';
    case BANDING_MORPHOMETRICS = 'BANDING_MORPHOMETRICS';
    case CALCULATORS = 'CALCULATORS';
    case CUSTOM_FIELD = 'CUSTOM_FIELD';
    case DAILY_EXAM = 'DAILY_EXAM';
    case EXPENSES = 'EXPENSES';
    case LAB_REPORTS = 'LAB_REPORTS';
    case NECROPSY = 'NECROPSY';
    case PAPER_FORMS = 'PAPER_FORMS';
    case QUICK_ADMIT = 'QUICK_ADMIT';
    case OIL_SPILL_PROCESSING = 'OIL_SPILL_PROCESSING';
    case OIL_SPILL_WASH = 'OIL_SPILL_WASH';
    case OIL_SPILL_CONDITIONING = 'OIL_SPILL_CONDITIONING';
    case OIL_SPILL = 'OIL_SPILL';
    case OWCN_OWRMD = 'OWCN_OWRMD';
    case OWCN_IOA = 'OWCN_IOA';
    case OWCN_MEMBER_ORGANIZATION = 'OWCN_MEMBER_ORGANIZATION';

    public function label(): string
    {
        return match ($this) {
            self::ATTACHMENTS => __('Attachments'),
            self::BANDING_MORPHOMETRICS => __('Banding and Morphometrics'),
            self::CALCULATORS => __('Calculators'),
            self::CUSTOM_FIELD => __('Custom Field'),
            self::DAILY_EXAM => __('Daily Exam'),
            self::EXPENSES => __('Expenses'),
            self::LAB_REPORTS => __('Lab Reports'),
            self::NECROPSY => __('Necropsy'),
            self::PAPER_FORMS => __('Paper Forms'),
            self::QUICK_ADMIT => __('Quick Admit'),
            self::OIL_SPILL_PROCESSING => __('Oil Spill Patient Processing'),
            self::OIL_SPILL_WASH => __('Oil Spill Patient Wash'),
            self::OIL_SPILL_CONDITIONING => __('Oil Spill Patient Conditioning'),
            self::OIL_SPILL => __('Oil Spill'),
            self::OWCN_OWRMD => __('O-WRMD'),
            self::OWCN_IOA => __('OWCN IOA'),
            self::OWCN_MEMBER_ORGANIZATION => __('OWCN Member Organization')
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ATTACHMENTS => __('Upload image and pdf files to your records.'),
            self::BANDING_MORPHOMETRICS => __('Banding & morphometrics data compatible with Bandit 4.0.'),
            self::CALCULATORS => __('Adds commonly needed calculators to the sidebar.'),
            self::CUSTOM_FIELD => __('Add custom fields when no other field meets your needs.'),
            self::DAILY_EXAM => __('Record daily exams on your patients.'),
            self::EXPENSES => __('Add a cost-of-care expense ledger to your patients.'),
            self::LAB_REPORTS => __('Include detailed lab values collected on your patients.'),
            self::NECROPSY => __('Write detailed necropsy reports on your patients.'),
            self::PAPER_FORMS => __('Use paper forms to log your intake and daily treatments.'),
            self::QUICK_ADMIT => __('Even more quickly admit patients into WRMD.'),
            self::OIL_SPILL_PROCESSING,
            self::OIL_SPILL_WASH,
            self::OIL_SPILL_CONDITIONING,
            self::OIL_SPILL,
            self::OWCN_OWRMD,
            self::OWCN_IOA,
            self::OWCN_MEMBER_ORGANIZATION => '',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ATTACHMENTS => 'PhotoIcon',
            self::BANDING_MORPHOMETRICS => 'SwatchIcon',
            self::CALCULATORS => 'CalculatorIcon',
            self::CUSTOM_FIELD => 'SquaresPlusIcon',
            self::DAILY_EXAM => 'ClipboardDocumentListIcon',
            self::EXPENSES => 'BanknotesIcon',
            self::LAB_REPORTS => 'BeakerIcon',
            self::NECROPSY => 'ScissorsIcon',
            self::PAPER_FORMS => 'PrinterIcon',
            self::QUICK_ADMIT => 'ClockIcon',
            self::OIL_SPILL_PROCESSING,
            self::OIL_SPILL_WASH,
            self::OIL_SPILL_CONDITIONING,
            self::OIL_SPILL,
            self::OWCN_OWRMD,
            self::OWCN_IOA,
            self::OWCN_MEMBER_ORGANIZATION => '',
        };
    }

    public function helpScoutArticleId(): string
    {
        return match ($this) {
            self::ATTACHMENTS => '64098e9216d5327537bcbcb6',
            self::BANDING_MORPHOMETRICS => '64099aef2d000e3c29ecfa66',
            self::CALCULATORS => '640aba03a0408f7cb10376ae',
            self::CUSTOM_FIELD => '640abf4b61f16344e3a344b5',
            self::DAILY_EXAM => '640ac55ca1dc8336325f86f6',
            self::EXPENSES => '640ac9e361f16344e3a344c3',
            self::LAB_REPORTS => '640ad0d7a1dc8336325f8708',
            self::NECROPSY => '640ad6daa0408f7cb10376ec',
            self::PAPER_FORMS => '640adac58ca4460845b4a07b',
            self::QUICK_ADMIT => '640ae303a0408f7cb1037718',
            self::OIL_SPILL_PROCESSING,
            self::OIL_SPILL_WASH,
            self::OIL_SPILL_CONDITIONING,
            self::OIL_SPILL,
            self::OWCN_OWRMD,
            self::OWCN_IOA,
            self::OWCN_MEMBER_ORGANIZATION => '',
        };
    }

    public function public(): bool
    {
        return match ($this) {
            self::ATTACHMENTS,
            self::BANDING_MORPHOMETRICS,
            self::CALCULATORS,
            self::CUSTOM_FIELD,
            self::DAILY_EXAM,
            self::EXPENSES,
            self::LAB_REPORTS,
            self::NECROPSY,
            self::PAPER_FORMS,
            self::QUICK_ADMIT => true,
            self::OIL_SPILL_PROCESSING,
            self::OIL_SPILL_WASH,
            self::OIL_SPILL_CONDITIONING,
            self::OIL_SPILL,
            self::OWCN_OWRMD,
            self::OWCN_IOA,
            self::OWCN_MEMBER_ORGANIZATION => false,
        };
    }

    public function pro(): bool
    {
        return match ($this) {
            self::BANDING_MORPHOMETRICS,
            self::CALCULATORS,
            self::DAILY_EXAM,
            self::EXPENSES,
            self::LAB_REPORTS,
            self::NECROPSY,
            self::PAPER_FORMS,
            self::QUICK_ADMIT => false,
            self::ATTACHMENTS,
            self::CUSTOM_FIELD,
            self::OIL_SPILL_PROCESSING,
            self::OIL_SPILL_WASH,
            self::OIL_SPILL_CONDITIONING,
            self::OIL_SPILL,
            self::OWCN_OWRMD,
            self::OWCN_IOA,
            self::OWCN_MEMBER_ORGANIZATION => true,
        };
    }

    public function dependencies(): array
    {
        return match ($this) {
            self::ATTACHMENTS,
            self::BANDING_MORPHOMETRICS,
            self::CALCULATORS,
            self::CUSTOM_FIELD,
            self::DAILY_EXAM,
            self::EXPENSES,
            self::LAB_REPORTS,
            self::NECROPSY,
            self::PAPER_FORMS,
            self::QUICK_ADMIT,
            self::OIL_SPILL_PROCESSING,
            self::OIL_SPILL_WASH,
            self::OIL_SPILL_CONDITIONING => [],
            self::OIL_SPILL_PROCESSING => [
                self::ATTACHMENTS,
                self::BANDING_MORPHOMETRICS,
            ],
            self::OIL_SPILL => [
                self::LAB_REPORTS,
                self::NECROPSY,
                self::DAILY_EXAM,
                self::OIL_SPILL_PROCESSING,
                self::OIL_SPILL_WASH,
                self::OIL_SPILL_CONDITIONING
            ],
            self::OWCN_OWRMD => [
                self::OIL_SPILL
            ],
            self::OWCN_IOA => [
                self::OIL_SPILL_PROCESSING,
                self::OIL_SPILL_WASH,
            ],
            self::OWCN_MEMBER_ORGANIZATION => [
                self::OIL_SPILL_PROCESSING,
                self::OIL_SPILL_WASH,
            ],
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'description' => $this->description(),
            'icon' => $this->icon(),
            'help_scout_article_id' => $this->helpScoutArticleId(),
            'public' => $this->public(),
            'pro' => $this->pro(),
            'dependencies' => $this->dependencies(),
        ];
    }
}
