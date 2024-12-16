<?php

namespace Tests\Unit\Models;

use App\Models\LabFecalResult;
use App\Models\LabReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabFecalResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aLabFecalResultBelongsToALabReport(): void
    {
        $labReport = LabReport::factory()->for(LabFecalResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabFecalResult::class, $labReport->labResult);
    }

    #[Test]
    public function aLabFecalResultIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabFecalResult::factory()->create(),
            'created'
        );
    }
}
