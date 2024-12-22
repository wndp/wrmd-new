<?php

namespace Tests\Feature\SubAccounts;

use App\Enums\Ability;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class SubAccountsExtensionsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_sub_accounts_extensions(): void
    {
        $team = Team::factory()->create();

        $this->get(route('sub_accounts.extensions.edit', $team))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_sub_accounts_extensions(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.extensions.edit', $team))->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_sub_account_before_displaying_theextensions_page(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.extensions.edit', $team))
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_sub_account_extensions_edit_page(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create(['master_account_id' => $me->team->id]);

        $this->actingAs($me->user)
            ->get(route('sub_accounts.extensions.edit', $team))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('SubAccounts/Extensions')
                    ->has('extensions')
                    ->where('subAccount.id', $team->id)
            );
    }
}
