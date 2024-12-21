<?php

namespace Tests\Feature\Maintenance;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\FormulaType;
use App\Models\AttributeOption;
use App\Models\Formula;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithTests;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;
use Tests\Traits\FeatureMacros;

final class FormulaControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use FeatureMacros;
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function unAuthenticatedUsersCantAccessFormula(): void
    {
        $this->get(route('maintenance.formulas.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessFormula(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('maintenance.formulas.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheFormulaIndexPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $response = $this->actingAs($me->user)->get(route('maintenance.formulas.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Maintenance/Formulary/Index')
                    ->has('formulas');
            });
    }

    #[Test]
    public function itDisplaysTheFormulaCreatePage(): void
    {
        AttributeOption::factory()->create([
            'name' => AttributeOptionName::DAILY_TASK_ROUTES,
        ]);

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $response = $this->actingAs($me->user)->get(route('maintenance.formulas.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Maintenance/Formulary/Create')
                    ->hasOption('dailyTaskRoutesOptions');
            });
    }

    #[Test]
    public function aUiniqueNameIsRequiredToCreateAFormula(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $this->actingAs($me->user)->post(route('maintenance.formulas.store'))
            ->assertInvalid(['name' => 'The name field is required.']);

        $this->actingAs($me->user)->post(route('maintenance.formulas.store', ['name' => 'foo', 'drug' => 'foo']));

        $this->actingAs($me->user)->post(route('maintenance.formulas.store', ['name' => 'foo']))
            ->assertInvalid(['name' => 'The name has already been taken.']);
    }

    #[Test]
    public function aPrescriptionDrugIsRequiredToCreateAFormula(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $this->actingAs($me->user)->post(route('maintenance.formulas.store'))
            ->assertInvalid(['drug' => 'The drug field is required.']);
    }

    #[Test]
    public function aNewFormulaIsSavedToStorage(): void
    {
        $concentrationUnitIsMgPerMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML
        )->attribute_option_id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $this->actingAs($me->user)->post(route('maintenance.formulas.store', [
            'name' => 'Mammal Metacam',
            'drug' => 'Metacam',
            'concentration' => '1.5',
            'concentration_unit_id' => $concentrationUnitIsMgPerMlId,
            'dosage' => null,
            'is_controlled_substance' => 1,
        ]))
            ->assertRedirect(route('maintenance.formulas.index'));

        $this->assertDatabaseHas('formulas', [
            'type' => FormulaType::PRESCRIPTION,
            'team_id' => $me->team->id,
            'name' => 'Mammal Metacam',
            'defaults' => json_encode([
                'auto_calculate_dose' => false,
                'concentration' => '1.5',
                'concentration_unit_id' => "$concentrationUnitIsMgPerMlId",
                'drug' => 'Metacam',
                'is_controlled_substance' => true,
                'start_on_prescription_date' => false,
            ]),
        ]);
    }

    #[Test]
    public function itDisplaysThePageToEditAFormula(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $formula = Formula::factory()->create(['team_id' => $me->team->id]);

        $response = $this->actingAs($me->user)->get(route('maintenance.formulas.edit', $formula))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Maintenance/Formulary/Edit')
                    ->has('formula')
                    ->where('formula.id', $formula->id)
            );
    }

    #[Test]
    public function aUiniqueNameIsRequiredToUpdateAFormula(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $formula = Formula::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('maintenance.formulas.update', $formula))
            ->assertInvalid(['name' => 'The name field is required.']);

        $this->actingAs($me->user)->post(route('maintenance.formulas.store'), ['name' => 'foo', 'drug' => 'foo']);

        $this->actingAs($me->user)->put(route('maintenance.formulas.update', $formula), ['name' => 'foo'])
            ->assertInvalid(['name' => 'The name has already been taken.']);
    }

    #[Test]
    public function aPrescriptionDrugIsRequiredToUpdateAFormula(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $formula = Formula::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('maintenance.formulas.update', $formula))
            ->assertInvalid(['drug' => 'The drug field is required.']);
    }

    #[Test]
    public function itValidatesOwnershipOfAFormulaBeforeUpdating(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $formula = Formula::factory()->create();

        $this->actingAs($me->user)->put(route('maintenance.formulas.update', $formula), [
            'name' => 'Mammal Metacam',
            'drug' => 'Metacam',
        ])->assertOwnershipValidationError();
    }

    #[Test]
    public function aFormulaIsUpdatedToStorage(): void
    {
        $concentrationUnitIsMgPerMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML
        )->attribute_option_id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $formula = Formula::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('maintenance.formulas.update', $formula), [
            'name' => 'Mammal Metacam',
            'drug' => 'Metacam',
            'concentration' => '1.5',
            'concentration_unit_id' => $concentrationUnitIsMgPerMlId,
            'dosage' => null,
            'is_controlled_substance' => 1,
        ])
            ->assertRedirect(route('maintenance.formulas.index'));

        $this->assertDatabaseHas('formulas', [
            'id' => $formula->id,
            'team_id' => $me->team->id,
            'name' => 'Mammal Metacam',
            'defaults' => json_encode([
                'auto_calculate_dose' => false,
                'concentration' => '1.5',
                'concentration_unit_id' => $concentrationUnitIsMgPerMlId,
                'drug' => 'Metacam',
                'is_controlled_substance' => true,
                'start_on_prescription_date' => false,
            ]),
        ]);
    }

    #[Test]
    public function itValidatesOwnershipOfAFormulaBeforeDeleting(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $formula = Formula::factory()->create();

        $this->actingAs($me->user)->delete(route('maintenance.formulas.destroy', $formula))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function aFormulaCanBeDeleted(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $formula = Formula::factory()->create(['team_id' => $me->team->id, 'name' => 'Mammal Metacam']);

        $this->actingAs($me->user)->delete(route('maintenance.formulas.destroy', $formula))
            ->assertRedirect(route('maintenance.formulas.index'));

        $this->assertDatabaseMissing('formulas', [
            'id' => $formula->id,
            'team_id' => $me->team->id,
        ]);
    }
}
