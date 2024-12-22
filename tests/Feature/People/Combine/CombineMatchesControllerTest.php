<?php

namespace Tests\Feature\People\Combine;

use App\Enums\Role;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class CombineMatchesControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_combine_people(): void
    {
        $this->get(route('people.combine.matches'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_combine_people(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('people.combine.matches'))->assertForbidden();
    }

    public function test_a_fields_array_is_required_to_search_for_matches(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('people.combine.matches'))
            ->assertInvalid(['fields' => 'At least one field must be selected to search.']);

        $this->actingAs($me->user)->get(route('people.combine.matches', [
            'fields' => 'foo',
        ]))
            ->assertInvalid(['fields' => 'The fields field must be an array.']);
    }

    public function test_it_displays_the_combine_search_results_page(): void
    {
        $me = $this->createTeamUser();
        Person::factory()->count(2)->create([
            'team_id' => $me->team->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '(925) 555-1234',
        ]);

        $this->actingAs($me->user)->get(route('people.combine.matches', [
            'fields' => ['first_name', 'last_name', 'phone'],
        ]))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('People/Combine/Matches')
                    ->where('people.0.aggregate', 2)
                    ->where('people.0.match', 'John Doe (925) 555-1234')
                    ->where('sentence', 'First Name, Last Name and Phone');
            });
    }
}
