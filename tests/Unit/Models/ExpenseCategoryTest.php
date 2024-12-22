<?php

namespace Tests\Unit\Models;

use App\Collections\ExpenseTransactionCollection;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTransaction;
use App\Models\Team;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class ExpenseCategoryTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_an_expense_category_belongs_to_a_team(): void
    {
        $category = ExpenseCategory::factory()->make([
            'team_id' => Team::Factory(),
        ]);

        $this->assertInstanceOf(Team::class, $category->team);
    }

    public function test_an_expense_category_has_many_transactions(): void
    {
        $category = ExpenseCategory::factory()->has(ExpenseTransaction::factory()->count(3))->create();

        $this->assertInstanceOf(ExpenseTransactionCollection::class, $category->expenseTransactions);
        $this->assertInstanceOf(ExpenseTransaction::class, $category->expenseTransactions->first());
    }

    public function test_an_expense_category_may_have_child_categories(): void
    {
        $parent = ExpenseCategory::factory()->create();
        $this->assertTrue($parent->children->isEmpty());

        $child = ExpenseCategory::factory()->create(['parent_id' => $parent->id]);
        $this->assertFalse($parent->fresh()->children->isEmpty());
        $this->assertTrue($parent->fresh()->children->first()->is($child));
    }

    public function test_an_expense_category_may_have_a_parent_category(): void
    {
        $parent = ExpenseCategory::factory()->create(['name' => 'foo']);
        $child = ExpenseCategory::factory()->create(['parent_id' => $parent->id]);

        $this->assertTrue($child->fresh()->parent->is($parent));
    }

    public function test_an_expense_category_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            ExpenseCategory::factory()->create(),
            'created'
        );
    }

    public function test_a_parent_expense_category_can_be_found_by_its_name_by_any_team(): void
    {
        $anyTeam = Team::factory()->create();
        $category = ExpenseCategory::factory()->create(['team_id' => null, 'name' => 'Prescription']);
        $this->assertTrue(ExpenseCategory::findByName('Prescription', $anyTeam)->is($category));
    }

    public function test_a_child_expense_category_can_only_be_found_by_its_name_by_the_account_that_created_it(): void
    {
        $team = Team::factory()->create();
        $someOtherAccount = Team::factory()->create();
        $parent = ExpenseCategory::factory()->create();
        $child = ExpenseCategory::factory()->create(['team_id' => $team->id, 'parent_id' => $parent->id, 'name' => 'Prescription']);

        try {
            ExpenseCategory::findByName('Prescription', $someOtherAccount);
            $this->fail('Expected ModelNotFoundException was not thrown.');
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }

        $this->assertTrue(ExpenseCategory::findByName('Prescription', $team)->is($child));
    }

    public function test_a_child_expense_category_name_that_exists_twice_is_found_by_the_account_that_created_it(): void
    {
        $myTeam = Team::factory()->create();
        $wrongTeam = Team::factory()->create();

        $parent = ExpenseCategory::factory()->create(['team_id' => null]);
        $child = ExpenseCategory::factory()->create(['team_id' => $myTeam->id, 'parent_id' => $parent->id, 'name' => 'Clavamox']);
        $wrongChild = ExpenseCategory::factory()->create(['team_id' => $wrongTeam->id, 'parent_id' => $parent->id, 'name' => 'Clavamox']);

        $this->assertTrue(ExpenseCategory::findByName('Clavamox', $myTeam)->is($child));
        $this->assertTrue(ExpenseCategory::findByName('Clavamox', $wrongTeam)->is($wrongChild));
    }

    public function test_a_category_knows_if_it_is_a_parent_category(): void
    {
        $team = Team::factory()->create();
        $parent = ExpenseCategory::factory()->create(['team_id' => null]);
        $child = ExpenseCategory::factory()->create(['team_id' => $team->id, 'parent_id' => $parent->id]);

        $this->assertTrue($parent->isParent());
        $this->assertFalse($child->isParent());
    }
}
