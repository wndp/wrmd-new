<?php

namespace Tests\Feature\Admissions;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Jobs\AssociatePatientToIncident;
use App\Models\Incident;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesUiBehavior;

final class AssociatePatientToIncidentTest extends TestCase
{
    use CreateCase;
    use CreatesUiBehavior;
    use RefreshDatabase;

    #[Test]
    public function aPatientIsAssociatedToAnIncident(): void
    {
        $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED);

        $team = Team::factory()->create();
        $incident = Incident::factory()->create(['team_id' => $team->id]);
        $admission = $this->createCase($team);

        AssociatePatientToIncident::dispatchSync(
            $team,
            $admission->patient,
            $incident->id
        );

        $this->assertTrue(
            $admission->patient->is($incident->fresh()->patient)
        );
    }
}
