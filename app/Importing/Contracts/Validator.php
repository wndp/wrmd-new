<?php

namespace App\Importing\Contracts;

use Illuminate\Support\Collection;

abstract class Validator
{
    protected $failed;

    public function __construct()
    {
        $this->failed = new Collection;
    }

    /**
     * Validate records to be imported.
     *
     * @param  Illuminate\Support\Collection  $collection
     */
    abstract public function validate(Collection $collection, string $wrmdColumn): bool;

    /**
     * Get the failed columns.
     */
    public function getFailed()
    {
        return $this->failed;
    }
}
