<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Enums\Plan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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
            'name' => fake()->unique()->company(),
            'user_id' => User::factory(),
            'is_master_account' => false,
            'personal_team' => true,
            'status' => AccountStatus::ACTIVE,
            'contact_name' => fake()->name,
            'contact_email' => fake()->unique()->safeEmail(),
            'country' => 'US',
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'subdivision' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode,
            'phone' => fake()->phoneNumber,
        ];
    }

    public function suspended(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'account_status' => 'suspended',
            ];
        });
    }

    public function withProSubscription(): static
    {
        return $this->afterCreating(function (Team $team) {
            optional($team->customer)->update(['trial_ends_at' => null]);

            $subscription = $team->subscriptions()->create([
                'type' => 'default',
                'paddle_id' => fake()->unique()->numberBetween(1, 1000),
                'status' => 'active',
                'trial_ends_at' => null,
                'paused_at' => null,
                'ends_at' => null,
            ]);

            $proPlan = Arr::first(config('spark.billables.team.plans'), fn ($plan) => $plan['name'] === Plan::PRO->value);

            $subscription->items()->create([
                'product_id' => fake()->unique()->numberBetween(1, 1000),
                'price_id' => $proPlan['yearly_id'],
                'status' => 'active',
                'quantity' => 1,
            ]);
        });
    }
}
