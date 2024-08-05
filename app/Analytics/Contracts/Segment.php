<?php

namespace App\Analytics\Contracts;

use Illuminate\Database\Eloquent\Builder;

abstract class Segment
{
    protected $query;

    protected $parameters;

    public function __construct(Builder $query, $parameters = [])
    {
        $this->query = $query;
        $this->parameters = $parameters;
    }
}
