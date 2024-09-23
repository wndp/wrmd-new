<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface Summarizable
{
    /**
     * Get a presentable representation of the models attributes.
     *
     * @return string
     */
    public function summaryBody(): Attribute;

    /**
     * Get the models attribute which represents the date to be used to order the summary.
     *
     * @return string
     */
    public function summaryDate(): Attribute;
}
