<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\AttendancePoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
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
            'attendance_point_id' => AttendancePoint::factory(),
            'date' => now()->toDateString(),
            'check_in_time' => '08:00:00',
            'check_out_time' => '17:00:00',
            'check_in_latitude' => -6.200000,
            'check_in_longitude' => 106.816666,
            'check_out_latitude' => -6.200000,
            'check_out_longitude' => 106.816666,
            'status' => 'hadir',
            'notes' => 'Tepat waktu',
        ];
    }
}
