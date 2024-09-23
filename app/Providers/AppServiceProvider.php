<?php

namespace App\Providers;

use Api2Pdf\Api2Pdf;
use App\Models\Patient;
use App\PdfApiInterface;
use App\Repositories\SettingsStore;
use App\Services\DomPdfEngine;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Query\Builder;
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

        $this->app->singleton(PdfApiInterface::class, function () {
            if (config('wrmd.reporting.pdf_driver') === 'api2pdf') {
                return new Api2Pdf(config('services.api2pdf.key'));
            } else if (config('wrmd.reporting.pdf_driver') === 'domPdf') {
                return new DomPdfEngine();
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Queue::createPayloadUsing(function ($connection, $queue, $payload) {
            $jobData = $payload['data'];

            if (! isset($jobData['initiated_by'])) {
                $jobData = array_merge($payload['data'], array_filter([
                    'initiated_by' => request()->user()->id ?? null,
                ]));
            }

            return ['data' => $jobData];
        });

        RateLimiter::for(
            'auth',
            fn (Request $request) =>
            App::isProduction() ? [
                Limit::perMinute(500),
                Limit::perMinute(5)->by($request->ip()),
                Limit::perMinute(5)->by($request->input('email')),
            ] : Limit::none()
        );

        RateLimiter::for(
            'api',
            fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        );

        Route::bind('voidedPatient', fn ($value) => Patient::onlyVoided()->findOrFail($value));

        foreach ([
            \App\Policies\AdminPolicy::class,
            \App\Policies\PrivacyPolicy::class,
            \App\Policies\OperationsPolicy::class,
        ] as $policy) {
            foreach (get_class_methods(new $policy()) as $method) {
                Gate::define($method, "$policy@$method");
            }
        }

        foreach ([
            'significantFigures' => \App\Macros\SignificantFigures::class,
            'percentageOf' => \App\Macros\PercentageOf::class,
            'survivalRate' => \App\Macros\SurvivalRate::class
        ] as $macro => $class) {
            Number::macro($macro, app($class)());
        }
    }
}
