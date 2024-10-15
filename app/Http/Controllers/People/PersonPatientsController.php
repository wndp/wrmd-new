<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Listing\Lists\RescuerPatientsList;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PersonPatientsController extends Controller
{
    /**
     * Return a listing of a persons donations.
     */
    public function __invoke(Person $person): Response
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        $listFigures = tap(
            new RescuerPatientsList(Auth::user()->currentTeam),
            fn ($list) => $list->withRequest(new Request([
                'rescuerId' => $person->id,
            ]))
        )
            ->get();

        return Inertia::render('People/Patients', compact('person', 'listFigures'));
    }
}
