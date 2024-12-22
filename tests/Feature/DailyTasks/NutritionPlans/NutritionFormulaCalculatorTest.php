<?php

namespace Tests\Feature\DailyTasks\NutritionPlans;

use App\Enums\FormulaType;
use App\Models\Formula;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

#[Group('daily-tasks')]
final class NutritionFormulaCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_starts_the_nutrition_plan_the_day_it_is_written(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create([
            'type' => FormulaType::NUTRITION,
            'defaults' => [
                'start_on_plan_date' => true,
            ],
        ]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertTrue(Carbon::parse($results['started_at'])->isSameDay(Carbon::now()));
    }

    public function test_it_ends_the_nutrition_plan_the_give_number_of_days_past_the_start_date(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create([
            'type' => FormulaType::NUTRITION,
            'defaults' => [
                'start_on_plan_date' => true,
                'duration' => 7,
            ],
        ]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertTrue(Carbon::parse($results['ended_at'])->isSameDay(Carbon::now()->addDays(6)));
    }

    public function test_it_leaves_the_nutrition_plan_open_if_no_duration_is_given(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create([
            'type' => FormulaType::NUTRITION,
            'defaults' => [
                'start_on_plan_date' => true,
            ],
        ]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertNull($results['ended_at']);
    }
}
