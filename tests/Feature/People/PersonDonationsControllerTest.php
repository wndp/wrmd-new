<?php

namespace Tests\Feature\People;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\Role;
use App\Models\AttributeOption;
use App\Models\Donation;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class PersonDonationsControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_a_persons_donations(): void
    {
        $person = Person::factory()->has(Donation::Factory())->create();

        $this->get(route('people.donations.index', $person))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_people(): void
    {
        $me = $this->createTeamUser(role: Role::USER);
        $person = Person::factory()->has(Donation::Factory())->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->get(route('people.donations.index', $person))->assertForbidden();
    }

    public function test_it_displays_persons_donations(): void
    {
        $donationMethodIsCashId = $this->createUiBehavior(
            AttributeOptionName::DONATION_METHODS,
            AttributeOptionUiBehavior::DONATION_METHOD_IS_CASH
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $person = Person::factory()->has(Donation::Factory())->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->get(route('people.donations.index', $person))
            ->assertOk()
            ->assertInertia(function ($page) use ($person, $donationMethodIsCashId) {
                $page->component('People/Donations')
                    ->has('person.donations')
                    ->hasOption('donationMethodsOptions')
                    ->where('donationMethodIsCashId', $donationMethodIsCashId)
                    ->where('person.donations.0.id', $person->donations->first()->id);
            });
    }

    public function test_it_fails_validation_when_trying_to_store_a_new_donation(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->post(route('people.donations.store', $person))
            ->assertInvalid(['donated_at' => 'The donation date field is required.'])
            ->assertInvalid(['method_id' => 'The method field is required.']);

        $this->actingAs($me->user)->post(route('people.donations.store', $person), [
            'donated_at' => 'foo',
            'value' => 'foo',
            'method_id' => AttributeOption::factory()->create()->id,
        ])
            ->assertInvalid(['donated_at' => 'The donation date is not a valid date.'])
            ->assertInvalid(['value' => 'The value field must be a number.'])
            ->assertInvalid(['method_id' => 'The selected method is invalid.']);
    }

    public function test_it_stores_a_new_donation(): void
    {
        $cashId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::DONATION_METHODS->value,
        ])->id;

        $me = $this->createTeamUser();
        $person = Person::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->post(route('people.donations.store', $person), [
            'donated_at' => 'June 4, 2022',
            'method_id' => $cashId,
            'value' => '10.00',
            'comments' => 'test',
        ])
            ->assertRedirect(route('people.donations.index', $person));

        $this->assertDatabaseHas('donations', [
            'person_id' => $person->id,
            'donated_at' => '2022-06-04',
            'method_id' => $cashId,
            'value' => '1000',
            'comments' => 'test',
        ]);
    }

    public function test_it_validates_ownership_of_a_donation_before_updating(): void
    {
        $cashId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::DONATION_METHODS->value,
        ])->id;

        $me = $this->createTeamUser();
        $person = Person::factory()->has(Donation::Factory())->create();

        $this->actingAs($me->user)->put(route('people.donations.update', [$person, $person->donations->first()]), [
            'donated_at' => 'June 4, 2022',
            'value' => '10.00',
            'method_id' => $cashId,
        ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_a_person(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->has(Donation::Factory())->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('people.donations.update', [$person, $person->donations->first()]))
            ->assertInvalid(['donated_at' => 'The donation date field is required.'])
            ->assertInvalid(['method_id' => 'The method field is required.']);

        $this->actingAs($me->user)->put(route('people.donations.update', [$person, $person->donations->first()]), [
            'donated_at' => 'foo',
            'value' => 'foo',
            'method_id' => AttributeOption::factory()->create()->id,
        ])
            ->assertInvalid(['donated_at' => 'The donation date is not a valid date.'])
            ->assertInvalid(['value' => 'The value field must be a number.'])
            ->assertInvalid(['method_id' => 'The selected method is invalid.']);
    }

    public function test_it_updates_a_donation(): void
    {
        $cashId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::DONATION_METHODS->value,
        ])->id;

        $me = $this->createTeamUser();
        $person = Person::factory()->has(Donation::Factory())->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)
            ->put(route('people.donations.update', [$person, $person->donations->first()]), [
                'donated_at' => 'June 4, 2022',
                'method_id' => $cashId,
                'value' => '10.00',
                'comments' => 'test',
            ])
            ->assertRedirect(route('people.donations.index', $person));

        $this->assertDatabaseHas('donations', [
            'id' => $person->donations->first()->id,
            'donated_at' => '2022-06-04',
            'method_id' => $cashId,
            'value' => '1000',
            'comments' => 'test',
        ]);
    }

    public function test_it_validates_ownership_of_a_donation_before_deleting(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->has(Donation::Factory())->create();

        $this->actingAs($me->user)->delete(route('people.donations.destroy', [$person, $person->donations->first()]))
            ->assertOwnershipValidationError();
    }

    public function test_it_deletes_a_donation(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->has(Donation::Factory())->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->delete(route('people.donations.destroy', [$person, $person->donations->first()]))
            ->assertRedirect(route('people.donations.index', $person));

        $this->assertSoftDeleted('donations', ['id' => $person->donations->first()->id]);
    }
}
