<?php

namespace Tests\Unit\Models;

use App\Models\Communication;
use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('hotline')]
final class CommunicationTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aCommunicationBelongsToAnIncident(): void
    {
        $this->assertInstanceOf(Incident::class, Communication::factory()->create()->incident);
    }

    #[Test]
    public function aCommunicationIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Communication::factory()->create(),
            'created'
        );
    }
}
