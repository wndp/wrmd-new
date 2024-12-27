<?php

namespace Tests\Feature\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class EmailVerificationTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $me = $this->createTeamUser(userOverrides: [
            'email_verified_at' => null,
        ]);

        $this->actingAs($me->user)->get(route('verification.notice'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Auth/VerifyEmail');
            });
    }

    public function test_email_can_be_verified(): void
    {
        $me = $this->createTeamUser(userOverrides: [
            'email_verified_at' => null,
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $me->user->id, 'hash' => sha1($me->user->email)]
        );

        $response = $this->actingAs($me->user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($me->user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(config('fortify.home').'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $me = $this->createTeamUser(userOverrides: [
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $me->user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($me->user)->get($verificationUrl);

        $this->assertFalse($me->user->fresh()->hasVerifiedEmail());
    }
}
