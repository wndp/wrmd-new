<?php

namespace App\Enums;

enum ExamBodyPart: string
{
    case HEAD = 'head';
    case CNS = 'cns';
    case CARDIOPULMONARY = 'cardiopulmonary';
    case GASTROINTESTINAL = 'gastrointestinal';
    case MUSCULOSKELETAL = 'musculoskeletal';
    case INTEGUMENT = 'integument';
    case BODY = 'body';
    case FORELIMB = 'forelimb';
    case HINDLIMB = 'hindlimb';

    public function label(): string
    {
        return match ($this) {
            self::HEAD => __('Eyes / Ears / Mouth / Nares'),
            self::CNS => __('Neurologic'),
            self::CARDIOPULMONARY => __('Heart / Lungs'),
            self::GASTROINTESTINAL => __('GI / Vent'),
            self::MUSCULOSKELETAL => __('Musculoskeletal'),
            self::INTEGUMENT => __('Feathers / Fur / Skin'),
            self::BODY => __('Body'),
            self::FORELIMB => __('Wings / Arms'),
            self::HINDLIMB => __('Legs / Feet / Hocks'),
        };
    }
}
