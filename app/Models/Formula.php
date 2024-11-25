<?php

namespace App\Models;

use App\Actions\NutritionFormulaCalculator;
use App\Actions\PrescriptionFormulaCalculator;
use App\Concerns\ValidatesOwnership;
use App\Enums\FormulaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Formula extends Model
{
    use HasFactory;
    use HasVersion7Uuids;
    use ValidatesOwnership;
    use LogsActivity;

    protected $fillable = [
        'team_id',
        'type',
        'name',
        'defaults',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'type' => FormulaType::class,
        'name' => 'string',
        'defaults' => 'collection',
    ];

    protected $appends = [
        'defaults_for_humans',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the calculated attributes of the formula.
     */
    public function calculatedAttributes(Patient $patient = null)
    {
        if ($patient) {
            return match ($this->type) {
                FormulaType::PRESCRIPTION => PrescriptionFormulaCalculator::run($this, $patient),
                FormulaType::NUTRITION => NutritionFormulaCalculator::run($this, $patient),
                default => null,
            };
        }

        return new NullFormulaCalculator($this);
    }

    /**
     * Get the human readable representation of the formula.
     */
    public function getDefaultsForHumansAttribute(): string
    {
        $defaults = $this->defaults ?? new Collection();

        $autoCalculate = $defaults->get('auto_calculate_dose') ? 'Auto-calculated dose of' : '';
        $dose = empty($defaults->get('dose')) ? '' : $defaults->get('dose').$defaults->get('dose_unit').' of';
        $concentration = empty($defaults->get('concentration')) ? '' : $defaults->get('concentration').$defaults->get('concentration_unit');
        $dosage = empty($defaults->get('dosage')) ? '' : $defaults->get('dosage').$defaults->get('dosage_unit');
        $duration = empty($defaults->get('duration')) ? '' : "for {$defaults->get('duration')} days";

        $instructions = empty($defaults->get('instructions')) ? '' : ' '.trim($defaults->get('instructions'), '.').'.';

        return preg_replace(
            '/\s+/',
            ' ',
            trim("$autoCalculate $dose $concentration {$defaults->get('drug')} $dosage {$defaults->get('route')} {$defaults->get('frequency')} $duration").'.'.$instructions
        );
    }

    /**
     * Search formulas.
     * Json values are case sensitive so we normalize both the stored values and the searched value.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        $like = Str::lower('%'.$search.'%');

        return $query->where(
            fn ($query) => $query->where('name', 'like', $like)
                ->orWhere(DB::raw("LOWER(JSON_EXTRACT_STRING(defaults, 'drug'))"), 'like', $like)
            //fn ($query) => $query->where('name', 'like', $like)->orWhereRaw('LOWER(`defaults`->\'$."drug"\') like ?', $like)
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
