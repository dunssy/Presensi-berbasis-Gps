<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('PT Technodrives');
    }

    public function test_admin_can_authenticate_and_redirect_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@technodrives.com',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@technodrives.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_manager_can_authenticate_and_redirect_to_manager_dashboard(): void
    {
        $manager = User::factory()->manager()->create([
            'email' => 'manager@technodrives.com',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'manager@technodrives.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($manager);
        $response->assertRedirect(route('manager.dashboard'));
    }

    public function test_karyawan_can_authenticate_and_redirect_to_karyawan_dashboard(): void
    {
        $karyawan = User::factory()->karyawan()->create([
            'email' => 'karyawan@technodrives.com',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'karyawan@technodrives.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($karyawan);
        $response->assertRedirect(route('karyawan.dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@technodrives.com',
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'email' => 'user@technodrives.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'inactive@technodrives.com',
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'email' => 'inactive@technodrives.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
    }

    public function test_user_can_logout_and_session_is_invalidated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login'));
    }
}
