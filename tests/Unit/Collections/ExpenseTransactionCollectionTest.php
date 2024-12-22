<?php

namespace Tests\Unit\Collections;

use App\Collections\ExpenseTransactionCollection;
use App\Models\ExpenseTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTransactionCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_expense_transaction_collection_can_calculate_its_total_debits()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
            ExpenseTransaction::factory()->make(['debit' => 13.50]),
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
        ]);

        $this->assertEquals(3850, $collection->totalDebits());
    }

    public function test_an_expense_transaction_collection_can_format_the_total_debits()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
            ExpenseTransaction::factory()->make(['debit' => 13.50]),
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
        ]);

        $this->assertSame('38.50', $collection->totalDebits(true));
    }

    public function test_an_expense_transaction_collection_can_calculate_its_total_credits()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
            ExpenseTransaction::factory()->make(['credit' => 13.50]),
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
        ]);

        $this->assertEquals(3850, $collection->totalCredits());
    }

    public function test_an_expense_transaction_collection_can_format_the_total_credits()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
            ExpenseTransaction::factory()->make(['credit' => 13.50]),
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
        ]);

        $this->assertSame('38.50', $collection->totalCredits(true));
    }

    public function test_an_expense_transaction_collection_can_calculate_its_cost_of_care()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
            ExpenseTransaction::factory()->make(['debit' => 13.50]),
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
        ]);

        $this->assertEquals(1350, $collection->costOfCare());
    }

    public function test_an_expense_transaction_collection_can_format_the_cost_of_care()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
            ExpenseTransaction::factory()->make(['debit' => 13.50]),
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
        ]);

        $this->assertSame('13.50', $collection->costOfCare(true));
    }
}
