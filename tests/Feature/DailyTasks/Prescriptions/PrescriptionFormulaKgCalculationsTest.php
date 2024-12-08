<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\FormulaType;
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
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function itCalculatesTheDoseIfAutoCalculateIsFalse(): void
    {
        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        )->attribute_option_id;

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

    #[Test]
    public function itCalculatesTheDoseForTypicalMgPerKgMgPerMlFormulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        )->attribute_option_id;

        $dosageUnitIsPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG
        )->attribute_option_id;

        $this->createUiBehavior($dosageUnitIsPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG);

        $concentrationUnitIsMgIds = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG
        )->attribute_option_id;

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

    #[Test]
    public function itCalculatesTheDoseForMgPerKgMgPerTabFormulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $doseUnitIsTabId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_TAB
        )->attribute_option_id;

        $dosageUnitIsPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG
        )->attribute_option_id;

        $this->createUiBehavior($dosageUnitIsPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG);

        $concentrationUnitIsMgIds = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG
        )->attribute_option_id;

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

    #[Test]
    public function itCalculatesTheDoseForMgPerKgFormulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $dosageUnitIsMgPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG
        )->attribute_option_id;

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

    #[Test]
    public function itCalculatesTheDoseForMlPerKgFormulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $dosageUnitIsMlPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_ML_PER_KG
        )->attribute_option_id;

        $this->createUiBehavior($dosageUnitIsMlPerKgId, AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG);

        $patient = Patient::factory()->create();
        Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 500, 'weight_unit_id' => $gWeightId]);

        $formula = Formula::factory()->create(['defaults' => [
            'auto_calculate_dose' => true,
            'dosage' => 0.5,
            'dosage_unit_id' => $dosageUnitIsMlPerKgId
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertSame(0.25, $results['dose']);
    }

    #[Test]
    public function itCalculatesTheDoseForIuPerKgFormulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $dosageUnitIsIuPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_KG
        )->attribute_option_id;

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

    #[Test]
    public function itCalculatesTheDoseForIuPerKgIuPerMlFormulas(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        )->attribute_option_id;

        $concentrationUnitIsIuPerMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU_PER_ML
        )->attribute_option_id;

        $this->createUiBehavior($concentrationUnitIsIuPerMlId, AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU);

        $dosageUnitIsIuPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_KG
        )->attribute_option_id;

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
