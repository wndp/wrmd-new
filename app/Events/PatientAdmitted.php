<?php

namespace App\Events;

use App\Models\Patient;
use App\Models\Team;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class PatientAdmitted
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Team $team, public Patient $patient, public ?array $requestData = null)
    {
        $this->team = $team;
        $this->patient = $patient;
        $this->requestData = $requestData ?? array_filter(Request::all());
    }
}
