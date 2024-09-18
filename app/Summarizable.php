<?php

namespace App;

interface Summarizable
{
    /**
     * Get a presentable representation of the models attributes.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function getSummaryBodyAttribute();

    /**
     * Get the models attribute which represents the date to be used to order the summary.
     *
     * @return string
     */
    public function getSummaryDateAttribute();
}
