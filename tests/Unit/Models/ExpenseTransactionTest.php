<?php

namespace Tests\Unit\Models;

use App\Collections\ExpenseTransactionCollection;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTransaction;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class ExpenseTransactionTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_transaction_belongs_to_a_patient(): void
    {
        $transaction = ExpenseTransaction::factory()->make();
        $this->assertInstanceOf(Patient::class, $transaction->patient);
    }

    public function test_a_transaction_may_belong_to_an_expense_category(): void
    {
        $transaction = ExpenseTransaction::factory()->make(['expense_category_id' => null]);
        $this->assertNull($transaction->expenseCategory);

        $transaction = ExpenseTransaction::factory()->make(['expense_category_id' => ExpenseCategory::factory()]);
        $this->assertInstanceOf(ExpenseCategory::class, $transaction->expenseCategory);
    }

    public function test_a_transaction_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            ExpenseTransaction::factory()->create(),
            'created'
        );
    }

    public function test_an_expense_transaction_debit_is_stored_as_an_integer(): void
    {
        $transaction = ExpenseTransaction::factory()->make(['debit' => '25.50']);
        $this->assertSame(2550, $transaction->debit);
        $this->assertSame(0, $transaction->credit);
    }

    public function test_transactions_debits_less_than1_are_stored_as_an_integer(): void
    {
        $transaction = ExpenseTransaction::factory()->make(['debit' => '.50']);
        $this->assertSame(50, $transaction->debit);
        $this->assertSame(0, $transaction->credit);
    }

    public function test_if_a_debits_value_is0_then_it_is_not_set_in_the_mutator(): void
    {
        $transaction = ExpenseTransaction::factory()->create(['debit' => '0']);
        $this->assertNull($transaction->debit);
        $this->assertNull($transaction->credit);
    }

    public function test_an_expense_transaction_has_an_appended_debit_for_humans_attribute(): void
    {
        $transaction = ExpenseTransaction::factory()->make(['debit' => '25.50']);
        $this->assertSame(2550, $transaction->debit);
        $this->assertSame('25.50', $transaction->debit_for_humans);
    }

    public function test_an_expense_transaction_credit_is_stored_as_an_integer(): void
    {
        $transaction = ExpenseTransaction::factory()->make(['credit' => '25.50']);
        $this->assertSame(2550, $transaction->credit);
        $this->assertSame(0, $transaction->debit);
    }

    public function test_transactions_creditss_less_than1_are_stored_as_an_integer(): void
    {
        $transaction = ExpenseTransaction::factory()->make(['credit' => '.50']);
        $this->assertSame(0, $transaction->debit);
        $this->assertSame(50, $transaction->credit);
    }

    public function test_if_a_credits_value_is0_then_it_is_not_set_in_the_mutator(): void
    {
        $transaction = ExpenseTransaction::factory()->create(['credit' => '0']);
        $this->assertNull($transaction->credit);
        $this->assertNull($transaction->debit);
    }

    public function test_an_expense_transaction_has_an_appended_credit_for_humans_attribute(): void
    {
        $transaction = ExpenseTransaction::factory()->make(['credit' => '25.50']);
        $this->assertSame(2550, $transaction->credit);
        $this->assertSame('25.50', $transaction->credit_for_humans);
    }

    public function test_an_expense_transaction_has_an_appended_charge_formatted_attribute(): void
    {
        $transaction = ExpenseTransaction::factory()->make(['debit' => '15.50', 'credit' => null]);
        $this->assertSame('($15.50)', $transaction->charge_for_humans);

        $transaction = ExpenseTransaction::factory()->make(['debit' => null, 'credit' => '25.50']);
        $this->assertSame('$25.50', $transaction->charge_for_humans);
    }

    // #[Test]
    // public function anExpenseTransactionHasAnAppendedTransactionForHumansAttribute(): void
    // {
    //     $me = $this->createTeamUser();
    //     Auth::loginUsingId($me->user->id);

    //     $category = Category::factory()->create([
    //         'name' => 'Prescriptions',
    //     ]);

    //     $transaction = ExpenseTransaction::factory()->create([
    //         'category_id' => $category->id,
    //         'transacted_at' => '2018-02-05',
    //         'memo' => 'lorem ipsum',
    //         'debit' => '12.50',
    //     ]);

    //     $this->assertSame('Feb 5, 2018 <br> Debit: 12.50 <br> Category: Prescriptions <br> <i>lorem ipsum</i>', $transaction->transaction_for_humans);
    // }

    public function test_a_list_of_transaction_returns_a_custom_collection(): void
    {
        $transactions = ExpenseTransaction::factory()->count(2)->create();
        $this->assertInstanceOf(ExpenseTransactionCollection::class, $transactions);
    }

    public function test_if_an_expense_transaction_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $patient->id, 'memo' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $transaction->patient->refresh();

        // Cant update
        $transaction->update(['memo' => 'NEW']);
        $this->assertEquals('OLD', $transaction->fresh()->memo);

        // Cant save
        $transaction->memo = 'NEW';
        $transaction->save();
        $this->assertEquals('OLD', $transaction->fresh()->memo);
    }

    public function test_if_an_expense_transaction_patient_is_locked_then_it_can_not_be_created(): void
    {
        $transaction = ExpenseTransaction::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($transaction->exists);
    }

    public function test_if_an_expense_transaction_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $transaction = ExpenseTransaction::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $transaction->patient->refresh();

        $transaction->delete();
        $this->assertDatabaseHas('expense_transactions', ['id' => $transaction->id, 'deleted_at' => null]);
    }
}
