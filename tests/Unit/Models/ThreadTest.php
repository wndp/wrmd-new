<?php

namespace Tests\Unit\Models;

use App\Domain\Accounts\Account;
use App\Domain\Forum\Channel;
use App\Domain\Forum\Channels;
use App\Domain\Forum\Thread;
use App\Domain\Users\User;
use App\Notifications\ThreadWasCreated;
use App\Notifications\ThreadWasUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('forum')]
final class ThreadTest extends TestCase
{
    use RefreshDatabase;
    use Assertions;

    protected $thread;

    #[Test]
    public function aThreadCanStartItself(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $channel = Channel::factory()->create(['id' => Channels::WELCOME]);

        $thread = Thread::start('title', 'body', $team->id, $user->id, Channels::WELCOME);

        $this->assertTrue($thread->exists);
        $this->assertEquals('title', $thread->title);
        $this->assertTrue($thread->account->is($team));
        $this->assertTrue($thread->channel->is($channel));
    }

    #[Test]
    public function aThreadHasAnAccount(): void
    {
        $this->assertInstanceOf(Account::class, $this->thread->account);
    }

    #[Test]
    public function aThreadHasAUser(): void
    {
        $this->assertInstanceOf(User::class, $this->thread->user);
    }

    #[Test]
    public function aThreadHasReplies(): void
    {
        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    #[Test]
    public function aThreadBelongsToAChannel(): void
    {
        $this->assertInstanceOf(Channel::class, $this->thread->channel);
    }

    #[Test]
    public function aThreadCanAddAReply(): void
    {
        $teamId = Team::factory()->create()->id;
        $userId = User::factory()->create()->id;

        $this->thread->addReply($teamId, $userId, 'lorem');

        $this->assertCount(1, $this->thread->replies);
        $this->assertEquals($this->thread->replies->first()->body, 'lorem');
        $this->assertEquals($this->thread->replies->first()->team_id, $teamId);
        $this->assertEquals($this->thread->replies->first()->user_id, $userId);
        $this->assertEquals($this->thread->replies->first()->thread_id, $this->thread->id);
    }

    #[Test]
    public function aThreadCanBeSubscribedTo(): void
    {
        $team = Team::factory()->create();

        $this->thread->subscribe($team);

        $this->assertEquals(
            1,
            $this->thread->subscriptions()->where('team_id', $team->id)->count()
        );
    }

    #[Test]
    public function aThreadCanBeUnsubscribedFrom(): void
    {
        $team = Team::factory()->create();

        $this->thread->subscribe($team);

        $this->thread->unsubscribe($team);

        $this->assertCount(0, $this->thread->subscriptions);
    }

    #[Test]
    public function itKnowsIfTheAuthenticatedUsersCurrentAccountIsSubscribedToIt(): void
    {
        $team = Team::factory()->create();

        $this->assertFalse($this->thread->isSubscribedToBy($team));

        $this->thread->subscribe($team);

        $this->assertTrue($this->thread->isSubscribedToBy($team));
    }

    #[Test]
    public function aThreadNotifiesAllSubscribersWhenItIsCreated(): void
    {
        Notification::fake();

        $me = Team::factory()->create();
        $you = Team::factory()->create();
        $them = Team::factory()->create();

        $this->thread->subscribe($me);
        $this->thread->subscribe($you);

        $this->thread->notifyAccountsThreadWasCreated();

        Notification::assertSentTo($me, ThreadWasCreated::class);
        Notification::assertSentTo($you, ThreadWasCreated::class);
        Notification::assertNotSentTo($them, ThreadWasCreated::class);
    }

    #[Test]
    public function aThreadNotifiesAllSubscribersWhenAReplyIsAdded(): void
    {
        Notification::fake();

        $me = Team::factory()->create();
        $you = Team::factory()->create();
        $user = User::factory()->create();

        $this->thread->subscribe($me);
        $this->thread->subscribe($you);

        $this->thread->addReply($me->id, $user->id, 'lorem');

        Notification::assertNotSentTo($me, ThreadWasUpdated::class);
        Notification::assertSentTo($you, ThreadWasUpdated::class);
    }

    #[Test]
    public function aThreadCanBeMadePrivateWithAccounts(): void
    {
        $you = Team::factory()->create();

        $this->assertFalse($this->thread->isPrivate());

        $this->thread->privateWith($you);

        $this->assertTrue($this->thread->fresh()->isPrivate());

        $participants = $this->thread->privateBetween()->pluck('accounts.id');
        $this->assertStringContainsString($this->thread->team_id, $participants);
        $this->assertStringContainsString($you->id, $participants);
    }

    #[Test]
    public function aCollectionOfThreadsMayIncludePrivateThreadsThatAnAccountIsPartOf(): void
    {
        $this->thread->privateWith(Team::factory()->create());

        $this->assertTrue(
            Thread::includePrivate($this->thread->team_id)->first()->is($this->thread)
        );
    }

    #[Test]
    public function aCollectionOfThreadsMayOnlyHavePrivateThreadsThatAnAccountIsPartOf(): void
    {
        $this->assertEmpty(
            Thread::onlyPrivate($this->thread->team_id)->get()
        );

        $this->thread->privateWith($you = Team::factory()->create());

        $this->assertTrue(
            Thread::onlyPrivate($this->thread->team_id)->first()->is($this->thread)
        );
        $this->assertTrue(
            Thread::onlyPrivate($you->id)->first()->is($this->thread)
        );

        $this->assertEmpty(
            Thread::onlyPrivate(Team::factory()->create()->id)->get()
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create([
            'team_id' => Team::factory()->create(['organization' => 'foo'])->id,
            'user_id' => User::factory()->create(['name' => 'foo'])->id,
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim',
            'created_at' => Carbon::create(2017, 3, 25, 0, 0, 0),
        ]);
    }
}
