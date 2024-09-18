<?php

namespace App;

interface Weighable
{
    /**
     * Get the models weight.
     *
     * @return string
     */
    public function getSummaryWeightAttribute();

    /**
     * Get the models weight unit.
     *
     * @return string
     */
    public function getSummaryWeightUnitIdAttribute();
}
