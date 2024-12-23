<?php

namespace App\Jobs;

use App\Enums\SettingKey;
use App\Mail\ReportEmail;
use App\Models\Team;
use App\Models\User;
use App\Reporting\Reports\PatientMedicalRecord;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ShareMedicalRecords implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Team $team,
        public User $user,
        public array $patientIds,
        public $request,
        public $deviceUuid,
        public $format
    ) {
        $this->team = $team;
        $this->user = $user;
        $this->patientIds = $patientIds;
        $this->request = $request;
        $this->deviceUuid = $deviceUuid;
        $this->format = $format;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        call_user_func_array([$this, $this->format], [$this->patientIds]);
    }

    public function pdf($patientIds)
    {
        $this->recordSharedCases($patientIds, 'Patient medical record was printed.');

        (new PatientMedicalRecord($this->team))
            ->withRequest(new Request($this->request))
            ->fromDevice($this->deviceUuid)
            ->patient($patientIds)
            ->pdf();
    }

    public function export($patientIds)
    {
        $this->recordSharedCases($patientIds, 'Patient medical record was exported.');

        (new PatientMedicalRecord($this->team))
            ->withRequest(new Request($this->request))
            ->fromDevice($this->deviceUuid)
            ->patient($patientIds)
            ->export();
    }

    public function email($patientIds)
    {
        $mail = Mail::to(preg_split('/[,\s]+/', $this->request['to']));

        if (array_key_exists('bcc_me', $this->request) && $this->request['bcc_me']) {
            $mail->bcc($this->user->email, $this->user->name);
        }

        $mail->send(new ReportEmail(
            $this->user,
            (new PatientMedicalRecord($this->team))->withRequest(new Request($this->request))->patient($patientIds),
            $this->request['subject'],
            $this->request['body']
        ));

        $this->recordSharedCases($patientIds, "Patient medical record was emailed to {$this->request['to']}.");
    }

    /**
     * Record that a case has been shared.
     *
     * @param  \Illuminate\Support\Collection|array  $patientIds
     */
    protected function recordSharedCases($patientIds, string $message): void
    {
        if ((bool) $this->team->settingsStore()->get(SettingKey::LOG_SHARES)) {
            dispatch(new RecordSharedCases(
                $this->user,
                $patientIds,
                now($this->team->settingsStore()->get(SettingKey::TIMEZONE)),
                $message
            ));
        }
    }
}
