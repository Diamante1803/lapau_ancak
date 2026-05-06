<?php

use App\Http\Controllers\PenawaranController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

// Bid
Route::post('/bid/{lelang}', [PenawaranController::class, 'requestBid']);

// Magic link
Route::get('/verify/{pembeli}', [VerifikasiController::class, 'verify'])
    ->name('verifikasi.magic')
    ->middleware('signed');

// Landing setelah verifikasi
Route::get('/berhasil-verifikasi', function () {
    return view('public.success');
});