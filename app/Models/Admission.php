<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Admission extends Model
{
    use HasFactory;

    public function scopeJoinPatients(Builder $query): Builder
    {
        return $query->join('patients', function ($join) {
            $join->on('admissions.patient_id', '=', 'patients.id')->where('is_voided', false);
        });
    }

    public static function inCareOnDate(Team $team, Carbon $date): Collection
    {
        return static::where('team_id', $team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->where('is_voided', false)
            ->whereDate('date_admitted_at', '<=', $date->format('Y-m-d'))
            ->where(function ($query) use ($date) {
                $query->where(function ($query) {
                    $query->whereNull('dispositioned_at')->where('disposition', 'Pending');
                })
                    ->orWhere(function ($query) use ($date) {
                        $query->whereDate('dispositioned_at', '>=', $date->format('Y-m-d'))->where('disposition', '!=', 'Pending');
                    });
            })
            ->orderBy('admissions.case_year')
            ->orderBy('admissions.case_id')
            ->get();
    }

    /**
     * Query within a date range.
     */
    public function scopeDateRange(Builder $query, $start, $end, string $field = 'patients.date_admitted_at'): Builder
    {
        if (is_null($start) && is_null($end)) {
            return $query;
        } elseif (is_null($start)) {
            return $query->whereDate($field, '<=', $end);
        } elseif (is_null($end)) {
            return $query->whereDate($field, '>=', $start);
        } elseif ($start === $end) {
            return $query->whereDate($field, '=', $start);
        }

        //$field = protect_identifiers($field);

        return $query->whereRaw("$field between '$start' and '$end 23:59:59.999999'");
    }
}
