<?php

namespace App\Events;

use App\Models\Team;

class ListsRegistering
{
    public function __construct(public Team $team)
    {
        $this->team = $team;
    }
}
