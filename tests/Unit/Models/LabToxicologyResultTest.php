<?php

namespace Tests\Unit\Models;

use App\Models\LabReport;
use App\Models\LabToxicologyResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabToxicologyResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_lab_toxicology_result_belongs_to_a_lab_report(): void
    {
        $labReport = LabReport::factory()->for(LabToxicologyResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabToxicologyResult::class, $labReport->labResult);
    }

    public function test_a_lab_toxicology_result_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabToxicologyResult::factory()->create(),
            'created'
        );
    }
}
