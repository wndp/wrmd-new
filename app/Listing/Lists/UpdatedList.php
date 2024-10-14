<?php

namespace App\Listing\Lists;

use App\Enums\SettingKey;
use App\Domain\Admissions\Admission;
use App\Listing\ListingQuery;
use App\Listing\LiveList;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UpdatedList extends LiveList
{
    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('Patients Updated Today');
    }

    /**
     * The lists icon to display on the view.
     *
     * @var string
     */
    public function icon(): string
    {
        return 'edit-pencil';
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
            ->whereDate('patients.updated_at', Carbon::now(Wrmd::settings(SettingKey::TIMEZONE))->format('Y-m-d'))
            ->with('patient')
            ->get();
    }
}
