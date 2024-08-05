<?php

namespace App\Http\Controllers\Analytics;

use App\Domain\Analytics\AnalyticFilters;
use App\Domain\Analytics\AnalyticFiltersStore;
use App\Domain\Classifications\ClassificationOptions;
use App\Domain\OptionsStore;
use App\Domain\Taxonomy\TaxonomyOptions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsViewController extends Controller
{
    public function __invoke($group, $subGroup = null): Response
    {
        $component = $this->component($group, $subGroup);

        Inertia::share(['analytics' => [
            'filters' => (new AnalyticFilters(AnalyticFiltersStore::all()))->toArray(),
            'groupStudly' => Str::studly($group),
        ]]);

        OptionsStore::merge(new TaxonomyOptions());
        OptionsStore::merge(new ClassificationOptions('CircumstancesOfAdmission'), 'circumstancesOfAdmission');
        OptionsStore::merge(new ClassificationOptions('ClinicalClassifications'), 'clinicalClassifications');

        return Inertia::render($component);
    }

    private function component($group, $subGroup = null)
    {
        $classificationTags = ['circumstances-of-admission', 'clinical-classifications'];

        if (in_array($group, $classificationTags)) {
            return 'Analytics/ClassificationTags/'.Str::studly($subGroup ?? 'Overview');
        }

        return $this->routeMap(trim("$group/$subGroup", '/'));
    }

    private function routeMap($route)
    {
        return match ($route) {
            '' => 'Analytics/Patients/Overview',
            'taxa' => 'Analytics/Patients/Taxa/Overview',
            'taxa/prevalent-patients' => 'Analytics/Patients/Taxa/MostPrevalent',
            'taxa/class' => 'Analytics/Patients/Taxa/Class',
            'taxa/taxonomy-tree' => 'Analytics/Patients/Taxa/TaxonomyTree',
            'demographics' => 'Analytics/Patients/Demographics/Overview',
            'demographics/health-indicators' => 'Analytics/Patients/Demographics/HealthIndicators',
            'demographics/age' => 'Analytics/Patients/Demographics/Age',
            'demographics/weight' => 'Analytics/Patients/Demographics/Weight',
            'demographics/sex' => 'Analytics/Patients/Demographics/Sex',
            'demographics/attitude' => 'Analytics/Patients/Demographics/Attitude',
            'demographics/body-condition' => 'Analytics/Patients/Demographics/BodyCondition',
            'demographics/dehydration' => 'Analytics/Patients/Demographics/Dehydration',
            'demographics/mucous-membrane' => 'Analytics/Patients/Demographics/MucousMembrane',
            'origin' => 'Analytics/Locations/Origin/Overview',
            'origin/subdivision' => 'Analytics/Locations/Origin/Subdivision',
            'origin/city' => 'Analytics/Locations/Origin/City',
            'origin/map' => 'Analytics/Locations/Origin/Map',
            'location' => 'Analytics/Locations/Location/Overview',
            'location/facility' => 'Analytics/Locations/Location/Facility',
            'location/area' => 'Analytics/Locations/Location/Area',
            'circumstances-of-admission' => 'Analytics/CircumstancesOfAdmission/Overview',
            'circumstances-of-admission/all' => 'Analytics/CircumstancesOfAdmission/All',
            'circumstances-of-admission/select' => 'Analytics/CircumstancesOfAdmission/Select',
            'circumstances-of-admission/over-time' => 'Analytics/CircumstancesOfAdmission/OverTime',
            'circumstances-of-admission/survival-rate' => 'Analytics/CircumstancesOfAdmission/SurvivalRate',
            'disposition' => 'Analytics/Disposition/Overview',
            'disposition/releases' => 'Analytics/Disposition/Releases',
            'disposition/transfers' => 'Analytics/Disposition/Transfers',
            'disposition/survival-rate' => 'Analytics/Disposition/SurvivalRate',
            'disposition/map' => 'Analytics/Disposition/Map',
            'hotline' => 'Analytics/Hotline/Overview',
            'hotline/call-duration' => 'Analytics/Hotline/CallDuration',
            'hotline/map' => 'Analytics/Hotline/Map',
        };
    }
}
