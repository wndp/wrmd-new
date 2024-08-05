<?php

namespace App\Http\Controllers\Forum;

use App\Domain\Accounts\Account;
use App\Domain\Forum\Channels;
use App\Domain\Forum\ForumRepository;
use App\Domain\Forum\Group;
use App\Domain\Forum\Thread;
use App\Http\Controllers\Controller;
use App\Notifications\ForumGroupCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumGroupsController extends Controller
{
    /**
     * Store a newly created thread in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'members' => 'required|array',
            'settings' => 'nullable|array',
        ]);

        $account = Auth::user()->current_account;
        $group = Group::create($data);
        $group->members()->save($account, ['role' => 'admin']);
        $group->members()->saveMany(
            Account::whereIn('id', $data['members'])->where('id', '!=', $account->id)->get(),
            ['role' => 'user']
        );

        $thread = tap(new Thread([
            'title' => __('Welcome to the :groupName group!', ['groupName' => $group->name]),
            'body' => __(':createdBy created this group and added you and :count other organizations.', [
                'createdBy' => $account->organization, 'count' => count($data['members']),
            ]),
        ]), function ($thread) use ($account) {
            $thread->account_id = $account->id;
            $thread->user_id = Auth::id();
            $thread->channel_id = Channels::GENERAL;
            $thread->status = 'Solved';
            $thread->save();
        });

        $thread->addToGroup($group)->subscribeGroupMembersToThread();

        $group->members->each(fn ($account) => ForumRepository::forgetGroupsFor($account));
        $group->members
            ->filter(fn ($account) => $account->id !== Auth::user()->current_account_id)
            ->each(fn ($account) => $account->notify(new ForumGroupCreated($group)));

        return redirect()->route('forum.index', ['group' => $group->slug])
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Your :groupName group has been created.', ['groupName' => $group->name]));
    }

    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'members' => 'required|array',
            'settings' => 'nullable|array',
        ]);

        $group->update($data);

        $originalMembers = $group->members;
        $groupAdmins = $group->members()->wherePivot('role', 'admin')->get()->pluck('id');

        $group->members()->syncWithPivotValues(
            Account::whereIn('id', $data['members'])->get(),
            ['role' => 'user']
        );

        $group->members()->updateExistingPivot(
            Account::whereIn('id', $data['members'])->whereIn('id', $groupAdmins)->get(),
            ['role' => 'admin']
        );

        $group->members->each(fn ($account) => ForumRepository::forgetGroupsFor($account));
        $group->fresh()->members
            ->filter(function ($account) {
                return $account->id !== Auth::user()->current_account_id;
            })
            ->diff($originalMembers)
            ->each(fn ($account) => $account->notify(new ForumGroupCreated($group)));

        return redirect()->route('forum.index', ['group' => $group->slug])
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('The :groupName group was updated.', ['groupName' => $data['name']]));
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->members->each(fn ($account) => ForumRepository::forgetGroupsFor($account));
        $group->members()->detach();
        $group->threads->each(fn ($thread) => $thread->replies()->delete());
        $group->threads()->detach();
        $group->threads->each->delete();
        $group->delete();

        return redirect()->route('forum.index')
            ->with('flash.notificationHeading', __(':groupName group deleted.', ['groupName' => $group->name]))
            ->with('flash.notification', __('We hope you meant that.'));
    }
}
