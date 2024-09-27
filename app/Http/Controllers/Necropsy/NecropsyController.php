<?php

namespace App\Http\Controllers\Necropsy;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\Extension;
use App\Enums\NecropsyBodyPart;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Models\Necropsy;
use App\Models\Patient;
use App\Options\Options;
use App\Repositories\OptionsStore;
use App\Rules\AttributeOptionExistsRule;
use App\Support\ExtensionManager;
use App\Support\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class NecropsyController extends Controller
{
    public function edit()
    {
        abort_unless(
            ExtensionManager::isActivated(Extension::NECROPSY),
            Response::HTTP_FORBIDDEN
        );

        $admission = $this->loadAdmissionAndSharePagination();

        if (! ($teamIsInPossession = $admission->patient->team_possession_id === Auth::user()->current_team_id)) {
            $admission->load('patient.possession');
        }

        $patientTaxaClassAgeUnits = match ('Aves') {
            'Mammalia' => AttributeOptionName::EXAM_MAMMALIA_AGE_UNITS->value,
            'Amphibia' => AttributeOptionName::EXAM_AMPHIBIA_AGE_UNITS->value,
            'Reptilia' => AttributeOptionName::EXAM_REPTILIA_AGE_UNITS->value,
            'Aves' => AttributeOptionName::EXAM_AVES_AGE_UNITS->value,
            default => null,
        };

        OptionsStore::add([
            'bodyPartOptions' => Options::enumsToSelectable(NecropsyBodyPart::cases()),
            'taxaClassAgeUnits' => Options::arrayToSelectable(AttributeOption::getDropdownOptions([
                $patientTaxaClassAgeUnits
            ])->first()),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::EXAM_WEIGHT_UNITS->value,
                AttributeOptionName::EXAM_CHRONOLOGICAL_AGE_UNITS,
                AttributeOptionName::EXAM_SEXES->value,
                AttributeOptionName::EXAM_BODY_CONDITIONS->value,
                AttributeOptionName::NECROPSY_CARCASS_CONDITIONS->value,
                AttributeOptionName::NECROPSY_SAMPLES->value,
                AttributeOptionName::EXAM_BODY_PART_FINDINGS->value,
            ])
        ]);

        [$necropsySampleOtherId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::NECROPSY_SAMPLES->value, AttributeOptionUiBehavior::NECROPSY_SAMPLES_IS_OTHER->value
        ]);

        return Inertia::render('Patients/Necropsy/Edit', [
            'patient' => $admission->patient->load('necropsy'),
            'necropsySampleOtherId' => $necropsySampleOtherId
        ]);
    }

    public function update(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'necropsied_at' => 'required|date',
            'prosector' => 'required|string',
            'carcass_condition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::NECROPSY_CARCASS_CONDITIONS),
            ],
            'is_photos_collected' => 'nullable|boolean',
            'is_carcass_radiographed' => 'nullable|boolean',
            'is_previously_frozen' => 'nullable|boolean',
            'is_scavenged' => 'nullable|boolean',
            'is_discarded' => 'nullable|boolean',
        ], [
            'necropsied_at.required' => 'The necropsy date field is required.',
            'necropsied_at.date' => 'The necropsy date is not a valid date.',
        ]);

        $necropsiedAt = Timezone::convertFromLocalToUtc($request->input('necropsied_at'));

        $necropsy = Necropsy::firstOrNew(['patient_id' => $patient->id]);
        $necropsy->patient_id = $patient->id;
        $necropsy->fill([
            'date_necropsied_at' => $necropsiedAt->toDateString(),
            'time_necropsied_at' => $necropsiedAt->toTimeString(),
            'prosector' => $request->input('prosector'),
            'is_photos_collected' => $request->boolean('is_photos_collected'),
            'is_carcass_radiographed' => $request->boolean('is_carcass_radiographed'),
        ]);
        $necropsy->save();

        return back();
    }
}
