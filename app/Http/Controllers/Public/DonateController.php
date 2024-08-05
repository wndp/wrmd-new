<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DonateController extends Controller
{
    public function index(Request $request)
    {
        $teams = TeamRepository::active();
        $organizations = $teams->count();
        $countries = $teams->unique('country')->pluck('country')->count();

        return Inertia::render('Public/Donate', compact(
            'organizations',
            'countries',
        ));
    }

    public function thanks(Request $request)
    {
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $patientsCount = Cache::remember('patientsCountx', now()->addDay(), function () {
            $patientsCount = Admission::joinPatients()
                ->join('teams', 'admissions.team_id', '=', 'teams.id')
                ->where('teams.status', 'Active')
                ->count();

            return number_format($patientsCount);
        });

        return Inertia::render('Public/Thanks', compact(
            'firstName',
            'lastName',
            'amount',
            'currency',
            'patientsCount'
        ));
    }
}
