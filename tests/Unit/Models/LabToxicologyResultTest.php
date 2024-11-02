<?php

namespace Tests\Unit\Models;

use App\Models\LabToxicologyResult;
use App\Models\LabReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\Support\AssistsWithTests;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('lab')]
final class LabToxicologyResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aLabToxicologyResultBelongsToALabReport(): void
    {
        $labReport = LabReport::factory()->for(LabToxicologyResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabToxicologyResult::class, $labReport->labResult);
    }

    #[Test]
    public function aLabToxicologyResultIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabToxicologyResult::factory()->create(),
            'created'
        );
    }
}
