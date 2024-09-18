<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RecentPatients
{
    public static function updated(Team $team)
    {
        return Patient::where('team_possession_id', $team->id)
            ->orderByDesc('patients.updated_at')
            ->limit(3)
            ->withWhereHas('admissions', fn ($q) => $q->where('team_id', $team->id))
            ->get()
            ->transform(
                fn ($patient) => [
                    'common_name' => $patient->common_name,
                    'case_number' => $patient->admissions->first()->case_number,
                    'url' => $patient->admissions->first()->url
                ]
            );
    }

    public static function admitted(Team $team)
    {
        return Patient::where('team_possession_id', $team->id)
            ->orderByDesc('patients.date_admitted_at')
            ->orderByDesc('patients.time_admitted_at')
            ->limit(3)
            ->withWhereHas('admissions', fn ($q) => $q->where('team_id', $team->id))
            ->get()
            ->transform(fn ($patient) => [
                'common_name' => $patient->common_name,
                'case_number' => $patient->admissions->first()->case_number,
                'url' => $patient->admissions->first()->url
            ]);
    }
}
