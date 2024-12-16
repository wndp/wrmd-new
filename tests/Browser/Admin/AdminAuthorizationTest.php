<?php

namespace Tests\Browser\Admin;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\DuskTestCase;
use Tests\Traits\CreatesTeamUser;

final class AdminAuthorizationTest extends DuskTestCase
{
    use CreatesTeamUser;
    use DatabaseTruncation;

    #[Test]
    public function wrmdStaffCanAccessAdminArea(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->browse(function ($browser) use ($me) {
            $browser->visit(route('home'))
                ->waitForText('Remember me')
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->waitForText('Admin')
                ->clickLink('Admin')
                ->waitForText('WRMD Admin')
                ->assertRouteIs('admin.dashboard');
        });
    }

    #[Test]
    public function ensureOthersCanNotAccessAdminArea(): void
    {
        $me = $this->createTeamUser();

        $this->browse(function ($browser) use ($me) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->assertDontSee('Admin')
                ->visit('/accounts')
                ->waitForText('404');
        });
    }
}
