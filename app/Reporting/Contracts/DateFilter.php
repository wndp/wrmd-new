<?php

namespace App\Reporting\Contracts;

abstract class DateFilter extends Filter
{
    protected $attribute;

    public function __construct($attribute = 'admitted_at')
    {
        $this->attribute = $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function component(): string
    {
        return 'DateFilter';
    }
}
