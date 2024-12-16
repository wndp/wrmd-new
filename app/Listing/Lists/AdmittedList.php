<?php

namespace App\Listing\Lists;

use App\Enums\SettingKey;
use App\Listing\ListingQuery;
use App\Listing\LiveList;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdmittedList extends LiveList
{
    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('Patients Admitted Today');
    }

    /**
     * The lists icon to display on the view.
     *
     * @var string
     */
    public function icon(): string
    {
        return 'document-add';
    }

    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): Collection
    {
        return ListingQuery::run()
            ->selectColumns($this->columns)
            ->selectAdmissionKeys()
            ->joinTables($this->columns)
            ->whereDate('date_admitted_at', Carbon::now(Wrmd::settings(SettingKey::TIMEZONE))->format('Y-m-d'))
            ->with('patient')
            ->get();
    }
}
