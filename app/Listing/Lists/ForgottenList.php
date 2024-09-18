<?php

namespace App\Listing\Lists;

use App\Domain\Admissions\Admission;
use App\Listing\ListingQuery;
use App\Listing\LiveList;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ForgottenList extends LiveList
{
    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('Forgotten Patients');
    }

    /**
     * The lists icon to display on the view.
     *
     * @var string
     */
    public function icon(): string
    {
        return 'question';
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
            ->where(function ($query) {
                $query->where('patients.updated_at', '<', Carbon::now()->subDays(3))
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('rechecks')
                            ->whereRaw('rechecks.patient_id = patients.id');
                    });
            })
            ->with('patient')
            ->get();
    }
}
