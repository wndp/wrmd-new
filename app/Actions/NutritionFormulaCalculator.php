<?php

namespace App\Actions;

use App\Enums\SettingKey;
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

class NutritionFormulaCalculator implements JsonSerializable, Arrayable
{
    use AsAction;

    protected $defaults;
    protected $patient;

    public function handle(Formula $formula, Patient $patient)
    {
        $this->defaults = $formula->defaults;
        $this->patient = $patient;

        return [
            'patient_id' => $this->patient->id,
            'started_at' => $this->calculateStartDate()?->toDateString(),
            'ended_at' => $this->calculateEndDate()?->toDateString(),
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
     * Calculate the formula start date for a patient.
     */
    public function calculateStartDate(): ?Carbon
    {
        if ($this->defaults->get('start_on_plan_date')) {
            return Carbon::now(Wrmd::settings(SettingKey::TIMEZONE));
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
}
