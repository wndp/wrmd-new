<?php

namespace Tests\Feature\Analytics\Tables;

use App\Analytics\AnalyticFilters;
use App\Analytics\Tables\CitiesFound;
use App\Models\Team;
use Tests\TestCase;

final class CitiesFoundTableTest extends TestCase
{
    public function test_some_test(): void
    {
        $table = (new CitiesFound(
            Team::factory()->create(),
            new AnalyticFilters
        ))->compute();
    }
}
