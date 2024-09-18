<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Contracts\Chart;
use App\Analytics\Series;

class TaxonomyTreeMap extends Chart
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $segment = $this->filters->segments[0];

        $this->series = (new Series())
            ->usingCategories(['class', 'order', 'family', 'genus', 'species'])
            ->addTreeMapSeriesData($this->query($segment));
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('taxa.*', 'patients.common_name')
            ->selectAdmissionKeys()
            ->joinPatients()
            ->joinTaxa();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
