<?php

namespace App\Listing\Lists;

use App\Domain\Admissions\Admission;
use App\Listing\ListingQuery;
use App\Listing\LiveList;
use Illuminate\Support\Collection;

class PatientsList extends LiveList
{
    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): Collection
    {
        // $select = collect($this->columns)->diff(
        //     fields()->where('computed', true)->keys()->toArray()
        // );

        return ListingQuery::run()
            ->eagerLoadUsing($this->columns)
            ->where('team_id', $this->team->id)
            ->whereIn('admissions.patient_id', explode(',', $this->request->patients))
            ->orderBy('admissions.case_year', 'desc')
            ->orderBy('admissions.case_id', 'desc')
            ->get();

        // return Admission::where('team_id', $this->team->id)
        //     ->select($select->toArray())
        //     ->with('patient')
        //     ->selectAdmissionKeys()
        //     ->joinTables($this->columns)
    }
}
