<?php

namespace App\Analytics;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TreeMapSeries
{
    protected $data;

    protected $series = [];

    protected $treeLevels;

    public function __construct(Collection $data, array $treeLevels)
    {
        $this->data = $data;
        $this->treeLevels = $treeLevels;
    }

    /**
     * Set the series for a multilevel tree map.
     */
    public function setTreeMapSeries()
    {
        $this->data->groupBy($this->treeLevels)->sortBy(function ($collection, $key) {
            return $key;
        })->each(function ($collection, $key) {
            $this->series[] = [
                'name' => Str::title($this->treeLevels[0]).': '.$key,
                'id' => strtolower($key),
                'value' => $collection->flatten()->count(),
            ];

            $this->setChildLevelInTreeMap($collection, $key, 1);
        });

        return $this;
    }

    /**
     * Set the child levels for a multilevel tree map.
     */
    protected function setChildLevelInTreeMap(Collection $collection, string $parent, int $index): void
    {
        $collection->sortBy(function ($collection, $key) {
            return $key;
        })
            ->each(function ($subCollection, $classification) use ($parent, $index) {
                $id = strtolower($parent.'_'.$classification);

                if ($classification) {
                    $this->series[] = [
                        'name' => Str::title($this->treeLevels[$index]).': '.$classification,
                        'id' => $id,
                        'parent' => strtolower($parent),
                        'value' => $subCollection->flatten()->count(),
                    ];
                }

                if ($subCollection->first() instanceof \Illuminate\Support\Collection) {
                    $this->setChildLevelInTreeMap($subCollection, $id, $index + 1);
                } elseif ($subCollection->first() instanceof \App\Models\Admission) {
                    $this->setAdmissionLevelInTreeMap($subCollection, $id);
                }
            });
    }

    /**
     * Set the child levels for a multilevel tree map.
     */
    protected function setAdmissionLevelInTreeMap(Collection $collection, $parent): void
    {
        $admissions = $collection->reduce(function ($carry, $admission) {
            $carry = array_merge($carry, [$admission->case_number.' :: '.$admission->common_name]);

            return $carry;
        }, []);

        $this->series[] = [
            'name' => implode(', ', $admissions),
            'parent' => $parent,
            'value' => count($admissions),
        ];
    }

    public function getSeries()
    {
        return $this->series;
    }
}
