<?php

namespace Database\Factories;

use App\Models\Logbook;
use App\Models\Projek;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Logbook>
 */
class LogbookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'projek_id' => Projek::factory(),
            'date' => now()->toDateString(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'progress_percentage' => fake()->numberBetween(10, 100),
            'status' => 'submitted',
            'review_notes' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }
}
