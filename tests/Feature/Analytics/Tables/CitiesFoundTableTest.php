<?php

namespace Tests\Feature\Analytics\Tables;

use App\Analytics\AnalyticFilters;
use App\Analytics\Tables\CitiesFound;
use App\Models\Team;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CitiesFoundTableTest extends TestCase
{
    #[Test]
    public function someTest(): void
    {
        $table = (new CitiesFound(
            Team::factory()->create(),
            new AnalyticFilters()
        ))->compute();
    }
}
