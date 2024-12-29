<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTransaction;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class ExpensesControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_expenses(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.expenses.index', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_expenses(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->get(route('patients.expenses.index', $patient))->assertForbidden();
    }

    public function test_it_displays_the_expenses_index_view(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);

        $this->actingAs($me->user)->get(route('patients.expenses.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Expenses/Index')
                    ->hasAll(['expenseTransactions', 'expenseTotals'])
            );
    }

    public function test_un_authenticated_users_cant_store_an_expense(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.expenses.store', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_store_an_expense(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        $this->actingAs($me->user)->post(route('patients.expenses.store', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_storing_the_expense(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)
            ->post(route('patients.expenses.store', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_store_the_expenses(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2017-02-13']);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)
            ->post(route('patients.expenses.store', $admission->patient))
            ->assertInvalid([
                'transacted_at' => 'The transaction date field is required.',
                'category' => 'A category is required.',
                'charge' => 'A debit or credit is required.'
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.expenses.store', $admission->patient), [
                'transacted_at' => 'foo',
                'category' => 'xxxx',
                'debit' => 'foo',
            ])
            ->assertInvalid([
                'transacted_at' => 'The transaction date is not a valid date.',
                'category' => 'The selected category is invalid.',
                'debit' => 'The debit field must be a number.'
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.expenses.store', $admission->patient), [
                'credit' => 'foo',
            ])
            ->assertInvalid(['credit' => 'The credit field must be a number.']);
    }

    public function test_it_stores_an_expense(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2017-02-13']);
        $category = ExpenseCategory::factory()->create(['team_id' => $me->team->id, 'name' => 'Prescription']);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)
            ->post(route('patients.expenses.store', $admission->patient), [
                'transacted_at' => '2017-04-09',
                'category' => 'Prescription',
                'debit' => '15.50',
                'memo' => 'lorem ipsum',
            ])
            ->assertRedirect(route('patients.expenses.index', ['y' => now()->format('Y'), 'c' => 1]));

        $this->assertDatabaseHas('expense_transactions', [
            'patient_id' => $admission->patient_id,
            'expense_category_id' => $category->id,
            'transacted_at' => '2017-04-09',
            'debit' => 1550,
            'credit' => 0,
            'memo' => 'lorem ipsum',
        ]);
    }

    public function test_un_authenticated_users_cant_update_an_expense(): void
    {
        $patient = Patient::factory()->create();
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $patient->id]);
        $this->put(route('patients.expenses.update', [$patient, $transaction]))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_an_expense(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        $this->actingAs($me->user)->put(route('patients.expenses.update', [$patient, $transaction]))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_expense_before_updating_it(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)
            ->put(route('patients.expenses.update', [$patient, $transaction]))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_expense(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2017-02-13']);
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)
            ->put(route('patients.expenses.update', [$admission->patient, $transaction]))
            ->assertInvalid([
                'transacted_at' => 'The transaction date field is required.',
                'category' => 'A category is required.',
                'charge' => 'A debit or credit is required.'
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.expenses.update', [$admission->patient, $transaction]), [
                'transacted_at' => 'foo',
                'category' => 'xxxx',
                'debit' => 'foo',
            ])
            ->assertInvalid([
                'transacted_at' => 'The transaction date is not a valid date.',
                'category' => 'The selected category is invalid.',
                'debit' => 'The debit field must be a number.'
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.expenses.update', [$admission->patient, $transaction]), [
                'credit' => 'foo',
            ])
            ->assertInvalid(['credit' => 'The credit field must be a number.']);
    }

    public function test_it_updates_an_expense(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2017-02-13']);
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $admission->patient_id]);
        $category = ExpenseCategory::factory()->create(['team_id' => $me->team->id, 'name' => 'Prescription']);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)
            ->put(route('patients.expenses.update', [$admission->patient, $transaction]), [
                'transacted_at' => '2017-04-09',
                'category' => 'Prescription',
                'debit' => '15.50',
                'memo' => 'lorem ipsum',
            ])
            ->assertRedirect(route('patients.expenses.index', ['y' => now()->format('Y'), 'c' => 1]));

        $this->assertDatabaseHas('expense_transactions', [
            'id' => $transaction->id,
            'expense_category_id' => $category->id,
            'transacted_at' => '2017-04-09',
            'debit' => 1550,
            'credit' => 0,
            'memo' => 'lorem ipsum',
        ]);
    }

    public function test_un_authenticated_users_cant_delete_an_exam(): void
    {
        $patient = Patient::factory()->create();
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $patient->id]);
        $this->delete(route('patients.expenses.destroy', [$patient, $transaction]))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_delete_an_exam(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        $this->actingAs($me->user)->delete(route('patients.expenses.destroy', [$patient, $transaction]))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_expense_before_delete_it(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)
            ->delete(route('patients.expenses.destroy', [$patient, $transaction]))
            ->assertOwnershipValidationError();
    }

    public function test_it_deletes_an_expense(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXPENSES->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)
            ->delete(route('patients.expenses.destroy', [$admission->patient, $transaction]))
            ->assertRedirect(route('patients.expenses.index', ['y' => now()->format('Y'), 'c' => 1]));

        $this->assertSoftDeleted('expense_transactions', [
            'id' => $transaction->id,
            'patient_id' => $admission->patient_id,
        ]);
    }
}
