<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use App\Support\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientLocation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'moved_in_at',
        'facility_id',
        'area',
        'enclosure',
        'hours',
        'comments',
    ];

    protected $casts = [
        'moved_in_at' => 'datetime',
        'facility_id' => 'integer',
        'area' => 'string',
        'enclosure' => 'string',
        'hours' => 'float',
        'comments' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    protected $appends = [
        'location_for_humans',
        //'moved_in_at_for_humans',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'facility_id');
    }

    protected function locationForHumans(): Attribute
    {
        return Attribute::get(
            function () {
                $string = $this->area;

                if ($this->facility === 'Homecare') {
                    return $string;
                }

                return $string .= trim($this->enclosure) === '' ? '' : ', '.$this->enclosure;
            }
        );
    }

    // protected function movedInAtForHumans(): Attribute
    // {
    //     return Attribute::get(
    //         fn () => Timezone::convertFromUtcToLocal($this->moved_in_at)->toDayDateTimeString(),
    //     );
    // }
}
