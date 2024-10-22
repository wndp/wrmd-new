<?php

namespace Tests\Unit\Models;

use App\Models\OilProcessing;
use App\Models\Patient;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\HttpResponseException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('oil')]
final class OilProcessingTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function anOilProcessingBelongsToAPatient(): void
    {
        $this->assertInstanceOf(Patient::class, OilProcessing::factory()->create()->patient);
    }

    #[Test]
    public function anOilProcessingIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            OilProcessing::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifAnOilProcessingsPatientIsLockedThenItCanNotBeUpdated(): void
    {
        $patient = Patient::factory()->create();
        $processing = OilProcessing::factory()->create(['patient_id' => $patient->id, 'comments' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $processing->patient->refresh();

        // Cant update
        $processing->update(['comments' => 'NEW']);
        $this->assertEquals('OLD', $processing->fresh()->comments);

        // Cant save
        $processing->comments = 'NEW';
        $processing->save();
        $this->assertEquals('OLD', $processing->fresh()->comments);
    }

    #[Test]
    public function ifAnOilProcessingsPatientIsLockedThenItCanNotBeCreated(): void
    {
        $processing = OilProcessing::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($processing->exists);
    }

    #[Test]
    public function ifAnOilProcessingsPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $processing = OilProcessing::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $processing->patient->refresh();

        $processing->delete();
        $this->assertDatabaseHas('oil_processings', ['id' => $processing->id, 'deleted_at' => null]);
    }

    #[Test]
    public function whenAPatientIsReplicatedSoAreTheEventProcessings(): void
    {
        $patient = Patient::factory()->create();
        OilProcessing::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(1, OilProcessing::where('patient_id', $patient->id)->get());
        $this->assertCount(1, OilProcessing::where('patient_id', $newPatient->id)->get());
    }

    #[Test]
    public function anOilProcessingCanValidateOwnership(): void
    {
        $team = Team::factory()->create();
        $processing = OilProcessing::factory()->create();

        $this->expectException(HttpResponseException::class);
        $processing->validateOwnership($team->id);
    }
}
