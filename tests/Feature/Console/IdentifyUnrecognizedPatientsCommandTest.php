<?php

namespace Tests\Feature\Console;

use App\Models\CommonName;
use App\Models\Taxon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class IdentifyUnrecognizedPatientsCommandTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public $connectionsToTransact = ['singlestore', 'wildalert'];

    public function test_it_identifies_an_unrecognized_patient_with_one_common_name(): void
    {
        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly(['id' => '123'])->id,
            'common_name' => 'foobar',
        ]);

        $case = $this->createCase(patientOverrides: ['common_name' => 'foobar', 'taxon_id' => null]);

        $this->artisan('wrmd:identify-unrecognized-patients')->assertSuccessful();

        $this->assertDatabaseHas('patients', [
            'id' => $case->patient_id,
            'common_name' => 'foobar',
            'taxon_id' => '123',
        ]);
    }

    public function test_it_identifies_an_unrecognized_patient_with_multiple_common_names(): void
    {
        $speciesId = Taxon::factory()->createQuietly(['id' => '123'])->id;

        CommonName::factory()->createQuietly(['taxon_id' => $speciesId, 'common_name' => 'barbaz']);
        CommonName::factory()->createQuietly(['taxon_id' => $speciesId, 'common_name' => 'borboz']);

        $case = $this->createCase(patientOverrides: ['common_name' => 'barbaz', 'taxon_id' => null]);

        $this->artisan('wrmd:identify-unrecognized-patients');

        $this->assertDatabaseHas('patients', [
            'id' => $case->patient_id,
            'common_name' => 'barbaz',
            'taxon_id' => '123',
        ]);
    }

    public function test_it_identifies_multiple_chunks_of_unrecognizedspecies(): void
    {
        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly(['id' => '123'])->id,
            'common_name' => 'zippy',
        ]);

        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly(['id' => '456'])->id,
            'common_name' => 'zapper',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->createCase(patientOverrides: ['common_name' => 'zippy', 'taxon_id' => null]);
        }

        for ($i = 0; $i < 5; $i++) {
            $this->createCase(patientOverrides: ['common_name' => 'zapper', 'taxon_id' => null]);
        }

        $this->artisan('wrmd:identify-unrecognized-patients');

        $this->assertDatabaseMissing('patients', [
            'common_name' => 'zippy',
            'taxon_id' => null,
        ]);

        $this->assertDatabaseMissing('patients', [
            'common_name' => 'zapper',
            'taxon_id' => null,
        ]);
    }
}
