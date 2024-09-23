<?php

namespace App\Listing\Lists;

use App\Listing\ListingQuery;
use App\Listing\LiveList;
use App\Models\Admission;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientsThisYearList extends LiveList
{
    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('All Patients From This Year');
    }

    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): LengthAwarePaginator
    {
        $year = $this->request->get('y', Carbon::now()->year);

        return ListingQuery::run()
            // ->selectColumns($this->columns)
            // ->selectAdmissionKeys()
            // ->joinTables($this->columns)
            ->eagerLoadUsing($this->columns)
            ->where([
                'team_id' => $this->team->id,
                'case_year' => $year,
            ])
            ->orderBy('admissions.case_year', 'desc')
            ->orderBy('admissions.case_id', 'desc')
            ->paginate()
            ->withQueryString()
            ->onEachSide(1);
    }
}
