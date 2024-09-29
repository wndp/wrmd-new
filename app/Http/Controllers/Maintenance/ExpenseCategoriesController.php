<?php

namespace App\Http\Controllers\Maintenance;

use App\Extensions\Expenses\Models\Category;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Options\Options;
use App\Repositories\OptionsStore;
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
        $categories = ExpenseCategory::whereNull('parent_id')
            ->whereNull('team_id')
            ->with(['children' => function ($query) use ($request) {
                $query->where('team_id', Auth::user()->current_team_id)
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
        $parentCategories = ExpenseCategory::whereNull('parent_id')->whereNull('team_id')
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
            'parent_category' => ['required', Rule::exists('expense_categories', 'name')->whereNull('parent_id')->whereNull('team_id')],
            'name' => ['required', Rule::unique('expense_categories')->where(function ($query) {
                return $query->where('team_id', Auth::user()->current_team_id);
            })],
        ]);

        $childCategory = new ExpenseCategory($request->all('name', 'description'));
        $childCategory->parent_id = ExpenseCategory::where('name', $request->parent_category)->whereNull('parent_id')->whereNull('team_id')->first()->id;
        $childCategory->team_id = Auth::user()->current_team_id;
        $childCategory->save();

        return redirect()->route('maintenance.expense_categories.index');
    }

    /**
     * Display the page to edit an expense category.
     */
    public function edit(ExpenseCategory $category)
    {
        abort_if($category->isParent(), 404);

        $category->validateOwnership(Auth::user()->current_team_id)->loadCount('transactions');

        $parentCategories = ExpenseCategory::whereNull('parent_id')->whereNull('team_id')
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
    public function update(Request $request, ExpenseCategory $category)
    {
        abort_if($category->isParent(), 404);

        $category->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'parent_category' => ['required', Rule::exists('expense_categories', 'name')->whereNull('parent_id')->whereNull('team_id')],
            'name' => ['required', Rule::unique('expense_categories')->where(function ($query) {
                return $query->where('team_id', Auth::user()->current_team_id);
            })->ignore($category->id)],
        ]);

        $category->parent_id = ExpenseCategory::where('name', $request->parent_category)->whereNull('parent_id')->whereNull('team_id')->first()->id;
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
                ->with('notification.heading', __('We hope you meant that.'))
                ->with('notification.text', __('The :categoryName category was deleted.', ['categoryName' => $category->name]));
        }

        return redirect()->route('maintenance.expense_categories.index')
            ->with('notification.heading', __('Oops!'))
            ->with('notification.text', __('Can not delete a category used by an expense transaction.'))
            ->with('flash.style', 'danger');
    }
}
