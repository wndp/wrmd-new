<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Exam;
use App\Models\Formula;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

/** TODO: Test for each possible dosage / concentration combination. */
#[Group('daily-tasks')]
final class PrescriptionFormulaKgCalculationsTest extends TestCase
{
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_it_calculates_the_dose_if_auto_calculate_is_false(): void
    {
        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        );

        $patient = Patient::factory()->create();
        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => false,
            'dose' => 123,
            'dose_unit_id' => $doseUnitIsMlId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(123.00, $results['dose']);
        $this->assertSame($doseUnitIsMlId, $results['dose_unit_id']);
    }

    public function test_it_calculates_the_dose_for_typical_mg_per_kg_mg_per_ml_formulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        );

        $dosageUnitIsPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG
        );

        $this->createUiBehavior($dosageUnitIsPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG);

        $concentrationUnitIsMgIds = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG
        );

        $this->createUiBehavior($concentrationUnitIsMgIds, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 35, 'weight_unit_id' => $gWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'concentration' => 22.7,
            'concentration_unit_id' => $concentrationUnitIsMgIds,
            'dosage' => 15,
            'dosage_unit_id' => $dosageUnitIsPerKgId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(0.02, $results['dose']);
        $this->assertSame($doseUnitIsMlId, $results['dose_unit_id']);
    }

    public function test_it_calculates_the_dose_for_mg_per_kg_mg_per_tab_formulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $doseUnitIsTabId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_TAB
        );

        $dosageUnitIsPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG
        );

        $this->createUiBehavior($dosageUnitIsPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG);

        $concentrationUnitIsMgIds = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG
        );

        $this->createUiBehavior($concentrationUnitIsMgIds, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_TAB);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 500, 'weight_unit_id' => $gWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'concentration' => 5,
            'concentration_unit_id' => $concentrationUnitIsMgIds,
            'dosage' => 20,
            'dosage_unit_id' => $dosageUnitIsPerKgId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(2.0, $results['dose']);
        $this->assertSame($doseUnitIsTabId, $results['dose_unit_id']);
    }

    public function test_it_calculates_the_dose_for_mg_per_kg_formulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $dosageUnitIsMgPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG
        );

        $this->createUiBehavior($dosageUnitIsMgPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 500, 'weight_unit_id' => $gWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'dosage' => 0.5,
            'dosage_unit_id' => $dosageUnitIsMgPerKgId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(0.25, $results['dose']);
    }

    public function test_it_calculates_the_dose_for_ml_per_kg_formulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $dosageUnitIsMlPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_ML_PER_KG
        );

        $this->createUiBehavior($dosageUnitIsMlPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 500, 'weight_unit_id' => $gWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'dosage' => 0.5,
            'dosage_unit_id' => $dosageUnitIsMlPerKgId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(0.25, $results['dose']);
    }

    public function test_it_calculates_the_dose_for_iu_per_kg_formulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $dosageUnitIsIuPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_KG
        );

        $this->createUiBehavior($dosageUnitIsIuPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 500, 'weight_unit_id' => $gWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'dosage' => 0.5,
            'dosage_unit_id' => $dosageUnitIsIuPerKgId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(0.25, $results['dose']);
    }

    public function test_it_calculates_the_dose_for_iu_per_kg_iu_per_ml_formulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        );

        $concentrationUnitIsIuPerMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU_PER_ML
        );

        $this->createUiBehavior($concentrationUnitIsIuPerMlId, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU);

        $dosageUnitIsIuPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_KG
        );

        $this->createUiBehavior($dosageUnitIsIuPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 500, 'weight_unit_id' => $gWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'concentration' => 5,
            'concentration_unit_id' => $concentrationUnitIsIuPerMlId,
            'dosage' => 0.5,
            'dosage_unit_id' => $dosageUnitIsIuPerKgId,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(0.05, $results['dose']);
        $this->assertSame($doseUnitIsMlId, $results['dose_unit_id']);
    }
}
