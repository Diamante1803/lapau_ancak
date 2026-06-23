<?php

use App\Http\Controllers\AdminSatkerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PerkaraController;
use App\Http\Controllers\LelangController;
use App\Http\Controllers\LaporanController;

Route::middleware(['auth', 'role:admin_satker'])
    ->prefix('satker')
    ->name('satker.')
    ->group(function () {

    // ================================
    // DASHBOARD
    // ================================
    Route::get('/dashboard', [AdminSatkerController::class, 'dashboard'])
        ->name('dashboard');

    // ================================
    // PENGAJUAN — LIST & DELETE
    // (dipertahankan dari sebelumnya)
    // ================================
    Route::get('pengajuan', [AdminSatkerController::class, 'index'])
        ->name('pengajuan.index');
    Route::delete('pengajuan/{pengajuan}', [AdminSatkerController::class, 'destroy'])
        ->name('pengajuan.destroy');
    Route::post('pengajuan/{pengajuan}/submit', [AdminSatkerController::class, 'submit'])
        ->name('pengajuan.submit');

    // ================================
    // WIZARD — CREATE BARU
    // POST /satker/pengajuan → store()
    // lalu redirect ke step 1
    // ================================
    Route::get('pengajuan/create', [AdminSatkerController::class, 'create'])
        ->name('pengajuan.create');
    Route::post('pengajuan', [AdminSatkerController::class, 'store'])
        ->name('pengajuan.store');

    // ================================
    // WIZARD — STEP PER STEP
    // ================================

    // Step 1 — Info Pengajuan & Dokumen
    Route::get('pengajuan/{pengajuan}/step/1', [AdminSatkerController::class, 'step1'])
        ->name('pengajuan.step1');
    Route::post('pengajuan/{pengajuan}/step/1', [AdminSatkerController::class, 'saveStep1'])
        ->name('pengajuan.saveStep1');

    // Upload & hapus dokumen pengajuan (dipanggil dari step 1)
    Route::post('pengajuan/{pengajuan}/dokumen', [AdminSatkerController::class, 'uploadDokumenPengajuan'])
        ->name('pengajuan.uploadDokumen');
    Route::delete('dokumen/{dokumen}', [AdminSatkerController::class, 'destroyDokumenPengajuan'])
        ->name('dokumen.destroy');

    // Step 2 — Perkara & Dokumen Perkara
    Route::get('pengajuan/{pengajuan}/step/2', [AdminSatkerController::class, 'step2'])
        ->name('pengajuan.step2');
    Route::post('pengajuan/{pengajuan}/step/2', [AdminSatkerController::class, 'saveStep2'])
        ->name('pengajuan.saveStep2');

    // CRUD Perkara (dipanggil dari step 2)
    Route::post('pengajuan/{pengajuan}/perkara', [PerkaraController::class, 'storePerkara'])
        ->name('pengajuan.perkara.store');
    Route::put('perkara/{perkara}', [PerkaraController::class, 'updatePerkara'])
        ->name('pengajuan.perkara.update');
    Route::delete('perkara/{perkara}', [PerkaraController::class, 'destroyPerkara'])
        ->name('pengajuan.perkara.destroy');

    // Upload & hapus dokumen perkara (dipanggil dari step 2)
    Route::post('perkara/{perkara}/dokumen', [PerkaraController::class, 'uploadDokumenPerkara'])
        ->name('pengajuan.perkara.uploadDokumen');
    Route::delete('perkara/dokumen/{id}', [PerkaraController::class, 'destroyDokumenPerkara'])
        ->name('pengajuan.perkara.dokumen.destroy');

    // Step 3 — Barang & Foto
    Route::get('pengajuan/{pengajuan}/step/3', [AdminSatkerController::class, 'step3'])
        ->name('pengajuan.step3');

    // CRUD Barang (dipanggil dari step 3)
    Route::post('perkara/{perkara}/barang', [BarangController::class, 'storeBarang'])
        ->name('perkara.barang.store');
    Route::resource('barang', BarangController::class)->only(['update', 'destroy']);
    Route::post('barang/{barang}/foto', [BarangController::class, 'uploadFotoBarang'])
        ->name('barang.uploadFoto');
    Route::delete('barang/foto/{id}', [BarangController::class, 'destroyFoto'])
        ->name('barang.foto.destroy');

    // Step 4 — Review & Submit
    Route::get('pengajuan/{pengajuan}/step/4', [AdminSatkerController::class, 'step4'])
        ->name('pengajuan.step4');

    // ================================
    // LELANG SATKER
    // (dipertahankan dari sebelumnya)
    // ================================
    Route::get('lelang/aktif', [LelangController::class, 'aktifSatker'])
        ->name('lelang.aktif');
    Route::get('lelang/selesai', [LelangController::class, 'selesaiSatker'])
        ->name('lelang.selesai');
    Route::post('lelang/{lelang}/ganti-pemenang', [LelangController::class, 'gantiPemenang'])
        ->name('lelang.ganti-pemenang');
    Route::get('lelang/{lelang}/detail', [LelangController::class, 'detail'])
        ->name('lelang.detail');
    Route::post('lelang/{lelang}/ulang', [LelangController::class, 'ajukanLelangUlang'])
        ->name('lelang.ulang');

    // ================================
    // LAPORAN
    // (dipertahankan dari sebelumnya)
    // ================================
    Route::get('laporan', [LaporanController::class, 'satker'])
        ->name('laporan.index');
    Route::post('laporan/{lelang}/upload', [LaporanController::class, 'uploadLaporan'])
        ->name('laporan.upload');

});
