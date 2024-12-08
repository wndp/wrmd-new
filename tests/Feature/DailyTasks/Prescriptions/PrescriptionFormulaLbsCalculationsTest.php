<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\FormulaType;
use App\Models\Exam;
use App\Models\Formula;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class PrescriptionFormulaLbsCalculationsTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function itCalculatesTheDoseForMlPerLbFormulas(): void
    {
        [$kgWeightId] = $this->weightUnits();

        $dosageUnitIsMlPerLbId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_ML_PER_LB
        )->attribute_option_id;

        $this->createUiBehavior($dosageUnitIsMlPerLbId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_LB);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 1.2, 'weight_unit_id' => $kgWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'dosage' => 0.5,
            'dosage_unit_id' => $dosageUnitIsMlPerLbId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(1.32, $results['dose']);
        //$this->assertSame('ml', $results['dose_unit']);
    }

    #[Test]
    public function itCalculatesTheDoseForIuPerLbFormulas(): void
    {
        [$kgWeightId] = $this->weightUnits();

        $dosageUnitIsIuPerLbId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_LB
        )->attribute_option_id;

        $this->createUiBehavior($dosageUnitIsIuPerLbId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_LB);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 1.2, 'weight_unit_id' => $kgWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'dosage' => 0.5,
            'dosage_unit_id' => $dosageUnitIsIuPerLbId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(1.32, $results['dose']);
    }

    #[Test]
    public function itCalculatesTheDoseForMgPerLbMgPerMlFormulas(): void
    {
        [$kgWeightId] = $this->weightUnits();

        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        )->attribute_option_id;

        $dosageUnitIsMgPerLbId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_LB
        )->attribute_option_id;

        $this->createUiBehavior($dosageUnitIsMgPerLbId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_LB);

        $concentrationUnitIsMgIds = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG
        )->attribute_option_id;

        $this->createUiBehavior($concentrationUnitIsMgIds, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 1, 'weight_unit_id' => $kgWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'concentration' => 5,
            'concentration_unit_id' => $concentrationUnitIsMgIds,
            'dosage' => 2,
            'dosage_unit_id' => $dosageUnitIsMgPerLbId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(0.88, $results['dose']);
        $this->assertSame($doseUnitIsMlId, $results['dose_unit_id']);
    }

    #[Test]
    public function itCalculatesTheDoseForMgPerLbMgPerTabFormulas(): void
    {
        [$kgWeightId] = $this->weightUnits();

        $doseUnitIsTabId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_TAB
        )->attribute_option_id;

        $dosageUnitIsMgPerLbId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_LB
        )->attribute_option_id;

        $this->createUiBehavior($dosageUnitIsMgPerLbId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_LB);

        $concentrationUnitIsMgIds = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG
        )->attribute_option_id;

        $this->createUiBehavior($concentrationUnitIsMgIds, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_TAB);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 0.45, 'weight_unit_id' => $kgWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'concentration' => 5,
            'concentration_unit_id' => $concentrationUnitIsMgIds,
            'dosage' => 20,
            'dosage_unit_id' => $dosageUnitIsMgPerLbId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(3.97, $results['dose']);
        $this->assertSame($doseUnitIsTabId, $results['dose_unit_id']);
    }
}
