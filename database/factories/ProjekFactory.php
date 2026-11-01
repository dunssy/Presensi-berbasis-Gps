<?php

namespace Database\Factories;

use App\Models\Projek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Projek>
 */
class ProjekFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Project',
            'code' => 'PRJ-'.fake()->unique()->numerify('####'),
            'description' => fake()->sentence(),
            'client_name' => fake()->company(),
            'start_date' => now()->subMonths(1)->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'status' => 'aktif',
        ];
    }
}
