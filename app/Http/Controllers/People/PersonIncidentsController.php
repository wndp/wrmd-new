<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PersonIncidentsController extends Controller
{
    /**
     * Return a listing of a persons donations.
     */
    public function __invoke(Person $person): Response
    {
        $person->validateOwnership(Auth::user()->current_team_id);
        $person->load('hotline');

        return Inertia::render('People/Incidents', compact('person'));
    }
}
