<?php

namespace Tests\Feature\Sharing;

use App\Enums\Ability;
use App\Jobs\SendTransferRequest;
use App\Models\Team;
use App\Notifications\TransferRequestWasSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class SendTransferRequestTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;

    //use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_a_transfer_request_is_sent(): void
    {
        Notification::fake();

        $me = $this->createTeamUser();
        // BouncerFacade::allow($me->user)->to(Ability::SHARE_PATIENTS->value);
        // dd($me->user->can(Ability::SHARE_PATIENTS->value));
        $admission = $this->createCase($me->team);
        $team = Team::factory()->create();

        SendTransferRequest::dispatchSync(
            $me->team,
            $team,
            $admission->patient,
            $me->user,
            true
        );

        Notification::assertSentTo($team, TransferRequestWasSent::class, function ($n) use ($me) {
            $url = Uri::of($n->link);

            return $n->message = "Transfer request from {$me->team->organization}"
                && Str::contains($url->path(), 'maintenance/transfers')
                && Str::isUuid($url->query()->get('id'));
        });

        $this->assertDatabaseHas('transfers', [
            'patient_id' => $admission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $team->id,
            'is_collaborative' => 1,
            'is_accepted' => null,
            'responded_at' => null,
        ]);
    }
}
