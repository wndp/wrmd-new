<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Models\Customer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'user_id' => User::factory(),
            'personal_team' => true,
            'status' => AccountStatus::ACTIVE,
            'contact_name' => $this->faker->name,
            'contact_email' => $this->faker->email,
            'country' => 'US',
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'subdivision' => $this->faker->stateAbbr(),
            'postal_code' => $this->faker->postcode,
            'phone_number' => $this->faker->phoneNumber,
        ];
    }

    public function withSubscription(string|int $priceId = null): static
    {
        return $this->afterCreating(function (Team $team) use ($priceId) {
            optional($team->customer)->update(['trial_ends_at' => null]);

            $subscription = $team->subscriptions()->create([
                'type' => 'default',
                'paddle_id' => fake()->unique()->numberBetween(1, 1000),
                'status' => 'active',
                'trial_ends_at' => null,
                'paused_at' => null,
                'ends_at' => null,
            ]);

            $subscription->items()->create([
                'product_id' => fake()->unique()->numberBetween(1, 1000),
                'price_id' => $priceId,
                'status' => 'active',
                'quantity' => 1,
            ]);
        });
    }
}
