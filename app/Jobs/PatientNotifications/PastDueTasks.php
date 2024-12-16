<?php

namespace App\Jobs\PatientNotifications;

use App\Collections\DailyTasksCollection;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\SettingKey;
use App\Events\NotifyPatient;
use App\Models\Patient;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PastDueTasks implements ShouldQueue
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
        $tomorrow = Carbon::tomorrow($this->team->settingsStore()->get(SettingKey::TIMEZONE));

        Cache::remember($this->fingerPrint(), $tomorrow, function () {
            return DailyTasksCollection::make()->getPastDueForPatient($this->patient, $this->team);
        })->whenNotEmpty(function ($pastDueTasks) {
            $count = $pastDueTasks->sum(function ($dateGroup) {
                return $dateGroup['tasks']->count();
            });

            NotifyPatient::dispatch(
                $this->patient,
                __('Past Due Tasks!'),
                __('This patient has :number :tasks past due!', [
                    'number' => $count,
                    'tasks' => Str::plural('task', $count),
                ]),
                'danger'
            );
        });
    }

    public function fingerPrint()
    {
        return "PastDueTasks.{$this->team->id}.{$this->patient->id}";
    }
}
