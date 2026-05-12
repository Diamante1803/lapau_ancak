<?php

namespace App\Http\Controllers;

use App\Models\Lelang;
use App\Models\LaporanLelang;
use App\Models\Satker;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // ─── HELPER: group lelangs per satker ────────────────────────────────────
    private function groupBySatker($lelangs)
    {
        return $lelangs->groupBy(fn($l) => $l->barang->perkara->pengajuan->satker->nama_satker)
            ->map(function ($items, $satkerNama) {
                $totalLimit   = $items->sum('harga_awal');
                $totalTerjual = $items->sum('harga_tertinggi');
                $kenaikan     = $totalLimit > 0
                    ? round((($totalTerjual - $totalLimit) / $totalLimit) * 100, 2)
                    : 0;
                return [
                    'nama_satker'   => $satkerNama,
                    'items'         => $items,
                    'total_limit'   => $totalLimit,
                    'total_terjual' => $totalTerjual,
                    'kenaikan'      => $kenaikan,
                    'sudah_bast'    => $items->filter(fn($l) => $l->laporan?->file_bast)->count(),
                    'belum_bast'    => $items->filter(fn($l) => !$l->laporan?->file_bast)->count(),
                ];
            });
    }

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

        $lelangs        = $query->orderByDesc('tanggal_selesai')->get();
        $satkers        = Satker::orderBy('nama_satker')->get();
        $grouped        = $this->groupBySatker($lelangs);

        $totalNilaiLimit   = $lelangs->sum('harga_awal');
        $totalNilai        = $lelangs->sum('harga_tertinggi');
        $totalTerjual      = $lelangs->whereNotNull('pemenang_id')->count();
        $sudahBAST         = $lelangs->filter(fn($l) => $l->laporan?->file_bast)->count();
        $belumBAST         = $lelangs->count() - $sudahBAST;
        $kenaikanGlobal    = $totalNilaiLimit > 0
            ? round((($totalNilai - $totalNilaiLimit) / $totalNilaiLimit) * 100, 2)
            : 0;
        $isPusat           = true;
        $totalNilaiBilling = $lelangs
            ->filter(fn($l) => $l->laporan?->file_bukti_bayar)
            ->sum('harga_tertinggi');

        return view('admin.laporan.index', compact(
            'lelangs', 'grouped', 'satkers', 'totalNilaiBilling',
            'totalNilai', 'totalNilaiLimit', 'totalTerjual',
            'sudahBAST', 'belumBAST', 'kenaikanGlobal', 'isPusat'
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

        $lelangs        = $query->orderByDesc('tanggal_selesai')->get();
        $grouped        = $this->groupBySatker($lelangs);

        $totalNilaiLimit   = $lelangs->sum('harga_awal');
        $totalNilai        = $lelangs->sum('harga_tertinggi');
        $totalTerjual      = $lelangs->whereNotNull('pemenang_id')->count();
        $sudahBAST         = $lelangs->filter(fn($l) => $l->laporan?->file_bast)->count();
        $belumBAST         = $lelangs->count() - $sudahBAST;
        $kenaikanGlobal    = $totalNilaiLimit > 0
            ? round((($totalNilai - $totalNilaiLimit) / $totalNilaiLimit) * 100, 2)
            : 0;
        $isPusat           = false;
        $totalNilaiBilling = $lelangs
            ->filter(fn($l) => $l->laporan?->file_bukti_bayar)
            ->sum('harga_tertinggi');

        return view('admin.laporan.index', compact(
            'lelangs', 'grouped', 'totalNilaiBilling',
            'totalNilai', 'totalNilaiLimit', 'totalTerjual',
            'sudahBAST', 'belumBAST', 'kenaikanGlobal', 'isPusat'
        ));
    }

    // ─── UPLOAD / UPDATE LAPORAN (satker) ────────────────────────────────────
    public function uploadLaporan(Request $request, Lelang $lelang)
    {
        $request->validate([
            'file_bast'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_bukti_bayar' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'nomor_bast'       => 'nullable|string|max:100',
            'nomor_billing'    => 'nullable|string|max:100',
            'tanggal_bast'     => 'nullable|date',
            'tanggal_bayar'    => 'nullable|date',
            'catatan'          => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        if ($lelang->barang->perkara->pengajuan->satker_id !== $user->satker_id) {
            abort(403, 'Tidak punya akses ke lelang ini.');
        }

        $laporan                   = LaporanLelang::firstOrNew(['lelang_id' => $lelang->id]);
        $laporan->satker_id        = $user->satker_id;
        $laporan->nomor_bast       = $request->nomor_bast    ?? $laporan->nomor_bast;
        $laporan->nomor_billing    = $request->nomor_billing  ?? $laporan->nomor_billing;
        $laporan->tanggal_bast     = $request->tanggal_bast   ?? $laporan->tanggal_bast;
        $laporan->tanggal_bayar    = $request->tanggal_bayar  ?? $laporan->tanggal_bayar;
        $laporan->catatan          = $request->catatan         ?? $laporan->catatan;

        if ($request->hasFile('file_bast')) {
            $laporan->file_bast = $request->file('file_bast')->store('laporan/bast', 'public');
        }
        if ($request->hasFile('file_bukti_bayar')) {
            $laporan->file_bukti_bayar = $request->file('file_bukti_bayar')
                ->store('laporan/billing', 'public');
        }

        $laporan->status = $laporan->isLengkap() ? 'lengkap' : 'sebagian';
        $laporan->save();

        return back()->with('success', 'Laporan berhasil disimpan.');
    }
}