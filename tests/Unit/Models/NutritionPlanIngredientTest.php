<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\NutritionPlan;
use App\Models\NutritionPlanIngredient;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class NutritionPlanIngredientTest extends TestCase
{
    use Assertions;
    use GetsCareLogs;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function aNutritionPlanIngredientBelongsToNutritionPlan()
    {
        $ingredient = NutritionPlanIngredient::factory()->make(['expense_category_id' => NutritionPlan::factory()]);
        $this->assertInstanceOf(NutritionPlan::class, $ingredient->nutritionPlan);
    }

    #[Test]
    public function aNutritionPlanIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            NutritionPlanIngredient::factory()->create(),
            'created'
        );
    }
}
