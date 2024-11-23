<?php

namespace App\Http\Controllers\DailyTasks;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\DailyTaskSchedulable;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Options\Options;
use App\Repositories\OptionsStore;
use App\Support\DailyTasksCollection;
use App\Support\DailyTasksFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DailyTasksController extends Controller
{
    public function index(Request $request): Response
    {
        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PATIENT_LOCATION_FACILITIES->value,
                AttributeOptionName::EXAM_WEIGHT_UNITS->value,
                AttributeOptionName::DAILY_TASK_ASSIGNMENTS->value,
                AttributeOptionName::DAILY_TASK_FREQUENCIES->value,
                AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value,
                AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value,
                AttributeOptionName::DAILY_TASK_DOSE_UNITS->value,
                AttributeOptionName::DAILY_TASK_ROUTES->value,
                AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES->value,
            ]),
            'schedulableOptions' => Options::enumsToSelectable(DailyTaskSchedulable::cases())
        ]);

        [
            $singleDoseId,
            $veterinarianId,
            $mgPerMlId,
            $mgPerKgId,
            $mlId,
            $gramId,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE->value],
            [AttributeOptionName::DAILY_TASK_ASSIGNMENTS->value, AttributeOptionUiBehavior::DAILY_TASK_ASSIGNMENT_IS_VETERINARIAN->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML->value],
            [AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG->value],
            [AttributeOptionName::DAILY_TASK_DOSE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML->value],
            [AttributeOptionName::EXAM_WEIGHT_UNITS->value, AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_G->value],
        ]);

        Inertia::share([
            'dailyTaskOptionUiBehaviorIds' => compact(
                'singleDoseId',
                'veterinarianId',
                'mgPerMlId',
                'mgPerKgId',
                'mlId',
                'gramId'
            )
        ]);

        $filters = new DailyTasksFilters($request->validate([
            'date' => 'nullable|date',
            'facility' => 'nullable|string',
            'group_by' => 'nullable|string',
            'include_non_pending' => 'nullable',
            'include_non_possession' => 'nullable',
            'include' => 'nullable|array',
        ]));

        $taskGroups = DailyTasksCollection::make()
            ->withFilters($filters)
            ->forTeam(Auth::user()->currentTeam);

        return Inertia::render('DailyTasks/Index', [
            'filters' => $filters,
            'taskGroups' => $taskGroups,
        ]);
    }

    public function edit(Request $request): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        $filters = new DailyTasksFilters($request->validate([
            'date' => 'nullable|date',
        ]));

        return Inertia::render('Patients/DailyTasks', [
            'filters' => $filters,
            'tasks' => DailyTasksCollection::make()
                ->withFilters($filters)
                ->forPatient($admission->patient, Auth::user()->currentTeam),
        ]);
    }
}
