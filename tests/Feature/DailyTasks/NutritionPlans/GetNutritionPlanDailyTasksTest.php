<?php

namespace Tests\Feature\DailyTasks\NutritionPlans;

use App\Actions\GetNutritionPlanDailyTasks;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\SettingKey;
use App\Models\NutritionPlan;
use App\Support\DailyTasksFilters;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class GetNutritionPlanDailyTasksTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_nothing_is_returned_if_nutrition_plans_are_not_included(): void
    {
        $result = GetNutritionPlanDailyTasks::handle(
            new DailyTasksFilters(['include' => '']),
            []
        );

        $this->assertNull($result);
    }

    public function test_it_gets_a_patients_nutrition_plans(): void
    {
        $frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_NUTRITION_FREQUENCY_IS_HOURS
        );

        $me = $this->createTeamUser();

        $this->registerSettingsContainer($me->team);

        $admission = $this->createCase($me->team);

        $recheck = NutritionPlan::factory()->create([
            'patient_id' => $admission->patient_id,
            'started_at' => Carbon::now(Wrmd::settings(SettingKey::TIMEZONE)),
            'ended_at' => null,
            'frequency_unit_id' => $frequencyId,
        ]);

        $result = GetNutritionPlanDailyTasks::handle(
            new DailyTasksFilters(),
            [$admission->patient_id]
        );

        $this->assertTrue($result->first()->is($recheck));
    }
}
