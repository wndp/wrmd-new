<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reply>
 */
class ReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thread_id' => Thread::factory(),
            'team_id' => Team::factory()->createQuietly(),
            'user_id' => User::factory(),
            'body' => $this->faker->sentence()
        ];
    }
}
