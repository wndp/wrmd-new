<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'drug',
        'rx_started_at',
        'rx_ended_at',
        'concentration',
        'concentration_unit_id',
        'dosage',
        'dosage_unit_id',
        'loading_dose',
        'loading_dose_unit_id',
        'dose',
        'dose_unit_id',
        'frequency_id',
        'route_id',
        'is_controlled_substance',
        'instructions',
    ];

    protected $casts = [
        'rx_started_at' => 'datetime',
        'rx_ended_at' => 'datetime',
        'concentration' => 'float',
        'concentration_unit_id' => 'integer',
        'dosage' => 'float',
        'dosage_unit_id' => 'integer',
        'loading_dose' => 'float',
        'loading_dose_unit_id' => 'integer',
        'dose' => 'float',
        'dose_unit_id' => 'integer',
        'frequency_id' => 'integer',
        'route_id' => 'integer',
        'is_controlled_substance' => 'boolean',
        'instructions' => 'string',
    ];

    protected $appends = [
        // 'occurrences',
        // 'full_prescription',
        // 'is_completed',
        // 'summary_body',
        // 'start_date',
        // 'end_date',
        // 'badge_text',
        // 'badge_color',
    ];

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = [
        'patient',
    ];

    /**
     * Prescriptions belong to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Prescriptions can have a veterinarian.
     */
    public function veterinarian()
    {
        return $this->belongsTo(Veterinarian::class);  //withDefault
    }
}
