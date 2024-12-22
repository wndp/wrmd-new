<?php

namespace Tests\Feature\People;

use App\Enums\Role;
use App\Models\Donation;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PersonControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_people(): void
    {
        $this->get(route('people.rescuers.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_people(): void
    {
        $me = $this->createTeamUser(role: Role::USER);
        $this->actingAs($me->user)->get(route('people.rescuers.index'))->assertForbidden();
    }

    public function test_it_displays_rescuers_on_the_people_index_page(): void
    {
        $me = $this->createTeamUser();

        $rescuer = Person::factory()->create(['team_id' => $me->team->id, 'first_name' => 'John', 'last_name' => 'Doe']);
        $this->createCase($me->team, patientOverrides: ['date_admitted_at' => now(), 'rescuer_id' => $rescuer->id]);

        $this->actingAs($me->user)->get(route('people.rescuers.index'))
            ->assertOk()
            ->assertInertia(function ($page) use ($rescuer) {
                $page->component('People/Index')
                    ->has('people')
                    ->where('people.data.0.id', $rescuer->id);
            });
    }

    public function test_it_displays_volunteers_on_the_people_index_page(): void
    {
        $me = $this->createTeamUser();
        $volunteer = Person::factory()->create(['team_id' => $me->team->id, 'is_volunteer' => true]);

        $this->actingAs($me->user)->get(route('people.volunteers.index'))
            ->assertOk()
            ->assertInertia(function ($page) use ($volunteer) {
                $page->component('People/Index')
                    ->has('people')
                    ->where('people.data.0.id', $volunteer->id);
            });
    }

    public function test_it_displays_members_on_the_people_index_page(): void
    {
        $me = $this->createTeamUser();
        $member = Person::factory()->create(['team_id' => $me->team->id, 'is_member' => true]);

        $this->actingAs($me->user)->get(route('people.members.index'))
            ->assertOk()
            ->assertInertia(function ($page) use ($member) {
                $page->component('People/Index')
                    ->has('people')
                    ->where('people.data.0.id', $member->id);
            });
    }

    public function test_it_displays_donors_on_the_people_index_page(): void
    {
        $me = $this->createTeamUser();
        $donor = Person::factory()->has(Donation::Factory())->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->get(route('people.donors.index'))
            ->assertOk()
            ->assertInertia(function ($page) use ($donor) {
                $page->component('People/Index')
                    ->has('people')
                    ->where('people.data.0.id', $donor->id);
            });
    }

    public function test_it_displays_the_person_create_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to('create-people');

        $this->actingAs($me->user)->get(route('people.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('People/Create');
            });
    }

    public function test_it_fails_validation_when_trying_to_store_a_new_person(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to('create-people');

        $this->actingAs($me->user)->post(route('people.store'))
            ->assertInvalid(['organization' => 'The organization field is required when first name is not present.'])
            ->assertInvalid(['first_name' => 'The first name field is required when organization is not present.']);

        $this->actingAs($me->user)->post(route('people.store'), [
            'email' => 'foo',
            'no_solicitations' => 'foo',
            'is_volunteer' => 'foo',
            'is_member' => 'foo',
        ])
            ->assertInvalid(['email' => 'The email field must be a valid email address.'])
            ->assertInvalid(['no_solicitations' => 'The no solicitations field must be true or false.'])
            ->assertInvalid(['is_volunteer' => 'The is volunteer field must be true or false.'])
            ->assertInvalid(['is_member' => 'The is member field must be true or false.']);
    }

    public function test_it_stores_a_new_person(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to('create-people');

        $response = $this->actingAs($me->user)->post(route('people.store'), [
            'organization' => 'Dunder Mifflin',
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
            'phone' => '(570) 555-1234',
            'alternate_phone' => '(570) 555-5678',
            'email' => 'jim@dundermifflin.com',
            'subdivision' => 'PA',
            'city' => 'Scranton',
            'address' => '1725 Slough Avenue',
            'postal_code' => '18505',
            'notes' => 'Not a real place',
            'no_solicitations' => 0,
            'is_volunteer' => 1,
            'is_member' => 0,
        ]);

        $person = Person::where('organization', 'Dunder Mifflin')->first();
        $response->assertRedirect(route('people.edit', $person->id));

        $this->assertDatabaseHas('people', [
            'team_id' => $me->team->id,
            'organization' => 'Dunder Mifflin',
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
            'phone' => '(570) 555-1234',
            'alternate_phone' => '(570) 555-5678',
            'email' => 'jim@dundermifflin.com',
            'subdivision' => 'PA',
            'city' => 'Scranton',
            'address' => '1725 Slough Avenue',
            'postal_code' => '18505',
            'notes' => 'Not a real place',
            'no_solicitations' => 0,
            'is_volunteer' => 1,
            'is_member' => 0,
        ]);
    }

    public function test_it_validates_ownership_of_a_person_before_editting(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create();

        $this->actingAs($me->user)->get(route('people.edit', $person), [
            'organization' => 'Dunder Mifflin',
        ])
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_person_edit_page(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create(['team_id' => $me->team]);

        $this->actingAs($me->user)->get(route('people.edit', $person))
            ->assertOk()
            ->assertInertia(function ($page) use ($person) {
                $page->component('People/Edit')
                    ->where('person.id', $person->id)
                    ->has('person');
            });
    }

    public function test_it_validates_ownership_of_a_person_before_updating(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create();

        $this->actingAs($me->user)->put(route('people.update', $person), [
            'organization' => 'Dunder Mifflin',
        ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_a_person(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create(['team_id' => $me->team]);

        $this->actingAs($me->user)->put(route('people.update', $person))
            ->assertInvalid(['organization' => 'The organization field is required when first name is not present.'])
            ->assertInvalid(['first_name' => 'The first name field is required when organization is not present.']);

        $this->actingAs($me->user)->put(route('people.update', $person), [
            'email' => 'foo',
            'no_solicitations' => 'foo',
            'is_volunteer' => 'foo',
            'is_member' => 'foo',
        ])
            ->assertInvalid(['email' => 'The email field must be a valid email address.'])
            ->assertInvalid(['no_solicitations' => 'The no solicitations field must be true or false.'])
            ->assertInvalid(['is_volunteer' => 'The is volunteer field must be true or false.'])
            ->assertInvalid(['is_member' => 'The is member field must be true or false.']);
    }

    public function test_it_updates_a_person(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create(['team_id' => $me->team]);

        $this->actingAs($me->user)
            ->from(route('people.edit', $person))
            ->put(route('people.update', $person), [
                'organization' => 'Dunder Mifflin',
                'first_name' => 'Jim',
                'last_name' => 'Halpert',
                'phone' => '(570) 555-1234',
                'alternate_phone' => '(570) 555-5678',
                'email' => 'jim@dundermifflin.com',
                'subdivision' => 'PA',
                'city' => 'Scranton',
                'address' => '1725 Slough Avenue',
                'postal_code' => '18505',
                'notes' => 'Not a real place',
                'no_solicitations' => 0,
                'is_volunteer' => 1,
                'is_member' => 0,
            ])
            ->assertRedirect(route('people.edit', $person));

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'organization' => 'Dunder Mifflin',
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
            'phone' => '(570) 555-1234',
            'alternate_phone' => '(570) 555-5678',
            'email' => 'jim@dundermifflin.com',
            'subdivision' => 'PA',
            'city' => 'Scranton',
            'address' => '1725 Slough Avenue',
            'postal_code' => '18505',
            'notes' => 'Not a real place',
            'no_solicitations' => 0,
            'is_volunteer' => 1,
            'is_member' => 0,
        ]);
    }
}
