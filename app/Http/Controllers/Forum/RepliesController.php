<?php

namespace App\Http\Controllers\Forum;

use App\Domain\Forum\Thread;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{
    /**
     * Persist a new reply.
     */
    public function store(Request $request, Thread $thread): RedirectResponse
    {
        $request->validate(['reply' => 'required']);

        $thread->addReply(
            Auth::user()->current_account_id,
            Auth::id(),
            request('reply')
        );

        return redirect()->route('forum.show', $thread);
    }
}
