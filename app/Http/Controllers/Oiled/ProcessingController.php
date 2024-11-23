<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\MediaCollection;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProcessingController extends Controller
{
    public function __invoke(): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load('rescuer', 'oilProcessing');
        $admission->patient->oilProcessing->append('collected_at', 'processed_at');

        if (! ($teamIsInPossession = $admission->patient->team_possession_id === Auth::user()->current_team_id)) {
            $admission->load('patient.possession');
        }

        OptionsStore::add([
            new LocaleOptions(),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::OILED_PROCESSING_CONDITIONS->value,
                AttributeOptionName::OILED_PROCESSING_STATUSES->value,
                AttributeOptionName::OILED_PROCESSING_OILING_PERCENTAGES->value,
                AttributeOptionName::OILED_PROCESSING_OILING_DEPTHS->value,
                AttributeOptionName::OILED_PROCESSING_OILING_LOCATIONS->value,
                AttributeOptionName::OILED_PROCESSING_OIL_TYPES->value,
                AttributeOptionName::OILED_PROCESSING_EVIDENCES->value,
                AttributeOptionName::OILED_PROCESSING_CARCASS_CONDITIONS->value,
                AttributeOptionName::OILED_PROCESSING_EXTENT_OF_SCAVENGINGS->value,
                AttributeOptionName::OILED_PROCESSING_OIL_CONDITION->value,
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
                AttributeOptionName::PATIENT_RELEASE_TYPES->value,
                AttributeOptionName::PATIENT_TRANSFER_TYPES->value,
            ])
        ]);

        [
            $collectionConditionAliveId,
            $collectionConditionDeadId,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::OILED_PROCESSING_CONDITIONS->value, AttributeOptionUiBehavior::OILED_PROCESSING_CONDITION_IS_ALIVE->value],
            [AttributeOptionName::OILED_PROCESSING_CONDITIONS->value, AttributeOptionUiBehavior::OILED_PROCESSING_CONDITION_IS_DEAD->value]
        ]);

        return Inertia::render('Oiled/Processing', [
            'patient' => $admission->patient,
            'media' => fn () => $admission->patient->getMedia(MediaCollection::EVIDENCE_PHOTO->value),
            'attributeOptionUiBehaviors' => \App\Models\AttributeOptionUiBehavior::getGroupedAttributeOptions([
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
            ]),
            'teamIsInPossession' => $teamIsInPossession,
            'collectionConditionAliveId' => $collectionConditionAliveId,
            'collectionConditionDeadId' => $collectionConditionDeadId,
        ]);
    }
}
