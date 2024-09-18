<?php

namespace App\Listing;

use App\Concerns\AsAction;
use App\Models\Admission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListingQuery
{
    use AsAction;

    public $query;

    public function handle()
    {
        $this->query = Admission::query()->joinPatients();

        return $this;
    }

    public function selectColumns(array $select = []): self
    {
        if (Arr::first($select) === '*') {

        } else {
            $this->query->select($select);
        }

        return $this;
    }

    /**
     * Add admission keys to query to ensure results represent the actual admission.
     */
    public function selectAdmissionKeys(): self
    {
        $this->query->addSelect([
            'admissions.case_id as case_id',
            'admissions.case_year as case_year',
            'admissions.team_id as team_id',
            'admissions.patient_id as patient_id',
            'admissions.hash as hash',
        ]);

        return $this;
    }

    public function joinTables(array $selecting = []): self
    {
        if (Arr::first($selecting) === '*') {
            $this->joinAllTables();
        } else {
            $this->joinSelectedTables($selecting);
        }

        return $this;
    }

    /**
     * Join all available tables.
     */
    public function joinAllTables(): void
    {
        $this->query
            //->joinPatients()
            ->joinTaxa()
            ->joinPeople()
            //->joinTreatmentLogs()
            ->joinIntakeExam()
            ->joinLastLocation()
            ->joinRechecks();

        // Fire joinAll event to allow extensions to select and join in this query.
        event('JoinTables.all', $query);
    }

    /**
     * Cycle through the columns being selected to join the associated tables.
     */
    public function joinSelectedTables($selecting): void
    {
        collect($selecting)->map(function ($tableDotField) {
            return explode('.', $tableDotField)[0];
        })
            ->reject(function ($table) {
                return in_array($table, ['admissions', 'patients']);
            })
            //->prepend('patients')
            ->unique()
            ->each(function ($table) {
                $joinMethod = 'join'.Str::studly($table);

                if (method_exists($this, $joinMethod)) {
                    $this->$joinMethod();
                } else {
                    event('JoinTables.'.$table, $this->query);
                }
            });
    }

    // public function joinPatientLocations()
    // {
    //     $this->leftJoinCurrentLocation();

    //     return $this;
    // }

    public function __call(string $method, array $parameters)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $parameters);
        }

        return call_user_func_array([$this->query, $method], $parameters);
    }
}
