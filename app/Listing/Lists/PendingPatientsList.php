<?php

namespace App\Listing\Lists;

use App\Domain\Admissions\Admission;
use App\Listing\ListingQuery;
use App\Listing\LiveList;
use Illuminate\Pagination\LengthAwarePaginator;

class PendingPatientsList extends LiveList
{
    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('Pending Patients');
    }

    /**
     * The lists icon to display on the view.
     *
     * @var string
     */
    public function icon(): string
    {
        return 'realtime';
    }

    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): LengthAwarePaginator
    {
        // $select = collect($this->columns)->diff(
        //     fields()->where('computed', true)->keys()->toArray()
        // );

        return ListingQuery::run()
            ->selectColumns($this->columns)
            ->selectAdmissionKeys()
            ->joinTables($this->columns)
            ->where('team_id', $this->team->id)
            ->where('disposition', 'pending')
            ->with('patient')
            ->paginate()
            ->onEachSide(1)
            ->withQueryString();
    }
}
