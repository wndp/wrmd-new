<?php

namespace App\Jobs;

use App\Enums\SpecialAccount;
use App\Exceptions\UnprocessableAdmissionException;
use App\Models\Admission;
use App\Models\OilProcessing;
use App\Models\Team;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferIoaPatient implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public OilProcessing $processing)
    {
        $this->processing = $processing;
    }

    public function handle()
    {
        throw_if(
            $this->alreadyTransferd($this->processing->patient),
            UnprocessableAdmissionException::class,
            'This patient has already been transferred to the OWCN IOA account.'
        );

        $owcnIoa = Team::findOrFail(SpecialAccount::OWCN_IOA);
        $fromTeam = Admission::where('patient_id', $this->processing->patient_id)->first()->team;

        $admission = tap(new Admission(['case_year' => date('Y')]), function ($admission) use ($owcnIoa) {
            $admission->team()->associate($owcnIoa);
            $admission->patient()->associate($this->processing->patient);
            $admission->save();
        });

        $this->recordTransfer($this->processing->patient, $fromTeam, $owcnIoa);

        //event(new PatientAdmitted($owcnIoa, $admission->patient));
    }

    /**
     * Determine if the patient is already transfered to the OWCN IOB account.
     *
     * @param  Patient  $patient
     * @return bool
     */
    public function alreadyTransferd($patient)
    {
        return Admission::where([
            'team_id' => SpecialAccount::OWCN_IOA,
            'patient_id' => $patient->id,
        ])->exists();
    }

    public function recordTransfer($patient, $fromTeam, $owcnIoa)
    {
        $transfer = new Transfer([
            'uuid' => Str::uuid(),
            'is_collaborative' => true,
            'is_accepted' => true,
            'responded_at' => Carbon::now(),
        ]);
        $transfer->patient()->associate($patient);
        $transfer->fromTeam()->associate($fromTeam);
        $transfer->toTeam()->associate($owcnIoa);
        $transfer->save();

        RecordSharedCases::dispatch(
            User::wrmdbot(),
            [$patient->id],
            Carbon::now(),
            'Individual Oiled Animal was submitted to OWCN'
        );
    }
}
