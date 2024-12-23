<?php

namespace Tests\Unit\Models;

use App\Models\LabReport;
use App\Models\LabUrinalysisResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabUrinalysisResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_lab_urinalysis_result_belongs_to_a_lab_report(): void
    {
        $labReport = LabReport::factory()->for(LabUrinalysisResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabUrinalysisResult::class, $labReport->labResult);
    }

    public function test_a_lab_urinalysis_result_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabUrinalysisResult::factory()->create(),
            'created'
        );
    }
}
