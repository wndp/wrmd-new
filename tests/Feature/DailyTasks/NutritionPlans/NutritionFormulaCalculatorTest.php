<?php

namespace Tests\Feature\DailyTasks\NutritionPlans;

use App\Enums\FormulaType;
use App\Models\Formula;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[Group('daily-tasks')]
final class NutritionFormulaCalculatorTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itStartsTheNutritionPlanTheDayItIsWritten(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create([
            'type' => FormulaType::NUTRITION,
            'defaults' => [
                'start_on_plan_date' => true,
            ]
        ]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertTrue(Carbon::parse($results['started_at'])->isSameDay(Carbon::now()));
    }

    #[Test]
    public function itEndsTheNutritionPlanTheGiveNumberOfDaysPastTheStartDate(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create([
            'type' => FormulaType::NUTRITION,
            'defaults' => [
                'start_on_plan_date' => true,
                'duration' => 7,
            ]
        ]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertTrue(Carbon::parse($results['ended_at'])->isSameDay(Carbon::now()->addDays(6)));
    }

    #[Test]
    public function itLeavesTheNutritionPlanOpenIfNoDurationIsGiven(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create([
            'type' => FormulaType::NUTRITION,
            'defaults' => [
                'start_on_plan_date' => true,
            ]
        ]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertNull($results['ended_at']);
    }
}
