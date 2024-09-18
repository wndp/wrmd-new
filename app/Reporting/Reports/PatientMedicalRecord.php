<?php

namespace App\Reporting\Reports;

use App\Models\Admission;
use App\Reporting\Contracts\ExportableReport;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;

class PatientMedicalRecord extends ExportableReport implements FromCollection
{
    private $requestedComputedAttributes = null;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.medical-record.index';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Patient Medical Record');
    }

    public function data(): array
    {
        return [
            'account' => $this->team,
            'admissions' => $this->collection(),
            'shareOptions' => $this->request->get('include', []),
        ];
    }

    public function headings(): array
    {
        if ($this->request->offsetExists('fields')) {
            return collect($this->request->fields)->sort()->values()->toArray();
        }

        return $this->getVisibleAttributes($this->collection()->first())
            ->merge(
                $this->getRequestedComputedAttributes()->map(function ($column) {
                    return str_replace('.', '_', $column);
                })->flip()
            )
            ->keys()
            ->sort()
            ->values()
            ->toArray();
    }

    public function collection(): EloquentCollection
    {
        if (! empty($this->getRequestedComputedAttributes())) {
            //$this->admission->load('patient');
        }

        return Admission::where('account_id', $this->team->id)
            ->whereIn('patient_id', Arr::wrap($this->patient))
            ->get();

        // return $this->admission instanceof EloquentCollection
        //     ? $this->admission
        //     : new EloquentCollection(Arr::wrap($this->admission));
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        $this->getRequestedComputedAttributes()->map(function ($attribute) {
            return str_replace('.', '_', $attribute);
        })->each(function ($attribute) use ($row) {
            $computedAttribute = Str::after($attribute, 'patients_');
            $row->{$attribute} = object_get($row, "patient.$computedAttribute");
        });

        if ($this->request->offsetExists('fields')) {
            $fields = array_map(function ($column) {
                return str_replace('.', '_', $column);
            }, $this->request->fields);

            return collect($row->attributesToArray())
                ->sortKeys()
                ->only($fields)
                ->toArray();
        }

        return $this->getVisibleAttributes($row)->sortKeys()->toArray();
    }

    /**
     * Get the allowable visible attributes from the provided row.
     */
    public function getVisibleAttributes(Model $row): Collection
    {
        return (new Collection($row->attributesToArray()))
            ->except([
                'deleted_at',
                'created_at',
                'updated_at',
                'account_id',
                'href',
                'id',
                'patient_id',
            ]);
    }

    /**
     * Get the computed attributes for the current request.
     */
    public function getRequestedComputedAttributes(): Collection
    {
        if (is_null($this->requestedComputedAttributes)) {
            $computedFields = fields()->where('computed', true)->keys();

            $this->requestedComputedAttributes = $this->request->offsetExists('fields')
                ? $computedFields->intersect($this->request->fields)
                : $computedFields;
        }

        return $this->requestedComputedAttributes;
    }
}
