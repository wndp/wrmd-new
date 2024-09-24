<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamExtension extends Pivot
{
    use HasVersion7Uuids;
}
