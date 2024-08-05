<?php

namespace App\Http\Controllers\Forum;

use App\Domain\Forum\Thread;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThreadStatusController extends Controller
{
    /**
     * Store a new thread subscription.
     */
    public function __invoke(Request $request, Thread $thread): JsonResponse
    {
        $result = $thread->update(
            $request->validate(['status' => 'required'])
        );

        return response()->json($result);
    }
}
