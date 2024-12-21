<?php

namespace App\Http\Controllers\Maintenance;

use App\Actions\CustomFieldBuilder;
use App\Enums\AttributeOptionName;
use App\Events\AccountUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomFieldRequest;
use App\Http\Requests\UpdateCustomFieldRequest;
use App\Models\AttributeOption;
use App\Models\CustomField;
use App\Repositories\CustomFieldsRepository;
use App\Repositories\OptionsStore;
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
    public function index()
    {
        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::CUSTOM_FIELD_TYPES->value,
                AttributeOptionName::CUSTOM_FIELD_GROUPS->value,
                AttributeOptionName::CUSTOM_FIELD_PATIENT_PANELS->value,
                AttributeOptionName::CUSTOM_FIELD_LOCATIONS->value,
            ])
        ]);

        $fields = CustomFieldsRepository::getCustomFields(Auth::user()->current_team_id)
            ->groupBy('group')
            ->values();

        $numAllowedFields = CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS;

        return Inertia::render('Maintenance/CustomFields/Index', compact('fields', 'numAllowedFields'));
    }

    /**
     * Display the view to create an expense category.
     */
    public function create()
    {
        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::CUSTOM_FIELD_TYPES->value,
                AttributeOptionName::CUSTOM_FIELD_GROUPS->value,
                AttributeOptionName::CUSTOM_FIELD_PATIENT_PANELS->value,
                AttributeOptionName::CUSTOM_FIELD_LOCATIONS->value,
            ])
        ]);

        $fieldsCount = CustomFieldsRepository::getCustomFields(Auth::user()->current_team_id)->count();

        if ($fieldsCount === CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS) {
            return redirect()->route('maintenance.custom_fields.index')
                ->with('notification.heading', __('Oops!'))
                ->with('notification.text', __('You already have :numAllowedFields custom fields created!', ['numAllowedFields' => CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS]))
                ->with('notification.style', 'danger');
        }

        return Inertia::render('Maintenance/CustomFields/Create');
    }

    /**
     * Store a newly created expense category in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomFieldRequest $request)
    {
        CustomFieldsRepository::save(
            array_merge(
                $request->only(['label', 'group_id', 'type_id', 'panel_id', 'location_id', 'is_required']),
                ['options' => $this->sanatizedOptions($request->options)]
            ),
            Auth::user()->currentTeam
        );

        //event(new AccountUpdated(Auth::user()->currentTeam));

        return redirect()->route('maintenance.custom_fields.index');
    }

    /**
     * Display the page to edit a custom field.
     */
    public function edit(CustomField $customField)
    {
        $customField->validateOwnership(Auth::user()->current_team_id);

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::CUSTOM_FIELD_TYPES->value,
                AttributeOptionName::CUSTOM_FIELD_GROUPS->value,
                AttributeOptionName::CUSTOM_FIELD_PATIENT_PANELS->value,
                AttributeOptionName::CUSTOM_FIELD_LOCATIONS->value,
            ])
        ]);

        return Inertia::render('Maintenance/CustomFields/Edit', compact('customField'));
    }

    /**
     * Update a custom field in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomFieldRequest $request, CustomField $customField)
    {
        $customField->validateOwnership(Auth::user()->current_team_id);

        $customField->update(array_merge(
            $request->only(['label', 'panel_id', 'location_id', 'is_required']),
            ['options' => $this->sanatizedOptions($request->options)]
        ));

        //event(new AccountUpdated(Auth::user()->currentTeam));

        return redirect()->route('maintenance.custom_fields.index');
    }

    /**
     * Delete a custom field from storage.
     */
    public function destroy(CustomField $customField)
    {
        $customField->validateOwnership(Auth::user()->current_team_id);

        CustomFieldsRepository::flushCustomValues($customField);

        $customField->delete();

        //event(new AccountUpdated(Auth::user()->currentTeam));

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
