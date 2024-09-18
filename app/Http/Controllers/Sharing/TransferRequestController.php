<?php

namespace App\Http\Controllers\Sharing;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendTransferRequest;
use App\Models\Team;
use App\Options\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use MatanYadaev\EloquentSpatial\Objects\Point;

class TransferRequestController extends Controller
{
    public function create(): Response
    {
        $this->loadAdmissionAndSharePagination();

        $team = Auth::user()->currentTeam;
        $subAccounts = $team->subAccounts;

        $nearest = $team->coordinates instanceof Point
            ? Team::where('status', AccountStatus::ACTIVE->value)
                ->where('id', '!=', $team->id)
                ->whereNotIn('id', $subAccounts->pluck('id'))
                ->whereDistanceSphere('coordinates', $team->coordinates, '<=', 80500) // in meters ~= 50 miles
                ->orderByDistanceSphere('coordinates', $team->coordinates)
                ->get()
                ->pluck('name', 'id')
            : new Collection();

        $farthest = Team::where('status', AccountStatus::ACTIVE->value)
            ->where('id', '!=', $team->id)
            ->whereNotIn('id', $subAccounts->pluck('id'))
            ->whereNotIn('id', $nearest->keys())
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        return Inertia::render('Patients/Transfer', [
            'teams' => Options::arrayToSelectable(
                Collection::make([
                    __('Organizations Near Me') => $nearest->toArray(),
                    __('All Other Organizations') => $farthest->toArray(),
                ])->when($team->is_master_account, fn ($collection) => $collection->prepend(
                    $subAccounts->pluck('organization', 'id')->toArray(),
                    __('Sub-Accounts')
                ))->toArray()
            ),
        ]);
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $data = $request->validate([
            'transferTo' => 'required|exists:accounts,id',
        ], [
            'transferTo.exists' => __('The selected organization is unknown.'),
        ]);

        $transferTo = Team::findOrFail($data['transferTo']);

        SendTransferRequest::dispatch(
            Auth::user()->currentAccount,
            $transferTo,
            $patient,
            Auth::user(),
            $request->boolean('collaborate')
        );

        $admission = Admission::custody(Auth::user()->currentAccount, $patient);

        return redirect()->route('patients.continued.edit', ['y' => $admission->case_year, 'c' => $admission->case_id])
            ->with('flash.notificationHeading', __('Transfer Request Sent!'))
            ->with('flash.notification', __('A transfer request has been sent to :organization.', ['organization' => $transferTo->organization]));
    }
}
