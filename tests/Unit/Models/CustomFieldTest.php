<?php

namespace Tests\Unit\Models;

use App\Models\CustomField;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomFieldTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function aCustomFieldBelongsToATeam(): void
    {
        $this->assertInstanceOf(Team::class, CustomField::factory()->create()->team);
    }
}
