<?php

namespace App\Events;

use App\Models\Patient;
use Illuminate\Queue\SerializesModels;

class PatientReplicated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Patient $rootPatient, public Patient $newPatient)
    {
        $this->rootPatient = $rootPatient;
        $this->newPatient = $newPatient;
    }
}
