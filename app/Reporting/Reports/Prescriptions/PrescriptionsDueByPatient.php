<?php

namespace App\Reporting\Reports\Prescriptions;

use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateOn;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PrescriptionsDueByPatient extends Report
{
    use ReportUtilities;

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Prescriptions Due By Patient');
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.prescriptions.due-by-patient';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect([
            new DateOn(null, 'rx_started_at'),
        ]);
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $date = Carbon::parse($this->getAppliedFilterValue(DateOn::class));

        $prescriptions = $this->scopePrescriptions($date->format('Y-m-d'))
            ->get()
            ->filter(function ($prescription) use ($date) {
                return $prescription->isDueOn($date->format('Y-m-d'));
            })
            ->groupBy(function ($prescription) {
                return $this->groupByCaseNumber($prescription);
            });

        return [
            'date' => $date->format(config('wrmd.date_format')),
            'prescriptions' => $prescriptions,
        ];
    }
}
