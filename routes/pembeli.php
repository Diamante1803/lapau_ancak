<?php

use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

// Magic link
Route::get('/verify/{pembeli}', [VerifikasiController::class, 'verify'])
    ->name('verifikasi.magic')
    ->middleware('signed');

// Landing setelah verifikasi
Route::get('/berhasil-verifikasi', function () {
    return view('public.success');
});
