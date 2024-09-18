<?php

namespace App\Actions;

use App\Actions\GetPatientWeights;
use App\Concerns\AsAction;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Formula;
use App\Models\Patient;
use App\Support\Weight;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;

class PrescriptionFormulaCalculator implements JsonSerializable, Arrayable
{
    use AsAction;

    protected $dosageUnitIsPerKgIds;
    protected $dosageUnitIsPerLbIds;
    protected $dosageUnitIsMgPerKgId;
    protected $dosageUnitIsIuPerKgId;
    protected $dosageUnitIsMgPerLbId;
    protected $dosageUnitIsIuPerLbId;

    protected $concentrationUnitIsMgIds;
    protected $concentrationUnitIsIuIds;
    protected $concentrationUnitIsMgPerMlId;
    protected $concentrationUnitIsMgPerTabId;
    protected $concentrationUnitIsMgPerCapId;
    protected $concentrationUnitIsMgPerGramId;
    protected $concentrationUnitIsIuPerCapId;
    protected $concentrationUnitIsIuPerMlId;

    protected $doseUnitIsMlId;
    protected $oseUnitIsCapId;
    protected $oseUnitIsTabId;
    protected $oseUnitIsGramId;

    protected $defaults;
    protected $patient;

    /**
     * Constructor.
     */
    public function __construct()
    {
        [
            $this->dosageUnitIsPerKgIds,
            $this->dosageUnitIsPerLbIds,
            $this->dosageUnitIsMgPerKgId,
            $this->dosageUnitIsIuPerKgId,
            $this->dosageUnitIsMgPerLbId,
            $this->dosageUnitIsIuPerLbId,

            $this->concentrationUnitIsMgIds,
            $this->concentrationUnitIsIuIds,
            $this->concentrationUnitIsMgPerMlId,
            $this->concentrationUnitIsMgPerTabId,
            $this->concentrationUnitIsMgPerCapId,
            $this->concentrationUnitIsMgPerGramId,
            $this->concentrationUnitIsIuPerCapId,
            $this->concentrationUnitIsIuPerMlId,

            $this->doseUnitIsMlId,
            $this->doseUnitIsCapId,
            $this->doseUnitIsTabId,
            $this->doseUnitIsGramId,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG->value],
            [AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_LB->value],
            [AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG->value],
            [AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_KG->value],
            [AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_LB->value],
            [AttributeOptionName::DAILY_TASK_DOSAGE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_LB->value],

            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_TAB->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_CAP->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_GRAM->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU_PER_CAP->value],
            [AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU_PER_ML->value],

