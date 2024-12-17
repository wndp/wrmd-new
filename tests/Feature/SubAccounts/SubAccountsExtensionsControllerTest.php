<?php

namespace Tests\Feature\SubAccounts;

use App\Domain\Accounts\Account;
use App\Enums\Ability;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function unAuthenticatedUsersCantAccessSubAccountsExtensions(): void
    {
        $team = Team::factory()->create();

        $this->get(route('sub_accounts.extensions.edit', $team))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessSubAccountsExtensions(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.extensions.edit', $team))->assertForbidden();
    }

    #[Test]
    public function itValidatesOwnershipOfASubAccountBeforeDisplayingTheextensionsPage(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.extensions.edit', $team))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function itDisplaysTheSubAccountExtensionsEditPage(): void
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
