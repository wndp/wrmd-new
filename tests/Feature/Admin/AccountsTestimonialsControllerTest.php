<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AccountsTestimonialsControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessTestimonials(): void
    {
        $this->get(route('admin.testimonials.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessTestimonials(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('admin.testimonials.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheTestimonialsIndexPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->get(route('admin.testimonials.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Testimonials/Index')
                    ->has('testimonials');
            });
    }

    #[Test]
    public function itDisplaysThePageToCreateATestimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)
            ->get(route('admin.testimonials.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Testimonials/Create')
                    ->has('teams');
            });
    }

    #[Test]
    public function dataIsRequiredToStoreANewTestimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->post(route('admin.testimonials.store'))
            ->assertInvalid([
                'name' => 'The name field is required.',
                'text' => 'The text field is required.',
                'team_id' => 'The team id field is required.',
            ]);
    }

    #[Test]
    public function aNewTestimonialIsSavedToStorage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->post(route('admin.testimonials.store'), [
            'name' => 'Dr Nla',
            'text' => 'foo bar',
            'team_id' => $me->team->id,
        ])
            ->assertRedirect(route('admin.testimonials.index'));

        $this->assertDatabaseHas('testimonials', [
            'name' => 'Dr Nla',
            'text' => 'foo bar',
            'team_id' => $me->team->id,
        ]);
    }

    #[Test]
    public function itDisplaysThePageToEditATestimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $testimonial = Testimonial::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)
            ->get(route('admin.testimonials.edit', $testimonial->id))
            ->assertOk()
            ->assertInertia(function ($page) use ($testimonial) {
                $page->hasAll('teams', 'testimonial')
                    ->where('testimonial.id', $testimonial->id);
            });
    }

    #[Test]
    public function dataIsRequiredToUpdateANewTestimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $testimonial = Testimonial::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('admin.testimonials.update', $testimonial->id))
            ->assertInvalid([
                'name' => 'The name field is required.',
                'text' => 'The text field is required.',
                'team_id' => 'The team id field is required.',
            ]);
    }

    #[Test]
    public function aTestimonialIsUpdatedInStorage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $testimonial = Testimonial::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('admin.testimonials.update', $testimonial->id), [
            'name' => 'Dr Nla',
            'text' => 'foo bar',
            'team_id' => $me->team->id,
        ])
            ->assertRedirect(route('admin.testimonials.index'));

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'team_id' => $me->team->id,
            'name' => 'Dr Nla',
            'text' => 'foo bar',
        ]);
    }

    #[Test]
    public function itDeletesATestimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $testimonial = Testimonial::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->delete(route('admin.testimonials.destroy', $testimonial->id))
            ->assertRedirect(route('admin.testimonials.index'));

        $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
    }
}
