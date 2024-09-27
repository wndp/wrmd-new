<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NutritionPlanController extends Controller
{
    /**
     * Store a new Nutrition Plan.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'plan_start_at' => 'required|date',
            'plan_end_at' => 'nullable|date|after_or_equal:plan_start_at',
            'frequency' => 'required',
        ], [
            'plan_start_at.required' => 'The start date field is required.',
            'plan_start_at.date' => 'The start date is not a valid date.',
            'plan_end_at.date' => 'The end date is not a valid date.',
        ]);

        Nutrition::store($patient->id, [
            'plan_start_at' => $request->convertDateFromLocal('plan_start_at'),
            'plan_end_at' => $request->convertDateFromLocal('plan_end_at'),
            'frequency' => $request->frequency,
            'name' => $request->name,
            'route' => $request->route,
            'description' => $request->description,
            'amount' => $request->amount
        ]);

        $caseNumber = Admission::custody(Auth::user()->currentAccount, $patient)->case_number;

        return back()
            ->with('flash.notificationHeading', __('Nutrition Plan Created'))
            ->with('flash.notification', __('Your nutrition plan was assigned to patient :caseNumber.', ['caseNumber' => $caseNumber]));
    }

    /**
     * Update a nutrition.
     */
    public function update(Request $request, Patient $patient, Nutrition $nutrition)
    {
        $request->validate([
            'plan_start_at' => 'required|date',
            'plan_end_at' => 'nullable|date|after_or_equal:plan_start_at',
            'frequency' => 'required',
        ], [
            'plan_start_at.required' => 'The start date field is required.',
            'plan_start_at.date' => 'The start date is not a valid date.',
            'plan_end_at.date' => 'The end date is not a valid date.',
        ]);

        $nutrition->validateOwnership(Auth::user()->current_team_id)
            ->update([
                'plan_start_at' => $request->convertDateFromLocal('plan_start_at'),
                'plan_end_at' => $request->convertDateFromLocal('plan_end_at'),
                'frequency' => $request->frequency,
                'name' => $request->name,
                'route' => $request->route,
                'description' => $request->description,
                'amount' => $request->amount
            ]);

        $caseNumber = Admission::custody(Auth::user()->currentAccount, $patient)->case_number;

        return back()
            ->with('flash.notificationHeading', __('Nutrition Plan Updated'))
            ->with('flash.notification', __('Your nutrition plan for patient :caseNumber was updated.', ['caseNumber' => $caseNumber]));
    }
}
