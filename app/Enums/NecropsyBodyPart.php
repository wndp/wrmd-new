<?php

namespace App\Enums;

enum NecropsyBodyPart: string
{
    case INTEGUMENT = 'INTEGUMENT';
    case CAVITIES = 'CAVITIES';
    case CARDIOVASCULAR = 'CARDIOVASCULAR';
    case RESPIRATORY = 'RESPIRATORY';
    case GASTROINTESTINAL = 'GASTROINTESTINAL';
    case ENDOCRINE_REPRODUCTIVE = 'ENDOCRINE_REPRODUCTIVE';
    case LIVER_GALLBLADDER = 'LIVER_GALLBLADDER';
    case HEMATOPOIETIC = 'HEMATOPOIETIC';
    case RENAL = 'RENAL';
    case NERVOUS = 'NERVOUS';
    case MUSCULOSKELETAL = 'MUSCULOSKELETAL';
    case HEAD = 'HEAD';

    public function label(): string
    {
        return match ($this) {
            self::INTEGUMENT => __('Integument / External Examination'),
            self::CAVITIES => __('Peritoneal, Pleural and Pericardial Cavities'),
            self::CARDIOVASCULAR => __('Cardiovascular'),
            self::RESPIRATORY => __('Respiratory'),
            self::GASTROINTESTINAL => __('Gastrointestinal'),
            self::ENDOCRINE_REPRODUCTIVE => __('Endocrine, Reproductive'),
            self::LIVER_GALLBLADDER => __('Liver, Gallbladder'),
            self::HEMATOPOIETIC => __('Hematopoietic'),
            self::RENAL => __('Renal and Urinary'),
            self::NERVOUS => __('Nervous'),
            self::MUSCULOSKELETAL => __('Musculoskeletal'),
            self::HEAD => __('Eye, Ears, Mouth'),
        };
    }
}
