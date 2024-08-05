<?php

namespace App\Analytics\Contracts;

use Illuminate\Support\Number as IlluminateNumber;

abstract class Number extends Analytic
{
    public $difference;

    public $change;

    public $now;

    public $prev;

    /**
     * Calculate the percentage difference between two numbers.
     *
     * @param  float  $a
     * @param  float  $b
     * @return void
     */
    protected function calculatePercentageDifference($a, $b)
    {
        if (is_numeric($a) && is_numeric($b)) {
            $max = max($a, $b);
            $min = min($a, $b);

            $diff = $max != 0 ? ($max - $min) / $max * 100 : 0;
            $this->change = $a == $b ? 'same' : ($a > $b ? 'up' : 'down');
            $this->difference = IlluminateNumber::significantFigures($diff, 3);

            return;
        }

        $this->change = null;
        $this->difference = null;
    }
}
