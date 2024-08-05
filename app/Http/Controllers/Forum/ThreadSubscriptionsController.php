<?php

namespace App\Http\Controllers\Forum;

use App\Domain\Forum\Thread;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ThreadSubscriptionsController extends Controller
{
    /**
     * Store a new thread subscription.
     */
    public function store(Thread $thread): JsonResponse
    {
        try {
            $response = $thread->subscribe(Auth::user()->current_account);
        } catch (QueryException $e) {
            $response = false;
        }

        return response()->json($response);
    }

    /**
     * Delete an existing thread subscription.
     */
    public function destroy(Thread $thread): JsonResponse
    {
        try {
            $response = $thread->unsubscribe(Auth::user()->current_account);
        } catch (QueryException $e) {
            $response = false;
        }

        return response()->json($response);
    }
}
