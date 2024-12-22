<?php

namespace Tests\Feature\People\Combine;

use App\Enums\Role;
use App\Events\NewPersonCreated;
use App\Listeners\ReAssociateDonations;
use App\Listeners\ReAssociatePatients;
use App\Models\Donation;
use App\Models\Incident;
use App\Models\Patient;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class CombineMergeControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_merge_people(): void
    {
        $this->post(route('people.combine.merge'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_merge_people(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->post(route('people.combine.merge'))->assertForbidden();
    }

    public function test_a_new_person_array_is_required_to_search_for_matches(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->post(route('people.combine.merge'))
            ->assertInvalid(['newPerson' => 'There was a problem creating the new person.']);

        $this->actingAs($me->user)->post(route('people.combine.merge', [
            'newPerson' => 'foo',
        ]))
            ->assertInvalid(['newPerson' => 'There was a problem creating the new person.']);
    }

    public function test_an_old_people_array_is_required_to_search_for_matches(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->post(route('people.combine.merge'))
            ->assertInvalid(['oldPeople' => 'The old people field is required.']);

        $this->actingAs($me->user)->post(route('people.combine.merge', [
            'oldPeople' => 'foo',
        ]))
            ->assertInvalid(['oldPeople' => 'The old people field must be an array.']);

        $this->actingAs($me->user)->post(route('people.combine.merge', [
            'oldPeople' => ['foo'],
        ]))
            ->assertInvalid(['oldPeople.0' => 'There was a problem with one of the duplicate persons.']);
    }

    public function test_it_combine_the_search_results(): void
    {
        $me = $this->createTeamUser();
        $people = Person::factory()->count(2)->create([
            'team_id' => $me->team->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '(925) 555-1234',
        ]);

        $this->actingAs($me->user)
            ->from(route('people.combine.search'))
            ->post(route('people.combine.merge', [
                'newPerson' => [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'phone' => '(925) 555-5678',
                ],
                'oldPeople' => $people->pluck('id')->toArray(),
            ]))
            ->assertRedirect(route('people.combine.search'));

        $newPerson = Person::where([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'phone' => '(925) 555-5678',
        ])->get();

        // Event::assertDispatched(NewPersonCreated::class, function ($event) use ($people) {
        //     return $event->person->first_name === 'John'
        //         && $event->person->last_name === 'Smith'
        //         && $event->oldPeoplesIds === $people->pluck('id')->toArray();
        // });

        // Event::assertListening(NewPersonCreated::class, ReAssociatePatients::class);
        // Event::assertListening(NewPersonCreated::class, ReAssociateDonations::class);

        $this->assertCount(1, $newPerson);
        $this->assertNotContains($newPerson->first()->id, $people->pluck('id')->toArray());
        $this->assertDatabaseMissing('people', ['id' => $people->first()->id]);
        $this->assertDatabaseMissing('people', ['id' => $people->last()->id]);
    }

    public function test_it_re_associates_the_old_peoples_patients_to_the_new_person(): void
    {
        $me = $this->createTeamUser();

        $people = Person::factory()->has(
            Patient::factory()->count(2)
        )->count(2)->create();

        $patients = $people->pluck('patients')->collapse();

        $this->actingAs($me->user)
            ->from(route('people.combine.search'))
            ->post(route('people.combine.merge', [
                'newPerson' => [
                    'first_name' => 'John',
                ],
                'oldPeople' => $people->pluck('id')->toArray(),
            ]))
            ->assertRedirect(route('people.combine.search'));

        $newPerson = Person::where([
            'first_name' => 'John',
        ])->first();

        $patients
            ->each->refresh()
            ->pluck('rescuer_id')
            ->each(
                fn ($rescuerId) => $this->assertSame($newPerson->id, $rescuerId)
            );
    }

    public function test_it_re_associates_the_old_peoples_donations_to_the_new_person(): void
    {
        $me = $this->createTeamUser();

        $people = Person::factory()->has(
            Donation::factory()->count(2)
        )->count(2)->create();

        $donations = $people->pluck('donations')->collapse();

        $this->actingAs($me->user)
            ->from(route('people.combine.search'))
            ->post(route('people.combine.merge', [
                'newPerson' => [
                    'first_name' => 'John',
                ],
                'oldPeople' => $people->pluck('id')->toArray(),
            ]))
            ->assertRedirect(route('people.combine.search'));

        $newPerson = Person::where([
            'first_name' => 'John',
        ])->first();

        $donations
            ->each->refresh()
            ->pluck('person_id')
            ->each(
                fn ($personId) => $this->assertSame($newPerson->id, $personId)
            );
    }

    public function test_it_re_associates_the_old_peoples_incidents_to_the_new_person(): void
    {
        $me = $this->createTeamUser();

        $people = Person::factory()->has(
            Incident::factory()->count(2)
        )->count(2)->create();

        $incidents = $people->pluck('incidents')->collapse();

        $this->actingAs($me->user)
            ->from(route('people.combine.search'))
            ->post(route('people.combine.merge', [
                'newPerson' => [
                    'first_name' => 'John',
                ],
                'oldPeople' => $people->pluck('id')->toArray(),
            ]))
            ->assertRedirect(route('people.combine.search'));

        $newPerson = Person::where([
            'first_name' => 'John',
        ])->first();

        $incidents
            ->each->refresh()
            ->pluck('reporting_party_id')
            ->each(
                fn ($personId) => $this->assertSame($newPerson->id, $personId)
            );
    }
}
