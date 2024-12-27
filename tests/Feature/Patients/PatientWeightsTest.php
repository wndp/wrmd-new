<?php

use App\Domain\Patients\Exam;
use App\Domain\Patients\Patient;
use App\Domain\Patients\TreatmentLog;
use App\Domain\Taxonomy\Taxon;
use Illuminate\Support\Collection;
use Tests\TestCase;

final class PatientWeightsTest extends TestCase
{
    private $patient;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
        $this->patient = Patient::factory()->create();

        TreatmentLog::factory()->create([
            'patient_id' => $this->patient->id,
            'weight' => 123.45,
            'weight_unit' => 'g',
            'treated_at' => '2019-03-23 09:00:00',
        ]);

        Exam::factory()->create([
            'patient_id' => $this->patient->id,
            'weight' => 98.76,
            'weight_unit' => 'kg',
            'examined_at' => '2019-03-22 08:00:00',
        ]);
    }

    public function test_it_gets_a_collecion_of_the_patients_weights(): void
    {
        $result = $this->patient->getWeights();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function test_it_gets_the_last_patient_weight(): void
    {
        $result = $this->patient->getLastWeight();

        $this->assertEquals('Treatment Log', $result->type);
        $this->assertEquals(123.45, $result->weight);
        $this->assertEquals('g', $result->unit);
        $this->assertEquals('2019-03-23', $result->weighed_at_date);
    }
}
