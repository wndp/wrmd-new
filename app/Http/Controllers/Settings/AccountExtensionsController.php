<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Extension;
use App\Events\TeamUpdated;
use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Support\ExtensionManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccountExtensionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activated = ExtensionManager::getActivated(Auth::user()->currentTeam);

        $extensions = Arr::map(ExtensionManager::getPublic(), fn ($extension) => [
            ...$extension->toArray(),
            'is_activated' => $activated->contains('extension', $extension->value)
        ]);

        $standardExtensions = Arr::where($extensions, fn ($extension) => !$extension['pro']);
        $proExtensions = Arr::where($extensions, fn ($extension) => $extension['pro']);

        return Inertia::render('Settings/Extensions', compact('standardExtensions', 'proExtensions'));
    }

    /**
     * Activate an extension.
     */
    public function store(Extension $extension, Team $team = null): RedirectResponse
    {
        if ($team instanceof Team) {
            abort_unless(
                Auth::user()->can('manageAccounts') || Auth::user()->currentTeam->hasSubAccount($team),
                new RecordNotOwned()
            );
        }

        $team = $team ?: Auth::user()->currentTeam;

        ExtensionManager::activate($team, $extension);

        event(new TeamUpdated($team));

        return redirect()
            ->back()
            ->with('notification.heading', 'Extension Activated!')
            ->with('notification.text', "{$extension->label()} is activated and ready to use.");
    }

    /**
     * Deactivate an extension.
     */
    public function destroy(Extension $extension, Team $team = null): RedirectResponse
    {
        if ($team instanceof Team) {
            abort_unless(
                Auth::user()->can('manageAccounts') || Auth::user()->currentTeam->hasSubAccount($team),
                new RecordNotOwnedResponse()
            );
        }

        try {
            $team = $team ?: Auth::user()->currentTeam;

            ExtensionManager::deactivate($team, $extension);
            event(new TeamUpdated($team));

            return redirect()
                ->back()
                ->with('notification.heading', 'Extension Deactivated')
                ->with('notification.text', "{$extension->label()} is deactivated.");
        } catch (\DomainException $e) {
            return redirect()
                ->back()
                ->with('notification.heading', 'Oops!')
                ->with('notification.text', $e->getMessage())
                ->with('notification.style', 'danger');
        }
    }

    /**
     * Get an extension or abort with a 404.
     */
    public function getExtension(string $slug): Extension
    {
        try {
            return ExtensionManager::findBySlug($slug);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }
}
