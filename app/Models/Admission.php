<?php

namespace App\Models;

use App\Caches\PatientSelector;
use App\Concerns\JoinsTablesToPatients;
use App\Concerns\QueriesDateRange;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Admission extends Model
{
    use HasFactory;
    use HasUuids;
    use QueriesDateRange;
    use JoinsTablesToPatients;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'team_id',
        'case_year',
        'case_id',
        'patient_id',
        'hash'
    ];

    protected $casts = [
        'team_id' => 'integer',
        'case_year' => 'integer',
        'case_id' => 'integer',
        'patient_id' => 'integer',
        'hash' => 'string',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'case_number',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected function caseNumber(): Attribute
    {
        return Attribute::get(
            fn ($value) => Arr::first(
                array_filter(Arr::wrap(event('caseNumberFormatted', $this))),
                null,
                substr($this->case_year, -2).'-'.$this->case_id
            )
        );
    }

    protected function url(): Attribute
    {
        return Attribute::get(
            fn ($value) => Wrmd::patientRoute($this)
        );
    }

    public function scopeJoinPatients(Builder $query): Builder
    {
        return $query->join('patients', function ($join) {
            $join->on('admissions.patient_id', '=', 'patients.id')->whereNull('voided_at');
        });
    }

    /**
     * Scope patients that are unrecognized to WRMD.
     */
    public function scopeWhereUnrecognized(Builder $query): Builder
    {
        $query->joinPatients();

        return (new Patient())->scopeWhereUnrecognized($query);
    }

    public static function custody(Team $team, Patient $patient): ?self
    {
        return static::where([
            'team_id' => $team->id,
            'patient_id' => $patient->id,
        ])
            ->first();
    }

    /**
     * Get the last accountPatient id for the give account and year.
     */
    public static function getLastCaseId(int $teamId, int $caseYear): int
    {
        return static::where([
            'team_id' => $teamId,
            'case_year' => $caseYear
        ])->count();
    }

    /**
     * Get the admissions that were in care on a given date.
     */
    public static function inCareOnDate(Team $team, Carbon $date): Collection
    {
        [$dispositionPendingId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
        ]);

        return static::where('team_id', $team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->whereDate('date_admitted_at', '<=', $date->format('Y-m-d'))
            ->where(
                fn ($query) =>
                $query->where(
                    fn ($query) =>
                    $query->whereNull('dispositioned_at')->where('disposition_id', $dispositionPendingId)
                )
                ->orWhere(
                    fn ($query) =>
                    $query->whereDate('dispositioned_at', '>=', $date->format('Y-m-d'))->where('disposition_id', '!=', $dispositionPendingId)
                )
            )
            ->orderBy('admissions.case_year')
            ->orderBy('admissions.case_id')
            ->get();
    }

    public static function yearsInAccount(int $teamId): Collection
    {
        return Cache::remember("yearsInAccount.{$teamId}", 1800, function () use ($teamId) {
            $years = self::where('team_id', $teamId)
                ->groupBy('case_year')
                ->orderByDesc('case_year')
                ->pluck('case_year')
                ->prepend((int) date('Y'))
                ->unique()
                ->all();

            return collect(range(max($years), min($years)));
        });
    }

    /**
     * If the PatientSelector contains results then limit the list to those patients.
     */
    public function scopeLimitToSelected($query): Builder
    {
        $selected = PatientSelector::get();

        if (count($selected) > 0) {
            $query->whereIn('admissions.patient_id', $selected);
        }

        return $query;
    }
}
