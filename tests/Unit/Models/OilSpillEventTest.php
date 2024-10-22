<?php

namespace Tests\Unit\Models;

use App\Domain\Accounts\Account;
use App\Domain\Users\User;
use App\Enums\SettingKey;
use App\Extensions\Customfields\CustomField;
use App\Extensions\Owcn\Models\SpillEvent;
use App\Models\OilSpillEvent;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\Support\AssistsWithTests;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

#[Group('oil')]
final class OilSpillEventTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTeamUser;

    #[Test]
    public function aSpillEventIsJustAnExtensionOfATeam(): void
    {
        $spillEvent = OilSpillEvent::factory()->create();

        $this->assertInstanceOf(Team::class, $spillEvent);
        $this->assertEquals('teams', $spillEvent->getTable());
    }

    #[Test]
    public function itGetsTheListOfActiveSpillEvents(): void
    {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $this->setSetting($team2, SettingKey::OSPR_SPILL_ID, 'SPILL-1');

        $events = OilSpillEvent::activeSpillEvents();

        $this->assertCount(1, $events);
        $this->assertEquals($team2->id, $events->first()->id);
    }

    #[Test]
    public function itFiltersTheAvailableSpillEvents(): void
    {
        $team = Team::factory()->create();
        $this->setSetting($team, SettingKey::OSPR_SPILL_ID, 'SPILL-2');

        $this->assertSame([
            'SPILL-1',
            'SPILL-3',
            'SPILL-4',
            'SPILL-5',
            'SPILL-6',
            'SPILL-TEST',
        ], OilSpillEvent::filterAvailableSpillIds());

        $this->assertSame([
            'SPILL-1',
            'SPILL-2',
            'SPILL-3',
            'SPILL-4',
            'SPILL-5',
            'SPILL-6',
            'SPILL-TEST',
        ], OilSpillEvent::filterAvailableSpillIds(true));
    }

    #[Test]
    public function theMasterAccountsSettingsCanBeClonedIntoATargetSpillEvent(): void
    {
        $masterAccount = OilSpillEvent::factory()->create();
        $targetAccount = OilSpillEvent::factory()->create();

        $this->setSetting($masterAccount, 'foo', 'bar');

        $this->assertFalse($targetAccount->settingsStore()->has('foo'));
        $masterAccount->cloneSettingsTo($targetAccount);
        $this->assertTrue($targetAccount->fresh()->settingsStore()->has('foo'));
    }

    #[Test]
    public function theMasterAccountsCustomFieldsCanBeClonedIntoATargetSpillEvent(): void
    {
        $masterAccount = OilSpillEvent::factory()->create();
        $targetAccount = OilSpillEvent::factory()->create();

        CustomField::factory()->create(['team_id' => $masterAccount->id, 'label' => 'foobar']);

        $this->assertFalse($targetAccount->customFields->contains('label', 'foobar'));
        $masterAccount->cloneCustomFieldsTo($targetAccount);
        $this->assertTrue($targetAccount->fresh()->customFields->contains('label', 'foobar'));
    }

    #[Test]
    public function theMasterAccountsUsersCanBeClonedIntoATargetSpillEvent(): void
    {
        $masterAccount = OilSpillEvent::factory()->create();
        $targetAccount = OilSpillEvent::factory()->create();

        $jim = User::factory()->create(['name' => 'Jim'])->joinAccount($masterAccount, 'super-admin');
        $pam = User::factory()->create(['name' => 'Pam'])->joinAccount($masterAccount, 'user');

        $teamUsers = $targetAccount->accountUsers->load('user', 'roles');
        $this->assertFalse($teamUsers->contains('user_id', $jim->user_id));
        $this->assertFalse($teamUsers->contains('user_id', $pam->user_id));

        $masterAccount->cloneUsersTo($targetAccount, [$jim->user_id, $pam->user_id]);

        $teamUsers = $targetAccount->fresh()->accountUsers->load('user.roles');
        $this->assertTrue($teamUsers->contains('user_id', $jim->user_id));
        $this->assertTrue($teamUsers->contains('user_id', $pam->user_id));
        $this->assertEquals('super-admin', $teamUsers->firstWhere('user_id', $jim->user_id)->user->roles->first()->name);
        $this->assertEquals('user', $teamUsers->firstWhere('user_id', $pam->user_id)->user->roles->first()->name);
    }
}
