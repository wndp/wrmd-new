<?php

namespace Tests\Feature\DailyTasks\NutritionPlans;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\AttributeOption;
use App\Models\NutritionPlan;
use App\Models\NutritionPlanIngredient;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class NutritionPlanControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_store_a_nutrition_plan(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.nutrition.store', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_store_a_nutrition_plan(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->post(route('patients.nutrition.store', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_patient_before_storing_a_nutrition_plan(): void
    {
        $frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_NUTRITION_FREQUENCY_IS_HOURS
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->post(route('patients.nutrition.store', $patient), [
                'frequency_unit_id' => $frequencyId,
                'started_at' => '2022-09-05',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_store_a_nutrition_plan(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->post(route('patients.nutrition.store', $admission->patient))
            ->assertInvalid([
                'started_at' => 'The start date field is required.',
                'frequency_unit_id' => 'The frequency unit id field is required.',
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.nutrition.store', $admission->patient), [
                'started_at' => 'foo',
                'frequency' => 'foo',
            ])
            ->assertInvalid([
                'started_at' => 'The start date is not a valid date.',
                'frequency' => 'The frequency field must be a number.',
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.nutrition.store', $admission->patient), [
                'started_at' => '2022-06-30',
                'ended_at' => '2022-06-28',
            ])
            ->assertInvalid(['ended_at' => 'The ended at field must be a date after or equal to started at.']);

        $this->actingAs($me->user)
            ->post(route('patients.nutrition.store', $admission->patient), [
                'ingredients' => [[
                    'unit_id' => 1,
                ],
                ]])
            ->assertInvalid([
                'ingredients.0.quantity' => 'The ingredients.0.quantity field is required.',
                'ingredients.0.ingredient' => 'The ingredients.0.ingredient field is required.',
            ]);
    }

    public function test_it_stores_a_nutrition_plan(): void
    {
        $frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_NUTRITION_FREQUENCY_IS_HOURS
        )->attribute_option_id;

        $routeId = AttributeOption::factory()->create(['name' => AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES])->id;
        $ingredientUnitId = AttributeOption::factory()->create(['name' => AttributeOptionName::DAILY_TASK_NUTRITION_INGREDIENT_UNITS])->id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.nutrition.store', $admission->patient), [
                'name' => 'Foo',
                'started_at' => '2022-06-30',
                'ended_at' => '2022-06-30',
                'frequency' => 5,
                'frequency_unit_id' => $frequencyId,
                'route_id' => $routeId,
                'description' => 'Bar',
                'ingredients' => [[
                    'quantity' => 1,
                    'unit_id' => $ingredientUnitId,
                    'ingredient' => 'zap',
                ]],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('nutrition_plans', [
            'patient_id' => $admission->patient_id,
            'name' => 'Foo',
            'started_at' => '2022-06-30',
            'ended_at' => '2022-06-30',
            'frequency' => 5,
            'frequency_unit_id' => $frequencyId,
            'route_id' => $routeId,
            'description' => 'Bar',
        ]);

        $this->assertDatabaseHas('nutrition_plan_ingredients', [
            'nutrition_plan_id' => NutritionPlan::first()->id,
            'quantity' => 1,
            'unit_id' => $ingredientUnitId,
            'ingredient' => 'zap',
        ]);
    }

    public function test_it_validates_ownership_of_a_nutrition_plan_before_updating(): void
    {
        $frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_NUTRITION_FREQUENCY_IS_HOURS
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $nutritionPlan = NutritionPlan::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->put(route('patients.nutrition.update', [$patient, $nutritionPlan]), [
                'frequency_unit_id' => $frequencyId,
                'started_at' => '2022-09-05',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_a_nutrition_plan(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $nutritionPlan = NutritionPlan::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->put(route('patients.nutrition.update', [$admission->patient, $nutritionPlan]))
            ->assertInvalid([
                'started_at' => 'The start date field is required.',
                'frequency_unit_id' => 'The frequency unit id field is required.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.nutrition.update', [$admission->patient, $nutritionPlan]), [
                'started_at' => 'foo',
                'frequency' => 'foo',
            ])
            ->assertInvalid([
                'started_at' => 'The start date is not a valid date.',
                'frequency' => 'The frequency field must be a number.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.nutrition.update', [$admission->patient, $nutritionPlan]), [
                'started_at' => '2022-06-30',
                'ended_at' => '2022-06-28',
            ])
            ->assertInvalid(['ended_at' => 'The ended at field must be a date after or equal to started at.']);

        $this->actingAs($me->user)
            ->put(route('patients.nutrition.update', [$admission->patient, $nutritionPlan]), [
                'ingredients' => [[
                    'unit_id' => 1,
                ],
                ]])
            ->assertInvalid([
                'ingredients.0.quantity' => 'The ingredients.0.quantity field is required.',
                'ingredients.0.ingredient' => 'The ingredients.0.ingredient field is required.',
            ]);
    }

    public function test_it_updates_a_nutrition_plan(): void
    {
        $frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_NUTRITION_FREQUENCY_IS_HOURS
        )->attribute_option_id;

        $routeId = AttributeOption::factory()->create(['name' => AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES])->id;
        $ingredientUnitId = AttributeOption::factory()->create(['name' => AttributeOptionName::DAILY_TASK_NUTRITION_INGREDIENT_UNITS])->id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $nutritionPlan = NutritionPlan::factory()->create(['patient_id' => $admission->patient]);
        $nutritionPlanIngredient = NutritionPlanIngredient::factory()->create(['nutrition_plan_id' => $nutritionPlan->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.nutrition.update', [$admission->patient, $nutritionPlan]), [
                'name' => 'Foo',
                'started_at' => '2022-06-30',
                'ended_at' => '2022-06-30',
                'frequency' => 5,
                'frequency_unit_id' => $frequencyId,
                'route_id' => $routeId,
                'description' => 'Bar',
                'ingredients' => [[
                    'id' => $nutritionPlanIngredient->id,
                    'quantity' => 1,
                    'unit_id' => $ingredientUnitId,
                    'ingredient' => 'zap',
                ]],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('nutrition_plans', [
            'patient_id' => $admission->patient_id,
            'name' => 'Foo',
            'started_at' => '2022-06-30',
            'ended_at' => '2022-06-30',
            'frequency' => 5,
            'frequency_unit_id' => $frequencyId,
            'route_id' => $routeId,
            'description' => 'Bar',
        ]);

        $this->assertDatabaseHas('nutrition_plan_ingredients', [
            'id' => $nutritionPlanIngredient->id,
            'nutrition_plan_id' => $nutritionPlan->id,
            'quantity' => 1,
            'unit_id' => $ingredientUnitId,
            'ingredient' => 'zap',
        ]);
    }

    public function test_it_deletes_a_nutrition_plan_ingredient(): void
    {
        $this->withOutExceptionHandling();

        $frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_NUTRITION_FREQUENCY_IS_HOURS
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $nutritionPlan = NutritionPlan::factory()->create([
            'patient_id' => $admission->patient,
            // 'started_at' => '2022-06-30',
            // 'frequency_unit_id' => $frequencyId,
        ]);
        $nutritionPlanIngredient = NutritionPlanIngredient::factory()->create(['nutrition_plan_id' => $nutritionPlan->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.nutrition.update', [$admission->patient, $nutritionPlan]), [
                'started_at' => '2022-06-30',
                'frequency_unit_id' => $frequencyId,
                'ingredients' => [],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertSoftDeleted('nutrition_plan_ingredients', [
            'id' => $nutritionPlanIngredient->id,
        ]);
    }
}
