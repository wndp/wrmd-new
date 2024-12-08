<?php

namespace Tests;

use App\Caches\PatientSelector;
use App\Caches\QueryCache;
use App\Repositories\AdministrativeDivision;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;
use Silber\Bouncer\BouncerFacade;
use Tests\Traits\FeatureMacros;

abstract class TestCase extends BaseTestCase
{
    use FeatureMacros;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        BouncerFacade::dontCache();
        QueryCache::empty();
        PatientSelector::empty();
        AttributeOptionExistsRule::clearCache();

        app()->singleton(AdministrativeDivision::class, function () {
            return new AdministrativeDivision('en', 'US');
        });

        \Illuminate\Support\Facades\Http::fake([
            \Laravel\Paddle\Cashier::apiUrl().'/*' => \Illuminate\Support\Facades\Http::response([
                'data' => [[
                    'id' => fn () => \Illuminate\Support\Str::random(),
                    'name' => '',
                    'email' => '',
                ]]
            ]),
        ]);
    }
}
