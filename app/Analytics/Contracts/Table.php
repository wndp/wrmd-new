<?php

namespace App\Analytics\Contracts;

use App\Models\Admission;

abstract class Table extends Analytic
{
    /**
     * Build a basic query for a data table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function baseQuery()
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('patients.taxon_id', 'disposition', 'admissions.patient_id')
            ->joinPatients();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        return $query;
    }

    /**
     * Build a basic comparative query for a data table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function baseCompareQuery()
    {
        return Admission::where('team_id', $this->team->id)
            ->select('patients.taxon_id', 'disposition', 'admissions.patient_id')
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to);
    }
}
