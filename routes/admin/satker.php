<?php

use App\Http\Controllers\AdminSatkerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PerkaraController;
use App\Http\Controllers\LelangController;
use App\Http\Controllers\LaporanController;
use App\Models\Barang;

    Route::middleware(['auth', 'role:admin_satker'])
    ->prefix('satker')
    ->name('satker.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminSatkerController::class, 'dashboard'])
        ->name('dashboard');

    // Pengajuan
    Route::resource('pengajuan', AdminSatkerController::class);
    Route::post('pengajuan/{pengajuan}/submit', [AdminSatkerController::class, 'submit'])
        ->name('pengajuan.submit');
    Route::post('pengajuan/{pengajuan}/dokumen', [AdminSatkerController::class, 'uploadDokumenPengajuan'])
        ->name('pengajuan.uploadDokumen');
    Route::delete('dokumen/{dokumen}', [AdminSatkerController::class, 'destroyDokumenPengajuan'])
        ->name('dokumen.destroy');

    // Perkara
    Route::post('pengajuan/{pengajuan}/perkara', [PerkaraController::class, 'storePerkara'])
        ->name('pengajuan.perkara.store');
    Route::put('perkara/{perkara}', [PerkaraController::class, 'updatePerkara'])
        ->name('pengajuan.perkara.update');
    Route::delete('perkara/{perkara}', [AdminSatkerController::class, 'destroyPerkara'])
        ->name('pengajuan.perkara.destroy');

    // Dokumen Perkara
    Route::post('perkara/{perkara}/dokumen', [AdminSatkerController::class, 'uploadDokumenPerkara'])
        ->name('pengajuan.perkara.uploadDokumen');
    Route::delete('perkara/dokumen/{id}', [AdminSatkerController::class, 'destroyDokumenPerkara'])
        ->name('pengajuan.perkara.dokumen.destroy');

    // Barang
    Route::post('perkara/{perkara}/barang', [BarangController::class, 'storeBarang'])
        ->name('perkara.barang.store');
    Route::resource('barang', BarangController::class);
    Route::post('barang/{barang}/foto', [BarangController::class, 'uploadFotoBarang'])
        ->name('barang.uploadFoto');
    Route::delete('barang/foto/{id}', [BarangController::class, 'destroyFoto'])
        ->name('barang.foto.destroy');

    // routes/admin/satker.php — dalam group satker
    Route::get('lelang/aktif', [LelangController::class, 'aktifSatker'])
        ->name('lelang.aktif');
    Route::get('lelang/selesai', [LelangController::class, 'selesaiSatker'])
        ->name('lelang.selesai');
    Route::post('lelang/{lelang}/ganti-pemenang', [LelangController::class, 'gantiPemenang'])
        ->name('lelang.ganti-pemenang');
    Route::get('lelang/{lelang}/detail', [LelangController::class, 'detail'])
        ->name('lelang.detail');

    Route::get('laporan', [LaporanController::class, 'satker'])
        ->name('laporan.index'); 
    // routes/web.php
    Route::post('laporan/{lelang}/upload', [LaporanController::class, 'uploadLaporan'])
        ->name('laporan.upload');


});