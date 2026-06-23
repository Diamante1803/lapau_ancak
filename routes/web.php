<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminPusatController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PenawaranController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';
require __DIR__.'/admin/satker.php';
require __DIR__.'/admin/pusat.php';
require __DIR__.'/pembeli.php';

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// routes/web.php — public, tidak perlu auth
Route::get('/', [PublicController::class, 'index'])->name('public.index');
Route::get('/lelang/{lelang}', [PublicController::class, 'detail'])->name('public.detail');
Route::get('/satker/{satker}', [PublicController::class, 'satker'])->name('public.satker');

// routes/web.php
Route::get('/pembeli/cek-token', [PenawaranController::class, 'cekToken']);
Route::post('/lelang/{lelang}/magic-link', [PenawaranController::class, 'requestMagicLink'])
    ->name('public.magic-link');

Route::get('/verify', [PenawaranController::class, 'verifyMagicLink'])
    ->name('public.verify');

Route::post('/lelang/{lelang}/bid', [PenawaranController::class, 'submitPenawaran'])
    ->name('public.bid');

Route::get('/lelang/{lelang}/polling', [PenawaranController::class, 'pollingPenawaran']);
