<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banding extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;
    use HasVersion7Uuids;

    protected $fillable = [
        'patient_id',
        'band_number',
        'banded_at',
        'band_size_id',
        'band_disposition_id',
        'age_code_id',
        'how_aged_id',
        'sex_code_id',
        'how_sexed_id',
        'status_code_id',
        'additional_status_code_id',
        'master_bander_number',
        'banded_by',
        'location_number',
        'auxiliary_marker',
        'auxiliary_marker_type_id',
        'auxiliary_marker_color_id',
        'auxiliary_marker_code_color_id',
        'auxiliary_side_of_bird_id',
        'auxiliary_placement_on_leg_id',
        'remarks',
        'recaptured_at',
        'recapture_disposition_id',
        'present_condition_id',
        'how_present_condition_id',
    ];

    protected $casts = [
        'patient_id' => 'string',
        'band_number' => 'string',
        'banded_at' => 'datetime',
        'band_size_id' => 'integer',
        'band_disposition_id' => 'integer',
        'age_code_id' => 'integer',
        'how_aged_id' => 'integer',
        'sex_code_id' => 'integer',
        'how_sexed_id' => 'integer',
        'status_code_id' => 'integer',
        'additional_status_code_id' => 'integer',
        'master_bander_number' => 'string',
        'banded_by' => 'string',
        'location_number' => 'string',
        'auxiliary_marker' => 'string',
        'auxiliary_marker_type_id' => 'integer',
        'auxiliary_marker_color_id' => 'integer',
        'auxiliary_marker_code_color_id' => 'integer',
        'auxiliary_side_of_bird_id' => 'integer',
        'auxiliary_placement_on_leg_id' => 'integer',
        'remarks' => 'string',
        'recaptured_at' => 'datetime',
        'recapture_disposition_id' => 'integer',
        'present_condition_id' => 'integer',
        'how_present_condition_id' => 'integer',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function bandSize(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'band_size_id');
    }

    public function bandDisposition(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'band_disposition_id');
    }

    public function ageCode(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'age_code_id');
    }

    public function howAged(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'how_aged_id');
    }

    public function sexCode(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'sex_code_id');
    }

    public function howSexed(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'how_sexed_id');
    }

    public function statusCode(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'status_code_id');
    }

    public function additionalStatusCode(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'additional_status_code_id');
    }

    public function auxiliaryMarkerType(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'auxiliary_marker_type_id');
    }

    public function auxiliaryMarkerColor(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'auxiliary_marker_color_id');
    }

    public function auxiliaryMarkerCodeColor(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'auxiliary_marker_code_color_id');
    }

    public function auxiliarySideOfBird(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'auxiliary_side_of_bird_id');
    }

    public function auxiliaryPlacementOnLeg(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'auxiliary_placement_on_leg_id');
    }

    public function recaptureDisposition(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'recapture_disposition_id');
    }

    public function presentCondition(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'present_condition_id');
    }

    public function howPresentCondition(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'how_present_condition_id');
    }
}
