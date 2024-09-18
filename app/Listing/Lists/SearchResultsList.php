<?php

namespace App\Listing\Lists;

use App\Domain\Admissions\Admission;
use App\Listing\LiveList;
use App\Domain\Searching\SearchCache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchResultsList extends LiveList
{
    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('Search Results');
    }

    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): LengthAwarePaginator
    {
        if (! SearchCache::exists()) {
            return new Collection();
        }

        return Admission::selectColumns($this->columns)
            ->where('team_id', $this->team->id)
            ->with('patient')
            ->limitToSearchResults()
            ->joinTables($this->columns)
            ->selectAdmissionKeys()
            ->orderBy('admissions.case_year', 'desc')
            ->orderBy('admissions.case_id', 'desc')
            ->paginate()
            ->withQueryString()
            ->onEachSide(1);
    }
}
