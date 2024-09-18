<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Communication extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'incident_id',
        'communication_at',
        'communication_by',
        'communication',
    ];

    protected $casts = [
        'incident_id' => 'integer',
        'communication_at' => 'datetime',
        'communication_by' => 'string',
        'communication' => 'string',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
}
