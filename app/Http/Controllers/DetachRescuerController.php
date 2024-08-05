<?php

namespace App\Http\Controllers;

use App\Domain\Patients\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetachRescuerController extends Controller
{
    public function __invoke(Request $request, Patient $patient): RedirectResponse
    {
        $patient->validateOwnership(Auth::user()->current_account_id);

        $newRescuer = tap($patient->rescuer->replicate(), fn ($newRescuer) => $newRescuer->save());
        $patient->rescuer_id = $newRescuer->id;
        $patient->save();

        return redirect()->back()
            ->with('flash.notificationHeading', 'Success!')
            ->with('flash.notification', 'This rescuer has been detached.');
    }
}
