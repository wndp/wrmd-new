<?php

namespace App\Http\Controllers\DailyTasks;

use App\Concerns\GetSchedulableFromResource;
use App\Enums\DailyTaskSchedulable;
use App\Http\Controllers\Controller;
use App\Schedulable;
use App\Support\Wrmd;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DestroyDailyTaskController extends Controller
{
    use GetSchedulableFromResource;

    /**
     * Remove a daily-tasks from storage.
     */
    public function __invoke(string $resource, string $resourceId)
    {
        $schedulable = $this->getSchedulable($resource, $resourceId)
            ->validateOwnership(Auth::user()->current_team_id);

        abort_unless($schedulable instanceof Schedulable, 422);

        $name = Wrmd::humanize($schedulable);
        $schedulable->delete();

        return back()
            ->with('notification.heading', __('Task Deleted'))
            ->with('notification.text', "The $name task was deleted.");
    }
}
