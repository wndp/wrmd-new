<?php

namespace App\Jobs\PatientNotifications;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Events\NotifyPatient;
use App\Models\Patient;
use App\Models\Team;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExcessiveDaysInCare implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Team $team, public Patient $patient)
    {
        $this->team = $team;
        $this->patient = $patient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        [$dispositionPendingId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
        ]);

        $daysInCare = $this->patient->days_in_care;

        if ($this->patient->disposition_id === $dispositionPendingId && $daysInCare > 180) {
            NotifyPatient::dispatch(
                $this->patient,
                __('Days in Care'),
                __('This patient has been in care for :days days!', ['days' => $daysInCare]),
                'danger'
            );
        }
    }
}
