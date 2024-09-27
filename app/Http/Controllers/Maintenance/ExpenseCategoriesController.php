<?php

namespace App\Http\Controllers\Maintenance;

use App\Domain\Options;
use App\Domain\OptionsStore;
use App\Extensions\Expenses\Models\Category;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ExpenseCategoriesController extends Controller
{
    /**
     * Display a listing of expense categories.
     */
    public function index(Request $request)
    {
        ExtensionNavigation::emit('maintenance');

        $categories = Category::whereNull('parent_id')
            ->whereNull('account_id')
            ->with(['children' => function ($query) use ($request) {
                $query->where('account_id', Auth::user()->current_team_id)
                    ->when($request->get('search'), fn ($query, $search) => $query->search($search))
                    ->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return Inertia::render('Maintenance/ExpenseCategories/Index', compact('categories'));
    }

    /**
     * Display the view to create an expense category.
     */
    public function create()
    {
        ExtensionNavigation::emit('maintenance');

        $parentCategories = Category::whereNull('parent_id')->whereNull('account_id')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        OptionsStore::merge(['parentCategories' => Options::arrayToSelectable($parentCategories)]);

        return Inertia::render('Maintenance/ExpenseCategories/Create');
    }

    /**
     * Store a newly created expense category in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'parent_category' => ['required', Rule::exists('expense_categories', 'name')->whereNull('parent_id')->whereNull('account_id')],
            'name' => ['required', Rule::unique('expense_categories')->where(function ($query) {
                return $query->where('account_id', Auth::user()->current_team_id);
            })],
        ]);

        $childCategory = new Category($request->all('name', 'description'));
        $childCategory->parent_id = Category::where('name', $request->parent_category)->whereNull('parent_id')->whereNull('account_id')->first()->id;
        $childCategory->account_id = Auth::user()->current_team_id;
        $childCategory->save();

        return redirect()->route('maintenance.expense_categories.index');
    }

    /**
     * Display the page to edit an expense category.
     */
    public function edit(Category $category)
    {
        abort_if($category->isParent(), 404);

        $category->validateOwnership(Auth::user()->current_team_id)->loadCount('transactions');

        ExtensionNavigation::emit('maintenance');

        $parentCategories = Category::whereNull('parent_id')->whereNull('account_id')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        OptionsStore::merge(['parentCategories' => Options::arrayToSelectable($parentCategories)]);

        return Inertia::render('Maintenance/ExpenseCategories/Edit', compact('category'));
    }

    /**
     * Update an expense category in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        abort_if($category->isParent(), 404);

        $category->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'parent_category' => ['required', Rule::exists('expense_categories', 'name')->whereNull('parent_id')->whereNull('account_id')],
            'name' => ['required', Rule::unique('expense_categories')->where(function ($query) {
                return $query->where('account_id', Auth::user()->current_team_id);
            })->ignore($category->id)],
        ]);

        $category->parent_id = Category::where('name', $request->parent_category)->whereNull('parent_id')->whereNull('account_id')->first()->id;
        $category->update($request->all('name', 'description'));

        return redirect()->route('maintenance.expense_categories.index');
    }

    /**
     * Delete an expense category from storage.
     */
    public function destroy(Category $category)
    {
        abort_if($category->isParent(), 404);

        $category->validateOwnership(Auth::user()->current_team_id)->loadCount('transactions');

        if ($category->transactions_count === 0) {
            $category->delete();

            return redirect()->route('maintenance.expense_categories.index')
                ->with('flash.notificationHeading', __('We hope you meant that.'))
                ->with('flash.notification', __('The :categoryName category was deleted.', ['categoryName' => $category->name]));
        }

        return redirect()->route('maintenance.expense_categories.index')
            ->with('flash.notificationHeading', __('Oops!'))
            ->with('flash.notification', __('Can not delete a category used by an expense transaction.'))
            ->with('flash.style', 'danger');
    }
}
