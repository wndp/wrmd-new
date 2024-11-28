<?php

namespace Tests\Unit\Collections;

use App\Collections\ExpenseTransactionCollection;
use App\Models\ExpenseTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ExpenseTransactionCollectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_expense_transaction_collection_can_calculate_its_total_debits()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
            ExpenseTransaction::factory()->make(['debit' => 13.50]),
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
        ]);

        $this->assertEquals(3850, $collection->totalDebits());
    }

    #[Test]
    public function an_expense_transaction_collection_can_format_the_total_debits()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
            ExpenseTransaction::factory()->make(['debit' => 13.50]),
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
        ]);

        $this->assertSame('38.50', $collection->totalDebits(true));
    }

    #[Test]
    public function an_expense_transaction_collection_can_calculate_its_total_credits()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
            ExpenseTransaction::factory()->make(['credit' => 13.50]),
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
        ]);

        $this->assertEquals(3850, $collection->totalCredits());
    }

    #[Test]
    public function an_expense_transaction_collection_can_format_the_total_credits()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
            ExpenseTransaction::factory()->make(['credit' => 13.50]),
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
        ]);

        $this->assertSame('38.50', $collection->totalCredits(true));
    }

    #[Test]
    public function an_expense_transaction_collection_can_calculate_its_cost_of_care()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
            ExpenseTransaction::factory()->make(['debit' => 13.50]),
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
        ]);

        $this->assertEquals(1350, $collection->costOfCare());
    }

    #[Test]
    public function an_expense_transaction_collection_can_format_the_cost_of_care()
    {
        $collection = new ExpenseTransactionCollection([
            ExpenseTransaction::factory()->make(['debit' => 25.00]),
            ExpenseTransaction::factory()->make(['debit' => 13.50]),
            ExpenseTransaction::factory()->make(['credit' => 25.00]),
        ]);

        $this->assertSame('13.50', $collection->costOfCare(true));
    }
}
