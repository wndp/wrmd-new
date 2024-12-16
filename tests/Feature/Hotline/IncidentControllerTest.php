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

    #[Test]
    public function unAuthenticatedUsersCantAccessHotline(): void
    {
        $this->get(route('hotline.open.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessHotline(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('hotline.open.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheHotlineIndexPage(): void
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

    #[Test]
    public function itDisplaysThePageToCreateANewIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->get(route('hotline.incident.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Hotline/Create');
            });
    }

    #[Test]
    public function aReportedAtDateIsRequiredToCreateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'))
            ->assertInvalid(['reported_at' => 'The date reported field is required.']);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['reported_at' => 'foo'])
            ->assertInvalid(['reported_at' => 'The date reported field is not a valid date.']);
    }

    #[Test]
    public function anOccurredAtDateIsRequiredToCreateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'))
            ->assertInvalid(['occurred_at' => 'The date occurred field is required.']);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['occurred_at' => 'foo'])
            ->assertInvalid(['occurred_at' => 'The date occurred field is not a valid date.']);
    }

    #[Test]
    public function aCategoryIsRequiredToCreateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'))
            ->assertInvalid(['category_id' => 'The category id field is required.']);
    }

    #[Test]
    public function anIsPriorityBooleanIsRequiredToCreateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'))
            ->assertInvalid(['is_priority' => 'The is priority field is required.']);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['is_priority' => 'foo'])
            ->assertInvalid(['is_priority' => 'The is priority field must be true or false.']);
    }

    #[Test]
    public function theNumberOfAnimalsMustBeAnIntegerToCreateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['number_of_animals' => 'foo'])
            ->assertInvalid(['number_of_animals' => 'The number of animals field must be an integer.']);
    }

    #[Test]
    public function theDurationOfCallMustBeAnIntegerToCreateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.store'), ['duration_of_call' => 'foo'])
            ->assertInvalid(['duration_of_call' => 'The duration of call field must be a number.']);
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

    #[Test]
    public function aNewIncidentIsSavedToStorage(): void
    {
        $hotlineWildlifeCategoriesId = AttributeOption::factory()->create(['name' => AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES])->id;
        $hotlineStatusesId = AttributeOption::factory()->create(['name' => AttributeOptionName::HOTLINE_STATUSES])->id;
        $hotlineStatusIsResolvedId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED)->attribute_option_id;

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
            'phone' => '808 555 3432',
            'alternate_phone' => '925 633 4324',
            'email' => 'dwight@dundermifflin.com',
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
            'phone' => '8085553432',
            'alternate_phone' => '9256334324',
            'email' => 'dwight@dundermifflin.com',
            'subdivision' => 'PA',
            'city' => 'Scranton',
            'address' => '1725 Slough Avenue',
            'postal_code' => '91402',
            'notes' => 'not a real place',
        ])->firstOrFail();

        $incident = Incident::where([
            'team_id' => $me->team->id,
            'responder_id' => $reportingParty->id,
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

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeDisplayingThePageToEditIt(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create();

        $this->actingAs($me->user)->get(route('hotline.incident.edit', $incident))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function itDisplaysThePageToEditAnIncident(): void
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

    #[Test]
    public function aReportedAtDateIsRequiredToUpdateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident))
            ->assertInvalid(['reported_at' => 'The date reported field is required.']);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), ['reported_at' => 'foo'])
            ->assertInvalid(['reported_at' => 'The date reported field is not a valid date.']);
    }

    #[Test]
    public function anOccurredAtDateIsRequiredToUpdateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident))
            ->assertInvalid(['occurred_at' => 'The date occurred field is required.']);

        $this->actingAs($me->user)->put("/hotline/$incident->id", ['occurred_at' => 'foo'])
            ->assertInvalid(['occurred_at' => 'The date occurred field is not a valid date.']);
    }

    #[Test]
    public function aCategoryIsRequiredToUpdateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident))
            ->assertInvalid(['category_id' => 'The category id field is required.']);
    }

    #[Test]
    public function anIsPriorityBooleanIsRequiredToUpdateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident))
            ->assertInvalid(['is_priority' => 'The is priority field is required.']);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), ['is_priority' => 'foo'])
            ->assertInvalid(['is_priority' => 'The is priority field must be true or false.']);
    }

    #[Test]
    public function theNumberOfAnimalsMustBeAnIntegerToUpdateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), ['number_of_animals' => 'foo'])
            ->assertInvalid(['number_of_animals' => 'The number of animals field must be an integer.']);
    }

    #[Test]
    public function theDurationOfCallMustBeAnIntegerToUpdateAnIncident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.update', $incident), ['duration_of_call' => 'foo'])
            ->assertInvalid(['duration_of_call' => 'The duration of call field must be a number.']);
    }

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeUpdatingIt(): void
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

    #[Test]
    public function anIncidentIsUpdatedInStorage(): void
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

    #[Test]
    public function anIncidentNumberIsRequiredToDeleteAnIncident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident))
            ->assertInvalid(['incident_number' => 'The incident number field is required.']);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident), ['incident_number' => 'foo'])
            ->assertInvalid(['incident_number' => 'The provided incident number does not match the displayed incident number.']);
    }

    #[Test]
    public function theAuthenticatedUsersPasswordIsRequiredToDeleteAnIncident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident), ['password' => 'foo'])
            ->assertInvalid(['password' => 'The password is incorrect.']);
    }

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeDeletingIt(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.incident.destroy', $incident), [
            'incident_number' => $incident->incident_number, 'password' => 'password',
        ])
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function anIncidentCanBeDeleted(): void
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
