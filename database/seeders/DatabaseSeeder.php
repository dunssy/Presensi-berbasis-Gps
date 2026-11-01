<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendancePoint;
use App\Models\Logbook;
use App\Models\ProjectAssignment;
use App\Models\Projek;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Default Users with Different Roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@technodrives.com'],
            [
                'name' => 'Admin Technodrives',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'department' => 'IT & Operasional',
                'is_active' => true,
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@technodrives.com'],
            [
                'name' => 'Budi Santoso (Manager)',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '081234567891',
                'department' => 'Project Management',
                'is_active' => true,
            ]
        );

        $karyawan = User::firstOrCreate(
            ['email' => 'karyawan@technodrives.com'],
            [
                'name' => 'Ahmad Fauzi (Teknisi)',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'phone' => '081234567892',
                'department' => 'Field Engineering',
                'is_active' => true,
            ]
        );

        // 2. Seed Sample Project
        $projek = Projek::firstOrCreate(
            ['code' => 'PRJ-2026-001'],
            [
                'name' => 'Implementasi Jaringan Fiber Optik Surabaya',
                'description' => 'Pemasangan dan integrasi infrastruktur fiber optik dan GPS monitoring.',
                'client_name' => 'PT Telekomunikasi Nusantara',
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->addMonths(3)->toDateString(),
                'status' => 'aktif',
            ]
        );

        // 3. Seed Attendance Point for the Project
        $point = AttendancePoint::firstOrCreate(
            ['name' => 'Head Office & Data Center Surabaya'],
            [
                'projek_id' => $projek->id,
                'latitude' => -7.2574719,
                'longitude' => 112.7520883,
                'radius_meters' => 150,
                'address' => 'Jl. Pemuda No. 45, Genteng, Surabaya, Jawa Timur',
                'is_active' => true,
            ]
        );

        // 4. Assign Karyawan and Manager to Project
        ProjectAssignment::firstOrCreate(
            ['projek_id' => $projek->id, 'user_id' => $karyawan->id],
            ['role_in_project' => 'member', 'assigned_at' => now()->startOfMonth()->toDateString()]
        );

        ProjectAssignment::firstOrCreate(
            ['projek_id' => $projek->id, 'user_id' => $manager->id],
            ['role_in_project' => 'leader', 'assigned_at' => now()->startOfMonth()->toDateString()]
        );

        // 5. Seed Attendance for Today
        Attendance::firstOrCreate(
            ['user_id' => $karyawan->id, 'date' => now()->toDateString()],
            [
                'attendance_point_id' => $point->id,
                'check_in_time' => '07:55:00',
                'check_in_latitude' => -7.2574719,
                'check_in_longitude' => 112.7520883,
                'status' => 'hadir',
                'notes' => 'Presensi hadir tepat waktu di lokasi kantor',
            ]
        );

        // 6. Seed Sample Logbook
        Logbook::firstOrCreate(
            ['user_id' => $karyawan->id, 'date' => now()->toDateString()],
            [
                'projek_id' => $projek->id,
                'title' => 'Inspeksi & Terminasi Kabel Optical Distribution Cabinet',
                'description' => 'Melakukan inspeksi kelayakan kabel ODC cluster timur dan pengujian redaman OTDR.',
                'progress_percentage' => 45,
                'status' => 'submitted',
            ]
        );
    }
}
