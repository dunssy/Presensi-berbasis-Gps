<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\Projek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogbookTest extends TestCase
{
    use RefreshDatabase;

    public function test_karyawan_can_submit_logbook(): void
    {
        $karyawan = User::factory()->karyawan()->create();
        $projek = Projek::factory()->create();

        $response = $this->actingAs($karyawan)->post(route('karyawan.logbooks.store'), [
            'projek_id' => $projek->id,
            'date' => now()->toDateString(),
            'title' => 'Instalasi Perangkat Jaringan',
            'description' => 'Konfigurasi switch dan router di site Surabaya',
            'progress_percentage' => 75,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('logbooks', [
            'user_id' => $karyawan->id,
            'projek_id' => $projek->id,
            'title' => 'Instalasi Perangkat Jaringan',
            'status' => 'submitted',
        ]);

        $this->assertDatabaseHas('logbook_histories', [
            'user_id' => $karyawan->id,
            'action' => 'submitted',
        ]);
    }

    public function test_manager_can_approve_logbook(): void
    {
        $manager = User::factory()->manager()->create();
        $logbook = Logbook::factory()->create(['status' => 'submitted']);

        $response = $this->actingAs($manager)->post(route('manager.logbooks.approve', $logbook), [
            'review_notes' => 'Pekerjaan sangat baik.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('logbooks', [
            'id' => $logbook->id,
            'status' => 'approved',
            'reviewed_by' => $manager->id,
        ]);
    }

    public function test_manager_can_reject_logbook_with_notes(): void
    {
        $manager = User::factory()->manager()->create();
        $logbook = Logbook::factory()->create(['status' => 'submitted']);

        $response = $this->actingAs($manager)->post(route('manager.logbooks.reject', $logbook), [
            'review_notes' => 'Perbaiki lampiran laporan dan rincian durasi kerja.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('logbooks', [
            'id' => $logbook->id,
            'status' => 'rejected',
            'review_notes' => 'Perbaiki lampiran laporan dan rincian durasi kerja.',
            'reviewed_by' => $manager->id,
        ]);
    }
}
