<?php

namespace Tests\Feature\Maintenance;

use App\Enums\Ability;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class ExpenseCategoriesControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessExpenseCategories(): void
    {
        $this->get(route('maintenance.expense_categories.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessExpenseCategories(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('maintenance.expense_categories.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheExpenseCategoriesIndexPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)->get(route('maintenance.expense_categories.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Maintenance/ExpenseCategories/Index')
                    ->has('categories');
            });
    }

    #[Test]
    public function itDisplaysTheExpenseCategoryCreatePage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $this->actingAs($me->user)->get(route('maintenance.expense_categories.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Maintenance/ExpenseCategories/Create');
            });
    }

    // #[Test]
    // public function categoriesCanOnlyBeCreatedFromParentCategories()
    // {
    //     $me = $this->createTeamUser();
    //     BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

    //     $parent = Category::factory()->create();
    //     $category = Category::factory()->create(['team_id' => $me->team->id, 'parent_id' => $parent->id]);

    //     $this->actingAs($me->user)->get(route('maintenance.expense_categories.create', $category))
    //         ->assertStatus(404);
    // }

    #[Test]
    public function aUiniqueNameIsRequiredToCreateAnExpenseCategory(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);

        $this->actingAs($me->user)->post(route('maintenance.expense_categories.store'))
            ->assertInvalid(['name' => 'The name field is required.'])
            ->assertInvalid(['parent_category' => 'The parent category field is required.']);

        $this->actingAs($me->user)->post(route('maintenance.expense_categories.store'), ['parent_category' => 'foo'])
            ->assertInvalid(['parent_category' => 'The selected parent category is invalid.']);

        $this->actingAs($me->user)->post(route('maintenance.expense_categories.store'), ['name' => 'foo', 'parent_category' => $parentCategory->name]);

        $this->actingAs($me->user)->post(route('maintenance.expense_categories.store'), ['name' => 'foo', 'parent_category' => $parentCategory->name])
            ->assertInvalid(['name' => 'The name has already been taken.']);
    }

    #[Test]
    public function aNewExpenseCategoryIsSavedToStorage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);

        $this->actingAs($me->user)->post(route('maintenance.expense_categories.store'), [
            'parent_category' => $parentCategory->name,
            'name' => 'Foo Bar',
            'description' => 'lorem ipsum',
        ])
            ->assertRedirect(route('maintenance.expense_categories.index'));

        $this->assertDatabaseHas('expense_categories', [
            'parent_id' => $parentCategory->id,
            'team_id' => $me->team->id,
            'name' => 'Foo Bar',
            'description' => 'lorem ipsum',
        ]);
    }

    #[Test]
    public function parentCategoriesCanNotBeEdited()
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $category = ExpenseCategory::factory()->create();

        $this->actingAs($me->user)->get(route('maintenance.expense_categories.edit', $category))
            ->assertStatus(404);
    }

    #[Test]
    public function itDisplaysThePageToEditAnExpenseCategory(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);
        $category = ExpenseCategory::factory()->create(['team_id' => $me->team->id, 'parent_id' => $parentCategory->id]);

        $this->actingAs($me->user)->get(route('maintenance.expense_categories.edit', $category))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Maintenance/ExpenseCategories/Edit')
                    ->has('category')
                    ->where('category.id', $category->id)
            );
    }

    #[Test]
    public function aUiniqueNameIsRequiredToUpdateACategory(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);
        $category = ExpenseCategory::factory()->create(['team_id' => $me->team->id, 'parent_id' => $parentCategory->id]);

        $this->actingAs($me->user)->put(route('maintenance.expense_categories.update', $category))
            ->assertInvalid(['name' => 'The name field is required.'])
            ->assertInvalid(['parent_category' => 'The parent category field is required.']);

        $this->actingAs($me->user)->put(route('maintenance.expense_categories.update', $category), ['parent_category' => 'foo'])
            ->assertInvalid(['parent_category' => 'The selected parent category is invalid.']);

        $this->actingAs($me->user)->post(route('maintenance.expense_categories.store'), ['name' => 'foo', 'parent_category' => $parentCategory->name]);

        $this->actingAs($me->user)->put(route('maintenance.expense_categories.update', $category), ['name' => 'foo', 'parent_category' => $parentCategory->name])
            ->assertInvalid(['name' => 'The name has already been taken.']);
    }

    #[Test]
    public function itValidatesOwnershipOfAnExpenseCategoryBeforeUpdating(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);
        $category = ExpenseCategory::factory()->create(['parent_id' => $parentCategory->id]);

        $this->actingAs($me->user)->put(route('maintenance.expense_categories.update', $category), [
            'name' => 'foo bar',
        ])->assertOwnershipValidationError();
    }

    #[Test]
    public function anExpenseCategoryIsUpdatedToStorage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);
        $category = ExpenseCategory::factory()->create(['team_id' => $me->team->id, 'parent_id' => $parentCategory->id]);

        $this->actingAs($me->user)->put(route('maintenance.expense_categories.update', $category), [
            'parent_category' => $parentCategory->name,
            'name' => 'Foo Bar',
            'description' => 'lorem ipsum',
        ])
            ->assertRedirect(route('maintenance.expense_categories.index'));

        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'parent_id' => $parentCategory->id,
            'name' => 'Foo Bar',
            'description' => 'lorem ipsum',
        ]);
    }

    #[Test]
    public function parentCategoriesCanNotBeDeleted()
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);

        $category = ExpenseCategory::factory()->create();

        $this->actingAs($me->user)->delete(route('maintenance.expense_categories.destroy', $category))
            ->assertStatus(404);
    }

    #[Test]
    public function aUsedChildCategoryCanNotBeDeleted()
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);
        $category = ExpenseCategory::factory()->create(['team_id' => $me->team->id, 'parent_id' => $parentCategory->id]);

        ExpenseTransaction::factory()->create(['expense_category_id' => $category->id]);

        $this->actingAs($me->user)->delete(route('maintenance.expense_categories.destroy', $category))
            ->assertRedirect('maintenance/expense-categories')
            ->assertHasNotificationMessage('Can not delete a category used by an expense transaction.');

        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'parent_id' => $parentCategory->id,
            'team_id' => $me->team->id,
        ]);
    }

    #[Test]
    public function itValidatesOwnershipOfAnExpenseCategoryBeforeDeleting(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);
        $category = ExpenseCategory::factory()->create(['parent_id' => $parentCategory->id]);

        $this->actingAs($me->user)->delete(route('maintenance.expense_categories.destroy', $category))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function anExpenseCategoryCanBeDeleted(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXPENSES->value);
        $parentCategory = ExpenseCategory::factory()->create(['team_id' => null, 'parent_id' => null]);
        $category = ExpenseCategory::factory()->create(['team_id' => $me->team->id, 'parent_id' => $parentCategory->id]);

        $this->actingAs($me->user)->delete(route('maintenance.expense_categories.destroy', $category))
            ->assertRedirect(route('maintenance.expense_categories.index'));

        $this->assertDatabaseMissing('expense_categories', [
            'id' => $category->id,
            'team_id' => $me->team->id,
        ]);
    }
}
