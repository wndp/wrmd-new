<?php

namespace Tests\Unit\Extensions\Owcn;

use App\Domain\Patients\Exam;
use App\Extensions\Labs\Lab;
use Mockery;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class RepositoryTest extends TestCase
{
    //use AssistsWithCases;
    //use AssistsWithAuthentication;

    protected $repo;

    protected function setUp(): void
    {
        parent::setUp();

        $dispatcher = Mockery::mock('Illuminate\Contracts\Events\Dispatcher');

        $this->repo = new \App\Extensions\Owcn\Repository($dispatcher);

        $this->setSetting(Team::factory()->create(), 'date_format', 'Y-m-d');
    }

    public function test_only_lab_vitals(): void
    {
        $case = $this->createCase();

        Lab::factory()->create(['patient_id' => $case->patient_id, 'data' => ['pcv' => '40', 'tp' => '3.3'], 'performed_at' => '2016-06-16', 'test' => 'cbc']);

        $result = $this->repo->getVitals($case->patient_id);

        $this->assertEquals('2016-06-16', $result[0]['date']->format('Y-m-d'));
        $this->assertNull($result[0]['weight']);
        $this->assertNull($result[0]['temperature']);
        $this->assertEquals(40, $result[0]['pcv']);
    }

    public function test_only_treatment_vitals(): void
    {
        $case = $this->createCase();

        Exam::factory()->daily()->create(['patient_id' => $case->patient_id, 'weight' => '500', 'examined_at' => '2016-06-16']);

        $result = $this->repo->getVitals($case->patient_id);

        $this->assertEquals('2016-06-16', $result[0]['date']->format('Y-m-d'));
        $this->assertEquals(500, $result[0]['weight']);
        $this->assertNull($result[0]['pcv']);
        $this->assertNull($result[0]['tp']);
    }

    public function test_combine_vitals_dates(): void
    {
        $case = $this->createCase();

        Exam::factory()->daily()->create(['patient_id' => $case->patient_id, 'weight' => '500', 'temperature' => '100', 'examined_at' => '2015-07-02']);
        Lab::factory()->create(['patient_id' => $case->patient_id, 'data' => ['pcv' => '40', 'tp' => '3.3'], 'performed_at' => '2015-07-02', 'test' => 'cbc']);

        $result = $this->repo->getVitals($case->patient_id);

        $this->assertEquals('2015-07-02', $result[0]['date']->format('Y-m-d'));
        $this->assertEquals(500, $result[0]['weight']);
        $this->assertEquals(100, $result[0]['temperature']);
        $this->assertEquals(40, $result[0]['pcv']);
        $this->assertEquals(3.3, $result[0]['tp']);
    }
}
