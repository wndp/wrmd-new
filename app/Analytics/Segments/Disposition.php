<?php

namespace App\Analytics\Segments;

use App\Analytics\Contracts\Segment;

class Disposition extends Segment
{
    public function handle()
    {
        $disposition = $this->parameters[0];

        if (in_array($disposition, ['Died', 'Euthanized'])) {
            $this->query->where('disposition', 'like', "%$disposition%");
        } else {
            $this->query->where('disposition', $disposition);
        }
    }
}
