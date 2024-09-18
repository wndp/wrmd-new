<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Categories;
use App\Analytics\CategorizedCollection;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;

class ClassificationTags extends Chart
{
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $classificationClass = "App\Domain\Classifications\\{$this->filters->category}";

        $this->categories = (new Categories($classificationClass::terms()))->sortBy(function ($term) {
            return strtolower($term);
        })->values();

        foreach ($this->filters->segments as $segment) {
            $this->series->addSeries(
                $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                $this->query($segment)
                    ->padCategories($this->categories)
                    ->sumSeriesGroup()
            );

            if ($this->filters->compare) {
                $this->series->addSeries(
                    $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                    $this->compareQuery($segment)
                        ->padCategories($this->categories)
                        ->sumSeriesGroup()
                );
            }
        }

        $this->categories = $this->categories->forget(
            $this->series->trimEmptyCategories()
        )->values();
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, prediction as subgroup')
            ->joinPatients()
            ->join('patient_model_predictions', 'patients.id', '=', 'patient_model_predictions.patient_id')
            ->where('category', $this->filters->category)
            ->orderBy('prediction')
            ->groupBy('prediction');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return new CategorizedCollection($query->get()->mapInto(DataSet::class));
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, prediction as subgroup')
            ->joinPatients()
            ->join('patient_model_predictions', 'patients.id', '=', 'patient_model_predictions.patient_id')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->where('category', $this->filters->category)
            ->groupBy('prediction')
            ->orderBy('prediction');

        $this->withSegment($query, $segment);

        return new CategorizedCollection($query->get()->mapInto(DataSet::class));
    }
}
