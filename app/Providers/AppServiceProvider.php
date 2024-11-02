<?php

namespace App\Providers;

use Api2Pdf\Api2Pdf;
use App\Models\Patient;
use App\PdfApiInterface;
use App\Repositories\AdministrativeDivision;
use App\Repositories\SettingsStore;
use App\Services\DomPdfEngine;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
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
        });

        $this->app->singleton(PdfApiInterface::class, function () {
            if (config('wrmd.reporting.pdf_driver') === 'api2pdf') {
                return new Api2Pdf(config('services.api2pdf.key'));
            } elseif (config('wrmd.reporting.pdf_driver') === 'domPdf') {
                return new DomPdfEngine();
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => \App\Models\User::class,
            'team' => \App\Models\Team::class,
            'cbc' => \App\Models\LabCbcResult::class,
            'settings' => \App\Models\Setting::class,
            'person' => \App\Models\Person::class,
            'patient' => \App\Models\Patient::class,
            'labReport' => \App\Models\LabReport::class,
            'fecal' => \App\Models\LabFecalResult::class,
            'cytology' => \App\Models\LabCytologyResult::class,
            'chemistry' => \App\Models\LabChemistryResult::class,
            'urinalysis' => \App\Models\LabUrinalysisResult::class,
            'toxicology' => \App\Models\LabToxicologyResult::class,
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

        // Policies
        foreach ([
            \App\Policies\AdminPolicy::class,
            \App\Policies\PrivacyPolicy::class,
            \App\Policies\OperationsPolicy::class,
        ] as $policy) {
            foreach (get_class_methods(new $policy()) as $method) {
                Gate::define($method, "$policy@$method");
            }
        }

        // Number macros
        foreach ([
            'significantFigures' => \App\Macros\SignificantFigures::class,
            'percentageOf' => \App\Macros\PercentageOf::class,
            'survivalRate' => \App\Macros\SurvivalRate::class
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
