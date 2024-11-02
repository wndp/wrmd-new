<?php

namespace Tests\Unit\Models;

use App\Models\LabCbcResult;
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
final class LabCbcResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aLabCbcResultBelongsToALabReport(): void
    {
        $labReport = LabReport::factory()->for(LabCbcResult::factory(), 'labResult')->create();

        $this->assertInstanceOf(LabCbcResult::class, $labReport->labResult);
    }

    #[Test]
    public function aLabCbcResultIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabCbcResult::factory()->create(),
            'created'
        );
    }
}
