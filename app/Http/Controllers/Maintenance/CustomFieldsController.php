<?php

namespace App\Http\Controllers\Maintenance;

use App\Domain\Options;
use App\Domain\OptionsStore;
use App\Events\AccountUpdated;
use App\Extensions\CustomFields\CustomField;
use App\Extensions\CustomFields\CustomFieldBuilder;
use App\Extensions\CustomFields\CustomFieldOptions;
use App\Extensions\CustomFields\CustomFieldsRepository;
use App\Extensions\Expenses\Models\Category;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CustomFieldsController extends Controller
{
    /**
     * Display a listing of expense categories.
     */
    public function index(CustomFieldOptions $options)
    {
        ExtensionNavigation::emit('maintenance');

        OptionsStore::merge($options);

        $fields = CustomFieldsRepository::getCustomFields(Auth::user()->current_account_id)
            ->groupBy('group')
            ->values();

        $numAllowedFields = CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS;

        return Inertia::render('Maintenance/CustomFields/Index', compact('fields', 'numAllowedFields'));
    }

    /**
     * Display the view to create an expense category.
     */
    public function create(CustomFieldOptions $options)
    {
        ExtensionNavigation::emit('maintenance');

        OptionsStore::merge($options);

        $fieldsCount = CustomFieldsRepository::getCustomFields(Auth::user()->current_account_id)->count();

        if ($fieldsCount === CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS) {
            return redirect()->route('maintenance.custom_fields.index')
                ->with('flash.notificationHeading', __('Oops!'))
                ->with('flash.notification', __('You already have :numAllowedFields custom fields created!', ['numAllowedFields' => CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS]))
                ->with('flash.style', 'danger');
        }

        return Inertia::render('Maintenance/CustomFields/Create');
    }

    /**
     * Store a newly created expense category in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'group' => ['required', Rule::in(CustomFieldOptions::$groups)],
            'location' => ['required', Rule::in(CustomFieldOptions::$locations)],
            'panel' => ['required_if:group,Patient', 'nullable', Rule::in(CustomFieldOptions::$patientPanels)],
            'type' => ['required', Rule::in(array_keys(CustomFieldOptions::$fieldTypes))],
            'is_required' => ['required', 'boolean'],
            'options' => Rule::requiredIf(fn () => in_array($request->type, ['select', 'checkbox-group'])),
        ]);

        CustomFieldsRepository::save(
            array_merge(
                $request->all(['group', 'label', 'type', 'panel', 'location', 'is_required']),
                ['options' => $this->sanatizedOptions($request->options)]
            ),
            Auth::user()->currentAccount
        );

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('maintenance.custom_fields.index');
    }

    /**
     * Display the page to edit a custom field.
     */
    public function edit(CustomFieldOptions $options, CustomField $customField)
    {
        $customField->validateOwnership(Auth::user()->current_account_id);

        ExtensionNavigation::emit('maintenance');

        OptionsStore::merge($options);

        return Inertia::render('Maintenance/CustomFields/Edit', compact('customField'));
    }

    /**
     * Update a custom field in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomField $customField)
    {
        $customField->validateOwnership(Auth::user()->current_account_id);

        $request->validate([
            'label' => 'required',
            'location' => ['required', Rule::in(CustomFieldOptions::$locations)],
            'panel' => [
                Rule::requiredIf(fn () => $customField->group === 'Patient'),
                'nullable',
                Rule::in(CustomFieldOptions::$patientPanels),
            ],
            'is_required' => ['required', 'boolean'],
            'options' => Rule::requiredIf(fn () => in_array($customField->type, ['select', 'checkbox-group'])),
        ]);

        $customField->update(array_merge(
            $request->all(['label', 'panel', 'location', 'is_required']),
            ['options' => $this->sanatizedOptions($request->options)]
        ));

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('maintenance.custom_fields.index');
    }

    /**
     * Delete a custom field from storage.
     */
    public function destroy(CustomField $customField)
    {
        $customField->validateOwnership(Auth::user()->current_account_id);

        CustomFieldsRepository::flushCustomValues($customField);

        $customField->delete();

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('maintenance.custom_fields.index')
            ->with('flash.notificationHeading', __('We hope you meant that.'))
            ->with('flash.notification', __('The ":customfieldLabel" custom field was deleted.', ['customfieldLabel' => $customField->label]));
    }

    /**
     * Sanitize the request options.
     *
     * @param  string  $options
     * @return array
     */
    private function sanatizedOptions($options)
    {
        return Str::of($options)
            ->split('/[\n\,]+/')
            ->map(fn ($option) => trim($option, "\r"))
            ->map(fn ($option) => trim($option))
            ->filter()
            ->values()
            ->toArray();
    }
}
