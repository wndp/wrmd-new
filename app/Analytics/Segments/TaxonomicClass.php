<?php

namespace App\Analytics\Segments;

use App\Analytics\Contracts\Segment;

class TaxonomicClass extends Segment
{
    public function handle()
    {
        $class = $this->parameters[0];

        if (! collect($this->query->getQuery()->joins)->contains('table', 'species')) {
            $this->query->joinTaxa();
        }

        $this->query->where('class', $class);
    }
}
