<?php

namespace App\Providers;

use Api2Pdf\Api2Pdf;
use App\Models\NutritionPlan;
use App\Models\Patient;
use App\PdfApiInterface;
use App\Repositories\AdministrativeDivision;
use App\Repositories\SettingsStore;
use App\Services\DomPdfEngine;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsStore::class, function () {
            if (Auth::check()) {
                return new SettingsStore(Auth::user()->currentTeam);
            }
        });

        $this->app->singleton(AdministrativeDivision::class, function () {
            if (Auth::check()) {
                return new AdministrativeDivision(App::getLocale(), Auth::user()->currentTeam->country);
            }

            return new AdministrativeDivision(config('app.locale'), 'US');
        });

        $this->app->singleton(PdfApiInterface::class, function () {
            if (config('wrmd.reporting.pdf_driver') === 'api2pdf') {
                return new Api2Pdf(config('services.api2pdf.key'));
            } elseif (config('wrmd.reporting.pdf_driver') === 'domPdf') {
                return new DomPdfEngine;
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'banding' => \App\Models\Banding::class,
            'care_log' => \App\Models\CareLog::class,
            'communication' => \App\Models\Communication::class,
            'cbc' => \App\Models\LabCbcResult::class,
            'chemistry' => \App\Models\LabChemistryResult::class,
            'cytology' => \App\Models\LabCytologyResult::class,
            'custom_field' => \App\Models\CustomField::class,
            'donation' => \App\Models\Donation::class,
            'expense_category' => \App\Models\ExpenseCategory::class,
            'expense_transaction' => \App\Models\ExpenseTransaction::class,
            'exam' => \App\Models\Exam::class,
            'fecal' => \App\Models\LabFecalResult::class,
            'formula' => \App\Models\Formula::class,
            'incident' => \App\Models\Incident::class,
            'labReport' => \App\Models\LabReport::class,
            'location' => \App\Models\Location::class,
            'media' => \App\Models\Media::class,
            'morphometric' => \App\Models\Morphometric::class,
            'necropsy' => \App\Models\Necropsy::class,
            'nutrition_plan' => NutritionPlan::class,
            'nutrition_plan_ingredient' => \App\Models\NutritionPlanIngredient::class,
            'oil_processing' => \App\Models\OilProcessing::class,
            'patient' => \App\Models\Patient::class,
            'patient_location' => \App\Models\PatientLocation::class,
            'person' => \App\Models\Person::class,
            'prescription' => \App\Models\Prescription::class,
            'recheck' => \App\Models\Recheck::class,
            'settings' => \App\Models\Setting::class,
            'team' => \App\Models\Team::class,
            'toxicology' => \App\Models\LabToxicologyResult::class,
            'urinalysis' => \App\Models\LabUrinalysisResult::class,
            'user' => \App\Models\User::class,
            'oil_spill_event' => \App\Models\OilSpillEvent::class,
            'oil_waterproofing_assessment' => \App\Models\OilWaterproofingAssessment::class,
            'veterinarian' => \App\Models\Veterinarian::class,
        ]);

        Route::bind('voidedPatient', fn ($value) => Patient::onlyVoided()->findOrFail($value));

        Queue::createPayloadUsing(function ($connection, $queue, $payload) {
            $jobData = $payload['data'];

            if (! isset($jobData['initiated_by'])) {
                $jobData = array_merge($payload['data'], array_filter([
                    'initiated_by' => request()->user()->id ?? null,
                ]));
            }

            return ['data' => $jobData];
        });

        // Policies, ie: computed permissions
        foreach ([
            \App\Policies\PrivacyPolicy::class,
            \App\Policies\OperationsPolicy::class,
        ] as $policy) {
            foreach (get_class_methods(new $policy) as $method) {
                Gate::define($method, "$policy@$method");
            }
        }

        // Number macros
        foreach ([
            'significantFigures' => \App\Macros\SignificantFigures::class,
            'percentageOf' => \App\Macros\PercentageOf::class,
            'survivalRate' => \App\Macros\SurvivalRate::class,
        ] as $macro => $class) {
            Number::macro($macro, app($class)());
        }

        // Array macros
        foreach ([
            'prefix' => \App\Macros\Prefix::class,
        ] as $macro => $class) {
            Arr::macro($macro, app($class)());
        }
    }
}
