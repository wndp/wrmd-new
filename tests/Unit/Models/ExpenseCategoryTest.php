<?php

namespace Tests\Unit\Models;

use App\Collections\ExpenseTransactionCollection;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTransaction;
use App\Models\Team;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class ExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;
    use Assertions;

    #[Test]
    public function anExpenseCategoryBelongsToATeam(): void
    {
        $category = ExpenseCategory::factory()->make([
            'team_id' => Team::Factory()
        ]);

        $this->assertInstanceOf(Team::class, $category->team);
    }

    #[Test]
    public function anExpenseCategoryHasManyTransactions(): void
    {
        $category = ExpenseCategory::factory()->has(ExpenseTransaction::factory()->count(3))->create();

        $this->assertInstanceOf(ExpenseTransactionCollection::class, $category->expenseTransactions);
        $this->assertInstanceOf(ExpenseTransaction::class, $category->expenseTransactions->first());
    }

    #[Test]
    public function anExpenseCategoryMayHaveChildCategories(): void
    {
        $parent = ExpenseCategory::factory()->create();
        $this->assertTrue($parent->children->isEmpty());

        $child = ExpenseCategory::factory()->create(['parent_id' => $parent->id]);
        $this->assertFalse($parent->fresh()->children->isEmpty());
        $this->assertTrue($parent->fresh()->children->first()->is($child));
    }

    #[Test]
    public function anExpenseCategoryMayHaveAParentCategory(): void
    {
        $parent = ExpenseCategory::factory()->create(['name' => 'foo']);
        $child = ExpenseCategory::factory()->create(['parent_id' => $parent->id]);

        $this->assertTrue($child->fresh()->parent->is($parent));
    }

    #[Test]
    public function anExpenseCategoryIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            ExpenseCategory::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function aParentExpenseCategoryCanBeFoundByItsNameByAnyTeam(): void
    {
        $anyTeam = Team::factory()->create();
        $category = ExpenseCategory::factory()->create(['team_id' => null, 'name' => 'Prescription']);
        $this->assertTrue(ExpenseCategory::findByName('Prescription', $anyTeam)->is($category));
    }

    #[Test]
    public function aChildExpenseCategoryCanOnlyBeFoundByItsNameByTheAccountThatCreatedIt(): void
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

    #[Test]
    public function aChildExpenseCategoryNameThatExistsTwiceIsFoundByTheAccountThatCreatedIt(): void
    {
        $myTeam = Team::factory()->create();
        $wrongTeam = Team::factory()->create();

        $parent = ExpenseCategory::factory()->create(['team_id' => null]);
        $child = ExpenseCategory::factory()->create(['team_id' => $myTeam->id, 'parent_id' => $parent->id, 'name' => 'Clavamox']);
        $wrongChild = ExpenseCategory::factory()->create(['team_id' => $wrongTeam->id, 'parent_id' => $parent->id, 'name' => 'Clavamox']);

        $this->assertTrue(ExpenseCategory::findByName('Clavamox', $myTeam)->is($child));
        $this->assertTrue(ExpenseCategory::findByName('Clavamox', $wrongTeam)->is($wrongChild));
    }

    #[Test]
    public function aCategoryKnowsIfItIsAParentCategory(): void
    {
        $team = Team::factory()->create();
        $parent = ExpenseCategory::factory()->create(['team_id' => null]);
        $child = ExpenseCategory::factory()->create(['team_id' => $team->id, 'parent_id' => $parent->id]);

        $this->assertTrue($parent->isParent());
        $this->assertFalse($child->isParent());
    }
}
