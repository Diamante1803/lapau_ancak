<?php

use App\Http\Controllers\AdminPusatController;
use App\Http\Controllers\SatkerController;
use App\Http\Controllers\LelangController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

// Akses BERSAMA (admin_pusat + admin_satker)
Route::middleware(['auth', 'role:admin_pusat,admin_satker'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminPusatController::class, 'dashboard'])
            ->name('dashboard');
        Route::get('lelang/{lelang}/tabel-penawaran', [LelangController::class, 'tabelPenawaran'])
            ->name('lelang.tabel-penawaran');

    });

// Akses KHUSUS admin_pusat saja
Route::middleware(['auth', 'role:admin_pusat'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('pengajuan', AdminPusatController::class)
            ->only(['index', 'show', 'destroy']);
        Route::post('/pengajuan/{pengajuan}/approve', [AdminPusatController::class, 'approve'])
            ->name('pengajuan.approve');
        Route::post('/pengajuan/{pengajuan}/revisi', [AdminPusatController::class, 'revisi'])
            ->name('pengajuan.revisi');

        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])
            ->name('users.reset-password');

        Route::get('lelang', [LelangController::class, 'dashboard'])->name('lelang.dashboard');
        Route::post('lelang/{pengajuan}/jadwalkan', [LelangController::class, 'jadwalkan'])->name('lelang.jadwalkan');
        Route::post('lelang/{pengajuan}/batal', [LelangController::class, 'batal'])->name('lelang.batal');
        Route::get('/lelang/aktif', [LelangController::class, 'aktif'])->name('lelang.aktif');
        Route::post('/lelang/{lelang}/tutup', [LelangController::class, 'tutup'])->name('lelang.tutup');
        Route::get('/lelang/{lelang}/detail', [LelangController::class, 'detail'])->name('lelang.detail');
        Route::get('lelang/selesai', [LelangController::class, 'selesai'])->name('lelang.selesai');
        Route::post('/lelang/{lelang}/batal-aktif', [LelangController::class, 'batalAktif'])->name('lelang.batalAktif');
        Route::post('satker', [SatkerController::class, 'store'])->name('satker.store');
        Route::put('satker/{satker}', [SatkerController::class, 'update'])->name('satker.update');
        Route::delete('satker/{satker}', [SatkerController::class, 'destroy'])->name('satker.destroy');
        Route::get('satker', [SatkerController::class, 'index'])->name('satker.index');

        Route::get('laporan', [LaporanController::class, 'pusat'])
        ->name('laporan.index');    

        Route::get('aktivitas', [AuditLogController::class, 'index'])
            ->name('aktivitas.index');
        
        Route::delete('/lelang/{lelang}/hapus-penawaran-tertinggi', 
            [LelangController::class, 'hapusPenawaranTertinggi'])
            ->name('lelang.hapusPenawaranTertinggi');

    });
