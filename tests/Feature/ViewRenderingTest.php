<?php

namespace Tests\Feature;

use App\Models\AttendancePoint;
use App\Models\Logbook;
use App\Models\Projek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_views_render_successfully(): void
    {
        $admin = User::factory()->admin()->create();
        $projek = Projek::factory()->create();
        AttendancePoint::factory()->create(['projek_id' => $projek->id]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.projects.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.attendance-points.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.employees.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.assignments.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.reports.attendance'))->assertOk();
        $this->actingAs($admin)->get(route('admin.reports.logbook'))->assertOk();
    }

    public function test_manager_views_render_successfully(): void
    {
        $manager = User::factory()->manager()->create();
        $projek = Projek::factory()->create();
        Logbook::factory()->create(['projek_id' => $projek->id]);

        $this->actingAs($manager)->get(route('manager.dashboard'))->assertOk();
        $this->actingAs($manager)->get(route('manager.projects.index'))->assertOk();
        $this->actingAs($manager)->get(route('manager.projects.show', $projek))->assertOk();
        $this->actingAs($manager)->get(route('manager.logbooks.index'))->assertOk();
    }

    public function test_karyawan_views_render_successfully(): void
    {
        $karyawan = User::factory()->karyawan()->create();
        Logbook::factory()->create(['user_id' => $karyawan->id]);

        $this->actingAs($karyawan)->get(route('karyawan.dashboard'))->assertOk();
        $this->actingAs($karyawan)->get(route('karyawan.logbooks.index'))->assertOk();
    }
}
