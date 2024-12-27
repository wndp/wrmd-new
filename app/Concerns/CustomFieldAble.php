<?php

namespace App\Concerns;

use App\Models\CustomValue;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait CustomFieldAble
{
    public function customValues(): MorphOne
    {
        return $this->morphOne(CustomValue::class, 'customable');
    }
}
