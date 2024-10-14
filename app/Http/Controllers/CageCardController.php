<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Enums\AttributeOptionName;
use App\Events\PatientUpdated;
use App\Jobs\AttemptTaxaIdentification;
use App\Models\Patient;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Timezone;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CageCardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $patient)
    {
        $request->validate([
            'common_name' => 'required',
            'admitted_at' => 'required|date|before_or_equal:'.($patient->dispositioned_at ?? Carbon::now(Wrmd::settings(SettingKey::TIMEZONE))),
            'morph_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::TAXA_MORPHS),
            ],
        ], [
            'admitted_at.required' => __('The date admitted field is required.'),
            'admitted_at.date' => __('The date admitted is not a valid date.'),
            'admitted_at.before_or_equal' => __('The date admitted must be a date before or equal to :date.', [
                'date' => $patient->dispositioned_at?->format('Y-m-d') ?? __('today'),
            ]),
        ]);

        $admittedAt = Timezone::convertFromLocalToUtc($request->input('admitted_at'));

        $patient->validateOwnership(Auth::user()->current_team_id)
            ->update([
                'common_name' => $request->input('common_name'),
                'taxon_id' => $request->input('taxon_id'),
                'date_admitted_at' => $admittedAt->toDateString(),
                'time_admitted_at' => $admittedAt->toTimeString(),
                'morph_id' => $request->input('morph_id'),
                'band' => $request->input('band'),
                'name' => $request->input('name'),
                'reference_number' => $request->input('reference_number'),
                'microchip_number' => $request->input('microchip_number'),
            ]);

        //AttemptTaxaIdentification::dispatch($patient)->delay(15);

        event(new PatientUpdated($patient));

        return back();
    }
}
