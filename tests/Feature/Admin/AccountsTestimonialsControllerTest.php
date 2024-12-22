<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AccountsTestimonialsControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_testimonials(): void
    {
        $this->get(route('admin.testimonials.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_testimonials(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('admin.testimonials.index'))->assertForbidden();
    }

    public function test_it_displays_the_testimonials_index_page(): void
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

    public function test_it_displays_the_page_to_create_a_testimonial(): void
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

    public function test_data_is_required_to_store_a_new_testimonial(): void
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

    public function test_a_new_testimonial_is_saved_to_storage(): void
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

    public function test_it_displays_the_page_to_edit_a_testimonial(): void
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

    public function test_data_is_required_to_update_a_new_testimonial(): void
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

    public function test_a_testimonial_is_updated_in_storage(): void
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

    public function test_it_deletes_a_testimonial(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $testimonial = Testimonial::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->delete(route('admin.testimonials.destroy', $testimonial->id))
            ->assertRedirect(route('admin.testimonials.index'));

        $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
    }
}
