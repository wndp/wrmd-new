<?php

namespace Tests\Feature\People;

use App\Models\Donation;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PersonExportControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_it_fails_validation_when_trying_to_export_a_people(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->post(route('people.export'))
            ->assertInvalid(['format' => 'The format field is required.']);

        $this->actingAs($me->user)->post(route('people.export'), [
            'format' => 'xlsx',
        ])
            ->assertInvalid(['group' => 'The group field is required.'])
            ->assertInvalid(['date_from' => 'The date from field is required.'])
            ->assertInvalid(['date_to' => 'The date to field is required.']);

        $this->actingAs($me->user)->post(route('people.export'), [
            'format' => 'xlsx',
            'date_from' => 'foo',
            'date_to' => 'foo',
        ])
            ->assertInvalid(['date_from' => 'The date from field must be a valid date'])
            ->assertInvalid(['date_to' => 'The date to field must be a valid date']);

        $this->actingAs($me->user)->post(route('people.export'), [
            'format' => 'xlsx',
            'date_from' => '2022-06-30',
            'date_to' => '2022-06-01',
        ])
            ->assertInvalid(['date_from' => 'The date from field must be a date before or equal to date to.'])
            ->assertInvalid(['date_to' => 'The date to field must be a date after or equal to date from.']);
    }

    public function test_it_exports_rescuers(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $me = $this->createTeamUser();
        $rescuer = Person::factory()->create(['team_id' => $me->team->id, 'first_name' => 'John', 'last_name' => 'Doe']);
        $this->createCase($me->team, patientOverrides: ['date_admitted_at' => now(), 'rescuer_id' => $rescuer->id]);

        $this->actingAs($me->user)
            ->from(route('people.rescuers.index'))
            ->post(route('people.export'), [
                'format' => 'xlsx',
                'group' => 'rescuers',
                'date_from' => now()->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('people.rescuers.index'));

        Excel::assertStored('/reports\/\d+\/\w+\/rescuers\.xlsx/', 's3', function ($export) use ($rescuer) {
            $people = $export->data()['data'];

            return $people->count() === 1 && $people->first()[0] === $rescuer->id;
        });
    }

    public function test_it_exports_volunteers(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $me = $this->createTeamUser();
        $volunteer = Person::factory()->create([
            'team_id' => $me->team->id, 'first_name' => 'John', 'last_name' => 'Doe', 'is_volunteer' => 1,
        ]);

        $this->actingAs($me->user)
            ->from(route('people.volunteers.index'))
            ->post(route('people.export'), [
                'format' => 'xlsx',
                'group' => 'volunteers',
                'date_from' => now()->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('people.volunteers.index'));

        Excel::assertStored('/reports\/\d+\/\w+\/volunteers\.xlsx/', 's3', function ($export) use ($volunteer) {
            $people = $export->data()['data'];

            return $people->count() === 1 && $people->first()[0] === $volunteer->id;
        });
    }

    public function test_it_exports_members(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $me = $this->createTeamUser();
        $member = Person::factory()->create([
            'team_id' => $me->team->id, 'first_name' => 'John', 'last_name' => 'Doe', 'is_member' => 1,
        ]);

        $this->actingAs($me->user)
            ->from(route('people.members.index'))
            ->post(route('people.export'), [
                'format' => 'xlsx',
                'group' => 'members',
                'date_from' => now()->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('people.members.index'));

        Excel::assertStored('/reports\/\d+\/\w+\/members\.xlsx/', 's3', function ($export) use ($member) {
            $people = $export->data()['data'];

            return $people->count() === 1 && $people->first()[0] === $member->id;
        });
    }

    public function test_it_exports_donors(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $me = $this->createTeamUser();
        $donor = Person::factory()
            ->has(Donation::factory()->state([
                'donated_at' => now(),
            ])->count(2))
            ->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)
            ->from(route('people.donors.index'))
            ->post(route('people.export'), [
                'format' => 'xlsx',
                'group' => 'donors',
                'date_from' => now()->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('people.donors.index'));

        Excel::assertStored('/reports\/\d+\/\w+\/donors\.xlsx/', 's3', function ($export) use ($donor) {
            $people = $export->data()['data'];

            return $people->count() === 1
                && $people->first()[0][0] === $donor->id
                && $people->first()[1][0] === $donor->id;
        });
    }
}
