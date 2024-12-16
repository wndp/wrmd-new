<?php

namespace App\Support;

use App\Options\Options;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AttributeOptionsCollection extends Collection
{
    public function optionsToSelectable(): array
    {
        return $this->mapWithKeys(fn ($array, $key) => [
            Str::of($key)->lower()->camel()->toString().'Options' => Options::arrayToSelectable($array),
        ])->toArray();
    }

    public function map(callable $callback)
    {
        $result = parent::map($callback);

        return new static($result);
    }
}
