<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Satker;
use App\Models\PengajuanLelang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function satker_admin_cannot_delete_other_satker_pengajuan()
    {
        // Setup dua satker berbeda
        $satkerA = Satker::create(['nama_satker' => 'Satker A']);
        $satkerB = Satker::create(['nama_satker' => 'Satker B']);

        $adminA = User::factory()->create(['role' => 'admin_satker', 'satker_id' => $satkerA->id]);
        
        // Pengajuan milik Satker B
        $pengajuanB = PengajuanLelang::create([
            'satker_id' => $satkerB->id,
            'judul_pengajuan' => 'Data Rahasia B',
            'status' => 'draft'
        ]);

        // Admin A mencoba menghapus data milik Satker B
        $response = $this->actingAs($adminA)
                         ->delete(route('satker.pengajuan.destroy', $pengajuanB->id));

        // Harusnya dilarang (403 Forbidden)
        $response->assertStatus(403);
        $this->assertDatabaseHas('pengajuan_lelangs', ['id' => $pengajuanB->id]);
    }

    /** @test */
    public function unauthorized_role_cannot_access_pusat_dashboard()
    {
        $satker = Satker::create(['nama_satker' => 'Satker X']);
        $user = User::factory()->create(['role' => 'admin_satker', 'satker_id' => $satker->id]);

        // Admin Satker mencoba akses manajemen user (hanya untuk pusat)
        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_submit_bid_without_session()
    {
        $lelang = \App\Models\Lelang::factory()->create(['status' => 'active']);

        $response = $this->postJson("/lelang/{$lelang->id}/bid", [
            'nilai_penawaran' => 1000000
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function bid_amount_must_be_higher_than_current_highest()
    {
        // Simulasi race condition atau bypass validasi frontend
        $lelang = \App\Models\Lelang::factory()->create([
            'status' => 'active',
            'harga_awal' => 500000,
            'harga_tertinggi' => 600000
        ]);

        session(['verified_pembeli_id' => 1, 'verified_expired' => now()->addHour()]);

        $response = $this->postJson("/lelang/{$lelang->id}/bid", ['nilai_penawaran' => 550000]);
        $response->assertStatus(422); // Harus gagal karena bid lebih rendah
    }

    /** @test */
    public function admin_pusat_can_access_shared_dashboard()
    {
        $adminPusat = User::factory()->create(['role' => 'admin_pusat']);

        $response = $this->actingAs($adminPusat)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertSee('Admin Pusat');
    }

    /** @test */
    public function admin_satker_can_access_shared_dashboard_with_filtered_data()
    {
        $satker = Satker::create(['nama_satker' => 'Satker Wilayah A']);
        $adminSatker = User::factory()->create(['role' => 'admin_satker', 'satker_id' => $satker->id]);

        $response = $this->actingAs($adminSatker)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertSee('Satker Wilayah A');
    }
}