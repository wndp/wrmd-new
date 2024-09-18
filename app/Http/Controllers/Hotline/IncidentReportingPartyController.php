<?php

namespace App\Http\Controllers\Hotline;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Models\Incident;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IncidentReportingPartyController extends Controller
{
    public function __invoke(Incident $incident): Response
    {
        $incident->validateOwnership(Auth::user()->current_team_id);

        OptionsStore::add([
            new LocaleOptions(),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PERSON_ENTITY_TYPES->value
            ])
        ]);

        [
            $statusOpenId,
            $statusUnresolvedId,
            $statusResolvedId
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN->value],
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_UNRESOLVED->value],
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED->value]
        ]);

        $incident->load('status');

        return Inertia::render('Hotline/ReportingParty', [
            'incident' => $incident,
            'reportingParty' => $incident->reportingParty,
            'statusOpenId' => $statusOpenId,
            'statusUnresolvedId' => $statusUnresolvedId,
            'statusResolvedId' => $statusResolvedId
        ]);
    }
}
