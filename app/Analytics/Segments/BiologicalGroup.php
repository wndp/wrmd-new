<?php

namespace App\Analytics\Segments;

use App\Analytics\Contracts\Segment;

class BiologicalGroup extends Segment
{
    public function handle()
    {
        $biologicalGroup = $this->parameters[0];

        if (! collect($this->query->getQuery()->joins)->contains('table', 'species')) {
            $this->query->joinTaxa();
        }

        $this->query->where('lay_groups', 'like', "%$biologicalGroup%");
    }
}
