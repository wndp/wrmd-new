<?php

namespace Tests\Feature\Analytics\Tables;

use App\Analytics\AnalyticFilters;
use App\Analytics\Tables\MostPrevalentSpecies;
use App\Models\Team;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MostPrevalentSpeciesTableTest extends TestCase
{
    #[Test]
    public function someTest(): void
    {
        $table = (new MostPrevalentSpecies(
            Team::factory()->create(),
            new AnalyticFilters()
        ))->compute();
    }
}
