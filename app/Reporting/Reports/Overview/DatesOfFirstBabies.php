<?php

namespace App\Reporting\Reports\Overview;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\IncludedTaxonomies;
use App\Reporting\Filters\SpeciesGrouping;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatesOfFirstBabies extends Report
{
    /**
     * {@inheritdoc}
     */
    public function title(): string
    {
        return __('Dates of First Babies by Year');
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return 'Dates of First Babies by Year report gives you a 5 year comparison of the date the first "baby" of the year came into care. Baby refers to any patient that is not Adult, Sub-adult, Juvenile or that has Years in the Initial Exam Age Unit. If the Age is left blank it will not be used.';
    }

    /**
     * {@inheritdoc}
     */
    public function viewPath(): string
    {
        return 'reports.overview.dates-of-first-babies-by-year';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect([
            new IncludedTaxonomies(),
            new SpeciesGrouping(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function data(): array
    {
        $this->years = Admission::yearsInAccount($this->team->id)->slice(0, 5)->reverse();
        $includedTaxonomies = $this->getAppliedFilterValue(IncludedTaxonomies::class);

        return [
            'map' => [
                'amphibian' => in_array('Amphibia', $includedTaxonomies) ? $this->admissionDates('Amphibia') : [],
                'bird' => in_array('Aves', $includedTaxonomies) ? $this->admissionDates('Aves') : [],
                'mammal' => in_array('Mammalia', $includedTaxonomies) ? $this->admissionDates('Mammalia') : [],
                'reptile' => in_array('Reptilia', $includedTaxonomies) ? $this->admissionDates('Reptilia') : [],
                'unidentified' => in_array('Unidentified', $includedTaxonomies) ? $this->admissionDates('Unidentified') : [],
            ],
            'years' => $this->years,
        ];
    }

    private function admissionDates($class = null)
    {
        [
            $examTypeID,
            $avesAgeUnits,
            $mammaliaAgeUnits,
            $amphibiaAgeUnits,
            $reptiliaAgeUnits,
            $chronologicalAgeUnits,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::EXAM_TYPES->value, AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE->value],
            [AttributeOptionName::EXAM_AVES_AGE_UNITS->value, AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE->value],
            [AttributeOptionName::EXAM_MAMMALIA_AGE_UNITS->value, AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE->value],
            [AttributeOptionName::EXAM_AMPHIBIA_AGE_UNITS->value, AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE->value],
            [AttributeOptionName::EXAM_REPTILIA_AGE_UNITS->value, AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE->value],
            [AttributeOptionName::EXAM_CHRONOLOGICAL_AGE_UNITS->value, AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE->value],
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->select('date_admitted_at', 'common_name', 'taxon_id')
            ->joinPatients()
            ->join('exams', 'patients.id', '=', 'exams.patient_id')
            ->where('exams.exam_type_id', $examTypeID)
            ->whereIn('case_year', $this->years)
            ->whereNotNull('age_unit_id')
            ->orderBy('common_name');

        if ($class) {
            $ageUnits = match (Str::lower($class)) {
                'aves' => $avesAgeUnits,
                'mammalia' => $mammaliaAgeUnits,
                'amphibia' => $amphibiaAgeUnits,
                'reptilia' => $reptiliaAgeUnits,
            };

            $query
                ->joinTaxa()
                ->whereIn('class', Arr::wrap($class))
                ->whereNotIn('age_unit_id', array_merge($ageUnits, $chronologicalAgeUnits));
        } else {
            $query
                ->whereNotIn('age_unit_id', array_merge(
                    $avesAgeUnits,
                    $mammaliaAgeUnits,
                    $amphibiaAgeUnits,
                    $reptiliaAgeUnits,
                    $chronologicalAgeUnits
                ));
        }

        return $query
            ->get()
            ->groupBy($this->getAppliedFilterValue(SpeciesGrouping::class))
            ->keyBy(function ($collection) {
                return $collection->first()->common_name;
            })
            ->map(function ($collection) {
                return $collection->map(function ($admission) {
                    return Carbon::parse($admission->admitted_at);
                })
                    ->groupBy(function ($admittedAt) {
                        return $admittedAt->year;
                    })
                    ->map(function ($collection) {
                        return $collection->min();
                    });
            });
    }
}
