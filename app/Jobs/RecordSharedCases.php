<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordSharedCases implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public array $patientIds,
        public Carbon $sharedAt,
        public string $comment
    ) {
        $this->user = $user;
        $this->patientIds = $patientIds;
        $this->sharedAt = $sharedAt;
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->patientIds as $patientId) {
            $treatmentLog = new TreatmentLog([
                'comments' => $this->comment,
                'treated_at' => $this->sharedAt,
                'user_name' => $this->user->name,
            ]);

            $treatmentLog->patient_id = $patientId;
            $treatmentLog->user_id = $this->user->id;

            $treatmentLog->save();
        }
    }
}
