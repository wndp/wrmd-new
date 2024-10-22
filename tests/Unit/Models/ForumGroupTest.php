<?php

namespace Tests\Unit\Models;

use App\Enums\ForumGroupRole;
use App\Models\ForumGroup;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[Group('forum')]
final class ForumGroupTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function aGroupHasMembers(): void
    {
        $this->assertInstanceOf(Collection::class, ForumGroup::factory()->create()->members);
    }

    #[Test]
    public function aGroupHasThreads(): void
    {
        $this->assertInstanceOf(Collection::class, ForumGroup::factory()->create()->threads);
    }

    #[Test]
    public function itKnowsIfATeamIsAMemberOfIt(): void
    {
        $team = Team::factory()->create();
        $group = ForumGroup::factory()->create();

        $this->assertFalse($group->hasMember($team));

        $group->members()->save($team, ['role' => ForumGroupRole::ADMIN]);

        $this->assertTrue($group->hasMember($team));
    }
}
