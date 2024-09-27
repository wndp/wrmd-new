<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use App\Summarizable;
use App\Support\Timezone;
use App\Weighable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Necropsy extends Model implements Summarizable, Weighable
{
    use HasFactory;
    use SoftDeletes;
    use HasVersion7Uuids;
    use ValidatesOwnership;

    protected $fillable = [
        'date_necropsied_at',
        'time_necropsied_at',
        'prosector',
        'is_photos_collected',
        'is_carcass_radiographed',
        'is_previously_frozen',
        'is_scavenged',
        'is_discarded',
        'carcass_condition_id',
        'weight',
        'weight_unit_id',
        'wing',
        'tarsus',
        'bill_depth',
        'culmen',
        'exposed_culmen',
        'body_condition_id',
        'sex_id',
        'age',
        'age_unit_id',
        'integument_finding_id',
        'integument',
        'cavities_finding_id',
        'cavities',
        'gastrointestinal_finding_id',
        'gastrointestinal',
        'liver_gallbladder_finding_id',
        'liver_gallbladder',
        'hematopoietic_finding_id',
        'hematopoietic',
        'renal_finding_id',
        'renal',
        'respiratory_finding_id',
        'respiratory',
        'cardiovascular_finding_id',
        'cardiovascular',
        'endocrine_reproductive_finding_id',
        'endocrine_reproductive',
        'nervous_finding_id',
        'nervous',
        'head_finding_id',
        'head',
        'musculoskeletal_finding_id',
        'musculoskeletal',
        'samples_collected',
        'other_sample',
        'morphologic_diagnosis',
        'gross_summary_diagnosis',
    ];

    protected $casts = [
        'date_necropsied_at' => 'date:Y-m-d',
        'time_necropsied_at' => 'string',
        'prosector',
        'is_photos_collected' => 'boolean',
        'is_carcass_radiographed' => 'boolean',
        'is_previously_frozen' => 'boolean',
        'is_scavenged' => 'boolean',
        'is_discarded' => 'boolean',
        'carcass_condition_id' => 'integer',
        'weight' => 'float',
        'weight_unit_id' => 'integer',
        'wing' => 'float',
        'tarsus' => 'float',
        'bill_depth' => 'float',
        'culmen' => 'float',
        'exposed_culmen' => 'float',
        'body_condition_id' => 'integer',
        'sex_id' => 'integer',
        'age' => 'float',
        'age_unit_id' => 'integer',
        'tarsus' => 'float',
        'bill_depth' => 'float',
        'culmen' => 'float',
        'exposed_culmen' => 'float',
        'integument_finding_id' => 'integer',
        'integument' => 'string',
        'cavities_finding_id' => 'integer',
        'cavities' => 'string',
        'gastrointestinal_finding_id' => 'integer',
        'gastrointestinal' => 'string',
        'liver_gallbladder_finding_id' => 'integer',
        'liver_gallbladder' => 'string',
        'hematopoietic_finding_id' => 'integer',
        'hematopoietic' => 'string',
        'renal_finding_id' => 'integer',
        'renal' => 'string',
        'respiratory_finding_id' => 'integer',
        'respiratory' => 'string',
        'cardiovascular_finding_id' => 'integer',
        'cardiovascular' => 'string',
        'endocrine_reproductive_finding_id' => 'integer',
        'endocrine_reproductive' => 'string',
        'nervous_finding_id' => 'integer',
        'nervous' => 'string',
        'head_finding_id' => 'integer',
        'head' => 'string',
        'musculoskeletal_finding_id' => 'integer',
        'musculoskeletal' => 'string',
        'samples_collected' => 'array',
        'other_sample' => 'string',
        'morphologic_diagnosis' => 'string',
        'gross_summary_diagnosis' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    protected $appends = [
        'necropsied_at',
        'necropsied_at_for_humans',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(fn () => '');
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(fn () => 'necropsied_at');
    }

    public function summaryWeight(): Attribute
    {
        return Attribute::get(fn () => $this->weight);
    }

    public function summaryWeightUnitId(): Attribute
    {
        return Attribute::get(fn () => $this->weight_unit_id);
    }

    protected function necropsiedAt(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_necropsied_at)) {
                return $this->date_necropsied_at;
            }
            return $this->date_necropsied_at->setTimeFromTimeString($this->time_necropsied_at);
        });
    }

    protected function necropsiedAtForHumans(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_necropsied_at)) {
                return $this->date_necropsied_at->translatedFormat(config('wrmd.date_format'));
            }
            return Timezone::convertFromUtcToLocal($this->date_necropsied_at->setTimeFromTimeString($this->time_necropsied_at))
                ?->translatedFormat(config('wrmd.date_time_format'));
        });
    }
}
