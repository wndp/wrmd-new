<?php

namespace App\Reporting\Reports\PatientReports;

use App\Enums\NecropsyBodyPart;
use App\Models\Admission;
use App\Options\Options;
use App\Reporting\Contracts\Report;

class NecropsyReport extends Report
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.necropsy.separate';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Necropsy');
    }

    /**
     * Get the data for the report.
     */
    public function data(): array
    {
        $necropsy = $this->patient->necropsy;
        $admission = Admission::custody($this->team, $this->patient);
        //$options = new NecropsyOptions();

        return [
            'admission' => $admission,
            'necropsy' => $necropsy,
            'necropsyBodyPartOptions' => Options::enumsToSelectable(NecropsyBodyPart::cases()),
        ];
    }
}
