<?php

namespace Tests\Unit\Models;

use App\Models\LabCytologyResult;
use App\Models\LabReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabCytologyResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_lab_cytology_result_belongs_to_a_lab_report(): void
    {
        $labReport = LabReport::factory()->for(LabCytologyResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabCytologyResult::class, $labReport->labResult);
    }

    public function test_a_lab_cytology_result_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabCytologyResult::factory()->create(),
            'created'
        );
    }
}
