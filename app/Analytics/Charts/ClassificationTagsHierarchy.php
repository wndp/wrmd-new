<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Contracts\Chart;
use App\Analytics\FlatHierarchy;
use Illuminate\Support\Str;

class ClassificationTagsHierarchy extends Chart
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $segment = $this->filters->segments[0];

        $counts = $this->query($segment)
            ->groupBy('prediction')
            ->map(function ($group, $term) {
                return $group->count();
            });

        $categoryForHumans = Str::snake($this->filters->category, '');
        $classificationClass = "App\Domain\Classifications\\{$this->filters->category}";

        $this->series = (new FlatHierarchy($categoryForHumans, $classificationClass::tree()))
            ->handle()
            ->transform(function ($item) use ($counts, $classificationClass) {
                if ($counts->has($item['name'])) {
                    if (empty($tree = $classificationClass::tree($item['name']))) {
                        $item['value'] = $counts->get($item['name']);
                    } else {
                        $nodes = $classificationClass::nodes($classificationClass::tree($item['name']));
                        $item['value'] = $counts->intersectByKeys(array_flip($nodes))->sum();
                    }
                }

                return $item;
            });
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('prediction')
            ->selectAdmissionKeys()
            ->joinPatients()
            ->join('patient_model_predictions', 'patients.id', '=', 'patient_model_predictions.patient_id')
            ->where('category', $this->filters->category);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
