<?php

namespace App\Http\Controllers\Forum;

use App\Domain\Forum\ForumRepository;
use App\Domain\Forum\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ForumGroupExitController extends Controller
{
    public function __invoke(Group $group): RedirectResponse
    {
        ForumRepository::forgetGroupsFor(Auth::user()->currentAccount);

        $group->members()->detach(Auth::user()->current_team_id);
        $group->threads->each(fn ($thread) => $thread->unsubscribe(Auth::user()->currentAccount));

        return redirect()->route('forum.index')
            ->with('notification.heading', __('We hope you meant that.'))
            ->with('notification.text', __('Removed from the group :groupName.', ['groupName' => $group->name]));
    }
}
