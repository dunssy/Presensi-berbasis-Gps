<?php

namespace Tests\Feature;

use App\Models\Projek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_project(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.projects.store'), [
            'name' => 'Projek Infrastruktur Fiber',
            'code' => 'PRJ-FIBER-01',
            'description' => 'Instalasi jaringan',
            'client_name' => 'PT Telekomunikasi',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(2)->toDateString(),
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseHas('projeks', [
            'code' => 'PRJ-FIBER-01',
            'name' => 'Projek Infrastruktur Fiber',
        ]);
    }

    public function test_admin_can_create_attendance_point(): void
    {
        $admin = User::factory()->admin()->create();
        $projek = Projek::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.attendance-points.store'), [
            'projek_id' => $projek->id,
            'name' => 'Site Tower Alpha',
            'latitude' => -7.2574719,
            'longitude' => 112.7520883,
            'radius_meters' => 100,
            'address' => 'Jl. Pahlawan Surabaya',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.attendance-points.index'));
        $this->assertDatabaseHas('attendance_points', [
            'name' => 'Site Tower Alpha',
            'radius_meters' => 100,
        ]);
    }
}
