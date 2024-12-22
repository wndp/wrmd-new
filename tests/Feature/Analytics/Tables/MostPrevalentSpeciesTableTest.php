<?php

namespace Tests\Feature\Analytics\Tables;

use App\Analytics\AnalyticFilters;
use App\Analytics\Tables\MostPrevalentSpecies;
use App\Models\Team;
use Tests\TestCase;

final class MostPrevalentSpeciesTableTest extends TestCase
{
    public function test_some_test(): void
    {
        $table = (new MostPrevalentSpecies(
            Team::factory()->create(),
            new AnalyticFilters
        ))->compute();
    }
}
