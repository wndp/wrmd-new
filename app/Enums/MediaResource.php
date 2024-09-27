<?php

namespace App\Enums;

use App\Models\Incident;
use App\Models\Patient;
use Spatie\MediaLibrary\HasMedia;

enum MediaResource: string
{
    case PATIENT = 'PATIENT';
    case INCIDENT = 'INCIDENT';

    public function owningModelInstance(string $id): HasMedia
    {
        /** @var HasMedia */
        return match ($this) {
            self::PATIENT => Patient::query()->findOrFail($id),
            self::INCIDENT => Incident::query()->findOrFail($id),
        };
    }
}
