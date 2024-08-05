<?php

namespace App\Analytics\Segments;

use App\Analytics\Contracts\Segment;

class CircumstanceOfAdmission extends Segment
{
    public function handle()
    {
        $term = $this->parameters[0];

        if (! collect($this->query->getQuery()->joins)->contains('table', 'patient_model_predictions')) {
            $this->query->join('patient_model_predictions', 'patients.id', '=', 'patient_model_predictions.patient_id')
                ->where('category', 'CircumstancesOfAdmission');
        }

        $this->query->where('prediction', $term);
    }
}
