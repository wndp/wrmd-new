<?php

namespace App\Listing\Lists;

use App\Domain\Admissions\Admission;
use App\Listing\ListingQuery;
use App\Listing\LiveList;
use Illuminate\Pagination\LengthAwarePaginator;

class AllPatientsList extends LiveList
{
    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('All Patients From All Years');
    }

    /**
     * The lists icon to display on the view.
     *
     * @var string
     */
    public function icon(): string
    {
        return 'list-numbered';
    }

    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): LengthAwarePaginator
    {
        return ListingQuery::run()
            ->selectColumns($this->columns)
            ->selectAdmissionKeys()
            ->joinTables($this->columns)
            ->where('team_id', $this->team->id)
            ->with('patient')
            ->orderBy('admissions.case_year', 'desc')
            ->orderBy('admissions.case_id', 'desc')
            ->paginate()
            ->withQueryString()
            ->onEachSide(1);
    }
}
