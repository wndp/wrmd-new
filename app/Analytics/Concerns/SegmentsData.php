<?php

namespace App\Analytics\Concerns;

use App\Caches\PatientSelector;
use App\Caches\SearchCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait SegmentsData
{
    public function addSegmentationMacros()
    {
        $self = $this;

        Builder::macro('withSegment', function ($segment) use ($self) {
            $self->withSegment($this, $segment);

            return $this;
        });

        Builder::macro('limitToSearchedPatients', function ($segment) use ($self) {
            $self->limitToSearchedPatients($this, $segment);

            return $this;
        });
    }

    public function withSegment(Builder $query, $segment)
    {
        [$class, $parameters] = array_pad(explode(':', $segment), 2, null);

        $parameters = is_null($parameters) ? $parameters : trim($parameters);

        $parameters = array_filter(explode(',', $parameters));

        app(
            '\App\Analytics\Segments\\'.Str::studly($class),
            compact('query', 'parameters')
        )
            ->handle();

        return $this;
    }

    public function limitToSearchedPatients(Builder $query)
    {
        $patientIds = $this->getPatientIds();

        if (! empty($patientIds)) {
            $query->whereIn('patients.id', $patientIds);
        }
    }

    /**
     * Get the patient IDs to batch update.
     */
    private function getPatientIds(): array
    {
        if (PatientSelector::exists()) {
            return PatientSelector::get();
        }

        if (SearchCache::exists()) {
            return SearchCache::get()->patientIds;
        }

        return [];
    }
}
