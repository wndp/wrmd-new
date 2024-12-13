<?php

namespace App\Jobs;

use App\Schedulable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateRecordedDailyTasks implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Schedulable $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->deleteOutsideTheWindow();

        $this->deleteGreaterFrequencies();
    }

    private function deleteOutsideTheWindow()
    {
        $this->model->recordedTasks()->where(
            fn ($query) => $query->where('occurrence_at', '<', $this->model->start_date)
                ->unless(is_null($this->model->end_date), fn ($query) => $query->orWhere('occurrence_at', '>', $this->model->end_date))
        )->delete();
    }

    private function deleteGreaterFrequencies()
    {
        $this->model->recordedTasks()->where('occurrence', '>', $this->model->occurrences)->delete();
    }
}
