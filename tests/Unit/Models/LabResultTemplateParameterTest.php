<?php

namespace Tests\Unit\Models;

use App\Models\AttributeOption;
use App\Models\LabResultTemplate;
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
final class LabResultTemplateParameterTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aLabResultTemplateParameterBelongsToALabResultTemplate(): void
    {
        $parameter = LabResultTemplateParameter::factory()->make();

        $this->assertInstanceOf(LabResultTemplate::class, $parameter->labResultTemplate);
    }

    #[Test]
    public function aLabResultTemplateParameterHasAParameter(): void
    {
        $parameter = LabResultTemplateParameter::factory()->make();

        $this->assertInstanceOf(AttributeOption::class, $parameter->parameter);
    }
}
