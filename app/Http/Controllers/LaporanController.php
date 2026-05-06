<?php

namespace App\Http\Controllers;

use App\Models\Lelang;
use App\Models\LaporanLelang;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // ─── ADMIN PUSAT ─────────────────────────────────────────────────────────
    public function pusat(Request $request)
    {
        $query = Lelang::with([
            'barang.perkara.pengajuan.satker',
            'barang.fotoBarang',
            'pemenang',
            'laporan',
        ])->where('status', 'closed');

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_selesai', [
                $request->dari . ' 00:00:00',
                $request->sampai . ' 23:59:59',
            ]);
        }

        if ($request->filled('satker_id')) {
            $query->whereHas('barang.perkara.pengajuan', function ($q) use ($request) {
                $q->where('satker_id', $request->satker_id);
            });
        }

        $lelangs      = $query->orderByDesc('tanggal_selesai')->get();
        $satkers      = \App\Models\Satker::orderBy('nama_satker')->get();
        $totalNilai   = $lelangs->sum('harga_tertinggi');
        $totalTerjual = $lelangs->whereNotNull('pemenang_id')->count();
        $sudahBAST    = $lelangs->filter(fn($l) => $l->laporan?->file_bast)->count();
        $belumBAST    = $lelangs->count() - $sudahBAST;
        $isPusat      = true;

        return view('admin.laporan.index', compact(
            'lelangs', 'satkers', 'totalNilai',
            'totalTerjual', 'sudahBAST', 'belumBAST', 'isPusat'
        ));
    }

    // ─── ADMIN SATKER ─────────────────────────────────────────────────────────
    public function satker(Request $request)
    {
        $user     = auth()->user();
        $satkerId = $user->satker_id;

        $query = Lelang::with([
            'barang.perkara.pengajuan.satker',
            'barang.fotoBarang',
            'pemenang',
            'laporan',
        ])
        ->where('status', 'closed')
        ->whereHas('barang.perkara.pengajuan', function ($q) use ($satkerId) {
            $q->where('satker_id', $satkerId);
        });

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_selesai', [
                $request->dari . ' 00:00:00',
                $request->sampai . ' 23:59:59',
            ]);
        }

        $lelangs      = $query->orderByDesc('tanggal_selesai')->get();
        $totalNilai   = $lelangs->sum('harga_tertinggi');
        $totalTerjual = $lelangs->whereNotNull('pemenang_id')->count();
        $sudahBAST    = $lelangs->filter(fn($l) => $l->laporan?->file_bast)->count();
        $belumBAST    = $lelangs->count() - $sudahBAST;
        $isPusat      = false;

        return view('admin.laporan.index', compact(
            'lelangs', 'totalNilai', 'totalTerjual',
            'sudahBAST', 'belumBAST', 'isPusat'
        ));
    }

    // ─── UPLOAD / UPDATE LAPORAN (satker) ────────────────────────────────────
    public function uploadLaporan(Request $request, Lelang $lelang)
    {
        $request->validate([
            'file_bast'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_bukti_bayar'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'nomor_bast'      => 'nullable|string|max:100',
            'nomor_billing'   => 'nullable|string|max:100',
            'tanggal_bast'    => 'nullable|date',
            'tanggal_bayar'   => 'nullable|date',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        if ($lelang->barang->perkara->pengajuan->satker_id !== $user->satker_id) {
            abort(403, 'Tidak punya akses ke lelang ini.');
        }

        $laporan = LaporanLelang::firstOrNew(['lelang_id' => $lelang->id]);
        $laporan->satker_id    = $user->satker_id;
        $laporan->nomor_bast   = $request->nomor_bast   ?? $laporan->nomor_bast;
        $laporan->nomor_billing= $request->nomor_billing ?? $laporan->nomor_billing;
        $laporan->tanggal_bast = $request->tanggal_bast  ?? $laporan->tanggal_bast;
        $laporan->tanggal_bayar= $request->tanggal_bayar ?? $laporan->tanggal_bayar;
        $laporan->catatan      = $request->catatan        ?? $laporan->catatan;

        if ($request->hasFile('file_bast')) {
            $laporan->file_bast = $request->file('file_bast')->store('laporan/bast', 'public');
        }
        if ($request->hasFile('file_bukti_bayar')) {
            $laporan->file_bukti_bayar = $request->file('file_bukti_bayar')->store('laporan/billing', 'public');
        }

        // Auto-set status
        $laporan->status = $laporan->isLengkap() ? 'lengkap' : 'sebagian';
        $laporan->save();

        return back()->with('success', 'Laporan berhasil disimpan.');
    }
}