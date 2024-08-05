<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;

class ClassificationTagsRoots extends Chart
{
    use HandlePieData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handlePieData();
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, prediction as subgroup')
            ->joinPatients()
            ->join('patient_model_predictions', 'patients.id', '=', 'patient_model_predictions.patient_id')
            ->where('category', $this->filters->category)
            ->orderByDesc('aggregate')
            ->groupBy('prediction');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return $this->aggregateData($query->get());
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, prediction as subgroup')
            ->joinPatients()
            ->join('patient_model_predictions', 'patients.id', '=', 'patient_model_predictions.patient_id')
            ->where('category', $this->filters->category)
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->orderByDesc('aggregate')
            ->groupBy('prediction');

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function aggregateData($data)
    {
        $classificationClass = "App\Domain\Classifications\\{$this->filters->category}";

        return $data->pluck('aggregate', 'subgroup')
            ->mapToGroups(function ($aggregate, $prediction) use ($classificationClass) {
                return [$classificationClass::root($prediction) => $aggregate];
            })
            ->filter(function ($collection, $root) {
                return ! empty($root);
            })
            ->map(function ($group, $root) {
                return [
                    'subgroup' => $root,
                    'aggregate' => $group->sum(),
                ];
            });
    }
}
