<?php

namespace App\Http\Controllers\Necropsy;

use App\Domain\OptionsStore;
use App\Domain\Patients\ExamOptions;
use App\Domain\Patients\Patient;
use App\Extensions\ExtensionNavigation;
use App\Extensions\Necropsy\Necropsy;
use App\Extensions\Necropsy\NecropsyOptions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NecropsyController extends Controller
{
    public function edit(NecropsyOptions $necropsyOptions, ExamOptions $examOptions)
    {
        OptionsStore::merge($necropsyOptions);
        OptionsStore::merge($examOptions);

        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load('necropsy');
        ExtensionNavigation::emit('patient', $admission);

        return Inertia::render('Patients/Necropsy/Edit');
    }

    public function update(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id);

        $request->validate([
            'necropsied_at' => 'required|date',
            'prosector' => 'required',
        ], [
            'necropsied_at.required' => 'The necropsy date field is required.',
            'necropsied_at.date' => 'The necropsy date is not a valid date.',
        ]);

        $necropsy = Necropsy::firstOrNew(['patient_id' => $patient->id]);
        $necropsy->patient_id = $patient->id;
        $necropsy->fill([
            'necropsied_at' => $request->convertDateFromLocal('necropsied_at'),
            'prosector' => $request->input('prosector'),
            'is_photos_collected' => $request->boolean('is_photos_collected'),
            'is_carcass_radiographed' => $request->boolean('is_carcass_radiographed'),
        ]);
        $necropsy->save();

        return back();
    }
}
