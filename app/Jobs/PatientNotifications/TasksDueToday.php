<?php

namespace App\Jobs\PatientNotifications;

use App\Collections\DailyTasksCollection;
use App\Enums\SettingKey;
use App\Events\NotifyPatient;
use App\Models\Patient;
use App\Models\Team;
use App\Support\DailyTasksFilters;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TasksDueToday implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Team $team, public Patient $patient)
    {
        $this->team = $team;
        $this->patient = $patient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date = Carbon::now($this->team->settingsStore()->get(SettingKey::TIMEZONE));

        Cache::remember($this->fingerPrint($date), $date->addHours(4), function () use ($date) {
            return DailyTasksCollection::make()->withFilters(new DailyTasksFilters([
                'date' => $date,
            ]))->forPatient($this->patient, $this->team);
        })->whenNotEmpty(function ($tasksDue) {
            NotifyPatient::dispatch(
                $this->patient,
                __('Tasks Due!'),
                __('This patient has :number :tasks due today.', [
                    'number' => $tasksDue->count(),
                    'tasks' => Str::plural('task', $tasksDue->count()),
                ])
            );
        });
    }

    public function fingerPrint($date)
    {
        return "TasksDueToday.{$this->team->id}.{$this->patient->id}.{$date->toDateString()}";
    }
}
