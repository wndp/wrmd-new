<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Extension;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Support\ExtensionManager;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class AccountsExtensionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Team $team)
    {
        $activated = ExtensionManager::getActivated($team);

        $extensions = Arr::map(Extension::cases(), fn ($extension) => [
            ...$extension->toArray(),
            'is_activated' => $activated->contains(fn ($active) => $active->extension === $extension->value),
        ]);

        return Inertia::render('Admin/Teams/Extensions', compact('team', 'extensions'));
    }
}
