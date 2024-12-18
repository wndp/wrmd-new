<?php

namespace Tests\Unit\Extensions\Owcn;

use App\Domain\Patients\Patient;
use App\Domain\Species\Species;
use App\Domain\Users\User;
use App\Extensions\Owcn\Events\OwcnPatientAdmitted;
use App\Extensions\Owcn\Models\WildlifeRecoveryPatient;
use Illuminate\Support\Str;
use MatanYadaev\EloquentSpatial\Objects\Point;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class JobsTest extends TestCase
{
    //use AssistsWithCases;
    //use AssistsWithAuthentication;

    /**
     * Unique hash used for individual tests.
     *
     * @var string
     */
    private $hash;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hash = Str::random();
    }

    #[Test]
    public function admit_wildlife_recovery_patient(): void
    {
        Species::factory()->create(['id' => 999999]);
        $team = Team::factory()->create();

        WildlifeRecoveryPatient::factory()->create([
            'team_id' => $team->id,
            'patient_id' => null,
            'qr_code' => 'https://wr.md/'.$this->hash,
            'entrytime' => '20160623-104257',
            'latitude' => '35.37027100104407',
            'longitude' => '-120.8656003699832',
            'condition' => 'Dead',
            'locdesc' => 'on the beach',
        ]);

        $this->setSetting($team, 'event_year', 2015);

        $this->expectsEvents(OwcnPatientAdmitted::class);

        $command = $this->mapInputToJob(\App\Extensions\Owcn\Bus\AdmitWildlifeRecoveryPatient::class, [
            'hash' => $this->hash,
            'admittedAt' => '2016-06-23 10:29:00',
            'user' => User::factory()->create(),
        ]);

        $command->handle();

        $this->assertDatabaseHas('wildlife_recovery_patients', [
            'qr_code' => 'https://wr.md/'.$this->hash,
        ]);

        $this->assertDatabaseHas('admissions', [
            'team_id' => $team->id,
            'case_year' => 2015,
            'hash' => $this->hash,
        ]);

        $patient = Patient::where([
            'admitted_at' => '2016-06-23 10:29:00',
            'found_at' => '2016-06-23',
            'disposition' => 'Dead on arrival',
            'dispositioned_at' => '2016-06-23',
        ])->first();

        $this->assertInstanceOf(Patient::class, $patient);
        $this->assertEquals($patient->coordinates_found->getLat(), '35.37027100104407');
        $this->assertEquals($patient->coordinates_found->getLng(), '-120.8656003699832');
    }

    #[Test]
    public function associate_wildlife_recovery_patients(): void
    {
        // wrmd
        Team::factory()->create(['id' => '1']);

        $team = Team::factory()->create();

        $case = $this->createCase([
            'team_id' => $team->id,
            'hash' => $this->hash,
        ], [
            'address_found' => 'the beach',
            'coordinates_found' => new Point(34.876787, 0),
            //            'lat_found' => '34.876787',
        ]);

        $case->patient->rescuer->organization = null;
        $case->patient->rescuer->first_name = 'Sally';
        $case->patient->rescuer->save();

        WildlifeRecoveryPatient::factory()->create([
            'team_id' => $team->id,
            'patient_id' => null,
            'patient_id' => null,
            'qr_code' => 'https://wr.md/'.$this->hash,
            'latitude' => '12.123123',
            'longitude' => '12.123123',
            'userorg' => 'Team 1a',
            'teamname' => 'Bob',
        ]);

        $command = new \App\Extensions\Owcn\Bus\AssociateWildlifeRecoveryPatients;
        $command->handle();

        $this->assertDatabaseHas('wildlife_recovery_patients', [
            'team_id' => $team->id,
            'patient_id' => $case->patient_id,
            'qr_code' => 'https://wr.md/'.$this->hash,
        ]);

        $patient = Patient::where([
            'id' => $case->patient_id,
            'address_found' => 'the beach',
            'criminal_activity' => 1,
            'diagnosis' => 'Affected by oil spill',
        ])->first();

        $this->assertInstanceOf(Patient::class, $patient);
        $this->assertEquals($patient->coordinates_found->getLat(), 34.876787);
        $this->assertEquals($patient->coordinates_found->getLng(), 0);

        $this->assertDatabaseHas('people', [
            'id' => $case->patient->rescuer_id,
            'organization' => 'Team 1a',
            'first_name' => 'Sally',
        ]);
    }
}
