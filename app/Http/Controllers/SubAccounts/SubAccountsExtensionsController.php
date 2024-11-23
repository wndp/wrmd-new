<?php

namespace App\Http\Controllers\SubAccounts;

use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Support\ExtensionManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SubAccountsExtensionsController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Team $subAccount)
    {
        abort_unless(
            Auth::user()->currentTeam->hasSubAccount($subAccount),
            new RecordNotOwned()
        );

        $activated = ExtensionManager::getActivated($subAccount);

        $publicExtensions = Arr::map(ExtensionManager::getPublic(), fn ($extension) => [
            ...$extension->toArray(),
            'is_activated' => $activated->contains('extension', $extension->value)
        ]);

        $nonPublicExtensions = Arr::map(ExtensionManager::getNonPublic(), fn ($extension) => [
            ...$extension->toArray(),
            'is_activated' => $activated->contains('extension', $extension->value)
        ]);

        $extensions = array_merge(
            $publicExtensions,
            array_filter($nonPublicExtensions, fn ($extension) => $extension['is_activated'])
        );

        return Inertia::render('SubAccounts/Extensions', compact('subAccount', 'extensions'));
    }
}
