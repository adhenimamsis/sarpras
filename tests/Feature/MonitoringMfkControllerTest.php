<?php

namespace Tests\Feature;

use App\Models\MonitoringMfk;
use App\Models\User;
use App\Models\UtilityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringMfkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_verified_user_can_open_monitoring_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        UtilityLog::create([
            'tgl_catat' => now()->toDateString(),
            'jenis' => 'listrik',
            'nama_meteran' => 'Panel Utama',
            'angka_meteran' => 1200,
            'petugas' => 'Admin SimSarpras',
        ]);

        $response = $this->actingAs($user)->get('/monitoring/mfk/listrik');

        $response->assertOk();
    }

    public function test_authenticated_verified_user_can_export_utility_csv(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        UtilityLog::create([
            'tgl_catat' => now()->toDateString(),
            'jenis' => 'air',
            'nama_meteran' => 'Tandon A',
            'angka_meteran' => 45.5,
            'petugas' => 'Petugas Uji',
            'catatan' => 'Normal',
        ]);

        $response = $this->actingAs($user)->get('/monitoring/mfk/export-excel');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('Tanggal Catat', $response->streamedContent());
    }

    public function test_authenticated_verified_user_can_print_single_monitoring_report(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $record = MonitoringMfk::create([
            'jenis_utilitas' => 'Listrik',
            'tgl_cek' => now()->toDateString(),
            'waktu_cek' => now()->format('H:i:s'),
            'status' => 'Normal',
            'petugas' => 'Admin SimSarpras',
            'parameter_cek' => 'Panel utama dalam kondisi baik.',
        ]);

        $response = $this->actingAs($user)->get("/monitoring/mfk/print/{$record->id}");

        $response->assertOk();
    }
}
