<?php

namespace App\Jobs;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;
use App\Models\Incident;
use App\Models\Patient;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssociatePatientToIncident implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Team $team, public Patient $patient, public string $incidentId)
    {
        $this->team = $team;
        $this->patient = $patient;
        $this->incidentId = $incidentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $incident = Incident::firstWhere([
            'id' => $this->incidentId,
            'team_id' => $this->team->id,
        ]);

        if ($incident) {
            [$incidentStatusResolvedId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
                AttributeOptionName::HOTLINE_STATUSES->value,
                AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED->value,
            ]);

            $admission = Admission::custody($this->team, $this->patient);

            $incident->patient()->associate($this->patient);
            $incident->resolved_at = Carbon::now();
            $incident->resolution = __('Admitted as patient :caseNumber', ['caseNumber' => $admission->case_number]);
            $incident->incident_status_id = $incidentStatusResolvedId;
            $incident->save();
        }
    }
}
