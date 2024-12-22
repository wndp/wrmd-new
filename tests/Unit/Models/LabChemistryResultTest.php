<?php

namespace Tests\Unit\Models;

use App\Models\LabChemistryResult;
use App\Models\LabReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabChemistryResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_lab_chemistry_result_belongs_to_a_lab_report(): void
    {
        $labReport = LabReport::factory()->for(LabChemistryResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabChemistryResult::class, $labReport->labResult);
    }

    public function test_a_lab_chemistry_result_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabChemistryResult::factory()->create(),
            'created'
        );
    }
}
