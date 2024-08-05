<?php

namespace App\Analytics\Segments;

use App\Analytics\Contracts\Segment;
use App\Domain\Species\SpeciesPreciseSearchQuery;

class CommonName extends Segment
{
    public function handle()
    {
        $commonName = $this->parameters[0];

        $speciesSearch = (new SpeciesPreciseSearchQuery())($commonName, false);

        if ($speciesSearch->isEmpty()) {
            $this->query->where('common_name', $commonName);
        } else {
            $this->query->where('taxon_id', $speciesSearch->first()['speciesId']);
        }
    }
}
