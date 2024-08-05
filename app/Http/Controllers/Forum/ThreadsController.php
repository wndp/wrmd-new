<?php

namespace App\Http\Controllers\Forum;

use App\Enums\Channel;
use App\Enums\SpecialAccount;
use App\Enums\ThreadStatus;
use App\Http\Controllers\Controller;
use App\Models\ForumGroup;
use App\Models\Team;
use App\Models\Thread;
use App\Repositories\ForumRepository;
use App\Repositories\TeamRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ThreadsController extends Controller
{
    public function index(Request $request): Response
    {
        $teams = TeamRepository::active();
        $groups = ForumRepository::groupsFor(Auth::user()->currentTeam);
        $channels = ForumRepository::channels();

        $threads = Thread::where('channel', '!=', Channel::WELCOME->value)
            ->when($request->get('mine'), function ($query) {
                $query->where('team_id', Auth::user()->current_team_id);
            })
            ->when($request->get('participant'), function ($query) {
                $query->whereRelation('replies', 'team_id', Auth::user()->current_team_id);
            })
            ->when($request->get('following'), function ($query) {
                $query->whereRelation('subscriptions', 'team_id', Auth::user()->current_team_id);
            })
            ->when($request->get('solved'), function ($query) {
                $query->where('status', ThreadStatus::SOLVED->value);
            })
            ->when($request->get('unsolved'), function ($query) {
                $query->where('status', ThreadStatus::UNSOLVED->value);
            })
            ->when($request->get('unanswered'), function ($query) {
                $query->doesntHave('replies');
            })
            ->when($request->get('group'), function ($query, $group) {
                $query->whereHas(
                    'group.members',
                    fn ($query) => $query->where('slug', $group)->where('forum_group_members.team_id', Auth::user()->current_team_id)
                );
            }, function ($query) {
                $query->whereDoesntHave('group');
            })
            ->when($request->get('channel'), function ($query, $channel) {
                if ($channel !== 'all') {
                    $query->whereRelation('channel', 'slug', $channel);
                }
            })
            ->when($request->get('search'), function ($query, $search) {
                $query->whereIn('id', Thread::where('title', 'like', "%$search%")->get()->pluck('id'));
            })
            ->latest()
            ->with('team', 'user')
            ->withCount('replies')
            ->paginate()
            ->onEachSide(1)
            ->withQueryString();

        $currentGroup = $request->filled('group')
            ? ForumGroup::whereSlug($request->get('group'))->with('members')->first()
            : null;

        return Inertia::render('Forum/Index', compact('channels', 'groups', 'threads', 'teams', 'currentGroup'));
    }

    /**
     * Store a newly created thread in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'channel' => ['required', Rule::enum(Channel::class)],

            // need to create a custom rule to determine if slug exits and
            // if the users team is a member of the group
            'group' => 'nullable|exists:forum_groups,slug',
        ], [
            'channel.required' => 'The channel field is required.',
            'channel.exists' => 'The selected channel is invalid.',
        ]);

        $thread = tap(new Thread([
            'title' => $data['title'],
            'body' => $data['body'],
        ]), function ($thread) use ($data) {
            $thread->team_id = Auth::user()->current_team_id;
            $thread->user_id = Auth::id();
            $thread->channel = $data['channel'];
            $thread->status = ThreadStatus::UNSOLVED->value;
            $thread->save();
        });

        if ($request->filled('group')) {
            $redirectSegment = ['group' => $data['group']];
            $group = ForumGroup::whereSlug($data['group'])->first();
            $thread->addToGroup($group)->subscribeGroupMembersToThread();
        } else {
            $redirectSegment = ['mine' => 1];
            $thread->subscribe(
                Team::findOrFail(SpecialAccount::WRMD->id())
            );
        }

        $thread
            ->subscribe(Auth::user()->currentTeam)
            ->notifyTeamsThreadWasCreated(Auth::user()->currentTeam);

        return redirect()->route('forum.index', $redirectSegment)
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Your discussion has been posted.'));
    }

    /**
     * Show the forum thread in storage.
     */
    public function show(Thread $thread): Response
    {
        if ($thread->belongsToGroup()) {
            abort_unless(
                $thread->group->hasMember(Auth::user()->currentTeam),
                404
            );
        }

        $teams = TeamRepository::active();
        $groups = ForumRepository::groupsFor(Auth::user()->currentTeam);
        $channels = $channels = ForumRepository::channels();

        $thread->load('team', 'user', 'replies.team', 'replies.user', 'group');
        $thread->setAttribute('is_following', $thread->isSubscribedToBy(Auth::user()->currentTeam));

        return Inertia::render('Forum/Show', compact('thread', 'channels', 'teams', 'groups'));
    }
}
