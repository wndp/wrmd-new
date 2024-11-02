<?php

namespace Tests\Unit\Models;

use App\Models\LabChemistryResult;
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
final class LabChemistryResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aLabChemistryResultBelongsToALabReport(): void
    {
        $labReport = LabReport::factory()->for(LabChemistryResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabChemistryResult::class, $labReport->labResult);
    }

    #[Test]
    public function aLabChemistryResultIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabChemistryResult::factory()->create(),
            'created'
        );
    }
}
