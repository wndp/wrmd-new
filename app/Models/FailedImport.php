<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedImport extends Model
{
    /** @use HasFactory<\Database\Factories\FailedImportFactory> */
    use HasFactory;
    use HasVersion7Uuids;

    protected $fillable = [
        'team_id',
        'user_id',
        'import_id',
        'disclosures',
        'row',
        'exception',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'user_id' => 'integer',
        'disclosures' => 'json',
        'row' => 'json',
        'exception' => 'string',
    ];
}
