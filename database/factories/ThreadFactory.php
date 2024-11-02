<?php

namespace Database\Factories;

use App\Enums\Channel;
use App\Enums\ThreadStatus;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thread>
 */
class ThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory()->createQuietly(),
            'channel' => Channel::GENERAL,
            'status' => ThreadStatus::UNSOLVED,
            'title' => $this->faker->word(),
            'body' => $this->faker->sentence(),
        ];
    }
}
