<?php

namespace App\Support;

use Illuminate\Support\Collection;

class WeightsCollection extends Collection
{
    public function toGrams()
    {
        return $this->map(function ($subject) {
            $subject->weight = Weight::toGrams($subject->weight, $subject->unit);
            $subject->unit = 'g';

            return $subject;
        });
    }

    public function getLastWeight()
    {
        return $this->last();
    }
}
