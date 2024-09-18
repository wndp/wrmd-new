<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;

/**
 * Maryland, United Sates
 */
class UsMdRabies extends AnnualReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reporting.reports.annual.usMdRabies';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Maryland Rabies Vector Species Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        $admissions = Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->joinTaxa()
            ->where('disposition', '!=', 'Void')
            ->where(function ($query) {
                $query->whereIn('species.family', ['Procyonidae', 'Canidae', 'Mustelidae', 'Felidae', 'Cervidae', 'Mephitidae'])
                    ->orWhere('order', 'Chiroptera');
            })
            ->with('patient.rescuer', 'patient.exams')
            ->get();

        return [
            'year' => $this->year,
            'admissions' => $admissions,
        ];
    }
}
