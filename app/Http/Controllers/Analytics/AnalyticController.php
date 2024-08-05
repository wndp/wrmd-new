<?php

namespace App\Http\Controllers\Analytics;

use App\Analytics\AnalyticFilters;
use App\Analytics\AnalyticFiltersStore;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AnalyticController extends Controller
{
    /**
     * Get the data for the requested analytic.
     */
    public function show(Request $request, string $type, string $name): JsonResponse
    {
        $analytic = $this->deriveAnalyticClass($type, $name);

        $team = $request->filled('teamId')
            ? Team::findOrFail($request->get('teamId'))
            : Auth::user()->currentTeam;

        $filters = new AnalyticFilters(
            AnalyticFiltersStore::all()->merge($request->query())
        );

        $footprint = $request->get('v', $this->footprint($analytic, $team, $filters));

        $results = Cache::tags(['analytics', "team.{$team->id}"])->remember(
            $footprint,
            now()->addMinutes(5),
            function () use ($analytic, $team, $filters) {
                return $analytic::analyze(
                    $team,
                    $filters
                );
            }
        );

        return response()->json($results);
    }

    /**
     * Derive the path to the analytic class.
     *
     * @return string
     */
    public function deriveAnalyticClass(string $type, string $name)
    {
        $type = Str::studly($type);
        [$namespace, $class] = array_map(fn ($word) => Str::studly($word), array_pad(explode('::', $name), 2, ''));

        if (! empty($class)) {
            return "\App\Extensions\\$namespace\Analytics\\$type\\$class";
        }

        return "\App\Analytics\\$type\\$namespace";
    }

    /**
     * Build the analytic cache footprint.
     *
     * @return string
     */
    public function footprint(string $analytic, Team $team, AnalyticFilters $filters)
    {
        return md5(trim("{$analytic}.{$team->id}.".Arr::query($filters->toArray()), '.'));
    }
}
