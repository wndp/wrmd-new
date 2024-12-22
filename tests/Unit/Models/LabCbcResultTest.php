<?php

namespace Tests\Unit\Models;

use App\Models\LabCbcResult;
use App\Models\LabReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabCbcResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_lab_cbc_result_belongs_to_a_lab_report(): void
    {
        $labReport = LabReport::factory()->for(LabCbcResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabCbcResult::class, $labReport->labResult);
    }

    public function test_a_lab_cbc_result_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabCbcResult::factory()->create(),
            'created'
        );
    }
}
