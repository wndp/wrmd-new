<?php

namespace App\Jobs;

use App\Jobs\RecordSharedCases;
use App\Models\Patient;
use App\Models\Team;
use App\Models\Transfer;
use App\Models\User;
use App\Notifications\TransferRequestWasSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SendTransferRequest implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Team $fromTeam,
        public Team $toTeam,
        public Patient $patient,
        public User $user,
        public bool $isCollaborative = false
    ) {
        $this->fromTeam = $fromTeam;
        $this->toTeam = $toTeam;
        $this->patient = $patient;
        $this->user = $user;
        $this->isCollaborative = $isCollaborative;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transfer = $this->storeTransferRequest();

        // $this->toTeam->notify(new TransferRequestWasSent(
        //     "Transfer request from {$this->fromTeam->organization}",
        //     URL::route('maintenance.transfers', ['uuid' => $transfer->uuid]),
        // ));

        if ((bool) $this->fromTeam->settingsStore()->get('logShares')) {
            dispatch(new RecordSharedCases(
                $this->user,
                [$this->patient->id],
                now($this->fromTeam->settingsStore()->get('timezone')),
                __('A transfer request was sent to :organization.', ['organization' => $this->toTeam->organization])
            ));
        }
    }

    /**
     * Store the transfer request.
     */
    protected function storeTransferRequest()
    {
        $transfer = new Transfer([
            'is_collaborative' => $this->isCollaborative,
        ]);
        $transfer->patient()->associate($this->patient);
        $transfer->fromTeam()->associate($this->fromTeam);
        $transfer->toTeam()->associate($this->toTeam);
        $transfer->save();

        return $transfer;
    }
}
