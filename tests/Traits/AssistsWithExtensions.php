<?php

namespace Tests\Traits;

use App\Enums\Extension;
use App\Models\Team;
use App\Support\ExtensionManager;

trait AssistsWithExtensions
{
    public function activateExtension(Extension $extension, Team|array $team = [], $attributes = [])
    {
        $team = $team instanceof Team ? $team : Team::factory()->create((array) $team);

        ExtensionManager::activate($team, $extension);

        return $team;

        // return [
        //     'team' => $team,
        //     'extension' => $extension,
        // ];
    }
}
