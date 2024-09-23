<?php

namespace App\Http\Controllers\DailyTasks;

use App\Concerns\GetSchedulableFromResource;
use App\Enums\DailyTaskSchedulable;
use App\Events\DailyTaskCompleted;
use App\Http\Controllers\Controller;
use App\Support\Timezone;
use App\Support\Wrmd;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RecordedDailyTaskListController extends Controller
{
    use GetSchedulableFromResource;

    /**
     * Store a new daily-tasks task check in storage.
     */
    public function store(Request $request, string $resource, int $resourceId): JsonResponse
    {
        $data = $request->validate([
            'occurrence' => 'required|integer|in:1,2,3,4',
            'occurrence_at' => 'required|date',
            'completed_at' => 'nullable|date',
        ]);

        $schedualableModel = $this->getSchedulable($resource, $resourceId)
            ->validateOwnership(Auth::user()->current_team_id);

        //try {
            $result = $schedualableModel->recordedTasks()->updateOrCreate([
                'occurrence' => $data['occurrence'],
                'occurrence_at' => $data['occurrence_at'],
            ], [
                'user_id' => Auth::id(),
                'summary' => $schedualableModel->summary_body,
                'completed_at' => Timezone::convertFromLocalToUtc($data['completed_at'])
            ]);

            //event(new DailyTaskCompleted($schedualableModel, $check));

            return response()->json($result);
        // } catch (QueryException $e) {
        //     if (Str::contains($e->getMessage(), 'Duplicate entry')) {
        //         return response()->json([
        //             'message' => 'Task already recorded.',
        //         ]);
        //     }
        //     throw $e;
        // }
    }

    /**
     * Remove a daily-tasks task check from storage.
     */
    public function destroy(Request $request, string $resource, int $resourceId): JsonResponse
    {
        $data = $request->validate([
            'occurrence' => 'required|integer|in:1,2,3,4',
            'occurrence_at' => 'required|date',
        ]);

        $schedualableModel = $this->getSchedulable($resource, $resourceId)
            ->validateOwnership(Auth::user()->current_team_id);

        return response()->json(
            $schedualableModel
                ->recordedTasks()
                ->where('occurrence', $data['occurrence'])
                ->where('occurrence_at', $data['occurrence_at'])
                ->delete()
        );
    }
}
