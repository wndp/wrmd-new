<?php

namespace App\Listing\Lists;

use App\Caches\PatientSelector;
use App\Listing\ListingQuery;
use App\Listing\LiveList;
use Illuminate\Support\Collection;

class SelectedPatientsList extends LiveList
{
    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('Selected Patients');
    }

    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): Collection
    {
        if (PatientSelector::count() === 0) {
            return new Collection();
        }

        return ListingQuery::run()
            ->eagerLoadUsing($this->columns)
            ->limitToSelected()
            ->where('team_id', $this->team->id)
            ->orderBy('admissions.case_year', 'desc')
            ->orderBy('admissions.case_id', 'desc')
            ->get();

        // return Admission::selectColumns($this->columns)
        //     ->where('team_id', $this->team->id)
        //     ->with('patient')
        //     ->joinTables($this->columns)
        //     ->selectAdmissionKeys()
        //     ->orderBy('admissions.case_year', 'desc')
        //     ->orderBy('admissions.case_id', 'desc')
        //     ->get();

        //return Admission::scopedList($this->team->id, null, $this->columns)->limitToSelected()->get();
    }
}
