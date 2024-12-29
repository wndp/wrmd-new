<?php

namespace App\Concerns;

use App\Enums\Ability;
use App\Enums\Entity;
use App\Enums\SettingKey;
use App\Models\CareLog;
use App\Models\Exam;
use App\Models\LabReport;
use App\Models\NutritionPlan;
use App\Models\Patient;
use App\Models\PatientLocation;
use App\Models\Prescription;
use App\Models\User;
use App\Summarizable;
use App\Support\Timezone;
use App\Support\Wrmd;
use Illuminate\Support\Collection;

trait GetsCareLogs
{
    public function getCareLogs(Patient $patient, User $user, bool $descending = true): Collection
    {
        //$records = event(new GetCareLogs($patient->id));
        $records[] = CareLog::where('patient_id', $patient->id)->with([
            'user',
            'weightUnit',
            'temperatureUnit',
        ])->get();
        $records[] = PatientLocation::where('patient_id', $patient->id)->get();
        $records[] = Exam::where('patient_id', $patient->id)->get();
        $records[] = Prescription::where('patient_id', $patient->id)->get();
        $records[] = NutritionPlan::where('patient_id', $patient->id)->get();
        $records[] = LabReport::where('patient_id', $patient->id)->get();

        return collect($records)
            ->collapse()
            ->filter(fn ($model) => $model instanceof Summarizable)
            ->map(function ($model) use ($user) {
                $localDateTime = Timezone::convertFromUtcToLocal($model->{$model->summary_date}, $user->currentTeam);

                return (object) [
                    'id' => $model instanceof CareLog ? $model->id : null,
                    'logged_at_date_time' => $localDateTime,
                    'logged_at_timestamp' => $model->{$model->summary_date}->timestamp,
                    'logged_at_for_humans' => $localDateTime->toFormattedDayDateString(),
                    'type' => Entity::tryFrom($model->getTable())->label(),
                    'body' => $model->summary_body,
                    //'edit_action' => $model->editAction(),
                    'can_edit' => $model instanceof CareLog ? $this->canEdit($user, $model) : false,
                    'can_delete' => $model instanceof CareLog ? $this->canDelete($user, $model) : false,
                    'user' => $model instanceof CareLog ? $model->user : null,
                    'model' => $model,
                ];
            })
            ->sortBy('logged_at_timestamp', SORT_NUMERIC, $descending)
            ->values();
    }

    /**
     * Can the current user edit the log entry?
     *
     * @param  mixed  $record
     */
    private function canEdit($user, $record): bool
    {
        return $user->can(Ability::MANAGE_CARE_LOGS->value)
            || (Wrmd::settings(SettingKey::LOG_ALLOW_AUTHOR_EDIT) && (int) $record->user_id === (int) $user->id);
    }

    /**
     * Can the current user delete the log entry?
     *
     * @param  mixed  $record
     */
    private function canDelete($user, $record): bool
    {
        return $user->can(Ability::MANAGE_CARE_LOGS->value)
            || (Wrmd::settings(SettingKey::LOG_ALLOW_AUTHOR_EDIT) && (int) $record->user_id === (int) $user->id);
    }
}
