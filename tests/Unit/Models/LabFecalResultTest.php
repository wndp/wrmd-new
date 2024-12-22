<?php

namespace Tests\Unit\Models;

use App\Models\LabFecalResult;
use App\Models\LabReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabFecalResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_lab_fecal_result_belongs_to_a_lab_report(): void
    {
        $labReport = LabReport::factory()->for(LabFecalResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabFecalResult::class, $labReport->labResult);
    }

    public function test_a_lab_fecal_result_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabFecalResult::factory()->create(),
            'created'
        );
    }
}
