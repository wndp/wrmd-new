<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface Weighable
{
    /**
     * Get the models weight.
     *
     * @return string
     */
    public function summaryWeight(): Attribute;

    /**
     * Get the models weight unit.
     *
     * @return string
     */
    public function summaryWeightUnitId(): Attribute;
}
