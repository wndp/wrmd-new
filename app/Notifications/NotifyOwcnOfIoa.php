<?php

namespace App\Notifications;

use App\Enums\SpecialAccount;
use App\Models\OilProcessing;
use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyOwcnOfIoa extends Notification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new notification instance.
     */
    public function __construct(public OilProcessing $processing)
    {
        $this->processing = $processing;
    }

    public function handle()
    {
        app(Dispatcher::class)->sendNow($this->notifiables(), $this);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $owcnIoaTeamId = SpecialAccount::OWCN_IOA;
        $admissions = $this->processing->patient->admissions;
        $originTeam = $admissions->firstWhere('team_id', '!=', $owcnIoaTeamId)->team;
        $IoaAdmission = $admissions->firstWhere('team_id', '===', $owcnIoaTeamId);

        $url = url("patients/oiled/processing?y={$IoaAdmission->case_year}&c={$IoaAdmission->id}&a={$owcnIoaTeamId}&queryCache=flush");

        return (new MailMessage)
            ->subject('IOA Notification')
            ->greeting('An Individual Oiled Animal Has Been Processed!')
            ->line("Organization: {$originTeam->organization}")
            ->line("Species: {$this->processing->patient->common_name}")
            ->line("IOA Case Number: {$IoaAdmission->case_number}")
            ->action('View Patient', $url);

        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the users to receive a notification of an IOA.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function notifiables()
    {
        $userEmails = Team::findOrFail(SpecialAccount::OWCN_IOA)->settingsStore()->get('owcn.notifyOfIoa');

        /**
         * ToDo
         * Verify that the users are still users of the OWCN IOA team.
         */

        return User::whereIn('email', $userEmails)->get();
    }
}
