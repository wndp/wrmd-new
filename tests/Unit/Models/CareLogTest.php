<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\CareLog;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class CareLogTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use GetsCareLogs;
    use RefreshDatabase;

    #[Test]
    public function aCareLogHasFormattedFullWeightAttribute(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $log = CareLog::factory()->make([
            'weight' => 123.02,
            'weight_unit_id' => $gWeightId,
        ]);

        $this->assertEquals('123.02g', $log->full_weight);

        $log = CareLog::factory()->make([
            'weight' => 12.0,
            'weight_unit_id' => '',
        ]);

        $this->assertEquals('', $log->full_weight);

        $log = CareLog::factory()->make([
            'weight' => 0,
        ]);

        $this->assertEquals('', $log->full_weight);
    }

    #[Test]
    public function aCareLogHasFormattedFullTemperatureAttribute(): void
    {
        [$cTemperatureId, $fTemperatureId, $kTemperatureId] = $this->temperatureUnits();

        $log = CareLog::factory()->make([
            'temperature' => 101.00,
            'temperature_unit_id' => $fTemperatureId,
        ]);

        $this->assertEquals('101F', $log->full_temperature);

        $log = CareLog::factory()->make([
            'temperature' => 103.00,
            'temperature_unit_id' => '',
        ]);

        $this->assertEquals('', $log->full_temperature);

        $log = CareLog::factory()->make([
            'temperature' => 0,
        ]);

        $this->assertEquals('', $log->full_temperature);
    }

    #[Test]
    public function aCareLogHasFormattedSummaryBodyAttribute(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();
        [$cTemperatureId, $fTemperatureId, $kTemperatureId] = $this->temperatureUnits();

        $careLog = CareLog::factory()->make([
            'weight' => 123.01,
            'weight_unit_id' => $gWeightId,
            'temperature' => 101.50,
            'temperature_unit_id' => $fTemperatureId,
            'comments' => 'foo bar',
        ]);

        $this->assertSame('Weight: 123.01g. Temperature: 101.5F. foo bar', $careLog->summary_body);
    }

    #[Test]
    public function aCareLogIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            CareLog::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifACareLogsPatientIsLockedThenItCanNotBeUpdated(): void
    {
        $patient = Patient::factory()->create();
        $CareLog = CareLog::factory()->create(['patient_id' => $patient->id, 'comments' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $CareLog->patient->refresh();

        // Cant update
        $CareLog->update(['comments' => 'NEW']);
        $this->assertEquals('OLD', $CareLog->fresh()->comments);

        // Cant save
        $CareLog->comments = 'NEW';
        $CareLog->save();
        $this->assertEquals('OLD', $CareLog->fresh()->comments);
    }

    #[Test]
    public function ifACareLogsPatientIsLockedThenItCanNotBeCreated(): void
    {
        $CareLog = CareLog::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($CareLog->exists);
    }

    #[Test]
    public function ifACareLogsPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $careLog = CareLog::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $careLog->patient->refresh();

        $careLog->delete();
        $this->assertNotSoftDeleted($careLog);
    }

    #[Test]
    public function whenAPatientIsReplicatedSoAreTheCareLogs(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        CareLog::factory()->count(2)->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(2, CareLog::where('patient_id', $patient->id)->get());
        $this->assertCount(2, CareLog::where('patient_id', $newPatient->id)->get());
    }
}
