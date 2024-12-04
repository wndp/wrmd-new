<?php

namespace Tests\Browser\Admin;

use App\Enums\Ability;
use App\Enums\WrmdStaff;
use App\Models\Team;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\DuskTestCase;
use Tests\Traits\CreatesTeamUser;

final class TestimonialsTest extends DuskTestCase
{
    use CreatesTeamUser;
    use DatabaseTruncation;

    #[Test]
    public function listTestimonials(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        Testimonial::factory()->create(['name' => 'Jim Halpert']);

        $this->browse(function ($browser) use ($me) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->waitForText('Admin')
                ->visit(route('admin.testimonials.index'))
                ->waitForText('Jim Halpert')
                ->assertSee('Jim Halpert')
                ->clickLink('Edit Testimonial')
                ->waitForText('Update Testimonial')
                ->assertSee('Update Testimonial');
        });
    }

    #[Test]
    public function createATestimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $team = Team::factory()->create();

        $this->browse(function ($browser) use ($me, $team) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->waitForText('Admin')
                ->visit(route('admin.testimonials.create'))
                ->waitForText('New Testimonial')
                ->select('team_id', $team->id)
                ->type('name', 'Pam Halpert')
                ->type('text', 'lorem ipsum')
                ->press('CREATE NEW TESTIMONIAL')
                ->waitForText('lorem ipsum');
        });

        $this->assertDatabaseHas('testimonials', [
            'team_id' => $team->id,
            'name' => 'Pam Halpert',
            'text' => 'lorem ipsum',
        ]);
    }

    #[Test]
    public function updateATestimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $testimonial = Testimonial::factory()->create(['name' => 'Jim Halpert']);
        $team = Team::factory()->create();

        $this->browse(function ($browser) use ($me, $testimonial, $team) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->waitForText('Admin')
                ->visit(route('admin.testimonials.edit', $testimonial))
                ->waitForText('Update Testimonial')
                ->select('team_id', $team->id)
                ->type('name', 'Pam Halpert')
                ->type('text', 'lorem ipsum')
                ->press('UPDATE TESTIMONIAL')
                ->waitForText('lorem ipsum');
        });

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'team_id' => $team->id,
            'name' => 'Pam Halpert',
            'text' => 'lorem ipsum',
        ]);
    }

    #[Test]
    public function searchTestimonials(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $testimonial1 = Testimonial::factory()->create(['name' => 'Jim Halpert']);
        $testimonial2 = Testimonial::factory()->create(['name' => 'Pam Halpert']);

        $this->browse(function ($browser) use ($me) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->waitForText('Admin')
                ->visit(route('admin.testimonials.index'))
                ->pause(1500)
                ->type('desktop-testimonial-search', 'Jim')
                ->pause(1500)
                ->assertSee('Jim Halpert')
                ->assertDontSee('Pam Halpert');
        });
    }

    #[Test]
    public function deleteATestimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $testimonial = Testimonial::factory()->create(['name' => 'Jim Halpert']);

        $this->browse(function ($browser) use ($me, $testimonial) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->waitForText('Admin')
                ->visit(route('admin.testimonials.edit', $testimonial))
                ->press('DELETE TESTIMONIAL')
                ->waitForText('Are you sure you want to delete this testimonial?')
                ->press('YES')
                ->waitForText("{$testimonial->name}'s testimonial was deleted.");
        });

        $this->assertDatabaseMissing('testimonials', [
            'id' => $testimonial->id,
        ]);
    }
}
