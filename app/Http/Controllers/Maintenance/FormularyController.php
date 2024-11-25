<?php

namespace App\Http\Controllers\Maintenance;

use App\Enums\AttributeOptionName;
use App\Enums\FormulaType;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Models\Formula;
use App\Repositories\OptionsStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FormularyController extends Controller
{
    /**
     * Display a listing of formulas.
     */
    public function index(Request $request): Response
    {
        $formulas = Formula::where('team_id', Auth::user()->current_team_id)
            ->where('type', FormulaType::PRESCRIPTION->value)
            ->when($request->get('search'), fn ($query, $search) => $query->search($search))
            ->paginate();

        return Inertia::render('Maintenance/Formulary/Index', compact('formulas'));
    }

    /**
     * Display the view to create a formula.
     */
    public function create(): Response
    {
        //ExtensionNavigation::emit('maintenance');

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::DAILY_TASK_FREQUENCIES->value,
                AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value,
                AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value,
                AttributeOptionName::DAILY_TASK_DOSE_UNITS->value,
                AttributeOptionName::DAILY_TASK_ROUTES->value,
            ]),
        ]);

        return Inertia::render('Maintenance/Formulary/Create');
    }

    /**
     * Store a newly created prescription in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', Rule::unique('formulas')->where(function ($query) {
                return $query->where([
                    'team_id' => Auth::user()->current_team_id,
                    'type' => FormulaType::PRESCRIPTION->value,
                ]);
            })],
            'drug' => 'required',
        ]);

        $formula = new Formula([
            'team_id' => Auth::user()->current_team_id,
            'name' => $request->input('name'),
            'type' => FormulaType::PRESCRIPTION->value,
            'defaults' => $this->filterDefaults($request),
        ]);
        $formula->save();

        return redirect()->route('maintenance.formulas.index');
    }

    /**
     * Display the page to edit a formula.
     */
    public function edit(Formula $formula): Response
    {
        //ExtensionNavigation::emit('maintenance');

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::DAILY_TASK_FREQUENCIES->value,
                AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value,
                AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value,
                AttributeOptionName::DAILY_TASK_DOSE_UNITS->value,
                AttributeOptionName::DAILY_TASK_ROUTES->value,
            ]),
        ]);

        $formula->validateOwnership(Auth::user()->current_team_id);

        return Inertia::render('Maintenance/Formulary/Edit', compact('formula'));
    }

    /**
     * Update a formula in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Formula $formula)
    {
        $formula->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'name' => ['required', Rule::unique('formulas')->where(function ($query) {
                return $query->where([
                    'team_id' => Auth::user()->current_team_id,
                    'type' => FormulaType::PRESCRIPTION->value,
                ]);
            })->ignore($formula->id, 'id')],
            'drug' => 'required',
        ]);

        $formula->update([
            'name' => $request->input('name'),
            'defaults' => $this->filterDefaults($request),
        ]);

        return redirect()->route('maintenance.formulas.index');
    }

    /**
     * Delete a formula from storage.
     */
    public function destroy(Formula $formula): RedirectResponse
    {
        $formula->validateOwnership(Auth::user()->current_team_id)->delete();

        return redirect()->route('maintenance.formulas.index')
            ->with('notification.heading', __('We hope you meant that.'))
            ->with('notification.text', __('The :formulaName formula deleted.', ['formulaName' => $formula->name]));
    }

    /**
     * Filter the request for the requested default values.
     *
     * @return array
     */
    public function filterDefaults($request)
    {
        $defaults = array_filter($request->only([
            'drug',
            'concentration',
            'concentration_unit_id',
            'route_id',
            'dosage',
            'dosage_unit_id',
            'frequency_id',
            'dose',
            'dose_unit_id',
            'duration',
            'loading_dose',
            'loading_dose_unit_id',
            'instructions',
        ]));

        $defaults['auto_calculate_dose'] = $request->boolean('auto_calculate_dose');
        $defaults['start_on_prescription_date'] = $request->boolean('start_on_prescription_date');
        $defaults['is_controlled_substance'] = $request->boolean('is_controlled_substance');

        return $defaults;
    }
}