            [AttributeOptionName::DAILY_TASK_DOSE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML->value],
            [AttributeOptionName::DAILY_TASK_DOSE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_CAP->value],
            [AttributeOptionName::DAILY_TASK_DOSE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_TAB->value],
            [AttributeOptionName::DAILY_TASK_DOSE_UNITS->value, AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_GRAM->value],
        ]);

        // why is this here?
        // Carbon::serializeUsing(function ($carbon) {
        //     return array_merge(get_object_vars($carbon), [
        //         'date_string' => $carbon->toDateString(),
        //         'time_string' => $carbon->toTimeString(),
        //     ]);
        // });
    }

    public function handle(Formula $formula, Patient $patient)
    {
        $this->defaults = $formula->defaults;
        $this->patient = $patient;

        return [
            'patient_id' => $this->patient->id,
            'dose' => $this->calculateDose(),
            'dose_unit_id' => $this->calculateDoseUnitId(),
            'rx_started_at' => $this->calculateStartDate()?->toDateString(),
            'rx_ended_at' => $this->calculateEndDate()?->toDateString(),
        ];
    }

    /**
     * String-ify the calculations.
     */
    public function __toString(): string
    {
        return $this->jsonSerialize();
    }

    /**
     * Calculate the formula dose for a patient.
     */
    public function calculateDose(): ?float
    {
        if (! $this->defaults->get('auto_calculate_dose')) {
            return $this->defaults->get('dose');
        }

        $weightObj = GetPatientWeights::run($this->patient)->getLastWeight();

        if (is_null($weightObj)) {
            return null;
        } elseif (in_array($this->defaults->get('dosage_unit_id'), $this->dosageUnitIsPerKgIds)) {
            return $this->calculateDoseForKg($weightObj);
        } elseif (in_array($this->defaults->get('dosage_unit_id'), $this->dosageUnitIsPerLbIds)) {
            return $this->calculateDoseForLb($weightObj);
        }
    }

    /**
     * Calculate the formula dose unit for a patient.
     */
    public function calculateDoseUnitId(): ?int
    {
        if (! $this->defaults->get('auto_calculate_dose')) {
            return $this->defaults->get('dosage_unit_id');
        }

        // Dosage in Kg
        //---------------------------------------------------------------
        //

        if (
            $this->defaults->get('concentration_unit_id') === null
            && in_array($this->defaults->get('dosage_unit_id'), $this->dosageUnitIsPerKgIds)
        ) {
            return null;
            //return str_replace('/kg', '', $this->defaults->get('dosage_unit_id'));
        }

        if ($this->defaults->get('dosage_unit_id') == $this->dosageUnitIsMgPerKgId) {
            if ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsMgPerMlId) {
                return $this->doseUnitIsMlId;
            } elseif ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsMgPerTabId) {
                return $this->doseUnitIsTabId;
            } elseif ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsMgPerCapId) {
                return $this->doseUnitIsCapId;
            } elseif ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsMgPerGramId) {
                return $this->doseUnitIsGramId;
            }
        }

        if ($this->defaults->get('dosage_unit_id') == $this->dosageUnitIsIuPerKgId) {
            if ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsIuPerMlId) {
                return $this->doseUnitIsMlId;
            } elseif ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsIuPerCapId) {
                return $this->doseUnitIsCapId;
            }
        }

        // Dosage in Lb
        //---------------------------------------------------------------

        if (
            $this->defaults->get('concentration_unit_id') === null
            && in_array($this->defaults->get('dosage_unit_id'), $this->dosageUnitIsPerLbIds)
        ) {
            return null;
            //return str_replace('/lb', '', $this->defaults->get('dosage_unit_id'));
        }

        if ($this->defaults->get('dosage_unit_id') == $this->dosageUnitIsMgPerLbId) {
            if ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsMgPerMlId) {
                return $this->doseUnitIsMlId;
            } elseif ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsMgPerTabId) {
                return $this->doseUnitIsTabId;
            } elseif ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsMgPerCapId) {
                return $this->doseUnitIsCapId;
            } elseif ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsMgPerGramId) {
                return $this->doseUnitIsGramId;
            }
        }

        if ($this->defaults->get('dosage_unit_id') == $this->dosageUnitIsIuPerLbId) {
            if ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsIuPerMlId) {
                return $this->doseUnitIsMlId;
            } elseif ($this->defaults->get('concentration_unit_id') == $this->concentrationUnitIsIuPerCapId) {
                return $this->doseUnitIsCapId;
            }
        }

        return null;
    }

    /**
     * Calculate the formula start date for a patient.
     */
    public function calculateStartDate(): ?Carbon
    {
        if ($this->defaults->get('start_on_prescription_date')) {
            return Carbon::now(Wrmd::settings('timezone'));
        }

        return null;
    }

    /**
     * Calculate the formula end date for a patient.
     */
    public function calculateEndDate(): ?Carbon
    {
        $startDate = $this->calculateStartDate();

        if ($startDate instanceof Carbon && $this->defaults->get('duration')) {
            return $startDate->addDays($this->defaults->get('duration') - 1);
        }

        return null;
    }

    /**
     * Specify data which should be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        $this->handle();
    }

    /**
     * Return the calculations in an array.
     */
    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * Calculate the formula dose in kilograms.
     */
    private function calculateDoseForKg($weightObj): ?float
    {
        $volumeOfDrug = Weight::toKilograms($weightObj->weight, $weightObj->unit_id) * $this->defaults->get('dosage');

        if (
            in_array($this->defaults->get('dosage_unit_id'), $this->dosageUnitIsPerKgIds)
            && $this->defaults->get('concentration_unit_id') === null
        ) {
            return round($volumeOfDrug, 2);
        }

        if (
            $this->defaults->get('dosage_unit_id') == $this->dosageUnitIsMgPerKgId
            && in_array($this->defaults->get('concentration_unit_id'), $this->concentrationUnitIsMgIds)
        ) {
            return round($volumeOfDrug / $this->defaults->get('concentration'), 2);
        }

        if (
            $this->defaults->get('dosage_unit_id') == $this->dosageUnitIsIuPerKgId
            && in_array($this->defaults->get('concentration_unit_id'), $this->concentrationUnitIsIuIds)
        ) {
            return round($volumeOfDrug / $this->defaults->get('concentration'), 2);
        }

        return null;
    }

    /**
     * Calculate the formula dose in pounds.
     */
    private function calculateDoseForLb($weightObj): float
    {
        $volumeOfDrug = Weight::toPounds($weightObj->weight, $weightObj->unit_id) * $this->defaults->get('dosage');

        if (
            in_array($this->defaults->get('dosage_unit_id'), $this->dosageUnitIsPerLbIds)
            && $this->defaults->get('concentration_unit_id') === null
        ) {
            return round($volumeOfDrug, 2);
        }

        if (
            $this->defaults->get('dosage_unit_id') == $this->dosageUnitIsMgPerLbId
            && in_array($this->defaults->get('concentration_unit_id'), $this->concentrationUnitIsMgIds)
        ) {
            return round($volumeOfDrug / $this->defaults->get('concentration'), 2);
        }

        if (
            $this->defaults->get('dosage_unit_id') == $this->dosageUnitIsIuPerLbId
            && in_array($this->defaults->get('concentration_unit_id'), $this->concentrationUnitIsIuIds)
        ) {
            return round($volumeOfDrug / $this->defaults->get('concentration'), 2);
        }
    }
}
