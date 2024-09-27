<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;

class TeamExtension extends Model
{
    use HasVersion7Uuids;

    protected $fillable = [
        'team_id',
        'extension',
    ];
}

