<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class TeamTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_team_can_retrieve_its_default_avatar_url(): void
    {
        $team = Team::factory(['name' => 'Foo Place'])->create();

        $this->assertTrue(
            Str::startsWith($team->profile_photo_url, 'https://ui-avatars.com/api/?name=F+P')
        );
    }

    public function test_a_team_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Team::factory()->create(),
            'created'
        );
    }

    public function test_when_a_teams_phone_number_is_accessed_it_is_formatted_for_their_country(): void
    {
        $team = Team::factory()->create([
            'phone' => '808-555-1234',
        ]);

        $this->assertEquals('8085551234', $team->phone_normalized);
        $this->assertEquals('+18085551234', $team->phone_e164);
        $this->assertEquals('(808) 555-1234', $team->phone_national);
    }

    public function test_when_a_teams_phone_number_is_saved_it_is_formatted_for_its_country(): void
    {
        $team = Team::factory()->create([
            'phone' => '808-555-1234',
        ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'phone' => '808-555-1234',
            'phone_normalized' => '8085551234',
            'phone_e164' => '+18085551234',
            'phone_national' => '(808) 555-1234',
        ]);
    }

    public function test_when_a_phone_number_does_not_match_a_country_format_it_still_saves_to_the_database(): void
    {
        $team = Team::factory()->create([
            'phone' => '123',
        ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'phone' => '123',
            'phone_normalized' => '123',
            'phone_e164' => '123',
            'phone_national' => '123',
        ]);
    }

    public function test_a_master_account_has_sub_accounts(): void
    {
        $masterAccount = Team::factory()
            ->has(Team::factory()->count(3), 'subAccounts')
            ->create();

        $this->assertCount(3, $masterAccount->subAccounts);
    }

    public function test_sub_accounts_have_a_master_account(): void
    {
        $subAccounts = Team::factory()
            ->count(3)
            ->for($masterAccount = Team::factory()->create(), 'masterAccount')
            ->create();

        $this->assertSame(
            [$masterAccount->id],
            $subAccounts->pluck('master_account_id')->unique()->toArray()
        );
    }

    public function test_a_team_knows_if_it_is_a_master_account(): void
    {
        $team = Team::factory()->create();

        $this->assertFalse($team->is_master_account);

        $masterAccount = Team::factory()->create(['is_master_account' => true]);
        $this->assertTrue($masterAccount->is_master_account);
    }

    public function test_a_team_knows_if_it_is_a_sub_account(): void
    {
        $team = Team::factory()->create();
        $this->assertFalse($team->isSubAccount());

        $subAccount = Team::factory()->for($team, 'masterAccount')->create();
        $this->assertTrue($subAccount->isSubAccount());
    }

    public function test_it_assigns_a_team_to_a_master_account(): void
    {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        $result = $team2->assignToMasterAccount($team1);

        $this->assertTrue($result->is($team2));
        $this->assertTrue($team1->subAccounts->contains($result));
    }

    public function test_a_team_knows_if_a_sub_account_belongs_to_it(): void
    {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $this->assertFalse($team1->hasSubAccount($team2));

        $team2->assignToMasterAccount($team1);

        $this->assertTrue($team1->refresh()->hasSubAccount($team2));
    }
}
