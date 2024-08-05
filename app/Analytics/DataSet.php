<?php

namespace App\Analytics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;

class DataSet extends Fluent
{
    public function __construct($attributes = [])
    {
        if ($attributes instanceof Model) {
            foreach ($attributes->getAttributes() as $key => $value) {
                $this->attributes[$key] = $value;
            }
        } else {
            parent::__construct($attributes);
        }
    }
}
