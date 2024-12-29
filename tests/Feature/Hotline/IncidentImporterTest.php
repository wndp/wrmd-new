<?php

namespace Tests\Feature\Hotline;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Importing\Declarations;
use App\Importing\Importers\IncidentImporter;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('hotline')]
final class IncidentImporterTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_it_imports_a_spreadsheet_of_new_hotline_incident_data(): void
    {
        $hotlineWildlifeCategoryIsInjuredId = $this->createUiBehavior(
            AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES,
            AttributeOptionUiBehavior::HOTLINE_WILDLIFE_CATEGORY_IS_INJURED
        );

        $hotlineStatusIsOpenId = $this->createUiBehavior(
            AttributeOptionName::HOTLINE_STATUSES,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN
        );

        $hotlineStatusIsResolvedId = $this->createUiBehavior(
            AttributeOptionName::HOTLINE_STATUSES,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED
        );

        // "Upload" the import file to storage
        Storage::putFileAs('imports', new File(__DIR__.'/../../storage/import_hotline_incidents.xlsx'), 'import_hotline_incidents.xlsx');

        $me = $this->createTeamUser();

        $importer = new IncidentImporter(
            $me->user,
            $me->team,
            new Declarations([
                'sessionId' => 'abc123',
                'mappedHeadings' => [
                    'incidents.reported_at' => 'reported_at',
                    'incidents.occurred_at' => 'occurred_at',
                    'incidents.recorded_by' => 'recorded_by',
                    'incidents.duration_of_call' => 'duration_of_call',
                    'incidents.category_id' => 'category',
                    'incidents.is_priority' => 'is_priority',
                    'incidents.suspected_species' => 'suspected_species',
                    'incidents.number_of_animals' => 'number_of_animals',
                    'incidents.incident_status_id' => 'status',
                    'incidents.incident_address' => 'location',
                    'incidents.incident_city' => 'city',
                    'incidents.incident_subdivision' => 'subdivision',
                    'incidents.incident_postal_code' => 'postal_code',
                    'incidents.coordinates' => 'coordinates',
                    'incidents.description' => 'description',
                    'incidents.resolved_at' => 'resolved_at',
                    'incidents.resolution' => 'resolution',
                    'incidents.given_information' => 'given_information',
                    'people.first_name' => 'reporting_party_first_name',
                    'people.last_name' => 'reporting_party_last_name',
                ],
                'translatedValues' => [
                    'incidents.incident_status_id' => [
                        'Resolved' => $hotlineStatusIsResolvedId,
                        'Open' => $hotlineStatusIsOpenId,
                    ],
                    'incidents.category_id' => [
                        'Window Strike' => $hotlineWildlifeCategoryIsInjuredId,
                        'Unknown Wing Injury' => $hotlineWildlifeCategoryIsInjuredId,
                    ],
                    'incidents.incident_subdivision' => ['Wyoming' => 'US-WY'],
                ],
            ])
        );

        Excel::import($importer, 'imports/import_hotline_incidents.xlsx', readerType: \Maatwebsite\Excel\Excel::XLSX);

        //$importer->import('imports/import_hotline_incidents.xlsx', readerType: \Maatwebsite\Excel\Excel::XLSX);

        $person1 = Person::where([
            'team_id' => $me->team->id,
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
        ])->first();

        $this->assertInstanceOf(Person::class, $person1);
        $this->assertDatabaseHas('incidents', [
            'team_id' => $me->team->id,
            'reporting_party_id' => $person1->id,
            'incident_number' => 'HL-19-0001',
            'reported_at' => '2019-11-01 15:20:00',
            'occurred_at' => '2019-10-31 00:00:00',
            'recorded_by' => 'JS',
            'duration_of_call' => '15.00',
            'category_id' => $hotlineWildlifeCategoryIsInjuredId,
            'is_priority' => 0,
            'suspected_species' => 'Northern Goshawk',
            'number_of_animals' => 1,
            'incident_status_id' => $hotlineStatusIsResolvedId,
            'incident_address' => 'Star Valley',
            'incident_city' => null,
            'incident_subdivision' => 'US-WY',
            'incident_postal_code' => null,
            'description' => 'Window Strike',
            'resolved_at' => null,
            'resolution' => 'Advised to observe wildlife and keep in contact, Bird flew away when JS contacted again after receiving photo',
            'given_information' => 0,
        ]);

        $person2 = Person::where([
            'team_id' => $me->team->id,
            'first_name' => 'Shanon',
            'last_name' => 'Smith',
        ])->first();

        $this->assertInstanceOf(Person::class, $person2);
        $this->assertDatabaseHas('incidents', [
            'team_id' => $me->team->id,
            'reporting_party_id' => $person2->id,
            'incident_number' => 'HL-19-0002',
            'reported_at' => '2019-11-01 15:22:38',
            'occurred_at' => '2019-11-01 00:00:00',
            'recorded_by' => 'JS',
            'duration_of_call' => '90.00',
            'category_id' => $hotlineWildlifeCategoryIsInjuredId,
            'is_priority' => 0,
            'suspected_species' => "Swainson's Hawk",
            'number_of_animals' => 1,
            'incident_status_id' => $hotlineStatusIsResolvedId,
            'incident_address' => '15 Christensen Creek Afton WY',
            'incident_city' => null,
            'incident_subdivision' => 'US-WY',
            'incident_postal_code' => null,
            'description' => 'Unknown Wing Injury',
            'resolved_at' => null,
            'resolution' => 'Contact WGFD or IDFG, James Afton Warden stopped by',
            'given_information' => 0,
        ]);
    }
}
