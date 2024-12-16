<?php

namespace App\Importing;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait FacilitatesImporting
{
    /**
     * The required fields to be imported.
     *
     * @var array
     */
    private $requiredFields = [
        'patients.common_name',
        'patients.date_admitted_at',
        'patients.disposition_id',
    ];

    /**
     * Fields used to search for an existing record.
     *
     * @var array
     */
    public function wrmdExistingColumns()
    {
        return [
            'case_number' => __('Case Number'),
            'reference_number' => __('Reference Number'),
            'microchip_number' => __('Microchip Number'),
            'band' => __('Band'),
        ];
    }

    /**
     * Return a collection of entities that can be imported.
     */
    public function importableEntities(): Collection
    {
        return collect([
            'patients' => __('Patients with their rescuer and initial exam'),
            'treatment_logs' => __('Treatment logs'),
            'patient_locations' => __('Location / Enclosure history'),
            'prescriptions' => __('Prescriptions'),
            'donations' => __('Rescuer donations'),
            'people' => __('Volunteers and Members'),
        ]);

        // return collect(array_merge([
        //     'patients' => __('Patients with their rescuer and initial exam'),
        //     'treatment_logs' => __('Treatment logs'),
        //     'patient_locations' => __('Location / Enclosure history'),
        //     'prescriptions' => __('Prescriptions'),
        //     'donations' => __('Rescuer donations'),
        //     'people' => __('Volunteers and Members'),
        // ], flatten_preserve(event('import.whatImporting'))));
    }

    /**
     * Filter the required fields according to the session variables.
     */
    public function filteredRequiredFields(): array
    {
        if (session()->get('import.whatImporting') !== 'patients') {
            $this->requiredFields = [];
        }

        return $this->requiredFields;
    }

    /**
     * Cached and return the full import as a collection for the provided session.
     */
    public function getImportCollectionForSession(string $sessionId, string $filePath): Collection
    {
        return Cache::remember("import.$sessionId", now()->addMinutes(30), function () use ($filePath) {
            return (new CompleteImport)->toCollection($filePath, 's3-accounts')->first();
        });
    }

    /**
     * Is it required to search for existing records?
     */
    public function shouldSearchForExistingRecords(): bool
    {
        $shouldNotSearchForExistingRecords = Arr::first(
            event('import.shouldNotSearchForExistingRecords.'.session()->get('import.whatImporting'))
        );

        if (is_bool($shouldNotSearchForExistingRecords)) {
            return ! $shouldNotSearchForExistingRecords;
        }

        return session('import.updateExistingRecords') || session()->get('import.whatImporting') !== 'patients';
    }
}
