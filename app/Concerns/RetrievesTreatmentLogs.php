<?php

namespace App\Concerns;

use App\Domain\Contracts\Summarizable;
use App\Domain\DailyTasks\Prescriptions\Prescription;
use App\Domain\Patients\Exam;
use App\Domain\Patients\Patient;
use App\Domain\Patients\PatientLocation;
use App\Domain\Patients\TreatmentLog;
use App\Domain\Timezone;
use App\Domain\Users\User;
use App\Events\GetTreatmentLogs;
use Illuminate\Support\Collection;

trait RetrievesTreatmentLogs
{
    /**
     * Get the treatment logs for a patient.
     */
    public function getTreatmentLogs(Patient $patient, User $user, bool $descending = true): Collection
    {
        $records = event(new GetTreatmentLogs($patient->id));
        $records[] = TreatmentLog::where('patient_id', $patient->id)->with('user')->get();
        $records[] = PatientLocation::where('patient_id', $patient->id)->get();
        $records[] = Exam::where('patient_id', $patient->id)->get();
        $records[] = Prescription::where('patient_id', $patient->id)->get();

        return collect($records)
            ->collapse()
            ->filter(fn ($model) => $model instanceof Summarizable)
            ->map(function ($model) use ($user) {
                $localDateTime = Timezone::convertToLocal($model->{$model->summary_date}, $user->currentAccount);

                return (object) [
                    'id' => $model instanceof TreatmentLog ? $model->id : null,
                    'logged_at_date_time' => $localDateTime,
                    'logged_at_timestamp' => $model->{$model->summary_date}->timestamp,
                    'logged_at_for_humans' => $localDateTime->toFormattedDayDateString(),
                    'type' => fields()->getTableValue($model->getTable()),
                    'body' => nl2br($model->summary_body),
                    'edit_action' => $model->editAction(),
                    'can_edit' => $model instanceof TreatmentLog ? $this->canEdit($user, $model) : false,
                    'can_delete' => $model instanceof TreatmentLog ? $this->canDelete($user, $model) : false,
                    'user' => $model instanceof TreatmentLog ? $model->user : null,
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
        return $user->can('manage-treatment-logs')
            || (settings('logAllowAuthorEdit') && (int) $record->user_id === (int) $user->id);
    }

    /**
     * Can the current user delete the log entry?
     *
     * @param  mixed  $record
     */
    private function canDelete($user, $record): bool
    {
        return $user->can('manage-treatment-logs')
            || (settings('logAllowAuthorEdit') && (int) $record->user_id === (int) $user->id);
    }
}
