<?php

namespace App\Http\Controllers\Maintenance;

use App\Enums\AttributeOptionName;
use App\Enums\FormulaType;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Models\Formula;
use App\Repositories\OptionsStore;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class NutritionCookbookController extends Controller
{
    public function index(Request $request): Response
    {
        $recipes = Formula::where('team_id', Auth::user()->current_team_id)
            ->where('type', FormulaType::NUTRITION->value)
            ->when($request->get('search'), fn ($query, $search) => $query->search($search))
            ->paginate();

        return Inertia::render('Maintenance/Cookbook/Index', compact('recipes'));
    }

    public function create(): Response
    {
        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES->value,
                AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES->value,
                AttributeOptionName::DAILY_TASK_NUTRITION_INGREDIENT_UNITS->value,
            ]),
        ]);

        return Inertia::render('Maintenance/Cookbook/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', Rule::unique('formulas')->where(function ($query) {
                return $query->where([
                    'team_id' => Auth::user()->current_team_id,
                    'type' => FormulaType::NUTRITION->value,
                ]);
            })],
            'description' => 'required_without:ingredients|nullable|string',
            'ingredients' => 'required_without:description|nullable|array',
            'ingredients.*.quantity' => 'required|numeric',
            'ingredients.*.unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_NUTRITION_INGREDIENT_UNITS),
            ],
            'ingredients.*.ingredient' => 'required|string'
        ]);

        $formula = new Formula([
            'team_id' => Auth::user()->current_team_id,
            'name' => $request->input('name'),
            'type' => FormulaType::NUTRITION->value,
            'defaults' => $this->filterDefaults($request),
        ]);
        $formula->save();

        return redirect()->route('maintenance.cookbook.index');
    }

    public function edit(Formula $formula): Response
    {
        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES->value,
                AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES->value,
                AttributeOptionName::DAILY_TASK_NUTRITION_INGREDIENT_UNITS->value,
            ]),
        ]);

        $formula->validateOwnership(Auth::user()->current_team_id);

        return Inertia::render('Maintenance/Cookbook/Edit', compact('formula'));
    }

    public function update(Request $request, Formula $formula)
    {
        $formula->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'name' => ['required', Rule::unique('formulas')->where(function ($query) {
                return $query->where([
                    'team_id' => Auth::user()->current_team_id,
                    'type' => FormulaType::NUTRITION->value,
                ]);
            })->ignore($formula->id, 'id')],
            'description' => 'required_without:ingredients|nullable|string',
            'ingredients' => 'required_without:description|nullable|array',
            'ingredients.*.quantity' => 'required|numeric',
            'ingredients.*.unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_NUTRITION_INGREDIENT_UNITS),
            ],
            'ingredients.*.ingredient' => 'required|string'
        ]);

        $formula->update([
            'name' => $request->input('name'),
            'defaults' => $this->filterDefaults($request),
        ]);

        return redirect()->route('maintenance.cookbook.index');
    }

    public function destroy(Formula $formula): RedirectResponse
    {
        $formula->validateOwnership(Auth::user()->current_team_id)->delete();

        return redirect()->route('maintenance.cookbook.index')
            ->with('notification.heading', __('We hope you meant that.'))
            ->with('notification.text', __('The :formulaName formula deleted.', ['formulaName' => $formula->name]));
    }

    protected function filterDefaults($request)
    {
        $defaults = array_filter($request->only([
            'duration',
            'frequency',
            'frequency_unit_id',
            'route_id',
            'description',
            'ingredients',
        ]));

        $defaults['start_on_plan_date'] = $request->boolean('start_on_plan_date');

        return $defaults;
    }
}
