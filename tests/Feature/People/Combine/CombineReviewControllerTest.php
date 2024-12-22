<?php

namespace Tests\Feature\People\Combine;

use App\Enums\Role;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class CombineReviewControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessReviewPeople(): void
    {
        $person = Person::factory()->create();
        $this->get(route('people.combine.review', $person))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessReviewPeople(): void
    {
        $me = $this->createTeamUser(role: Role::USER);
        $person = Person::factory()->create();

        $this->actingAs($me->user)->get(route('people.combine.review', $person))->assertForbidden();
    }

    #[Test]
    public function itValidatesOwnershipOfAPersonBeforeReviewing(): void
    {
        $me = $this->createTeamUser();
        $person = Person::factory()->create();

        $this->actingAs($me->user)->get(route('people.combine.review', $person))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function aFieldsArrayIsRequiredToSearchForMatches(): void
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

    #[Test]
    public function itDisplaysTheCombineReviewPage(): void
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
