<?php

namespace Database\Factories;

use App\Models\ProjectAssignment;
use App\Models\Projek;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectAssignment>
 */
class ProjectAssignmentFactory extends Factory
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
            'user_id' => User::factory(),
            'role_in_project' => 'member',
            'assigned_at' => now()->toDateString(),
        ];
    }
}
