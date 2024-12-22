<?php

namespace Tests\Unit\Models;

use App\Models\CustomField;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CustomFieldTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_custom_field_belongs_to_a_team(): void
    {
        $this->assertInstanceOf(Team::class, CustomField::factory()->create()->team);
    }
}
