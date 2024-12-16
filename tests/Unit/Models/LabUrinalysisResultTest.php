<?php

namespace Tests\Unit\Models;

use App\Models\LabReport;
use App\Models\LabUrinalysisResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabUrinalysisResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aLabUrinalysisResultBelongsToALabReport(): void
    {
        $labReport = LabReport::factory()->for(LabUrinalysisResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabUrinalysisResult::class, $labReport->labResult);
    }

    #[Test]
    public function aLabUrinalysisResultIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabUrinalysisResult::factory()->create(),
            'created'
        );
    }
}
