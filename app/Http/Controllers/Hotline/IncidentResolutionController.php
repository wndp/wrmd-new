<?php

namespace App\Http\Controllers\Hotline;

use App\Enums\SettingKey;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Support\Timezone;
use App\Support\Wrmd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentResolutionController extends Controller
{
    /**
     * Update an incident in storage.
     */
    public function __invoke(Request $request, Incident $incident): RedirectResponse
    {
        $incident->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'resolved_at' => 'nullable|required_with:resolution|date|after_or_equal:'.$incident->occurred_at->timezone(Wrmd::settings(SettingKey::TIMEZONE)),
            'resolution' => 'nullable|required_with:resolved_at',
            'given_information' => 'sometimes|boolean',
        ], [
            'resolved_at.required_with' => __('The date resolved field is required when a resolution is given.'),
            'resolved_at.date' => __('The date resolved field is not a valid date.'),
            'resolution.required_with' => __('The resolution field is required when resolved date is present.'),
        ]);

        [$statusResolvedId, $statusOpenId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED->value],
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN->value]
        ]);

        $incident->update([
            'resolved_at' => Timezone::convertFromLocalToUtc($request->input('resolved_at')),
            'resolution' => $request->resolution,
            'incident_status_id' => $request->filled('resolved_at') ? $statusResolvedId : $statusOpenId,
            'given_information' => $request->filled('given_information') ? $request->given_information : 0,
        ]);

        return redirect()->route('hotline.incident.edit', $incident)
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Hotline incident updated.'));
    }
}
