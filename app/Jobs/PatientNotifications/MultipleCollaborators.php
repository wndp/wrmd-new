<?php

namespace App\Jobs\PatientNotifications;

use App\Events\NotifyPatient;
use App\Models\Patient;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class MultipleCollaborators implements ShouldQueue
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
        $tomorrow = Carbon::tomorrow($this->team->settingsStore()->get('timezone'));

        Cache::remember($this->fingerPrint(), $tomorrow, function () {
            return Admission::where('patient_id', $this->patient->id)
                ->where('team_id', '!=', $this->team->id)
                ->with('team')
                ->get();
        })->whenNotEmpty(function ($sharedAdmissions) {
            $collaborators = $sharedAdmissions->map(
                fn ($admission) => "{$admission->team->organization} ($admission->case_number)"
            );

            $last = $collaborators->pop();

            if (! empty($collaborators)) {
                $title = 'Multiple Collaborators!';
                $text = 'This patient is shared with '.$collaborators->join(', ', ', and ');

                NotifyPatient::dispatch($this->patient, $title, $text);
            }
        });
    }

    public function fingerPrint()
    {
        return "MultipleCollaborators.{$this->team->id}.{$this->patient->id}";
    }
}
