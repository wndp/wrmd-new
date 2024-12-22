<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\AttributeOption;
use App\Models\Incident;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('hotline')]
final class IncidentControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_hotline(): void
    {
        $this->get(route('hotline.open.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_hotline(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('hotline.open.index'))->assertForbidden();
    }

    public function test_it_displays_the_hotline_index_page(): void
    {
        $hotlineStatusIsOpenId = $this->createUiBehavior(
            AttributeOptionName::HOTLINE_STATUSES,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN
        )->attribute_option_id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_HOTLINE->value);

        $incident = Incident::factory()->create([
            'team_id' => $me->team,
            'incident_status_id' => $hotlineStatusIsOpenId,
        ]);

        $this->actingAs($me->user)
            ->get(route('hotline.open.index'))
            ->assertOk()
            ->assertInertia(function ($page) use ($incident) {
                $page->component('Hotline/Index')
                    ->hasAll(['incidents', 'group'])
                    ->where('incidents.data.0.id', $incident->id);
            });
    }

    public function test_it_displays_the_page_to_create_a_new_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->get(route('hotline.incident.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Hotline/Create');
            });
    }

    public function test_a_reported_at_date_is_required_to_create_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'))
            ->assertInvalid(['reported_at' => 'The date reported field is required.']);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['reported_at' => 'foo'])
            ->assertInvalid(['reported_at' => 'The date reported field is not a valid date.']);
    }

    public function test_an_occurred_at_date_is_required_to_create_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'))
            ->assertInvalid(['occurred_at' => 'The date occurred field is required.']);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['occurred_at' => 'foo'])
            ->assertInvalid(['occurred_at' => 'The date occurred field is not a valid date.']);
    }

    public function test_a_category_is_required_to_create_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'))
            ->assertInvalid(['category_id' => 'The category id field is required.']);
    }

    public function test_an_is_priority_boolean_is_required_to_create_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'))
            ->assertInvalid(['is_priority' => 'The is priority field is required.']);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['is_priority' => 'foo'])
            ->assertInvalid(['is_priority' => 'The is priority field must be true or false.']);
    }

    public function test_the_number_of_animals_must_be_an_integer_to_create_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['number_of_animals' => 'foo'])
            ->assertInvalid(['number_of_animals' => 'The number of animals field must be an integer.']);
    }

    public function test_the_duration_of_call_must_be_an_integer_to_create_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['duration_of_call' => 'foo'])
            ->assertInvalid(['duration_of_call' => 'The duration of call field must be a number.']);
    }

    public function test_the_phone_mustbe_valid_to_create_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['phone' => '123'])
            ->assertInvalid(['phone' => 'The phone field must be a valid number.']);
    }

    /** @test */
    // public function aReportingPartyIsRequiredToCreateAnIncident()
    // {
    //     $me = $this->createTeamUser();
    //     BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

    //     $this->actingAs($me->user)->post(route('hotline.incident.store'))
    //         ->assertInvalid('person', 'A reporting party is required.');

    //     $this->actingAs($me->user)->post(route('hotline.incident.store'), ['person' => 'foo'])
    //         ->assertInvalid('person', 'A reporting party is required.');
    // }

    public function test_a_new_incident_is_saved_to_storage(): void
    {
        $hotlineWildlifeCategoriesId = AttributeOption::factory()->create(['name' => AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES])->id;
        $hotlineStatusesId = AttributeOption::factory()->create(['name' => AttributeOptionName::HOTLINE_STATUSES])->id;
        $hotlineStatusIsResolvedId = $this->createUiBehavior(
            AttributeOptionName::HOTLINE_STATUSES,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED
        )->attribute_option_id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), [
            'reported_at' => '2018-02-08 09:20:00',
            'occurred_at' => '2018-02-07 15:30:00',
            'recorded_by' => 'Jim Halpert',
            'category_id' => $hotlineWildlifeCategoriesId,
            'is_priority' => '1',
            'suspected_species' => 'House Finch',
            'incident_status_id' => $hotlineStatusesId,
            'incident_address' => '123 Main st.',
            'incident_city' => 'Any town',
            'incident_subdivision' => 'CA',
            'incident_postal_code' => '12345',
            'description' => 'Cat caught bird, unable to bring to hospital',
            'resolved_at' => '2018-02-08 10:20:00',
            'resolution' => 'Pam Beesly picked bird up',
            'given_information' => '1',

            'organization' => 'Dunder Mifflin',
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
            'phone' => '(808) 555-3432',
            'alternate_phone' => '(925) 633-4324',
            'email' => 'dwight@dundermifflin.com',
            'country' => 'US',
            'subdivision' => 'PA',
            'city' => 'Scranton',
            'address' => '1725 Slough Avenue',
            'postal_code' => '91402',
            'notes' => 'not a real place',

            'communication_at' => '2018-02-14 16:48:00',
            'communication' => 'Pam Beesly will pick bird up from Jim.',
            'communication_by' => 'Michael Scott',
        ])
            ->assertRedirect(route('hotline.open.index'));

        $reportingParty = Person::where([
            'team_id' => $me->team->id,
            'organization' => 'Dunder Mifflin',
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
            'phone' => '(808) 555-3432',
            'alternate_phone' => '(925) 633-4324',
            'email' => 'dwight@dundermifflin.com',
            'subdivision' => 'PA',
            'city' => 'Scranton',
            'address' => '1725 Slough Avenue',
            'postal_code' => '91402',
            'notes' => 'not a real place',
        ])->firstOrFail();

        $incident = Incident::where([
            'team_id' => $me->team->id,
            'reporting_party_id' => $reportingParty->id,
            'incident_number' => 'HL-18-0001',
            'reported_at' => '2018-02-08 17:20:00',
            'occurred_at' => '2018-02-07 23:30:00',
            'recorded_by' => 'Jim Halpert',
            'category_id' => $hotlineWildlifeCategoriesId,
            'suspected_species' => 'House Finch',
            'number_of_animals' => 1,
            'is_priority' => 1,
            'incident_status_id' => $hotlineStatusIsResolvedId,
            'incident_address' => '123 Main st.',
            'incident_city' => 'Any town',
            'incident_subdivision' => 'CA',
            'incident_postal_code' => '12345',
            'description' => 'Cat caught bird, unable to bring to hospital',
            'resolved_at' => '2018-02-08 18:20:00',
            'resolution' => 'Pam Beesly picked bird up',
            'given_information' => 1,
        ])->firstOrFail();

        $this->assertDatabaseHas('communications', [
            'incident_id' => $incident->id,
            'communication_at' => '2018-02-15 00:48:00',
            'communication_by' => 'Michael Scott',
            'communication' => 'Pam Beesly will pick bird up from Jim.',
        ]);
    }

    public function test_it_validates_ownership_of_an_incident_before_displaying_the_page_to_edit_it(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create();

        $this->actingAs($me->user)->get(route('hotline.incident.edit', $incident))
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_page_to_edit_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->get(route('hotline.incident.edit', $incident))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Hotline/Edit')
                    ->where('incident.id', $incident->id)
            );
    }

    public function test_a_reported_at_date_is_required_to_update_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident))
            ->assertInvalid(['reported_at' => 'The date reported field is required.']);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), ['reported_at' => 'foo'])
            ->assertInvalid(['reported_at' => 'The date reported field is not a valid date.']);
    }

    public function test_an_occurred_at_date_is_required_to_update_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident))
            ->assertInvalid(['occurred_at' => 'The date occurred field is required.']);

        $this->actingAs($me->user)->put("/hotline/$incident->id", ['occurred_at' => 'foo'])
            ->assertInvalid(['occurred_at' => 'The date occurred field is not a valid date.']);
    }

    public function test_a_category_is_required_to_update_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident))
            ->assertInvalid(['category_id' => 'The category id field is required.']);
    }

    public function test_an_is_priority_boolean_is_required_to_update_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident))
            ->assertInvalid(['is_priority' => 'The is priority field is required.']);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), ['is_priority' => 'foo'])
            ->assertInvalid(['is_priority' => 'The is priority field must be true or false.']);
    }

    public function test_the_number_of_animals_must_be_an_integer_to_update_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), ['number_of_animals' => 'foo'])
            ->assertInvalid(['number_of_animals' => 'The number of animals field must be an integer.']);
    }

    public function test_the_duration_of_call_must_be_an_integer_to_update_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), ['duration_of_call' => 'foo'])
            ->assertInvalid(['duration_of_call' => 'The duration of call field must be a number.']);
    }

    public function test_it_validates_ownership_of_an_incident_before_updating_it(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), [
            'reported_at' => '2018-02-08 09:20:00',
            'occurred_at' => '2018-02-07 15:30:00',
            'category' => 'Cat',
            'is_priority' => 1,
            'commonName' => 'House Finch',
            'person' => ['first_name' => 'Dwight'],
        ])
            ->assertOwnershipValidationError();
    }

    public function test_an_incident_is_updated_in_storage(): void
    {
        $hotlineWildlifeCategoriesId = AttributeOption::factory()->create(['name' => AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES])->id;
        $hotlineStatusIsResolvedId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED)->attribute_option_id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->for($me->team)->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), [
            'reported_at' => '2018-02-08 09:20:00',
            'occurred_at' => '2018-02-07 15:30:00',
            'recorded_by' => 'Jim Halpert',
            'category_id' => $hotlineWildlifeCategoriesId,
            'is_priority' => '1',
            'incident_status_id' => $hotlineStatusIsResolvedId,
            'suspected_species' => 'House Finch',
        ])
            ->assertRedirect(route('hotline.incident.edit', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'incident_number' => 'hl-'.date('y').'-0001',
            'reported_at' => '2018-02-08 17:20:00',
            'occurred_at' => '2018-02-07 23:30:00',
            'recorded_by' => 'Jim Halpert',
            'category_id' => $hotlineWildlifeCategoriesId,
            'is_priority' => 1,
            'incident_status_id' => $hotlineStatusIsResolvedId,
            'suspected_species' => 'House Finch',
        ]);
    }

    public function test_an_incident_number_is_required_to_delete_an_incident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident))
            ->assertInvalid(['incident_number' => 'The incident number field is required.']);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident), ['incident_number' => 'foo'])
            ->assertInvalid(['incident_number' => 'The provided incident number does not match the displayed incident number.']);
    }

    public function test_the_authenticated_users_password_is_required_to_delete_an_incident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident), ['password' => 'foo'])
            ->assertInvalid(['password' => 'The password is incorrect.']);
    }

    public function test_it_validates_ownership_of_an_incident_before_deleting_it(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident), [
            'incident_number' => $incident->incident_number, 'password' => 'password',
        ])
            ->assertOwnershipValidationError();
    }

    public function test_an_incident_can_be_deleted(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident), [
            'incident_number' => $incident->incident_number, 'password' => 'password',
        ])
            ->assertRedirect(route('hotline.open.index'));

        $this->assertSoftDeleted('incidents', [
            'id' => $incident->id,
        ]);
    }
}
