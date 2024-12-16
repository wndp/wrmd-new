<?php

namespace Tests\Unit\Models;

use App\Models\Patient;
use App\Models\Recheck;
use App\Schedulable;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class RecheckTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;
    //use CreatesTeamUser;

    #[Test]
    public function aRecheckIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Recheck::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifARechecksPatientIsLockedThenItCanNotBeUpdated(): void
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

    #[Test]
    public function ifARechecksPatientIsLockedThenItCanNotBeCreated(): void
    {
        $recheck = Recheck::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($recheck->exists);
    }

    #[Test]
    public function ifARechecksPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $recheck = Recheck::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $recheck->patient->refresh();

        $recheck->delete();
        $this->assertDatabaseHas('rechecks', ['id' => $recheck->id, 'deleted_at' => null]);
    }

    #[Test]
    public function aRecheckIsSchedualable(): void
    {
        $this->assertInstanceOf(Schedulable::class, Recheck::factory()->make());
    }
}
