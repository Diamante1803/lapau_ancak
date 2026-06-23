<?php
// app/Console/Commands/UpdateStatusLelang.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lelang;
use App\Events\LelangStatusUpdate;
use Carbon\Carbon;

class UpdateStatusLelang extends Command
{
    protected $signature   = 'lelang:update-status';
    protected $description = 'Update status lelang berdasarkan waktu (scheduled→active→closed)';

    public function handle()
    {
        $now = Carbon::now();

        // ✅ scheduled → active (waktu mulai sudah lewat)
        $diaktifkan = Lelang::where('status', 'scheduled')
            ->where('tanggal_mulai', '<=', $now)
            ->get();

        foreach ($diaktifkan as $lelang) {
            $lelang->update(['status' => 'active']);
            $lelang->barang->update(['status' => 'in_auction']);
            broadcast(new LelangStatusUpdate($lelang->id, 'active'));
            $this->info('Aktif: Lelang ID ' . $lelang->id . ' - ' . $lelang->barang->nama_barang);
        }

        // ✅ active → closed (waktu selesai sudah lewat)
        $diselesaikan = Lelang::where('status', 'active')
            ->where('tanggal_selesai', '<=', $now)
            ->get();

        foreach ($diselesaikan as $lelang) {
            // Tentukan pemenang dari penawaran tertinggi
            $pemenang = $lelang->penawaranTertinggi();

            $lelang->update([
                'status'          => 'closed',
                'harga_tertinggi' => $pemenang?->nilai_penawaran ?? $lelang->harga_awal,
                'pemenang_id'     => $pemenang?->pembeli_id ?? null,
            ]);

            // Update status barang
            if ($pemenang) {
                $lelang->barang->update(['status' => 'sold']);
            } else {
                $lelang->barang->update(['status' => 'unsold']);
            }

            broadcast(new LelangStatusUpdate($lelang->id, 'closed'));
            $this->info('Selesai: Lelang ID ' . $lelang->id . ' - ' . $lelang->barang->nama_barang);
        }

        $this->info('Selesai. Aktif: ' . $diaktifkan->count() . ', Closed: ' . $diselesaikan->count());
        return Command::SUCCESS;
    }
}
