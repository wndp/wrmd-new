<?php

namespace App\Mail;

use App\Models\User;
use App\Reporting\Contracts\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public Report $report, public $subject, public string $body)
    {
        $this->user = $user;
        $this->report = $report;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@wildneighborsdp.org', $this->user->name),
            replyTo: new Address($this->user->email),
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $generator = tap($this->report->shouldNotQueue()->pdf(), function ($generator) {
            $generator->temporaryUrl();
        });

        return [
            Attachment::fromStorageDisk('s3', $generator->filePath)
                ->as($generator->basename().'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
