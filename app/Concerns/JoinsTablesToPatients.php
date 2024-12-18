<?php

namespace App\Concerns;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait JoinsTablesToPatients
{
    public function scopeJoinTaxa(Builder $query): Builder
    {
        $wildAlertDbName = config('database.connections.wildalert.database');

        return $query->join("$wildAlertDbName.taxa", 'patients.taxon_id', '=', "$wildAlertDbName.taxa.id");
    }

    public function scopeJoinIntakeExam(Builder $query): Builder
    {
        [$intakeExamTypeId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::EXAM_TYPES->value,
            AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE->value,
        ]);

        return $query->join('exams', fn ($q) => $q->on('patients.id', '=', 'exams.id')->where('exam_type_id', $intakeExamTypeId));
    }

    public function scopeLeftJoinCurrentLocation(Builder $query): Builder
    {
        return $query->leftJoin('patient_locations', function ($join) {
            $lastLocation = DB::table('patient_locations')
                ->select('id')
                ->whereRaw('patient_locations.patient_id = `patients`.`id`')
                ->whereNull('patient_locations.deleted_at')
                ->orderBy('moved_in_at', 'desc')
                ->limit(1)
                ->toSql();

            $join->on(DB::raw("($lastLocation)"), '=', 'patient_locations.id');
        });
    }
}
