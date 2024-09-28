<?php

namespace App\Concerns;

use App\Caches\QueryCache;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\Extension;
use App\Exceptions\AdmissionNotFoundException;
use App\Exceptions\PatientVoidedException;
use App\Extensions\ExtensionNavigation;
use App\Models\Admission;
use App\Models\AttributeOption;
use App\Options\Options;
use App\Paginators\SearchResultPaginator;
use App\Repositories\OptionsStore;
use App\Support\DailyTasksCollection;
use App\Support\DailyTasksFilters;
use App\Support\ExtensionManager;
use App\Support\Timezone;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

trait LoadsAdmissionAndSharesPagination
{
    /**
     * This method is a shortcut to instantiate and share admission related pagination
     * as well as retrieve and load the admission into the view.
     */
    public function loadAdmissionAndSharePagination(): Admission
    {
        $teamId = Auth::user()->current_team_id;

        $admissionsPaginator = $this->getAdmissionsPaginator($teamId);
        $admission = $this->loadAdmission($admissionsPaginator, $teamId);
        $listPaginationPage = $this->getListPaginationPage($admissionsPaginator, $admission);
        $searchPaginator = $this->getSearchResultPaginator($admission);

        Inertia::share([
            'admissionsPaginator' => $admissionsPaginator,
            'searchPaginator' => $searchPaginator,
            'listPaginationPage' => $listPaginationPage,
            'patientMeta' => [
                'patient_id' => $admission->patient->id,
                'locked_at' => $admission->patient->locked_at,
                'voided_at' => $admission->patient->voided_at,
                'is_criminal_activity' => $admission->patient->is_criminal_activity,
                'is_resident' => $admission->patient->is_resident,
                'keywords' => $admission->patient->keywords,
                'taxon' => [
                    'class' => $admission->patient->taxon?->class,
                    'genus' => $admission->patient->taxon?->genus,
                    'species' => $admission->patient->taxon?->species,
                    'binomen' => $admission->patient->taxon?->binomen,
                    'bow_code' => $admission->patient->taxon?->bow_code,
                    'bow_url' => $admission->patient->taxon?->bow_url,
                    'inaturalist_taxon_id' => $admission->patient->taxon?->inaturalist_taxon_id,
                    'inaturalist_url' => $admission->patient->taxon?->inaturalist_url,
                    'iucn_id' => $admission->patient->taxon?->iucn_id,
                    'iucn_url' => $admission->patient->taxon?->iucn_url,
                    'iucn_conservation_status' => null
                ],
                'days_in_care' => $admission->patient->days_in_care,
                'incident' => [
                    'id' => $admission->patient->incident?->id,
                    'incident_number' => $admission->patient->incident?->incident_number,
                ],
                'numberOfTasksDueToday' => $this->getNumberOfTasksDueToday($admission->patient)
            ],
            'cageCard' => [
                'patient_id' => $admission->patient->id,
                'taxonID' => $admission->patient->taxon_id,
                'common_name' => $admission->patient->common_name,
                'admitted_at_for_humans' => Timezone::convertFromUtcToLocal($admission->patient->admitted_at)?->toDayDateTimeString(),
                'admitted_at_local' => Timezone::convertFromUtcToLocal($admission->patient->admitted_at)?->toDateTimeLocalString(),
                'morph_id' => $admission->patient->morph_id,
                'morph' => __($admission->patient->morph?->value),
                'band' => $admission->patient->band,
                'name' => $admission->patient->name,
                'reference_number' => $admission->patient->reference_number,
                'microchip_number' => $admission->patient->microchip_number,
                'case_number' => $admission->case_number
            ],
            'locationCard' => [
                'patient_id' => $admission->patient->id,
                'disposition_id' => $admission->patient->disposition_id,
                'disposition' => __($admission->patient->disposition->value),
                'dispositioned_at_formatted' => Timezone::convertFromUtcToLocal($admission->patient->dispositioned_at)?->toDayDateTimeString(),
                'patientLocations' => $admission->patient->locations->map(fn ($patientLocation) => [
                    'patient_location_id' => $patientLocation->id,
                    'location_for_humans' => $patientLocation->location_for_humans,
                    'moved_in_at_local' => Timezone::convertFromUtcToLocal($patientLocation->moved_in_at)?->toDateTimeLocalString(),
                    'moved_in_at_for_humans' => Timezone::convertFromUtcToLocal($patientLocation->moved_in_at)?->toDayDateTimeString(),
                    'facility_id' => $patientLocation->facility_id,
                    'facility' => $patientLocation->facility->value,
                    'area' => $patientLocation->area,
                    'enclosure' => $patientLocation->enclosure,
                    'comments' => $patientLocation->comments,
                ])
            ],
            'admission' => [
                'patient_id' => $admission->patient_id,
                'case_year' => $admission->case_year,
                'case_id' => $admission->case_id,
                'hash' => $admission->hash,
            ]
        ]);

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::TAXA_MORPHS->value,
                AttributeOptionName::PATIENT_LOCATION_FACILITIES->value,
                AttributeOptionName::DAILY_TASK_ASSIGNMENTS->value,
                AttributeOptionName::DAILY_TASK_FREQUENCIES->value,
                AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value,
                AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value,
                AttributeOptionName::DAILY_TASK_DOSE_UNITS->value,
                AttributeOptionName::DAILY_TASK_ROUTES->value,
                AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES->value,
            ]),
            'includableOptions' => Options::arrayToSelectable(array_filter([
                'rescuer' => __('Rescuer contact information'),
                'location_history' => __('Location history'),
                'rechecks' => __('Future rechecks'),
                'intake_exam' => __('Full intake exam'),
                'necropsy' => ExtensionManager::isActivated(Extension::NECROPSY) ? __('Necropsy Report') : false,
                'banding_morphometrics' => ExtensionManager::isActivated(Extension::BANDING_MORPHOMETRICS) ? __('Banding and Morphometrics') : false
            ]))
        ]);

        [
            $dispositionPendingId,
            $dispositionReleasedId,
            $dispositionTransferredId,
            $clinicFacilityId,
            $homecareFacilityId,
            $singleDoseId,
            $veterinarianId,
            $mgPerMlId,
            $mgPerKgId,
            $mlId
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value],
            [AttributeOptionName::PATIENT_LOCATION_FACILITIES->value, AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_CLINIC->value],
            [AttributeOptionName::PATIENT_LOCATION_FACILITIES->value, AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_HOMECARE->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE->value],
            [AttributeOptionName::DAILY_TASK_ASSIGNMENTS->value, AttributeOptionUiBehavior::DAILY_TASK_ASSIGNMENT_IS_VETERINARIAN->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML->value],
            [AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG->value],
            [AttributeOptionName::DAILY_TASK_DOSE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML->value],
        ]);

        Inertia::share([
            'locationOptionUiBehaviorIds' => compact(
                'dispositionPendingId',
                'dispositionReleasedId',
                'dispositionTransferredId',
                'clinicFacilityId',
                'homecareFacilityId'
            ),
            'dailyTaskOptionUiBehaviorIds' => compact(
                'singleDoseId',
                'veterinarianId',
                'mgPerMlId',
                'mgPerKgId',
                'mlId'
            )
        ]);

        //OptionsStore::add(new TaxonomyOptions());
        //ExtensionNavigation::emit('patient', $admission);

        $this->shareLastCaseId();

        return $admission;
    }

    /**
     * Get the admissions paginator.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getAdmissionsPaginator($teamId): Paginator
    {
        $caseYear = session('caseYear');

        $admissionsPaginator = Admission::where([
            'team_id' => $teamId,
            'case_year' => $caseYear,
        ])
            ->orderBy('case_id')
            ->paginate(1, ['*'], 'c')
            ->appends([
                'y' => $caseYear,
                'queryCache' => 'flush',
            ]);

        throw_if($admissionsPaginator->isEmpty(), AdmissionNotFoundException::class);

        return $admissionsPaginator;
    }

    /**
     * Get the list page number for the admission.
     */
    public function getListPaginationPage(Paginator $admissionsPaginator, Admission $admission): int
    {
        if (QueryCache::exists()) {
            $key = array_search($admission->patient_id, QueryCache::results()->patientIds) + 1;

            return ceil($key / 15);
        }

        $pages = ceil($admissionsPaginator->total() / 15) + 1; // 84

        return $pages - ceil($admission->case_id / 15);
    }

    /**
     * Get the search results paginator.
     *
     *
     * @throws \App\Exceptions\AdmissionNotFoundException
     */
    public function getSearchResultPaginator($admission): ?SearchResultPaginator
    {
        $searchPaginator = new SearchResultPaginator($admission);

        if ($searchPaginator->isEmpty()) {
            return null;
        }

        throw_if(Request::missing('c', 'y'), AdmissionNotFoundException::class);

        return $searchPaginator;
    }

    /**
     * Load the admission from an AdmissionPaginator.
     */
    public function loadAdmission(Paginator $admissionsPaginator, $teamId): Admission
    {
        return tap($admissionsPaginator->first(), function ($admission) use ($teamId) {
            $this->certifyAdmission($admission);

            //$admission->patient->attemptToLock();

            $admission->load(array_filter([
                'patient.taxon.metas',
                'patient.incident',
                //$admission->patient->team_possession_id !== $teamId ? 'patient.possession' : null,
            ]));

            //$admission->patient->append('formattedAddressInline');
            //$admission->patient->append('current_location');

            event('loadAdmission', $admission);
        });
    }

    /**
     * Certify that the admission is accessible.
     *
     * @throws \App\Exceptions\AdmissionNotFoundException
     * @throws \App\Exceptions\PatientVoidedException
     */
    protected function certifyAdmission(Admission $admission)
    {
        if (! $admission instanceof Admission) {
            throw new AdmissionNotFoundException();
        }

        if (!is_null($admission->patient()->withVoided()->first()->voided_at)) {
            throw new PatientVoidedException($admission);
        }
    }

    /**
     * Share the last case id.
     */
    public function shareLastCaseId(): void
    {
        $lastCaseId = Admission::getLastCaseId(
            Auth::user()->current_team_id,
            session('caseYear')
        );

        Inertia::share([
            'lastCaseId' => [
                'id' => $lastCaseId,
                'year' => session('caseYear'),
            ],
        ]);
    }

    public function getNumberOfTasksDueToday($patient)
    {
        return DailyTasksCollection::make()
            ->withFilters(new DailyTasksFilters([
                'date' => Carbon::now()
            ]))
            ->forPatient($patient, Auth::user()->currentTeam)
            ->sum(fn ($task) => $task['number_of_occurrences'] - (
                $task['completed_occurrences']->count() + $task['incomplete_occurrences']->count()
            ));
    }
}
