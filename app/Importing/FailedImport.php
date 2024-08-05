<?php

namespace App\Importing;

use Illuminate\Database\Eloquent\Model;

class FailedImport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'session_id',
        'disclosures',
        'row',
        'exception',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'team_id' => 'integer',
        'user_id' => 'integer',
        'disclosures' => 'json',
        'row' => 'json',
    ];
}
