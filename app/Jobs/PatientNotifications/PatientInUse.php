<?php

namespace App\Jobs\PatientNotifications;

use App\Events\NotifyPatient;
use App\Models\Patient;
use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PatientInUse implements ShouldQueue
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
    public function __construct(public Team $team, public Patient $patient, public User $user)
    {
        $this->team = $team;
        $this->patient = $patient;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lockedTo = $this->patient->lockedTo;

        if ($lockedTo && $this->user->isNot($lockedTo)) {
            NotifyPatient::dispatch(
                $this->patient,
                __('Heads Up!'),
                __(':userName is currently editing this patient!', ['userName' => $lockedTo->name]),
                'danger'
            );
        }
    }
}
