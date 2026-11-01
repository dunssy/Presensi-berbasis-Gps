<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_karyawan_cannot_access_admin_dashboard(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        $response = $this->actingAs($karyawan)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_manager_cannot_access_admin_dashboard(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_manager_can_access_manager_dashboard(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->get('/manager');

        $response->assertStatus(200);
    }

    public function test_karyawan_cannot_access_manager_dashboard(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        $response = $this->actingAs($karyawan)->get('/manager');

        $response->assertStatus(403);
    }

    public function test_karyawan_can_access_karyawan_dashboard(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        $response = $this->actingAs($karyawan)->get('/karyawan');

        $response->assertStatus(200);
    }
}
