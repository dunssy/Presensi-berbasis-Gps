<?php

namespace Database\Factories;

use App\Models\AttendancePoint;
use App\Models\Projek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendancePoint>
 */
class AttendancePointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'projek_id' => Projek::factory(),
            'name' => 'Site '.fake()->city(),
            'latitude' => -6.200000 + fake()->randomFloat(6, -0.05, 0.05),
            'longitude' => 106.816666 + fake()->randomFloat(6, -0.05, 0.05),
            'radius_meters' => 100,
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
