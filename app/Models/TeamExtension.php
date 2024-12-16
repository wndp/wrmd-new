<?php

namespace App\Models;

use App\Enums\Extension;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;

class TeamExtension extends Model
{
    use HasVersion7Uuids;

    protected $fillable = [
        'team_id',
        'extension',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'extension' => Extension::class,
    ];
}
