<?php

namespace Tests\Unit\Models;

use App\Models\LabCytologyResult;
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
final class LabCytologyResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aLabCytologyResultBelongsToALabReport(): void
    {
        $labReport = LabReport::factory()->for(LabCytologyResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabCytologyResult::class, $labReport->labResult);
    }

    #[Test]
    public function aLabCytologyResultIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabCytologyResult::factory()->create(),
            'created'
        );
    }
}
