<?php

namespace App\Analytics;

use App\Analytics\Concerns\HandleSeriesNames;
use JsonSerializable;

class MapIntoDataTableRow implements JsonSerializable
{
    use HandleSeriesNames;

    protected $data;

    protected $group;

    protected $segment;

    protected $compare;

    protected $dateFrom;

    protected $dateTo;

    public function __construct($data, $group)
    {
        $this->data = $data;
        $this->group = $group;
    }

    public function buildSeriesName($segment, $compare, $dateFrom, $dateTo)
    {
        $this->segment = $segment;
        $this->compare = $compare;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function jsonSerialize()
    {
        $data = [
            'group' => $this->formatSeriesName($this->segment, $this->group, $this->compare, $this->dateFrom, $this->dateTo),
            'filter' => http_build_query(['patients' => $this->data->pluck('patient_id')->implode(',')]),
            'total' => $this->data->count(),
            'species' => $this->data->unique('taxon_id')->count(),
            'pending' => $this->data->where('disposition', 'Pending')->count(),
            'released' => $this->data->where('disposition', 'Released')->count(),
            'transferred' => $this->data->where('disposition', 'Transferred')->count(),
            'died_in_24' => $this->data->where('disposition', 'Died in 24hr')->count(),
            'euthanized_in_24' => $this->data->where('disposition', 'Euthanized in 24hr')->count(),
            'died' => $this->data->whereIn('disposition', ['Died +24hr', 'Died in 24hr'])->count(),
            'euthanized' => $this->data->whereIn('disposition', ['Euthanized +24hr', 'Euthanized in 24hr'])->count(),
            'doa' => $this->data->where('disposition', 'Dead on arrival')->count(),
        ];

        $data['survival_rate_including_24hr'] = survival_rate($data);
        $data['survival_rate_after_24hr'] = survival_rate($data, true);

        return $data;
    }

    public function formatFilterQuery()
    {
        $this->subGroupName = 'class';
        $segment = array_map('trim', explode(':', $this->segment));

        return http_build_query([
            'filter' => [
                'from' => $this->dateFrom,
                'to' => $this->dateTo,
                'segment' => $segment,
                $this->subGroupName => $this->group,
            ],
        ]);
    }
}
