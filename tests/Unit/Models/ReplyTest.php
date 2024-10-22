<?php

namespace Tests\Unit\Models;

use App\Models\Reply;
use App\Models\Team;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[Group('forum')]
final class ReplyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function aReplyHasAThread(): void
    {
        $reply = Reply::factory()->create();

        $this->assertInstanceOf(Thread::class, $reply->thread);
    }

    #[Test]
    public function aReplyHasATeam(): void
    {
        $reply = Reply::factory()->create();

        $this->assertInstanceOf(Team::class, $reply->team);
    }

    #[Test]
    public function aReplyHasAUser(): void
    {
        $reply = Reply::factory()->create();

        $this->assertInstanceOf(User::class, $reply->user);
    }
}
