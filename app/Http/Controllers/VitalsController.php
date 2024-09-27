<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VitalsController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'recorded_at' => 'required|date',
        ], [
            'recorded_at.required' => 'The date field is required.',
            'recorded_at.date' => 'The date is not a valid date.',
        ]);

        $exam = new Exam();
        $lab = new Lab();

        if ($request->hasAny(['weight', 'temperature', 'attitude'])) {
            $exam->fill([
                'examined_at' => $request->convertDateFromLocal('recorded_at'),
                'type_id' => 'Daily',
                'weight' => $request->weight,
                'weight_unit_in' => 'g',
                'temperature' => $request->temperature,
                'temperature_unit_in' => 'F',
                'attitude_id' => $request->attitude,
                'examiner' => Auth::user()->name,
            ]);

            $exam->patient_id = $patient->id;
            $exam->save();
        };

        if ($request->hasAny(['pcv', 'tp'])) {
            $lab->fill([
                'performed_at' => $request->convertDateFromLocal('recorded_at'),
                'test' => 'cbc',
                'data' => $request->only('pcv', 'tp'),
                'technician' => Auth::user()->name,
            ]);

            $lab->patient_id = $patient->id;
            $lab->save();
        }

        Vital::create([
            'patient_id' => $patient->id,
            'exam_id' => $exam->id,
            'lab_id' => $lab->id,
            'recorded_at' => $request->convertDateFromLocal('recorded_at'),
        ]);

        return back();
    }

    /**
     * Update vitals in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Patient $patient, Vital $vital)
    {
        $vital->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'recorded_at' => 'required|date',
        ], [
            'recorded_at.required' => 'The date field is required.',
            'recorded_at.date' => 'The date is not a valid date.',
        ]);

        $exam = tap($vital->exam, function ($exam) use ($request, $patient) {
            $exam->fill([
                'examined_at' => $request->convertDateFromLocal('recorded_at'),
                'type' => 'Daily',
                'weight' => $request->weight,
                'weight_unit' => 'g',
                'temperature' => $request->temperature,
                'temperature_unit' => 'F',
                'attitude' => $request->attitude,
                'examiner' => Auth::user()->name,
            ]);
            $exam->patient_id = $patient->id;
            $exam->save();
        });

        $lab = tap($vital->lab, function ($lab) use ($request, $patient) {
            $lab->fill([
                'performed_at' => $request->convertDateFromLocal('recorded_at'),
                'test' => 'cbc',
                'data' => $request->only('pcv', 'tp'),
                'technician' => Auth::user()->name,
            ]);
            $lab->patient_id = $patient->id;
            $lab->save();
        });

        $vital->update([
            'exam_id' => $exam->id,
            'lab_id' => $lab->id,
            'recorded_at' => $request->convertDateFromLocal('recorded_at'),
        ]);

        return back();
    }

    /**
     * Update vitals in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Patient $patient, Vital $vital)
    {
        $vital->validateOwnership(Auth::user()->current_team_id);

        $vital->exam->delete();
        $vital->lab->delete();
        $vital->delete();

        return back();
    }
}
