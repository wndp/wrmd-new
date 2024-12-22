<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\SettingKey;
use App\Events\PatientUpdated;
use App\Models\Patient;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Wrmd;
use App\ValueObjects\SingleStorePoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutcomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  $Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $patient)
    {
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        [$dispositionPendingId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id)
            ->update($request->validate([
                'disposition_id' => [
                    'required',
                    'integer',
                    new AttributeOptionExistsRule(AttributeOptionName::PATIENT_DISPOSITIONS),
                ],
                'dispositioned_at' => [
                    'nullable',
                    'required_unless:disposition_id,'.$dispositionPendingId,
                    'date',
                    'after_or_equal:'.$admittedAt,
                ],
                'release_type_id' => [
                    'nullable',
                    'integer',
                    new AttributeOptionExistsRule(AttributeOptionName::PATIENT_RELEASE_TYPES),
                ],
                'transfer_type_id' => [
                    'nullable',
                    'integer',
                    new AttributeOptionExistsRule(AttributeOptionName::PATIENT_TRANSFER_TYPES),
                ],
                'disposition_address' => 'nullable',
                'disposition_city' => 'nullable',
                'disposition_subdivision' => 'nullable',
                'disposition_postal_code' => 'nullable',
                'reason_for_disposition' => 'nullable',
                'dispositioned_by' => 'nullable',
                'is_carcass_saved' => 'nullable|boolean',
            ], [
                'dispositioned_at.required_unless' => __('The disposition date field is required unless disposition is in Pending.'),
                'dispositioned_at.date' => __('The disposition date is not a valid date.'),
                'dispositioned_at.after_or_equal' => __('The disposition date must be a date after or equal to :date.', [
                    'date' => $admittedAt->toFormattedDateString(),
                ]),
            ]));

        if ($request->filled('disposition_lat', 'disposition_lng')) {
            $patient->disposition_coordinates = new SingleStorePoint($request->disposition_lat, $request->disposition_lng);
            $patient->save();
        }

        event(new PatientUpdated($patient));

        return back();
    }
}
