<?php

namespace Tests\Feature\People\Combine;

use App\Enums\Role;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class CombineReviewControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_review_people(): void
    {
        $person = Person::factory()->create();
        $this->get(route('people.combine.review', $person))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_review_people(): void
    {
        $me = $this->createTeamUser(role: Role::USER);
        $person = Person::factory()->create();

        $this->actingAs($me->user)->get(route('people.combine.review', $person))->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_person_before_reviewing(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create();

        $this->actingAs($me->user)->get(route('people.combine.review', $person))
            ->assertOwnershipValidationError();
    }

    public function test_a_fields_array_is_required_to_search_for_matches(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create(['team_id' => $me->team]);

        $this->actingAs($me->user)->get(route('people.combine.review', $person))
            ->assertInvalid(['fields' => 'At least one field must be selected to search.']);

        $this->actingAs($me->user)->get(route('people.combine.review', [
            $person,
            'fields' => 'foo',
        ]))
            ->assertInvalid(['fields' => 'The fields field must be an array.']);
    }

    public function test_it_displays_the_combine_review_page(): void
    {
        $me = $this->createTeamUser();
        $people = Person::factory()->count(2)->create([
            'team_id' => $me->team->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '(925) 555-1234',
        ])->sortBy('id');

        $this->actingAs($me->user)->get(route('people.combine.review', [
            $people->first(),
            'fields' => ['first_name', 'last_name', 'phone'],
        ]))
            ->assertOk()
            ->assertInertia(function ($page) use ($people) {
                $page->component('People/Combine/Review')
                    ->has('people', 2)
                    ->where('people.0.id', $people->first()->id)
                    ->where('people.1.id', $people->last()->id);
            });
    }
}
