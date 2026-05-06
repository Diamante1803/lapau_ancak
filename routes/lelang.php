<?php

use App\Http\Controllers\LelangController;
use Illuminate\Support\Facades\Route;

Route::prefix('lelang')->group(function () {

    Route::get('/', function () {
        return view('lelang/list');
    });

    Route::get('/{id}', [LelangController::class, 'show']);

    // Admin only (pakai policy di controller)
    Route::post('/{id}/aktifkan', [LelangController::class, 'aktifkan'])
        ->middleware('auth');

    Route::post('/{id}/tutup', [LelangController::class, 'tutup'])
        ->middleware('auth');
});