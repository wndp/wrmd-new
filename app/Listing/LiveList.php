<?php

namespace App\Listing;

use App\Caches\PatientSelector;
use App\Enums\Attribute;
use App\Models\Team;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use JsonSerializable;

abstract class LiveList implements JsonSerializable
{
    //use FiltersRecords;

    public $team;

    protected $year;

    protected $columns;

    protected $request;

    /**
     * Constructor.
     */
    public function __construct(Team $team)
    {
        $this->team = $team;

        $this->columns = $team->settingsStore()->get('listFields', [
            Attribute::PATIENT_LOCATIONS_FACILITY->value,
            Attribute::PATIENTS_BAND->value,
            Attribute::PATIENTS_DISPOSITION_ID->value,
            Attribute::PATIENTS_DISPOSITIONED_AT->value,
        ]);
    }

    /**
     * String-ify the report.
     *
     * @return array
     */
    public function __toString()
    {
        return $this->jsonSerialize();
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return Wrmd::humanize($this);
    }

    /**
     * Get the icon of the report.
     */
    public function icon(): string
    {
        return '';
    }

    /**
     * Return the data/cases to be displayed in the list.
     *
     * @return \Illuminate\Support\Collection | \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    abstract public function data();

    /**
     * Set the columns to select.
     *
     * @param  string|array  $columns
     * @return $this
     */
    public function select($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Set the year to restrict the list to.
     *
     * @param  string|int  $year
     * @return $this
     */
    public function year($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Set the request data.
     *
     * @return $this
     */
    public function withRequest(Request $request)
    {
        $this->request = new Fluent($request->all());

        return $this;
    }

    /**
     * Get the reports key. Used to generate links to the report.
     *
     * @return string
     */
    public function key()
    {
        return Wrmd::uriKey($this);
    }

    public function get()
    {
        $headers = $this->formatListHeaders();
        $rows = $this->formatListResults();
        $selected = PatientSelector::get();

        return compact('rows', 'headers', 'selected');
    }

    public function normalizeData($admissions)
    {
        return $admissions
            ->each(fn ($admission) => $admission->load('patient'))
            //->filter(fn ($admission) => $admission->patientIsNotVoided())
            ->map(function ($admission) {
                $data['patient_id'] = $admission->patient_id;
                $data['link'] = Wrmd::patientRoute($admission);
                $data['case_number'] = $admission->case_number;
                $data['admitted_at'] = $admission->patient->date_admitted_at->format(config('wrmd.date_format'));
                $data['common_name'] = $admission->patient->common_name;

                foreach ($this->columns as $tableColumn) {
                    [$table, $column] = explode('.', $tableColumn);

                    $value = $admission->{$column};

                    if (is_null($value)) {
                        $data[$tableColumn] = $value;
                    } elseif (Str::endsWith($column, '_at') || $value instanceof Carbon) {
                        $data[$tableColumn] = Carbon::parse($value)->format(config('wrmd.date_format'));
                    } else {
                        $data[$tableColumn] = $value;
                    }
                }

                return $data;
            })
            ->values();
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->title(),
            'icon' => $this->icon(),
            'key' => $this->key(),
        ];
    }

    /**
     * Format the list's headers to translated values.
     *
     * @return \App\Domain\Locality\FieldsCollection
     */
    public function formatListHeaders()
    {
        return array_map(function ($name) {
            $attribute = Attribute::tryFrom($name);
            return [
                'label' => $attribute->label(),
                'key' => $name
            ];
        }, $this->columns);

        // return fields()->getLabels()
        //     ->intersectByKeys(array_flip($this->columns))
        //     ->transform(function ($label, $key) {
        //         return compact('label', 'key');
        //     })
        //     ->values();

        // $keyList = array_flip($this->columns);
        //return array_intersect_key(array_replace($keyList, fields()->getLabels()->all()), $keyList);
    }

    /**
     * Format the list's results to be presented.
     *
     * @return array
     */
    private function formatListResults()
    {
        $cases = $this->data();
        $data = $this->normalizeData($cases);

        return $this->normalizePagination($cases, $data);

        //return compact('pagination');
    }

    private function normalizePagination($cases, $data)
    {
        if ($cases instanceof LengthAwarePaginator) {
            return array_merge($cases->toArray(), compact('data'));
            // return [
            //     'total' => $cases->total(),
            //     'per_page' => $cases->perPage(),
            //     'current_page' => $cases->currentPage(),
            //     'last_page' => $cases->lastPage(),
            //     'from' => $cases->firstItem(),
            //     'to' => $cases->lastItem(),
            // ];
        }

        return array_merge(compact('data'), [
            'total' => $cases->count(),
        ]);

        // return [
        //     'total' => $cases->count(),
        //     //'per_page' => $cases->count(),
        //     // 'current_page' => 1,
        //     // 'last_page' => 1,
        //     // 'from' => 1,
        //     // 'to' => 1,
        // ];
    }
}
