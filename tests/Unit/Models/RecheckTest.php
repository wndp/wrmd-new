<?php

namespace Tests\Unit\Models;

use App\Models\Patient;
use App\Models\Recheck;
use App\Schedulable;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class RecheckTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;
    //use CreatesTeamUser;

    public function test_a_recheck_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Recheck::factory()->create(),
            'created'
        );
    }

    public function test_if_a_rechecks_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $recheck = Recheck::factory()->create(['patient_id' => $patient->id, 'description' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $recheck->patient->refresh();

        // Cant update
        $recheck->update(['description' => 'NEW']);
        $this->assertEquals('OLD', $recheck->fresh()->description);

        // Cant save
        $recheck->description = 'NEW';
        $recheck->save();
        $this->assertEquals('OLD', $recheck->fresh()->description);
    }

    public function test_if_a_rechecks_patient_is_locked_then_it_can_not_be_created(): void
    {
        $recheck = Recheck::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($recheck->exists);
    }

    public function test_if_a_rechecks_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $recheck = Recheck::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $recheck->patient->refresh();

        $recheck->delete();
        $this->assertDatabaseHas('rechecks', ['id' => $recheck->id, 'deleted_at' => null]);
    }

    public function test_a_recheck_is_schedualable(): void
    {
        $this->assertInstanceOf(Schedulable::class, Recheck::factory()->make());
    }
}
