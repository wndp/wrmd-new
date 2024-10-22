<?php

namespace Tests\Unit\Models;

use App\Actions\GetPatientWeights;
use App\Models\Necropsy;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesUiBehavior;

final class NecropsyTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function aNecropsyIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Necropsy::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifANecropsiesPatientIsLockedThenItCanNotBeUpdated(): void
    {
        $patient = Patient::factory()->create();
        $necropsy = Necropsy::factory()->create(['patient_id' => $patient->id, 'prosector' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $necropsy->patient->refresh();

        // Cant update
        $necropsy->update(['prosector' => 'NEW']);
        $this->assertEquals('OLD', $necropsy->fresh()->prosector);

        // Cant save
        $necropsy->prosector = 'NEW';
        $necropsy->save();
        $this->assertEquals('OLD', $necropsy->fresh()->prosector);
    }

    #[Test]
    public function ifANecropsiesPatientIsLockedThenItCanNotBeCreated(): void
    {
        $necropsy = Necropsy::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($necropsy->exists);
    }

    #[Test]
    public function ifANecropsiesPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $necropsy = Necropsy::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $necropsy->patient->refresh();

        $necropsy->delete();
        $this->assertDatabaseHas('necropsies', ['id' => $necropsy->id, 'deleted_at' => null]);
    }

    #[Test]
    public function whenAPatientIsReplicatedSoIsTheNecropsy(): void
    {
        $patient = Patient::factory()->create();
        Necropsy::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(1, Necropsy::where('patient_id', $patient->id)->get());
        $this->assertCount(1, Necropsy::where('patient_id', $newPatient->id)->get());
    }

    #[Test]
    public function itFiltersTheNecropsyWeightIntoThePatientWeightsCollection(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $patient = Patient::factory()->create();

        Necropsy::factory()->create([
            'patient_id' => $patient->id,
            'weight' => 123.45,
            'weight_unit_id' => $gWeightId,
            'date_necropsied_at' => '2019-03-23',
        ]);

        $weightObj = GetPatientWeights::run($patient)->getLastWeight();

        $this->assertEquals('Necropsy', $weightObj->type);
        $this->assertEquals(123.45, $weightObj->weight);
        $this->assertEquals($gWeightId, $weightObj->unit_id);
        $this->assertEquals('2019-03-23', $weightObj->weighed_at_date);
    }
}
