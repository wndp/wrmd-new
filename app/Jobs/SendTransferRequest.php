<?php

namespace App\Jobs;

use App\Enums\SettingKey;
use App\Models\Patient;
use App\Models\Team;
use App\Models\Transfer;
use App\Models\User;
use App\Notifications\TransferRequestWasSent;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

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

        $this->toTeam->notify(new TransferRequestWasSent(
            __('Transfer request from :organization', ['organization' => $this->fromTeam->name]),
            URL::route('maintenance.transfers', ['id' => $transfer->id]),
        ));

        if ((bool) $this->fromTeam->settingsStore()->get(SettingKey::LOG_SHARES)) {
            dispatch(new RecordSharedCases(
                $this->user,
                [$this->patient->id],
                Carbon::now($this->fromTeam->settingsStore()->get(SettingKey::TIMEZONE)),
                __('A transfer request was sent to :organization.', ['organization' => $this->toTeam->name])
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
