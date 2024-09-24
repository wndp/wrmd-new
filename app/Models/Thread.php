<?php

namespace App\Models;

use App\Enums\Channel;
use App\Enums\ThreadStatus;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Thread extends Model
{
    use HasFactory;
    use HasVersion7Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'channel',
        'title',
        'body',
    ];

    protected $casts = [
        'status' => ThreadStatus::class,
        'channel' => Channel::class
    ];

    protected $appends = [
        'created_at_for_humans',
        'body_for_humans',
    ];

    /**
     * Static constructor to start a function.
     */
    public static function start(string $title, string $body, int $teamId, int $userId, Channels $channelId): Thread
    {
        $thread = new static(compact('title', 'body'));

        $thread->team_id = $teamId;
        $thread->user_id = $userId;
        $thread->channel_id = $channelId;

        $thread->save();

        return $thread;
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function group(): HasOneThrough
    {
        return $this->hasOneThrough(ForumGroup::class, ForumGroupThread::class, 'thread_id', 'id', 'id', 'forum_group_id');
    }

    public function privateBetween(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'private_thread_participants')->withTimestamps();
    }

    /**
     * Add a reply to the thread.
     */
    public function addReply(int $teamId, int $userId, string $body): void
    {
        $reply = new Reply(compact('body'));

        $reply->team_id = $teamId;
        $reply->user_id = $userId;

        $this->replies()->save($reply);

        $this->notifyTeamsThreadWasUpdated($reply);
    }

    /**
     * Notify all subscribed teams that the thread was created.
     *
     * @param  \App\Domain\Forum\Reply  $reply
     */
    public function notifyTeamsThreadWasCreated(Team $except = null): void
    {
        $this->subscriptions
            ->filter(function ($subscription) use ($except) {
                if ($except) {
                    return $subscription->team->id != $except->id;
                }

                return true;
            })
            ->each(function ($subscription) {
                $subscription->team->notify(new ThreadWasCreated($this));
            });
    }

    /**
     * Notify all subscribed teams but the team that sent the reply that the thread was updated.
     */
    private function notifyTeamsThreadWasUpdated(Reply $reply): void
    {
        $this->subscriptions
            ->filter(function ($subscription) use ($reply) {
                return $subscription->team->id != $reply->team->id;
            })
            ->each(function ($subscription) use ($reply) {
                $subscription->team->notify(new ThreadWasUpdated($this, $reply));
            });
    }

    /**
     * Subscribe an team to the current thread.
     */
    public function subscribe(Team $team)
    {
        if (! $this->isSubscribedToBy($team)) {
            $subscription = new ThreadSubscription();

            $subscription->team()->associate($team);
            $subscription->thread()->associate($this);

            $subscription->save();
        }

        return $this;
    }

    /**
     * Unsubscribe an team from the current thread.
     */
    public function unsubscribe(Team $team)
    {
        return $this->subscriptions()
            ->where('team_id', $team->id)
            ->delete();
    }

    /**
     * Determine if the provided team is subscribed to the current thread.
     */
    public function isSubscribedToBy(Team $team): bool
    {
        return $this->subscriptions()
            ->where('team_id', $team->id)
            ->exists();
    }

    /**
     * Make a thread private with the given teams.
     *
     * @param  mixed  $teams
     */
    public function privateWith($teams): void
    {
        $teamIds = collect(Arr::wrap($teams))->map(function ($team) {
            return $team instanceof Team ? $team->id : $team;
        })
            ->merge([$this->team_id])
            ->unique();

        $this->privateBetween()->sync($teamIds);
    }

    /**
     * Determine if a thread is private.
     */
    public function isPrivate(): bool
    {
        return $this->privateBetween->isNotEmpty();
    }

    public function scopeIncludePrivate($query, $teamId)
    {
        return $query->whereDoesntHave('privateBetween')
            ->orWhereHas('privateBetween', function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            });
    }

    public function scopeOnlyPrivate($query, $teamId)
    {
        return $query->whereHas('privateBetween', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        });
    }

    public function addToGroup(ForumGroup $group)
    {
        $group->threads()->save($this);

        return $this;
    }

    public function subscribeGroupMembersToThread()
    {
        $this->group->members->each(fn ($team) => $this->subscribe($team));

        return $this;
    }

    /**
     * Determine if the thread belongs to a group.
     */
    public function belongsToGroup(): bool
    {
        return $this->fresh()->group instanceof ForumGroup;
    }

    /**
     * Get a list of the threads participants.
     */
    public function getParticipantsAttribute(): Collection
    {
        $list = $this->replies->pluck('account.organization', 'account_id');

        return $list->prepend($this->account->organization, $this->account_id);
    }

    /**
     * Return an HTML formatted version of the body.
     */
    public function getBodyForHumansAttribute(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
    }

    public function getCreatedAtForHumansAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
