<?php

namespace App\Models;

use App\Badgeable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LabCbcResult extends Model implements Badgeable
{
    /** @use HasFactory<\Database\Factories\LabCbcResultFactory> */
    use HasFactory;

    use HasVersion7Uuids;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'packed_cell_volume',
        'total_solids',
        'buffy_coat',
        'plasma_color',
        'white_blod_cell_count',
        'white_blod_cell_count_unit_id',
        'segmented_neutrophils',
        'segmented_neutrophils_unit_id',
        'band_neutrophils',
        'band_neutrophils_unit_id',
        'eosinophils',
        'eosinophils_unit_id',
        'basophils',
        'basophils_unit_id',
        'lymphocytes',
        'lymphocytes_unit_id',
        'monocytes',
        'monocytes_unit_id',
        'hemoglobin',
        'mean_corpuscular_volume',
        'mean_corpuscular_hemoglobin',
        'mean_corpuscular_hemoglobin_concentration',
        'erythrocytes',
        'reticulocytes',
        'thrombocytes',
        'polychromasia',
        'anisocytosis',
    ];

    protected $casts = [
        'packed_cell_volume' => 'float',
        'total_solids' => 'float',
        'buffy_coat' => 'float',
        'plasma_color' => 'float',
        'white_blod_cell_count' => 'float',
        'white_blod_cell_count_unit_id' => 'integer',
        'segmented_neutrophils' => 'float',
        'segmented_neutrophils_unit_id' => 'integer',
        'band_neutrophils' => 'float',
        'band_neutrophils_unit_id' => 'integer',
        'eosinophils' => 'float',
        'eosinophils_unit_id' => 'integer',
        'basophils' => 'float',
        'basophils_unit_id' => 'integer',
        'lymphocytes' => 'float',
        'lymphocytes_unit_id' => 'integer',
        'monocytes' => 'float',
        'monocytes_unit_id' => 'integer',
        'hemoglobin' => 'float',
        'mean_corpuscular_volume' => 'float',
        'mean_corpuscular_hemoglobin' => 'float',
        'mean_corpuscular_hemoglobin_concentration' => 'float',
        'erythrocytes' => 'string',
        'reticulocytes' => 'string',
        'thrombocytes' => 'string',
        'polychromasia' => 'string',
        'anisocytosis' => 'string',
    ];

    public function labReport(): MorphOne
    {
        return $this->morphOne(LabReport::class, 'lab_result');
    }

    // public function whiteBlodCellCountUnit(): BelongsTo
    // {
    //     return $this->belongsTo(AttributeOption::class, 'white_blod_cell_count_unit_id');
    // }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => __('CBC (Complete Blood Count)'),
        );
    }

    public function badgeColor(): Attribute
    {
        return Attribute::get(
            fn () => 'red',
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
