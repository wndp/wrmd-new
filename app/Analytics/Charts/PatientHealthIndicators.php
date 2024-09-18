<?php

namespace App\Analytics\Charts;

use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Enums\AttributeOptionName;
use App\Models\Admission;
use App\Models\AttributeOption;
use App\Temperature;
use App\Weight;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PatientHealthIndicators extends Chart
{
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $attributeOptions = AttributeOption::getDropdownOptions([
            AttributeOptionName::EXAM_SEXES->value,
            AttributeOptionName::EXAM_CHRONOLOGICAL_AGE_UNITS->value,
            AttributeOptionName::EXAM_WEIGHT_UNITS->value,
            AttributeOptionName::EXAM_BODY_CONDITIONS->value,
            AttributeOptionName::EXAM_DEHYDRATIONS->value,
            AttributeOptionName::EXAM_ATTITUDES->value,
            AttributeOptionName::EXAM_MUCUS_MEMBRANE_COLORS->value,
            AttributeOptionName::EXAM_MUCUS_MEMBRANE_TEXTURES->value,
            AttributeOptionName::EXAM_TEMPERATURE_UNITS->value
        ]);

        $ages = [];

        $this->categories = [
            'Sex',
            'Age',
            'Weight (g)',
            'Body Condition',
            'Dehydration',
            'Attitude',
            'Mucous Membrane Color',
            'Mucous Membrane Texture',
            'Temperature (c)',
        ];

        $this->yAxis = [
            ['categories' => $attributeOptions[AttributeOptionName::EXAM_SEXES->value]],
            ['categories' => $ages],
            ['min' => 0, 'tooltipValueFormat' => '{value} g'],
            ['categories' => $attributeOptions[AttributeOptionName::EXAM_BODY_CONDITIONS->value]],
            ['categories' => $attributeOptions[AttributeOptionName::EXAM_DEHYDRATIONS->value]],
            ['categories' => $attributeOptions[AttributeOptionName::EXAM_ATTITUDES->value]],
            ['categories' => $attributeOptions[AttributeOptionName::EXAM_MUCUS_MEMBRANE_COLORS->value]],
            ['categories' => $attributeOptions[AttributeOptionName::EXAM_MUCUS_MEMBRANE_TEXTURES->value]],
            ['min' => 0, 'tooltipValueFormat' => '{value} C'],
        ];

        $segment = $this->filters->segments[0];

        if (Str::contains($segment, 'All Patients')) {
            return;
        }

        $data = $this->query($segment)->filter(function ($data) {
            return ! empty(
                $data->sex.
                $data->age.
                $data->age_unit.
                $data->weight.
                $data->weight_unit.
                $data->bcs.
                $data->attitude.
                $data->dehydration.
                $data->temperature.
                $data->temperature_unit.
                $data->mm_color.
                $data->mm_texture
            );
        })
            ->each(function ($data) use ($ages) {
                $this->series->localMerge([[
                    'showInLegend' => false,
                    'label' => ['enabled' => false],
                    'shadow' => false,
                    'name' => substr($data->case_year, -2).'-'.$data->case_id.' '.$data->common_name,
                    'data' => [
                        array_search($data->sex, ExamOptions::$sexes),
                        $ages->search($data->age_unit),
                        $data->weight ? Weight::toGrams($data->weight, $data->weight_unit) : null,
                        array_search($data->bcs, ExamOptions::$bodyConditions),
                        array_search($data->dehydration, ExamOptions::$dehydrations),
                        array_search($data->attitude, ExamOptions::$attitudes),
                        array_search($data->mm_color, ExamOptions::$mmColors),
                        array_search($data->mm_texture, ExamOptions::$mmTextures),
                        $data->temperature ? Temperature::toCelsius($data->temperature, $data->temperature_unit) : null,
                    ],
                ]]);
            });
    }

    /**
     * Query for the requested data.
     */
    public function query($segment): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->addSelect('common_name', 'sex', 'weight', 'weight_unit', 'bcs', 'age', 'age_unit', 'attitude', 'dehydration', 'temperature', 'temperature_unit', 'mm_color', 'mm_texture')
            ->selectAdmissionKeys()
            ->joinPatients()
            ->joinIntakeExam();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get()->mapInto(DataSet::class);
    }

    /**
     * Query for the requested comparative data.
     *
     * @return \App\Domain\Analytics\ChronologicalCollection
     */
    // public function compareQuery($segment)
    // {
    //     $query = Admission::where('team_id', $this->team->id)
    //         ->selectRaw("count(*) as aggregate, date_admitted_at as date")
    //         ->addSelect('bcs as subgroup')
    //         ->joinPatients()
    //         ->joinIntakeExam()
    //         ->whereNotNull('bcs')
    //         ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
    //         ->groupBy('date')
    //         ->groupBy('subgroup')
    //         ->orderBy('date');

    //     $this->withSegment($query, $segment);

    //     return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    // }
}
