<?php

namespace App\Listing\Lists;

use App\Listing\ListingQuery;
use App\Listing\LiveList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomelessList extends LiveList
{
    /**
     * The lists route.
     *
     * @var string
     */
    public static $route = 'homeless';

    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('Patients Without a Location');
    }

    /**
     * The lists icon to display on the view.
     *
     * @var string
     */
    public function icon(): string
    {
        return 'location-current';
    }

    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): Collection
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
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('patient_locations')
                    ->whereRaw('patient_locations.patient_id = patients.id');
            })
            ->with('patient')
            ->get();
    }
}
