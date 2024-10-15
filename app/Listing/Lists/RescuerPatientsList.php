<?php

namespace App\Listing\Lists;

use App\Domain\Admissions\Admission;
use App\Listing\ListingQuery;
use App\Listing\LiveList;
use Illuminate\Pagination\LengthAwarePaginator;

class RescuerPatientsList extends LiveList
{
    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): LengthAwarePaginator
    {
        return ListingQuery::run()
            ->eagerLoadUsing($this->columns)
            ->where('team_id', $this->team->id)
            ->withWhereHas('patient', fn ($q) => $q->where('rescuer_id', $this->request->rescuerId))
            ->orderBy('admissions.case_year', 'desc')
            ->orderBy('admissions.case_id', 'desc')
            ->paginate(15);



        // $select = collect($this->columns)->diff(
        //     fields()->where('computed', true)->keys()->toArray()
        // );

        // return Admission::where('team_id', $this->team->id)
        //     ->select($select->toArray())
        //     ->where('rescuer_id', $this->request->rescuerId)
        //     ->with('patient')
        //     ->selectAdmissionKeys()
        //     ->joinTables($this->columns)
        //     ->paginate(15);
    }
}
