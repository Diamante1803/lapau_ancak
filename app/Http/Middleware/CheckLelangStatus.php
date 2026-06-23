<?php
// app/Http/Middleware/CheckLelangStatus.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Lelang;
use App\Events\LelangStatusUpdate;
use Carbon\Carbon;

class CheckLelangStatus
{
    public function handle(Request $request, Closure $next)
    {
        $now = Carbon::now();

        // Update scheduled → active
        Lelang::where('status', 'scheduled')
            ->where('tanggal_mulai', '<=', $now)
            ->get()
            ->each(function ($lelang) {
                $lelang->update(['status' => 'active']);
                $lelang->barang->update(['status' => 'in_auction']);
                broadcast(new LelangStatusUpdate($lelang->id, 'active'));
            });

        // Update active → closed
        Lelang::where('status', 'active')
    ->where('tanggal_selesai', '<=', $now)
    ->get()
    ->each(function ($lelang) {
        try {
            $pemenang = $lelang->penawaranTertinggi();
            $lelang->update([
                'status'          => 'closed',
                'harga_tertinggi' => $pemenang?->nilai_penawaran ?? $lelang->harga_awal,
                'pemenang_id'     => $pemenang?->pembeli_id ?? null,
            ]);
            $lelang->barang->update([
                'status' => $pemenang ? 'sold' : 'unsold'
            ]);
            broadcast(new LelangStatusUpdate($lelang->id, 'closed'));
        } catch (\Exception $e) {
            \Log::error('CheckLelangStatus error: ' . $e->getMessage());
        }
    });

        return $next($request);
    }
}
