<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetachRescuerController extends Controller
{
    public function __invoke(Request $request, Patient $patient): RedirectResponse
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $newRescuer = tap($patient->rescuer->replicate(), fn ($newRescuer) => $newRescuer->save());
        $patient->rescuer_id = $newRescuer->id;
        $patient->save();

        return redirect()->back()
            ->with('notification.heading', 'Success!')
            ->with('notification.text', 'This rescuer has been detached.');
    }
}
