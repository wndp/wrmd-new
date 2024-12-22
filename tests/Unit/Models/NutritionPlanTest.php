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
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class NutritionPlanTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use GetsCareLogs;
    use RefreshDatabase;

    public function test_a_nutrition_plan_has_many_ingredients()
    {
        $nutritionPlan = NutritionPlan::factory()->has(NutritionPlanIngredient::factory()->count(3), 'ingredients')->create();

        $this->assertInstanceOf(Collection::class, $nutritionPlan->ingredients);
        $this->assertInstanceOf(NutritionPlanIngredient::class, $nutritionPlan->ingredients->first());
    }

    public function test_a_nutrition_plan_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            NutritionPlan::factory()->create(),
            'created'
        );
    }

    public function test_it_filters_nutrition_plans_into_the_care_log(): void
    {
        $me = $this->createTeamUser();
        Auth::loginUsingId($me->user->id);

        $patient = Patient::factory()->create();

        NutritionPlan::factory()->create([
            'patient_id' => $patient->id,
            'started_at' => '2017-04-09',
            'ended_at' => '2017-04-10',
        ]);

        $logs = $this->getCareLogs($patient, $me->user);

        $this->assertCount(1, $logs);
        $this->assertInstanceOf(NutritionPlan::class, $logs[0]->model);
        $this->assertEquals('2017-04-08 17:00:00', $logs[0]->logged_at_date_time->toDateTimeString());
    }

    public function test_if_a_nutrition_plans_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $nutritionPlan = NutritionPlan::factory()->create(['patient_id' => $patient->id, 'name' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $nutritionPlan->patient->refresh();

        // Cant update
        $nutritionPlan->update(['name' => 'NEW']);
        $this->assertEquals('OLD', $nutritionPlan->fresh()->name);

        // Cant save
        $nutritionPlan->name = 'NEW';
        $nutritionPlan->save();
        $this->assertEquals('OLD', $nutritionPlan->fresh()->name);
    }

    public function test_if_a_nutrition_plans_patient_is_locked_then_it_can_not_be_created(): void
    {
        $nutritionPlan = NutritionPlan::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($nutritionPlan->exists);
    }

    public function test_if_a_nutrition_plans_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $nutritionPlan = NutritionPlan::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $nutritionPlan->patient->refresh();

        $nutritionPlan->delete();
        $this->assertDatabaseHas('nutrition_plans', ['id' => $nutritionPlan->id, 'deleted_at' => null]);
    }

    public function test_when_a_patient_is_replicated_so_are_the_nutrition_plans(): void
    {
        $patient = Patient::factory()->create();

        NutritionPlan::factory()->create(['patient_id' => $patient->id]);
        NutritionPlan::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(2, NutritionPlan::where('patient_id', $patient->id)->get());
        $this->assertCount(2, NutritionPlan::where('patient_id', $newPatient->id)->get());
    }
}
