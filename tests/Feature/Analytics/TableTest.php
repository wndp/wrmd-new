<?php

namespace Tests\Feature\Analytics;

use App\Analytics\AnalyticFilters;
use App\Analytics\Contracts\Table;
use App\Models\Team;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TableTest extends TestCase
{
    #[Test]
    public function theQuerySelectsTheMinimalRequiredAttributes(): void
    {
        $table = new class (Team::factory()->create(), new AnalyticFilters()) extends Table {
            protected function compute()
            {
            }
        };

        $this->assertStringContainsString('`patients`.`taxon_id`, `disposition_id`, `admissions`.`patient_id`', $table->baseQuery()->toSql());
    }

    #[Test]
    public function theCompareQuerySelectsTheMinimalRequiredAttributes(): void
    {
        $table = new class (Team::factory()->create(), new AnalyticFilters()) extends Table {
            protected function compute()
            {
            }
        };

        $this->assertStringContainsString('`patients`.`taxon_id`, `disposition_id`, `admissions`.`patient_id`', $table->baseCompareQuery()->toSql());
    }
}
