<?php

namespace Tests\Feature\Admissions;

use App\Actions\AdmitPatient;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Events\PatientAdmitted;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class AdmitPatientTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    private $me;

    private $pendingDispositionUiBehavior;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pendingDispositionUiBehavior = $this->createUiBehavior(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING
        );

        $this->me = $this->createTeamUser();
        Auth::loginUsingId($this->me->user->id);
    }

    public function test_a_known_person_can_be_used_when_admitting_a_patient(): void
    {
        $person = Person::factory()->create([
            'team_id' => $this->me->team->id,
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
        ]);

        $result = AdmitPatient::run($this->me->team, 2018, [
            'id' => $person->id,
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
        ], [
            'admitted_at' => '2023-01-17 7:51',
            'common_name' => 'Red-headed Foo',
        ]);

        $this->assertTrue($result->first()->patient->rescuer->is($person));
        $this->assertEquals('Jim Halpert', $result->first()->patient->rescuer->full_name);
    }

    public function test_another_teams_known_rescuer_cannot_be_used_when_admitting_a_patient(): void
    {
        $wrongAccount = Team::factory()->create();

        $person = Person::factory()->create([
            'team_id' => $wrongAccount->id,
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
        ]);

        $result = AdmitPatient::run($this->me->team, 2018, [
            'id' => $person->id,
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
        ], [
            'admitted_at' => '2023-01-17 7:51',
            'common_name' => 'Red-headed Foo',
        ]);

        $this->assertFalse($result->first()->patient->rescuer->is($person));
        $this->assertEquals('Jim Halpert', $result->first()->patient->rescuer->full_name);
    }

    public function test_an_unknown_rescuer_can_be_used_when_admitting_a_patient(): void
    {
        $result = AdmitPatient::run($this->me->team, 2018, [
            'id' => null,
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
        ], [
            'admitted_at' => '2023-01-17 7:51',
            'common_name' => 'Red-headed Foo',
        ]);

        $this->assertInstanceOf(Person::class, $result->first()->patient->rescuer);
        $this->assertEquals('Jim Halpert', $result->first()->patient->rescuer->full_name);
    }

    public function test_a_rescuer_model_can_be_used_when_admitting_a_patient(): void
    {
        $person = Person::factory()->create([
            'team_id' => $this->me->team->id,
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
        ]);

        $result = AdmitPatient::run($this->me->team, 2018, $person, [
            'admitted_at' => '2023-01-17 7:51',
            'common_name' => 'Red-headed Foo',
        ]);

        $this->assertTrue($result->first()->patient->rescuer->is($person));
        $this->assertEquals('Jim Halpert', $result->first()->patient->rescuer->full_name);
    }

    public function test_events_are_fired_when_an_admissions_is_admitted(): void
    {
        Event::fake();

        AdmitPatient::run(
            $this->me->team,
            2018,
            [
                'first_name' => 'Jim',
                'last_name' => 'Halpert',
            ],
            [
                'admitted_at' => '2023-01-17 7:51',
                'common_name' => 'Red-headed Foo',
            ]
        );

        Event::assertDispatched(PatientAdmitted::class);
    }

    public function test_if_the_rescuer_is_not_a_model_than_it_must_be_an_array_when_admitting_a_patient(): void
    {
        $team = Team::factory()->create();
        $this->expectException(\TypeError::class);
        //$this->expectException(\App\Exceptions\UnprocessableAdmissionException::class);

        AdmitPatient::run($team, 2018, 'wrong', []);
    }

    public function test_the_account_possession_id_is_set(): void
    {
        $result = AdmitPatient::run($this->me->team, 2023, Person::factory()->create([
            'team_id' => $this->me->team->id,
        ]), [
            'admitted_at' => '2023-01-17 7:51',
            'common_name' => 'Red-headed Foo',
        ]);

        $this->assertSame($this->me->team->id, $result->first()->patient->team_possession_id);
    }

    public function test_multiple_admissions_are_persisted_using_the_created_models_when_admitting_a_patient(): void
    {
        $collection = AdmitPatient::run(
            $this->me->team,
            2018,
            [
                'first_name' => 'Jim',
                'last_name' => 'Halpert',
            ],
            [
                'admitted_at' => '2023-01-17 7:51',
                'common_name' => 'Red-headed Foo',
            ],
            2
        );

        $this->assertCount(2, $collection);
        $this->assertsame([1, 2], $collection->pluck('case_id')->toArray());
        $this->assertEquals('Red-headed Foo', $collection->get(0)->patient->common_name);
        $this->assertEquals('Red-headed Foo', $collection->get(1)->patient->common_name);
    }

    public function test_the_admission_is_persisted_using_the_created_models_when_admitting_a_patient(): void
    {
        $collection = AdmitPatient::run(
            $this->me->team,
            2018,
            [
                'first_name' => 'Jim',
                'last_name' => 'Halpert',
            ],
            [
                'admitted_at' => '2023-01-17 7:51',
                'common_name' => 'Red-headed Foo',
            ]
        );
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals($this->pendingDispositionUiBehavior->attribute_option_id, $collection->first()->patient->disposition_id);
        $this->assertEquals('2023-01-17', $collection->first()->patient->date_admitted_at->toDateString());
        $this->assertEquals('15:51:00', $collection->first()->patient->time_admitted_at);
        $this->assertEquals('Red-headed Foo', $collection->first()->patient->common_name);
    }

    public function test_the_coordinates_found_can_be_saved_to_the_patient_when_admitting_a_patient(): void
    {
        $result = AdmitPatient::run($this->me->team, 2018, [], [
            'admitted_at' => '2018-03-09 10:30:00',
            'common_name' => 'Red-headed Foo',
            'lat_found' => 12.34567898,
            'lng_found' => 98.76543218,
            'county_found' => 'somewhere',
        ]);

        $this->assertEquals(12.34567898, $result->first()->patient->coordinates_found?->latitude);
        $this->assertEquals(98.76543218, $result->first()->patient->coordinates_found?->longitude);
        $this->assertEquals('somewhere', $result->first()->patient->county_found);
    }

    public function test_the_number_of_cases_to_create_can_be_more_than_one_when_admitting_a_patient(): void
    {
        $results = AdmitPatient::run($this->me->team, 2018, [], [
            'admitted_at' => '2018-03-09 10:30:00',
            'common_name' => 'Red-headed Foo',
            'county_found' => 'somewhere',
        ], 2);

        $this->assertSame(2, $results->count());
    }

    public function test_the_patients_admittance_details_can_be_set_and_the_disposition_is_always_overriden_to_pending_when_admitting_a_patient(): void
    {
        $result = AdmitPatient::run($this->me->team, 2018, [], [
            'admitted_at' => '2023-01-17 7:51',
            'common_name' => 'Red-headed Foo',
            'disposition_id' => 999,
        ]);

        $this->assertEquals($this->pendingDispositionUiBehavior->attribute_option_id, $result->first()->patient->disposition_id);
    }

    public function test_type_errors_are_caught_when_trying_to_persist_the_patient(): void
    {
        $team = Team::factory()->create();
        $this->expectException(\TypeError::class);

        AdmitPatient::run($team, 2018, [], 'wrong');
    }
}
