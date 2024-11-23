<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class AccountsActionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Team $team): Response
    {
        return Inertia::render('Admin/Teams/Actions', compact('team'));
    }
}
