<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Events\PatientAdmitted;
use App\Exceptions\UnprocessableAdmissionException;
use App\Jobs\AttemptTaxaIdentification;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\Person;
use App\Models\Team;
use App\Support\Timezone;
use App\Support\Wrmd;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LogicException;
use MatanYadaev\EloquentSpatial\Objects\Point;
use TypeError;

class AdmitPatient
{
    use AsAction;

    /**
     * The number of case to create.
     *
     * @var int
     */
    public $casesToCreate = 1;

    /**
     * Create the models without events.
     *
     * @var bool
     */
    public $withoutEvents = false;

    /**
     * The team the patient belongs to.
     *
     * @var \App\Domain\Accounts\Account
     */
    protected $team;

    /**
     * The year to admit the patient into.
     *
     * @var int
     */
    protected $year;

    /**
     * The rescuer to admit the patient with.
     *
     * @var \App\Domain\People\Person
     */
    protected $rescuer;

    /**
     * Create the models without events.
     */
    public function withoutEvents(): static
    {
        $this->withoutEvents = true;

        return $this;
    }

    /**
     * Process the admission of a new patient.
     *
     * @param  array|\App\Domain\People\Person  $rescuer
     * @param  array|\App\Domain\Patients\Patient  $patient
     */
    public function handle(
        Team $team,
        int $year,
        Person|array $rescuer,
        Patient|array $patient,
        int $casesToCreate = 1
    ): Collection {
        $this->team = $team;
        $this->year = $year;
        $this->casesToCreate = $casesToCreate;

        //DB::beginTransaction();
        $admissions = $this->processInTransaction($rescuer, $patient);
        try {
        } catch (TypeError $e) {
            //DB::rollback();
            throw new UnprocessableAdmissionException($e->getMessage());
        } catch (Exception $e) {
            //DB::rollback();
            throw new UnprocessableAdmissionException($e->getMessage());
        }
        //DB::commit();

        return $admissions;
    }

    /**
     * Process the admission of a new patient with a transaction.
     *
     * @param  array|\App\Domain\People\Person  $rescuer
     * @param  array|\App\Domain\Patients\Patient  $patientData
     */
    protected function processInTransaction($rescuer, $patientData): Collection
    {
        $this->rescuedBy($rescuer);

        throw_unless($this->modelsAreSet(), new LogicException('Models not set'));

        return Collection::make(range(1, $this->casesToCreate))->map(function () use ($patientData) {
            $admission = $this->persistAdmission($this->persistPatient($patientData));

            AttemptTaxaIdentification::dispatch($admission->patient)->delay(5);

            if (! $this->withoutEvents) {
                event(new PatientAdmitted($this->team, $admission->patient, $patientData));
            }

            return $admission;
        });
    }

    /**
     * Set the rescuers contact details.
     *
     * @param  array|\App\Domain\People\Person  $rescuer
     */
    protected function rescuedBy($rescuer): void
    {
        if ($rescuer instanceof Person) {
            $this->rescuer = $rescuer;
            return;
        }

        throw_unless(is_array($rescuer), new TypeError('Expected a rescuer array but instead received a '.gettype($rescuer)));

        $this->rescuer = Person::updateOrCreate(array_filter([
            'id' => $rescuer['id'] ?? null,
            'team_id' => $this->team->id,
        ]), [
            ...$rescuer,
            'no_solicitations' => true
        ]);
    }

    /**
     * Persist the patient into storage.
     *
     * @param  array|\App\Domain\Patients\Patient  $patientData
     */
    protected function persistPatient($patientData): Patient
    {
        if ($patientData instanceof Patient) {
            return $patientData;
        }

        return tap(new Patient($patientData), function ($patient) use ($patientData) {
            $admittedAt = Timezone::convertFromLocalToUtc($patientData['admitted_at']);

            [$dispositionPendingId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
                AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
            ]);

            $patient->team_possession_id = $this->team->id;
            $patient->rescuer_id = $this->rescuer->id;
            //$patient->taxon_id = null;
            $patient->disposition_id = $dispositionPendingId;
            $patient->date_admitted_at = $admittedAt->toDateString();
            $patient->time_admitted_at = $admittedAt->toTimeString();

            if (isset($patientData['lat_found'], $patientData['lng_found'])) {
                $patient->coordinates_found = new Point($patientData['lat_found'], $patientData['lng_found']);
            }

            if ($this->withoutEvents) {
                $patient->saveQuietly();
            } else {
                $patient->save();
            }
        });
    }

    /**
     * Persist the admission to storage.
     *
     * @link https://mysql.rjweb.org/doc.php/myisam2innodb
     *
     * @note INDEX issue -- 2-column PK
     */
    protected function persistAdmission(Patient $patient): Admission
    {
        return DB::transaction(function () use ($patient) {
            $nextCaseId = Admission::where([
                'team_id' => $this->team->id,
                'case_year' => $this->year,
            ])
                ->lockForUpdate()
                ->count() + 1;

            return Admission::create([
                'team_id' => $this->team->id,
                'case_year' => $this->year,
                'case_id' => $nextCaseId,
                'patient_id' => $patient->id,
            ]);
        }, 5);
    }

    /**
     * Determine of the required models are all set.
     */
    protected function modelsAreSet(): bool
    {
        return $this->team instanceof Team
            && $this->rescuer instanceof Person;
    }

    /**
     * Get a collection of years available for a team to admit patients into.
     */
    public static function availableYears(Team $team): Collection
    {
        $availableYear = Arr::first(event('availableYears', $team));

        if (is_year($availableYear)) {
            return new Collection([$availableYear]);
        }

        return (new Collection(range(date('Y') - 30, date('Y'))))
            ->reverse()
            ->values();
    }
}
