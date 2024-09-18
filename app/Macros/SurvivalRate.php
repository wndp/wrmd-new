<?php

namespace App\Macros;

use Illuminate\Support\Collection;

class SurvivalRate
{
    public function __invoke()
    {
        return function ($dispositions, $after24Hours = false) {
            if ($dispositions instanceof Collection) {
                $r = $dispositions->sum('released');
                $p = $dispositions->sum('pending');
                $t = $dispositions->sum('transferred');
                $doa = $dispositions->sum('doa');
                $d_in = $dispositions->sum('died_in_24');
                $e_in = $dispositions->sum('euthanized_in_24');
                $total = $dispositions->sum('total');
            } else {
                $r = data_get($dispositions, 'released', 0);
                $p = data_get($dispositions, 'pending', 0);
                $t = data_get($dispositions, 'transferred', 0);
                $doa = data_get($dispositions, 'doa', 0);
                $d_in = data_get($dispositions, 'died_in_24', 0);
                $e_in = data_get($dispositions, 'euthanized_in_24', 0);
                $total = data_get($dispositions, 'total', 0);
            }

            $numerator = $p + $r + $t;

            $denominator = $after24Hours
                ? $total - $doa - $d_in - $e_in
                : $total - $doa;

            return $denominator === 0 ? 0.0 : round((100 * $numerator) / $denominator, 2);
        };
    }
}
