<?php

namespace App\Analytics;

use App\Analytics\Concerns\HandleAggregates;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategorizedCollection extends Collection
{
    use HandleAggregates;

    public function padCategories(Categories $categories)
    {
        $data = $this->sortBy(function ($row) {
            return Str::lower($row->subgroup);
        });

        if ($data->count() === $categories->count()) {
            return $data->values();
        }

        $union = $categories->mapWithKeys(function ($category) {
            return [Str::slug($category) => new DataSet([
                'subgroup' => $category,
                'aggregate' => 0,
            ])];
        });

        return $data->keyBy(function ($row) {
            return Str::slug($row->subgroup);
        })
            ->union($union)
            ->sortBy(function ($row) {
                return Str::lower($row->subgroup);
            })
            ->values();
    }
}
