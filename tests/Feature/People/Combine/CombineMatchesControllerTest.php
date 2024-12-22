<?php

namespace Tests\Feature\People\Combine;

use App\Enums\Role;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class CombineMatchesControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessCombinePeople(): void
    {
        $this->get(route('people.combine.matches'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessCombinePeople(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('people.combine.matches'))->assertForbidden();
    }

    #[Test]
    public function aFieldsArrayIsRequiredToSearchForMatches(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('people.combine.matches'))
            ->assertInvalid(['fields' => 'At least one field must be selected to search.']);

        $this->actingAs($me->user)->get(route('people.combine.matches', [
            'fields' => 'foo',
        ]))
            ->assertInvalid(['fields' => 'The fields field must be an array.']);
    }

    #[Test]
    public function itDisplaysTheCombineSearchResultsPage(): void
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
