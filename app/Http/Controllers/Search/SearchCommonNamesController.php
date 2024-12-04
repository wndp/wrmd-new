<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\CommonName;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SearchCommonNamesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $results = $request->whenFilled(
            'search',
            fn () => CommonName::search($request->input('search'))->get(),
            fn () => new Collection()
        )
        ->transform(fn ($commonName) => [
            'value' => $commonName->taxon_id,
            'label' => $commonName->common_name,
            'data' => []
        ])
        ->toBase()
        ->merge($this->getMostCommon($request->input('search')))
        ->unique('label')
        ->take(20);

        return response()->json($results);
    }

    private function getMostCommon($search)
    {
        return Cache::remember(
            'mostCommonSpecies.'.Auth::user()->current_team_id,
            Carbon::now()->addHours(2),
            fn () => Admission::select('patients.common_name', 'patients.taxon_id')
                ->selectRaw('count(`admissions`.`case_id`) as `admissions_count`')
                ->joinPatients()
                ->where('team_id', Auth::user()->current_team_id)
                ->whereIn('case_year', [Carbon::now()->format('Y'), Carbon::now()->format('Y') - 1])
                ->whereNotNull('patients.taxon_id')
                ->groupBy('patients.common_name', 'patients.taxon_id')
                ->orderByDesc('admissions_count')
                ->limit(50)
                ->get()
        )
            ->unless(
                is_null($search),
                fn ($collection) => $collection->filter(fn ($row) => Str::containsAll($row->common_name, Str::of($search)->squish()->explode(' '), true))
            )
            ->transform(fn ($record) => [
                'value' => $record->taxon_id,
                'label' => $record->common_name,
                'data' => []
            ]);
    }
}
