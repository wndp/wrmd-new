<?php

namespace Tests\Feature\Sharing;

use App\Jobs\QueuedReportGeneration;
use App\Jobs\ShareMedicalRecords;
use App\Mail\ReportEmail;
use App\Models\Admission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class ShareMedicalRecordsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->team = Team::factory()->create();
        $this->user = User::factory()->create();
        $this->admissions = Admission::where('team_id', $this->team->id)->get();
    }

    public function test_patient_medical_records_can_be_shared_as_a_pdf_report(): void
    {
        $job = new ShareMedicalRecords(
            $this->team,
            $this->user,
            $this->admissions->pluck('patient_id')->toArray(),
            [],
            '123-abc',
            'pdf'
        );

        $job->handle();

        Queue::assertPushed(QueuedReportGeneration::class, function ($job) {
            return $job->format === 'pdf'
                && $job->report->team->id === $this->team->id
                && $job->report->deviceUuid === '123-abc';
        });
    }

    public function test_patient_medical_records_can_be_shared_as_an_export_report(): void
    {
        $job = new ShareMedicalRecords(
            $this->team,
            $this->user,
            $this->admissions->pluck('patient_id')->toArray(),
            [],
            '123-abc',
            'export'
        );

        $job->handle();

        Queue::assertPushed(QueuedReportGeneration::class, function ($job) {
            return $job->format === 'export'
                && $job->report->team->id === $this->team->id
                && $job->report->deviceUuid === '123-abc';
        });
    }

    public function test_patient_medical_records_can_be_shared_as_an_emailed_report(): void
    {
        Mail::fake();

        $request = [
            'to' => 'foo@example.com, bar@example.com',
            'subject' => 'fake email',
            'body' => 'lorem',
        ];

        $job = new ShareMedicalRecords(
            $this->team,
            $this->user,
            $this->admissions->pluck('patient_id')->toArray(),
            $request,
            '123-abc',
            'email'
        );

        $job->handle();

        Mail::assertQueued(ReportEmail::class, function ($mail) {
            return $mail->hasTo('foo@example.com')
                && $mail->hasTo('bar@example.com')
                && $mail->hasReplyTo($this->user->email)
                && $mail->hasSubject('fake email');
            // && $mail->hasAttachment(
            //     Attachment::fromPath('/path/to/file')
            //         ->as('name.pdf')
            //         ->withMime('application/pdf')
            // );
            //&& $mail->hasAttachmentFromStorageDisk('s3', '$path', '$name');
        });
    }
}
