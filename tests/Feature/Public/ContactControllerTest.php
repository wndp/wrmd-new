<?php

namespace Tests\Feature\Public;

use App\Mail\ContactEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class ContactControllerTest extends TestCase
{
    public function test_send_contact_email(): void
    {
        //$this->withoutExceptionHandling();
        Mail::fake();

        $this->from(route('contact.create'))
            ->post(route('contact.store'), [
                'name' => 'Jane Smith',
                'email' => 'janesmith@example.com',
                'organization' => 'My Wildlife Place',
                'subject' => 'Good Job!',
                'message' => 'This looks great.',
            ])
            ->assertRedirect(route('contact.create'));

        Mail::assertQueued(ContactEmail::class, function ($mail) {
            return $mail->hasTo('support@wildneighborsdp.org') &&
                   $mail->hasFrom('janesmith@example.com') &&
                   $mail->hasSubject('Good Job!') &&
                   $mail->viewData['body'] = 'This looks great.';
        });
    }
}
