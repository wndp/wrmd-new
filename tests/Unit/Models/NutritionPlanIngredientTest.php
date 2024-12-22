<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\NutritionPlan;
use App\Models\NutritionPlanIngredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class NutritionPlanIngredientTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use GetsCareLogs;
    use RefreshDatabase;

    public function test_a_nutrition_plan_ingredient_belongs_to_nutrition_plan()
    {
        $ingredient = NutritionPlanIngredient::factory()->make(['expense_category_id' => NutritionPlan::factory()]);
        $this->assertInstanceOf(NutritionPlan::class, $ingredient->nutritionPlan);
    }

    public function test_a_nutrition_plan_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            NutritionPlanIngredient::factory()->create(),
            'created'
        );
    }
}
