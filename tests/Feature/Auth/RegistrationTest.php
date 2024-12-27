<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Auth/Register');
            });
    }

    public function test_a_valid_email_is_required_to_register(): void
    {
        $this->post('/register')
            ->assertInvalid(['email' => 'The email field is required.']);

        $this->post('/register', ['email' => 'x'])
            ->assertInvalid(['email' => 'The email field must be a valid email address.']);

        User::factory()->create(['email' => 'used@example.com']);

        $this->post('/register', ['email' => 'used@example.com'])
            ->assertInvalid(['email' => 'The email has already been taken.']);

        $this->post('/register', ['email' => 'example@example.com', 'email_confirmation' => 'wrong@example.com'])
            ->assertInvalid(['email' => 'The email field confirmation does not match.']);
    }

    public function test_a_valid_password_is_required_to_register(): void
    {
        $this->post(route('register.store'))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->post(route('register.store'), ['password' => 'bad-password', 'password_confirmation' => 'bad-password-again'])
            ->assertInvalid(['password' => 'The password field confirmation does not match.']);
    }

    public function test_other_data_is_required_to_register(): void
    {
        $this->post(route('register.store'))
            ->assertInvalid(['organization' => 'The organization field is required.'])
            ->assertInvalid(['name' => 'The name field is required.'])
            ->assertInvalid(['country' => 'The country field is required.'])
            ->assertInvalid(['address' => 'The address field is required.'])
            ->assertInvalid(['city' => 'The city field is required.'])
            ->assertInvalid(['subdivision' => 'The subdivision field is required.'])
            ->assertInvalid(['phone' => 'The phone field is required.'])
            ->assertInvalid(['timezone' => 'The timezone field is required.'])
            ->assertInvalid(['terms' => 'The terms field must be accepted.']);

        $this->post(route('register.store'), [
            'timezone' => 'wrong',
            'phone' => '123',
            'country' => 'wrong',
            'subdivision' => 'wrong',
        ])
            ->assertInvalid(['timezone' => 'The timezone field must be a valid timezone.'])
            ->assertInvalid(['phone' => 'The phone field must be a valid number.'])
            ->assertInvalid(['country' => 'The selected country is invalid.'])
            ->assertInvalid(['subdivision' => 'The selected subdivision is invalid.']);
    }

    public function test_a_new_account_can_be_registered(): void
    {
        $this->withoutExceptionHandling();
        Event::fake();
        Bus::fake();

        $this->post(route('register.store'), [
            'email' => 'example@example.com',
            'email_confirmation' => 'example@example.com',
            'password' => 'Secret132',
            'password_confirmation' => 'Secret132',
            'organization' => 'example wildlife center',
            'name' => 'Jim Halpert',
            'country' => 'US',
            'address' => '123 main st',
            'city' => 'Scranton',
            'subdivision' => 'US-PA',
            'phone' => '(808) 321-1234',
            'timezone' => 'America/New_York',
            'terms' => 1,
            'g-recaptcha-response' => 'example',
        ])
            ->assertRedirect(config('fortify.home'));

        $this->assertDatabaseHas('teams', [
            'contact_email' => 'example@example.com',
            'name' => 'example wildlife center',
            'contact_name' => 'Jim Halpert',
            'country' => 'US',
            'address' => '123 main st',
            'city' => 'Scranton',
            'subdivision' => 'US-PA',
            'phone' => '(808) 321-1234',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Jim Halpert',
            'email' => 'example@example.com',
        ]);

        Event::assertDispatched(Registered::class);
    }
}
