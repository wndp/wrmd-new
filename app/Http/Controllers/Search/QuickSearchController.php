<?php

namespace App\Http\Controllers\Search;

use App\Domain\Admissions\Admission;
use App\Domain\Searching\SearchResponse;
use App\Domain\Taxonomy\TaxaPreciseSearchQuery;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuickSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'search' => 'required',
            'limitToCurrentYear' => 'nullable|boolean',
            'stillPending' => 'nullable|boolean',
        ]);

        $query = Admission::where('admissions.account_id', Auth::user()->current_team_id)
            ->selectAdmissionKeys();

        // If the search is for a case number then split it up and search accordingly.
        if (preg_match('/^(20|19)?\d{2}-\d+$/u', $data['search'], $caseNumber)) {
            [$year, $caseId] = explode('-', $caseNumber[0]);
            $year = is_year($year) ? $year : Carbon::createFromFormat('y', $year)->format('Y');
            $query->where('case_year', $year)->where('case_id', $caseId);

            // If the search is for a wr.md url then search the hash.
        } elseif (preg_match('#^https://wr.md/[a-zA-Z0-9]{5,}$#u', $data['search'])) {
            $tmp = explode('/', $data['search']);
            $query->where('admissions.hash', Str::upper(array_pop($tmp)));

            // If the search is for a known common name search the species' id.
        } elseif ($taxon_id = $this->isKnownCommonName($data['search'])) {
            $query->joinPatients()->where('taxon_id', $taxon_id);

            // If the search term contains a comma then we will assume that its a location
            // string and split it into its area and enclosure parts and search each of
            // those parts separately.
        } elseif (Str::contains($data['search'], ',')) {
            [$area, $enclosure] = explode(',', $data['search']);

            $query->joinPatients()
                ->joinPatientLocations()
                ->orWhere(function ($query) use ($area, $enclosure) {
                    $query->where('area', 'like', '%'.trim($area).'%')
                        ->where('enclosure', 'like', '%'.trim($enclosure).'%');
                });

            // Else fuzzy search
        } else {
            $query->joinPatients()
                ->joinPatientLocations()
                ->joinPeople()
                ->where(function ($query) use ($data) {
                    $query->likeOneOfMany($data['search'], [
                        'admissions.case_id',
                        'admissions.hash',
                        'common_name',
                        'keywords',
                        'band',
                        'microchip_number',
                        'reference_number',
                        'name',
                        'diagnosis',
                        'address_found',
                        'city_found',
                        'subdivision_found',
                        'reasons_for_admission',
                        'care_by_rescuer',
                        'notes_about_rescue',
                        'first_name',
                        'last_name',
                        'area',
                        'enclosure',
                    ]);
                });

            if ($data['limitToCurrentYear'] ?? true) {
                $query->where('case_year', session('caseYear'));
            }

            if ($data['stillPending'] ?? true) {
                $query->where('disposition', 'Pending');
            }
        }

        return SearchResponse::make(
            $query
                ->orderBy('admissions.case_year')
                ->orderBy('admissions.case_id')
                ->queryCache()
                ->get()
        );
    }

    /**
     * Determine if the string is a known common name and return its taxon_id if so.
     *
     * @return int|bool
     */
    private function isKnownCommonName(string $search)
    {
        if (strlen($search) < 3) {
            return false;
        }

        $speciesSearch = (new TaxaPreciseSearchQuery())($search, false)->pluck('taxon_id')->unique();

        return $speciesSearch->count() === 1 ? $speciesSearch->first() : false;
    }
}
