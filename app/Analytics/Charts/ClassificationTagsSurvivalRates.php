<?php

namespace App\Analytics\Charts;

use App\Analytics\Categories;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Analytics\SurvivalRateCollection;
use App\Models\Admission;

class ClassificationTagsSurvivalRates extends Chart
{
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $classificationClass = "App\Domain\Classifications\\{$this->filters->category}";
        $this->categories = (new Categories($classificationClass::terms()))->sort()->values();

        foreach ($this->filters->segments as $segment) {
            $this->query($segment)
                ->padCategories($this->categories)
                ->calculateSurvivalRates()
                ->includingFirst24Hrs(function ($data) use ($segment) {
                    $this->series->addSeries(
                        $this->formatSeriesName($segment, 'Including first 24 hours', $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                        $data
                    );
                })
                ->afterFirst24Hrs(function ($data) use ($segment) {
                    $this->series->addSeries(
                        $this->formatSeriesName($segment, 'After first 24 hours', $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                        $data
                    );
                });

            if ($this->filters->compare) {
                $this->compareQuery($segment)
                    ->padCategories($this->categories)
                    ->calculateSurvivalRates()
                    ->includingFirst24Hrs(function ($data) use ($segment) {
                        $this->series->addSeries(
                            $this->formatSeriesName($segment, 'Including first 24 hours', $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                            $data
                        );
                    })
                    ->afterFirst24Hrs(function ($data) use ($segment) {
                        $this->series->addSeries(
                            $this->formatSeriesName($segment, 'After first 24 hours', $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                            $data
                        );
                    });
            }
        }

        $this->categories = $this->categories->forget(
            $this->series->trimEmptyCategories()
        )->values();
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('prediction as subgroup')
            ->selectRaw('count(*) as `total`')
            ->selectRaw("sum(if(`disposition` = 'Pending', 1, 0)) as `pending`")
            ->selectRaw("sum(if(`disposition` = 'Released', 1, 0)) as `released`")
            ->selectRaw("sum(if(`disposition` = 'Transferred', 1, 0)) as `transferred`")
            ->selectRaw("sum(if(`disposition` = 'Dead on arrival', 1, 0)) as `doa`")
            ->selectRaw("sum(if(`disposition` = 'Died in 24hr', 1, 0)) as `died_in_24`")
            ->selectRaw("sum(if(`disposition` = 'Euthanized in 24hr', 1, 0)) as `euthanized_in_24`")
            ->joinPatients()
            ->join('patient_model_predictions', 'patients.id', '=', 'patient_model_predictions.patient_id')
            ->where('category', $this->filters->category)
            ->groupBy('prediction')
            ->orderBy('prediction');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return new SurvivalRateCollection($query->get()->mapInto(DataSet::class));
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('prediction as subgroup')
            ->selectRaw('count(*) as `total`')
            ->selectRaw("sum(if(`disposition` = 'Pending', 1, 0)) as `pending`")
            ->selectRaw("sum(if(`disposition` = 'Released', 1, 0)) as `released`")
            ->selectRaw("sum(if(`disposition` = 'Transferred', 1, 0)) as `transferred`")
            ->selectRaw("sum(if(`disposition` = 'Dead on arrival', 1, 0)) as `doa`")
            ->selectRaw("sum(if(`disposition` = 'Died in 24hr', 1, 0)) as `died_in_24`")
            ->selectRaw("sum(if(`disposition` = 'Euthanized in 24hr', 1, 0)) as `euthanized_in_24`")
            ->joinPatients()
            ->join('patient_model_predictions', 'patients.id', '=', 'patient_model_predictions.patient_id')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->where('category', $this->filters->category)
            ->groupBy('prediction')
            ->orderBy('prediction');

        $this->withSegment($query, $segment);

        return new SurvivalRateCollection($query->get()->mapInto(DataSet::class));
    }
}
