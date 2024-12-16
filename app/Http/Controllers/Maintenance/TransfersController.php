<?php

namespace App\Http\Controllers\Maintenance;

use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Support\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TransfersController extends Controller
{
    /**
     * Display the maintenance index page.
     */
    public function __invoke(Request $request): Response
    {
        //ExtensionNavigation::emit('maintenance');

        $unansweredTransfers = Transfer::where('to_team_id', Auth::user()->current_team_id)
            ->whereNull('responded_at')
            ->with('patient.admissions', 'toTeam', 'fromTeam')
            ->latest()
            ->get()
            ->transform(fn ($transfer) => [
                ...$transfer->toArray(),
                'created_at_for_humans' => Timezone::convertFromUtcToLocal($transfer->created_at)->toDayDateTimeString(),
            ]);

        $transfers = Transfer::when(
            $unansweredTransfers->isNotEmpty(),
            fn ($q) => $q->whereNotIn('uuid', $unansweredTransfers->pluck('uuid'))
        )
            ->where(function ($query) {
                $query->where('from_team_id', Auth::user()->current_team_id)
                    ->orWhere('to_team_id', Auth::user()->current_team_id);
            })
            ->with('patient.admissions', 'clonedPatient.admissions', 'toTeam', 'fromTeam')
            ->latest()
            ->paginate()
            ->onEachSide(1)
            ->through(fn ($transfer) => [
                ...$transfer->toArray(),
                'created_at_for_humans' => Timezone::convertFromUtcToLocal($transfer->created_at)->toDayDateTimeString(),
            ]);

        $uuid = $request->input('uuid');

        return Inertia::render('Maintenance/Transfers', compact('unansweredTransfers', 'transfers', 'uuid'));
    }
}
