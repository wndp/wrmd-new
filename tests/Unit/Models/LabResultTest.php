<?php

namespace Tests\Unit\Models;

use App\Models\LabReport;
use App\Models\LabResult;
use App\Models\LabResultTemplateParameter;
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
final class LabResultTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aLabResultBelongsToALabReport(): void
    {
        $labResult = LabResult::factory()->make();

        $this->assertInstanceOf(LabReport::class, $labResult->labReport);
    }

    #[Test]
    public function aLabResultBelongsToALabResultTemplateParameter(): void
    {
        $labResult = LabResult::factory()->make();

        $this->assertInstanceOf(LabResultTemplateParameter::class, $labResult->labResultTemplateParameter);
    }
}
