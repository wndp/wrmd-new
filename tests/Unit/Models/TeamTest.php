<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class TeamTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aTeamCanRetrieveItsDefaultAvatarUrl(): void
    {
        $team = Team::factory(['name' => 'Foo Place'])->create();

        $this->assertTrue(
            Str::startsWith($team->profile_photo_url, 'https://ui-avatars.com/api/?name=Foo+Place')
        );
    }

    #[Test]
    public function aTeamIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Team::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function whenATeamsPhoneNumberIsAccessedItIsFormattedForItsCountry(): void
    {
        $team = Team::factory()->create([
            'phone_number' => '808*555-1234',
        ]);

        $this->assertEquals('(808) 555-1234', $team->phone_number);
    }

    #[Test]
    public function whenATeamsPhoneNumberIsSavedItIsFormattedForItsCountry(): void
    {
        $team = Team::factory()->create([
            'phone_number' => '808*555-1234',
        ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'phone_number' => '8085551234',
        ]);
    }

    #[Test]
    public function aMasterAccountHasSubAccounts(): void
    {
        $masterAccount = Team::factory()
            ->has(Team::factory()->count(3), 'subAccounts')
            ->create();

        $this->assertCount(3, $masterAccount->subAccounts);
    }

    #[Test]
    public function subAccountsHaveAMasterAccount(): void
    {
        $subAccounts = Team::factory()
            ->count(3)
            ->for($masterAccount = Team::factory()->create(), 'masterAccount')
            ->create();

        $this->assertSame(
            [$masterAccount->id],
            $subAccounts->pluck('master_team_id')->unique()->toArray()
        );
    }

    #[Test]
    public function aTeamKnowsIfItIsAMasterAccount(): void
    {
        $team = Team::factory()->create();
        $this->assertFalse($team->is_master_account);

        $masterAccount = Team::factory()->create(['is_master_account' => true]);
        $this->assertTrue($masterAccount->is_master_account);
    }

    #[Test]
    public function aTeamKnowsIfItIsASubAccount(): void
    {
        $team = Team::factory()->create();
        $this->assertFalse($team->isSubAccount());

        $subAccount = Team::factory()->for($team, 'masterAccount')->create();
        $this->assertTrue($subAccount->isSubAccount());
    }

    #[Test]
    public function itAssignsATeamToAMasterAccount(): void
    {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        $result = $team2->assignToMasterAccount($team1);

        $this->assertTrue($result->is($team2));
        $this->assertTrue($team1->subAccounts->contains($result));
    }

    #[Test]
    public function aTeamKnowsIfASubAccountBelongsToIt(): void
    {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $this->assertFalse($team1->hasSubAccount($team2));

        $team2->assignToMasterAccount($team1);

        $this->assertTrue($team1->refresh()->hasSubAccount($team2));
    }
}
