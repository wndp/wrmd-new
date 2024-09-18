<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use App\Summarizable;
use App\Support\Timezone;
use App\Support\Wrmd;
use App\Weighable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CareLog extends Model implements Summarizable, Weighable
{
    use HasFactory;
    use ValidatesOwnership;
    use SoftDeletes;

    protected $fillable = [
        'date_care_at',
        'weight',
        'weight_unit_id',
        'temperature',
        'temperature_unit_id',
        'comments',
    ];

    protected $casts = [
        'date_care_at' => 'datetime',
        'weight' => 'float',
        'weight_unit_id' => 'integer',
        'temperature' => 'float',
        'temperature_unit_id' => 'integer',
        'comments' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function weight(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'weight_id');
    }

    public function temperature(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'temperature_id');
    }

    public static function store(int $patientId, Collection $data, User $user): static
    {
        if ($data->get('date_care_at') instanceof Carbon) {
            $dateCareAt = $data->get('date_care_at');
        } else {
            $dateCareAtParsed = Carbon::parse($data->get('date_care_at'), Wrmd::settings('timezone'));
            $dateCareAt = $dateCareAtParsed->isToday() ? Carbon::now(Wrmd::settings('timezone')) : $dateCareAtParsed;
        }

        $careLog = new static([
            'date_care_at' => Timezone::convertFromLocalToUtc($dateCareAt),
            'weight' => $data->get('weight'),
            'weight_unit_id' => $data->get('weight_unit_id'),
            'temperature' => $data->get('temperature'),
            'temperature_unit_id' => $data->get('temperature_unit_id'),
            'comments' => $data->get('comments'),
        ]);

        $careLog->patient_id = $patientId;
        $careLog->user_id = $user->id;

        $careLog->save();

        return $careLog;
    }

    protected function fullWeight(): Attribute
    {
        return Attribute::get(
            fn () => empty($this->weight_unit_id) || !is_numeric($this->weight) || $this->weight == 0
                ? ''
                : $this->weight.$this->weightUnit?->value
        );
    }

    protected function fullTemperature(): Attribute
    {
        return Attribute::get(
            fn () => empty($this->temperature_unit_id) || !is_numeric($this->temperature) || $this->temperature == 0
                ? ''
                : $this->temperature.$this->temperatureUnit?->value
        );
    }

    // public function getFullWeightAttribute()
    // {
    //     return empty($this->weight_unit) || !is_numeric($this->weight) || $this->weight <= 0 ? '' : $this->weight.$this->weight_unit;
    // }

    // public function getFullTemperatureAttribute()
    // {
    //     return empty($this->temperature_unit) || !is_numeric($this->temperature) || $this->temperature <= 0 ? '' : $this->temperature.$this->temperature_unit;
    // }

    public function getSummaryBodyAttribute()
    {
        $vitals = [];
        $weight = $this->fullWeight;
        $temperature = $this->fullTemperature;

        $vitals[__('Weight')] = empty($weight) ? '' : $weight;
        $vitals[__('Temperature')] = empty($temperature) ? '' : $temperature;

        $details[] = collect(array_filter($vitals))->map(function ($value, $key) {
            return trim($key).': '.$value.'.';
        })->implode(' ');

        $details[] = $this->comments;

        return implode(' ', array_filter($details));

        //return '<span class="text-inline-emphasize">' . implode(', ', $vitals) . '</span> ' . $this->comments;
    }

    public function getSummaryDateAttribute()
    {
        return 'date_care_at';
    }

    public function getSummaryWeightAttribute()
    {
        return $this->weight;
    }

    public function getSummaryWeightUnitIdAttribute()
    {
        return $this->weight_unit_id;
    }
}
