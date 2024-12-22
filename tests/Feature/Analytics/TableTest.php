<?php

namespace Tests\Feature\Analytics;

use App\Analytics\AnalyticFilters;
use App\Analytics\Contracts\Table;
use App\Models\Team;
use Tests\TestCase;

final class TableTest extends TestCase
{
    public function test_the_query_selects_the_minimal_required_attributes(): void
    {
        $table = new class(Team::factory()->create(), new AnalyticFilters) extends Table
        {
            protected function compute() {}
        };

        $this->assertStringContainsString('`patients`.`taxon_id`, `disposition_id`, `admissions`.`patient_id`', $table->baseQuery()->toSql());
    }

    public function test_the_compare_query_selects_the_minimal_required_attributes(): void
    {
        $table = new class(Team::factory()->create(), new AnalyticFilters) extends Table
        {
            protected function compute() {}
        };

        $this->assertStringContainsString('`patients`.`taxon_id`, `disposition_id`, `admissions`.`patient_id`', $table->baseCompareQuery()->toSql());
    }
}
