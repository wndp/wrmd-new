<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class AccountsMetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Team $team): Response
    {
        $team->load('settings'); //customFields

        return Inertia::render('Admin/Teams/Meta', compact('team'));
    }
}
