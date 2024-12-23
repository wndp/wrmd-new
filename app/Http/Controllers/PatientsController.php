<?php

namespace App\Http\Controllers;

use App\Actions\AdmitPatient;
use App\Caches\QueryCache;
use App\Enums\AttributeOptionName;
use App\Http\Requests\StorePatientRequest;
use App\Jobs\AssociatePatientToIncident;
use App\Jobs\SaveAdmissionDonation;
use App\Listing\FacilitatesListing;
use App\Listing\ListsCollection;
use App\Models\AttributeOption;
use App\Models\Incident;
use App\Options\LocaleOptions;
use App\Options\Options;
use App\Repositories\OptionsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PatientsController extends Controller
{
    use FacilitatesListing;

    public function index(Request $request)
    {
        $list = $request->input('list', 'patients-this-year-list');
        $listObject = $this->guessListFromKey($list);

        abort_if(is_null($listObject), 404);

        $hasQueryCache = QueryCache::exists();
        $lists = ListsCollection::register();
        $listFigures = tap(
            $listObject,
            fn ($list) => $list->withRequest($request)
        )
            ->get();

        return Inertia::render('Patients/Index', compact('lists', 'list', 'listFigures', 'hasQueryCache'));
    }

    /**
     * Show the form for creating a new patient.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        OptionsStore::add([
            new LocaleOptions,
            'actionsAfterStore' => Options::arrayToSelectable([
                'return' => __('I want to admit another patient'),
                'view' => __("I want to view this patient's record"),
                'batch' => __('I want to batch update all the admitted patients'),
            ]),
            'availableYears' => Options::arrayToSelectable(
                AdmitPatient::availableYears(Auth::user()->currentTeam)->toArray()
            ),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::TAXA_MORPHS->value,
                AttributeOptionName::PERSON_ENTITY_TYPES->value,
                AttributeOptionName::DONATION_METHODS->value,
            ]),
        ]);

        if ($request->filled('incident')) {
            $incident = Incident::firstWhere([
                'id' => $request->query('incident'),
                'team_id' => Auth::user()->current_team_id,
                'patient_id' => null,
            ]);

            if ($incident) {
                Inertia::share(compact('incident'));
            }
        }

        $this->shareLastCaseId();

        return Inertia::render('Patients/Create');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(StorePatientRequest $request)
    {
        $admissions = AdmitPatient::run(
            Auth::user()->currentTeam,
            $request->input('case_year'),
            $request->only([
                'entity_id',
                'organization',
                'first_name',
                'last_name',
                'phone',
                'alternate_phone',
                'email',
                'subdivision',
                'city',
                'address',
                'county',
                'postal_code',
                'notes',
                'no_solicitation',
                'is_volunteer',
                'is_member',
            ]),
            $request->only([
                'admitted_at',
                'admitted_by',
                'taxon_id',
                'common_name',
                'transported_by',
                'found_at',
                'address_found',
                'city_found',
                'county_found',
                'subdivision_found',
                'postal_code_found',
                'lat_found',
                'lng_found',
                'reason_for_admission',
                'care_by_rescuer',
                'notes_about_rescue',
            ]),
            $request->get('cases_to_create', 1)
        );

        $firstAdmission = $admissions->first();

        if ($request->filled('incident')) {
            AssociatePatientToIncident::dispatch(
                Auth::user()->currentTeam,
                $firstAdmission->patient,
                $request->get('incident')
            );
        }

        if ($request->filled('donation.value')) {
            SaveAdmissionDonation::dispatch(
                Auth::user()->currentTeam,
                $firstAdmission->patient,
                $request->donation['value'],
                $request->donation['method'],
                $request->admitted_at,
                $request->donation['comments']
            );
        }

        switch ($request->action_after_store) {
            case 'view':
                $redirect = redirect()->route('patients.initial.edit', [
                    'y' => $firstAdmission->case_year,
                    'c' => $firstAdmission->case_id,
                ], 303);
                break;

            case 'batch':
                PatientSelector::empty();
                $admissions->pluck('patient_id')->each(function ($patientId) {
                    PatientSelector::add($patientId);
                });

                $redirect = redirect()->route('patients.batch.edit', [], 303);
                break;

            default:
                $redirect = redirect()->route('patients.create', [], 303);
                break;
        }

        $message = $admissions->count() > 1
            ? __('Patients :firstCaseNumber through :lastCaseNumber created.', ['firstCaseNumber' => $firstAdmission->case_number, 'lastCaseNumber' => $admissions->last()->case_number])
            : __('Patient :caseNumber created.', ['caseNumber' => $firstAdmission->case_number]);

        return $redirect
            ->with('notification.heading', 'Success!')
            ->with('notification.text', $message);
    }
}
