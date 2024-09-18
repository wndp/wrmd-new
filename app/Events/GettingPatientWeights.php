<?php

namespace App\Events;

use App\Models\Patient;
use Illuminate\Queue\SerializesModels;

class GettingPatientWeights
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Patient $patient)
    {
        $this->patient = $patient;
    }
}
