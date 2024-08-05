<?php

namespace App\Analytics\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

trait HandleAggregates
{
    /**
     * Get the sum of the segmented aggregate values.
     */
    public function sumSeriesGroup(callable $valueExtractor = null): static
    {
        return new static(
            $this->map(function ($group, $name) use ($valueExtractor) {
                return [
                    'name' => $name,
                    'y' => $this->deriveY($group, $valueExtractor),
                ];
            })
        );
    }

    public function sumSeriesGroups(callable $valueExtractor = null)
    {
        return new static(
            $this->map(function ($collection, $name) use ($valueExtractor) {
                return $collection->map(function ($group) use ($name, $valueExtractor) {
                    return [
                        'name' => $name,
                        'y' => $this->deriveY($group, $valueExtractor),
                    ];
                });
            })
        );
    }

    public function deriveY($data, $valueExtractor = null)
    {
        if (is_callable($valueExtractor)) {
            return $valueExtractor($data);
        }

        if ($data instanceof Collection) {
            return $data->sum('aggregate');
        }

        if ($data instanceof Fluent && $data->offsetExists('aggregate')) {
            return $data->aggregate;
        }

        return 0;
    }
}
