<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use App\Summarizable;
use App\Support\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Exam extends Model implements Summarizable
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'patient_id',
        'date_examined_at',
        'time_examined_at',
        'exam_type_id',
        'sex_id',
        'weight',
        'weight_unit_id',
        'body_condition_id',
        'age',
        'age_unit_id',
        'attitude_id',
        'dehydration_id',
        'temperature',
        'temperature_unit_id',
        'mucous_membrane_color_id',
        'mucous_membrane_texture_id',
        'head',
        'cns',
        'cardiopulmonary',
        'gastrointestinal',
        'musculoskeletal',
        'integument',
        'body',
        'forelimb',
        'hindlimb',
        'head_finding_id',
        'cns_finding_id',
        'cardiopulmonary_finding_id',
        'gastrointestinal_finding_id',
        'musculoskeletal_finding_id',
        'integument_finding_id',
        'body_finding_id',
        'forelimb_finding_id',
        'hindlimb_finding_id',
        'treatment',
        'nutrition',
        'comments',
        'examiner',
    ];

    protected $casts = [
        'patient_id' => 'integer',
        'date_examined_at' => 'datetime',
        'time_examined_at' => 'string',
        'exam_type_id' => 'integer',
        'sex_id' => 'integer',
        'weight' => 'float',
        'weight_unit_id' => 'integer',
        'body_condition_id' => 'integer',
        'age' => 'float',
        'age_unit_id' => 'integer',
        'attitude_id' => 'integer',
        'dehydration_id' => 'integer',
        'temperature' => 'float',
        'temperature_unit_id' => 'integer',
        'mucous_membrane_color_id' => 'integer',
        'mucous_membrane_texture_id' => 'integer',
        'head' => 'string',
        'cns' => 'string',
        'cardiopulmonary' => 'string',
        'gastrointestinal' => 'string',
        'musculoskeletal' => 'string',
        'integument' => 'string',
        'body' => 'string',
        'forelimb' => 'string',
        'hindlimb' => 'string',
        'head_finding_id' => 'integer',
        'cns_finding_id' => 'integer',
        'cardiopulmonary_finding_id' => 'integer',
        'gastrointestinal_finding_id' => 'integer',
        'musculoskeletal_finding_id' => 'integer',
        'integument_finding_id' => 'integer',
        'body_finding_id' => 'integer',
        'forelimb_finding_id' => 'integer',
        'hindlimb_finding_id' => 'integer',
        'treatment' => 'string',
        'nutrition' => 'string',
        'comments' => 'string',
        'examiner' => 'string',
    ];

    protected $appends = [
        'summary_body',
        'examined_at',
        'examined_at_for_humans',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function examType(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'exam_type_id');
    }

    public function sex(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'sex_id');
    }

    public function weightUnit(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'weight_unit_id');
    }

    public function bodyCondition(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'body_condition_id');
    }

    public function ageUnit(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'age_unit_id');
    }

    public function attitude(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'attitude_id');
    }

    public function dehydration(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'dehydration_id');
    }

    public function temperature(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'temperature_unit_id');
    }

    public function mucousMembraneColor(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'mucous_membrane_color_id');
    }

    public function mucousMembraneTexture(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'mucous_membrane_texture_id');
    }

    public function headFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'head_finding_id');
    }

    public function cnsFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'cns_finding_id');
    }

    public function cardiopulmonaryFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'cardiopulmonary_finding_id');
    }

    public function gastrointestinalFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'gastrointestinal_finding_id');
    }

    public function musculoskeletalFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'musculoskeletal_finding_id');
    }

    public function integumentFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'integument_finding_id');
    }

    public function bodyFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'body_finding_id');
    }

    public function forelimbFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'forelimb_finding_id');
    }

    public function hindlimbFinding(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'hindlimb_finding_id');
    }

    protected function examinedAt(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_examined_at)) {
                return $this->date_examined_at->toFormattedDayDateString();
            }
            return $this->date_examined_at->setTimeFromTimeString($this->time_examined_at);
        });
    }

    protected function examinedAtForHumans(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_examined_at)) {
                return $this->date_examined_at->toFormattedDayDateString();
            }
            return Timezone::convertFromUtcToLocal($this->date_examined_at->setTimeFromTimeString($this->time_examined_at))?->toDayDateTimeString();
        });
    }

    public function getSummaryDateAttribute()
    {
        return 'examined_at';
    }

    public function getSummaryBodyAttribute()
    {
        $weight = $this->fullWeight;
        $temperature = $this->fullTemperature;
        $age = $this->fullAge;

        $vitals[__('Weight')] = empty($weight) ? '' : $weight;
        $vitals[__('Temperature')] = empty($temperature) ? '' : $temperature;
        $vitals[__('Age')] = empty($age) ? '' : $age;
        $vitals[__('Sex')] = empty($this->sex) ? '' : $this->sex;
        $vitals[__('Body Condition')] = empty($this->bcs) ? '' : $this->bcs;
        $vitals[__('Dehydration')] = empty($this->dehydration) ? '' : $this->dehydration;
        $vitals[__('Mucous Membrane Color')] = empty($this->mm_color) ? '' : $this->mm_color;
        $vitals[__('Mucous Membrane Texture')] = empty($this->mm_texture) ? '' : $this->mm_texture;
        $vitals[__('Attitude')] = empty($this->attitude) ? '' : $this->attitude;

        $details = [];
        $details[] .= Collection::make($vitals)->filter()->map(function ($value, $key) {
            return Str::upper(trim($key)).': '.$value.'.';
        })->implode(' ');

        $details[] = $this->abnormal_condition;
        $details[] = $this->comments ? __('Comments').': '.$this->comments : '';
        $details[] = $this->treatment ? __('Treatment').': '.$this->treatment : '';
        $details[] = $this->examiner ? __('Examiner').': '.$this->examiner : '';

        return sprintf(
            '%s %s: %s',
            Str::ucfirst($this->type),
            __('Exam'),
            implode(' ', array_filter($details))
        );
    }
}
