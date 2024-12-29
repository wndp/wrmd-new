<?php

use App\Actions\GetPatientWeights;
use App\Models\CareLog;
use App\Models\Exam;
use App\Models\Patient;
use App\Support\WeightsCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\Traits\CreatesUiBehavior;

final class GetPatientWeightsTest extends TestCase
{
    use CreatesUiBehavior;
    use RefreshDatabase;

    private $patient;
    private $gWeightId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->patient = Patient::factory()->create();

        [$kgWeightId, $this->gWeightId] = $this->weightUnits();

        CareLog::factory()->create([
            'patient_id' => $this->patient->id,
            'weight' => 123.45,
            'weight_unit_id' => $this->gWeightId,
            'date_care_at' => '2019-03-23',
        ]);

        Exam::factory()->create([
            'patient_id' => $this->patient->id,
            'weight' => 98.76,
            'weight_unit_id' => $kgWeightId,
            'date_examined_at' => '2019-03-22',
        ]);
    }

    public function test_it_gets_a_collecion_of_the_patients_weights(): void
    {
        $weightObj = GetPatientWeights::run($this->patient);

        $this->assertInstanceOf(WeightsCollection::class, $weightObj);
        $this->assertCount(2, $weightObj);
    }

    public function test_it_gets_the_last_patient_weight(): void
    {
        $weightObj = GetPatientWeights::run($this->patient)->getLastWeight();

        $this->assertEquals('Care Log', $weightObj->type);
        $this->assertEquals(123.45, $weightObj->weight);
        $this->assertEquals($this->gWeightId, $weightObj->unit_id);
        $this->assertEquals('2019-03-23', $weightObj->weighed_at_date);
    }
}
