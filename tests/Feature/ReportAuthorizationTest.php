<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UtilityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_sensitive_kib_report(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->actingAs($staff)
            ->get('/cetak-kib/KIB_B')
            ->assertForbidden();
    }

    public function test_teknisi_cannot_access_sensitive_kib_report(): void
    {
        $teknisi = User::factory()->create([
            'role' => 'teknisi',
            'is_active' => true,
        ]);

        $this->actingAs($teknisi)
            ->get('/cetak-kib/KIB_B')
            ->assertForbidden();
    }

    public function test_kapus_can_access_sensitive_kib_report(): void
    {
        $kapus = User::factory()->create([
            'role' => 'kapus',
            'is_active' => true,
        ]);

        $response = $this->actingAs($kapus)->get('/cetak-kib/KIB_B');

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_teknisi_can_view_operational_monitoring_report(): void
    {
        $teknisi = User::factory()->create([
            'role' => 'teknisi',
            'is_active' => true,
        ]);

        $this->actingAs($teknisi)
            ->get('/monitoring/mfk/rekap')
            ->assertOk();
    }

    public function test_teknisi_cannot_export_operational_monitoring_report(): void
    {
        $teknisi = User::factory()->create([
            'role' => 'teknisi',
            'is_active' => true,
        ]);

        $this->actingAs($teknisi)
            ->get('/monitoring/mfk/export-excel')
            ->assertForbidden();
    }

    public function test_staff_can_export_operational_monitoring_report(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        UtilityLog::create([
            'tgl_catat' => now()->toDateString(),
            'jenis' => 'listrik',
            'nama_meteran' => 'Panel Utama',
            'angka_meteran' => 1000,
            'petugas' => 'Staff',
        ]);

        $response = $this->actingAs($staff)->get('/monitoring/mfk/export-excel');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }

    public function test_staff_filament_report_page_hides_sensitive_buttons(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->get('/admin/laporan-sarpras');

        $response->assertOk();
        $response->assertDontSee('Cetak KIB B (Peralatan)');
        $response->assertSee('Akses Laporan Sensitif Dibatasi');
    }

    public function test_kapus_filament_report_page_shows_sensitive_buttons(): void
    {
        $kapus = User::factory()->create([
            'role' => 'kapus',
            'is_active' => true,
        ]);

        $response = $this->actingAs($kapus)->get('/admin/laporan-sarpras');

        $response->assertOk();
        $response->assertSee('Cetak KIB B (Peralatan)');
    }
}
