<?php

namespace Tests\Unit\Models;

use App\Models\Communication;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('hotline')]
final class CommunicationTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_communication_belongs_to_an_incident(): void
    {
        $this->assertInstanceOf(Incident::class, Communication::factory()->create()->incident);
    }

    public function test_a_communication_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Communication::factory()->create(),
            'created'
        );
    }
}
