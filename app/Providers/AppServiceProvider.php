<?php

namespace App\Providers;

use App\Repositories\SettingsStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The application level policy definitions.
     *
     * @var array
     */
    protected $applicationPolicies = [
        \App\Policies\AdminPolicy::class,
        \App\Policies\PrivacyPolicy::class,
        \App\Policies\OperationsPolicy::class,
    ];

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

        foreach ($this->applicationPolicies as $policy) {
            foreach (get_class_methods(new $policy()) as $method) {
                Gate::define($method, "$policy@$method");
            }
        }

        Number::macro('significantFigures', function ($number, $significantFigures) {
            // May be negative.
            $decimalPlaces = intval(floor($significantFigures - log10(abs($number))));

            // Round as a regular number.
            $number = round($number, $decimalPlaces);

            // Leave the formatting to number_format(), but always format 0 to 0 decimal places.
            return (float) number_format($number, 0 == $number ? 0 : $decimalPlaces);
        });
    }
}
