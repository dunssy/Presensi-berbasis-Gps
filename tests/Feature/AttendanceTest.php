<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendancePoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_karyawan_can_check_in_with_gps_coordinates(): void
    {
        $karyawan = User::factory()->karyawan()->create();
        $point = AttendancePoint::factory()->create();

        $response = $this->actingAs($karyawan)->post(route('karyawan.attendance.check-in'), [
            'attendance_point_id' => $point->id,
            'latitude' => -7.2574719,
            'longitude' => 112.7520883,
            'notes' => 'Presensi masuk di site Surabaya',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $karyawan->id,
            'status' => 'hadir',
        ]);
    }

    public function test_karyawan_cannot_check_in_twice_on_the_same_day(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        // First check in
        Attendance::factory()->create([
            'user_id' => $karyawan->id,
            'date' => now()->toDateString(),
            'check_in_time' => '08:00:00',
        ]);

        $response = $this->actingAs($karyawan)->post(route('karyawan.attendance.check-in'), [
            'latitude' => -7.2574719,
            'longitude' => 112.7520883,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_karyawan_can_check_out_with_gps_coordinates(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        Attendance::factory()->create([
            'user_id' => $karyawan->id,
            'date' => now()->toDateString(),
            'check_in_time' => '08:00:00',
            'check_out_time' => null,
        ]);

        $response = $this->actingAs($karyawan)->post(route('karyawan.attendance.check-out'), [
            'latitude' => -7.2574719,
            'longitude' => 112.7520883,
            'notes' => 'Selesai tugas harian',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $attendance = Attendance::where('user_id', $karyawan->id)->whereDate('date', now()->toDateString())->first();
        $this->assertNotNull($attendance->check_out_time);
    }

    public function test_check_in_fails_when_coordinates_are_missing(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        $response = $this->actingAs($karyawan)->post(route('karyawan.attendance.check-in'), [
            'attendance_point_id' => 1,
        ]);

        $response->assertSessionHasErrors(['latitude', 'longitude']);
    }
}
