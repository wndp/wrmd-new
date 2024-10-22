<?php

namespace Tests;

use App\Caches\PatientSelector;
use App\Caches\QueryCache;
use App\Repositories\AdministrativeDivision;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Silber\Bouncer\BouncerFacade;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        BouncerFacade::dontCache();
        QueryCache::empty();
        PatientSelector::empty();

        app()->singleton(AdministrativeDivision::class, function () {
            return new AdministrativeDivision('en', 'US');
        });
    }
}
