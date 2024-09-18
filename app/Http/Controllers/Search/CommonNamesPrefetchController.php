<?php

namespace App\Http\Controllers\Search;

use App\Domain\Taxonomy\TaxaQueryTrait;
use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommonNamesPrefetchController extends Controller
{
    //use TaxaQueryTrait;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = Admission::select('patients.common_name', 'patients.taxon_id')
            ->selectRaw('count(`admissions`.`case_id`) as `admissions_count`')
            ->joinPatients()
            ->where('team_id', Auth::user()->current_team_id)
            ->whereIn('case_year', [Carbon::now()->format('Y'), Carbon::now()->format('Y') - 1])
            ->whereNotNull('patients.taxon_id')
            ->groupBy('patients.common_name', 'patients.taxon_id')
            ->orderByDesc('admissions_count')
            ->limit(50)
            ->get()
            ->transform(fn ($record) => [
                'value' => $record->taxon_id,
                'label' => $record->common_name
            ]);

        return response()->json($data);

        // $this->results = $this->selectColumns()
        //     ->joinMostPopular()
        //     ->whereLanguage(App::getLocale())
        //     ->limit(50)
        //     ->get();

        // return response()->json(
        //     $this->formatResults()
        // );
    }

    /**
     * Join with the most popular taxa for the account.
     */
    private function joinMostPopular(): Builder
    {
        $this->query->addSelect(DB::raw('count(`admissions`.`case_id`) as `count`'))
            ->join('patients', 'taxa.id', '=', 'patients.taxon_id')
            ->join('admissions', function ($join) {
                $join->on('patients.id', '=', 'admissions.patient_id')
                    ->where('account_id', '=', Auth::user()->current_team_id)
                    ->whereIn('case_year', [date('Y'), date('Y') - 1]);
            })
            ->groupBy('common_names.common_name', 'taxa.id')
            ->orderByDesc('count');

        return $this->query;
    }
}
