<?php

namespace App\Http\Controllers;

use App\Http\Requests\NutritionPlanRequest;
use App\Models\Admission;
use App\Models\NutritionPlan;
use App\Models\NutritionPlanIngredient;
use App\Models\Patient;
use App\Services\RequestRecordsSaver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NutritionPlanController extends Controller
{
    /**
     * Store a new Nutrition Plan.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(NutritionPlanRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $nutritionPlan = NutritionPlan::create([
            'patient_id' => $patient->id,
            'name' => $request->input('name'),
            'started_at' => $request->input('started_at'),
            'ended_at' => $request->input('ended_at'),
            'frequency' => $request->input('frequency'),
            'frequency_unit_id' => $request->input('frequency_unit_id'),
            'route_id' => $request->input('route_id'),
            'description' => $request->input('description'),
        ]);

        $requestRecordsSaver = new RequestRecordsSaver(
            $nutritionPlan,
            'ingredients',
            NutritionPlanIngredient::class,
            new Collection($request->get('ingredients'))
        );
        $requestRecordsSaver->save();

        $caseNumber = Admission::custody(Auth::user()->currentTeam, $patient)->case_number;

        return back()
            ->with('flash.notificationHeading', __('Nutrition Plan Created'))
            ->with('flash.notification', __('Your nutrition plan was assigned to patient :caseNumber.', ['caseNumber' => $caseNumber]));
    }

    /**
     * Update a nutrition.
     */
    public function update(NutritionPlanRequest $request, Patient $patient, NutritionPlan $nutrition)
    {
        $nutrition->validateOwnership(Auth::user()->current_team_id)
            ->update([
                'name' => $request->input('name'),
                'started_at' => $request->input('started_at'),
                'ended_at' => $request->input('ended_at'),
                'frequency' => $request->input('frequency'),
                'frequency_unit_id' => $request->input('frequency_unit_id'),
                'route_id' => $request->input('route_id'),
                'description' => $request->input('description'),
            ]);

        $requestRecordsSaver = new RequestRecordsSaver(
            $nutrition,
            'ingredients',
            NutritionPlanIngredient::class,
            new Collection($request->get('ingredients'))
        );
        $requestRecordsSaver->save();

        $caseNumber = Admission::custody(Auth::user()->currentTeam, $patient)->case_number;

        return back()
            ->with('flash.notificationHeading', __('Nutrition Plan Updated'))
            ->with('flash.notification', __('Your nutrition plan for patient :caseNumber was updated.', ['caseNumber' => $caseNumber]));
    }
}
