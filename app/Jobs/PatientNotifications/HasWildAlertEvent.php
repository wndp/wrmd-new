<?php

namespace App\Jobs\PatientNotifications;

use App\Events\NotifyPatient;
use App\Models\Patient;
use App\Models\Team;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HasWildAlertEvent implements ShouldQueue
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
        if (false) {
            NotifyPatient::dispatch(
                $this->patient,
                __('Patient Frozen'),
                __('This patient has been frozen and can not be updated.')
            );
        }
    }
}
