<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanLelang;
use App\Models\Barang;
use App\Models\Lelang;
use App\Models\Pembeli;
use App\Models\Penawaran;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\MagicLinkMail;

class LelangController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lelangs = Lelang::with(['barang.foto'])
        ->whereIn('status', ['active', 'scheduled']) // tampilkan yg siap & sedang lelang
        ->latest()
        ->get();

        return view('public.lelang-list', compact('lelangs'));
    }

    public function dashboard()
    {
        // Ambil semua pengajuan yang approved
        $pengajuans = PengajuanLelang::with([
                'satker',
                'perkaras.barangs.fotoBarang',
                'perkaras.barangs.lelang', // relasi lelang per barang
            ])
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('admin.lelang.dashboard', compact('pengajuans'));
    }

    public function jadwalkan(Request $request, PengajuanLelang $pengajuan)
    {
        $request->validate([
            'tanggal_mulai'   => 'required|date|after:now',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'tanggal_mulai.after'   => 'Tanggal mulai harus setelah sekarang.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        // Ambil semua perkara_id dari pengajuan
        $perkaraIds = $pengajuan->perkaras()->pluck('id');

        if ($perkaraIds->isEmpty()) {
            return back()->with('error', 'Tidak ada perkara dalam pengajuan ini.');
        }

        // Ambil semua barang
        $barangs = Barang::whereIn('perkara_id', $perkaraIds)->get();

        if ($barangs->isEmpty()) {
            return back()->with('error', 'Tidak ada barang dalam pengajuan ini.');
        }

        // Cek apakah sudah ada lelang aktif/terjadwal
        $sudahAda = Lelang::whereIn('barang_id', $barangs->pluck('id'))
            ->whereIn('status', ['scheduled', 'active'])
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Pengajuan ini sudah memiliki jadwal lelang yang berjalan.');
        }

        $dijadwalkan = 0;

        foreach ($barangs as $barang) {

            // Cek apakah ada lelang cancelled untuk barang ini
            $lelangLama = Lelang::where('barang_id', $barang->id)
                ->where('status', 'cancelled')
                ->latest()
                ->first();

            if ($lelangLama) {
                // ✅ Update lelang lama — tidak buat id baru
                $lelangLama->update([
                    'harga_awal'      => $barang->harga_awal,
                    'tanggal_mulai'   => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'status'          => 'scheduled',
                    'harga_tertinggi' => null,
                    'pemenang_id'     => null,
                ]);
            } else {
                // ✅ Buat lelang baru jika belum pernah ada
                Lelang::create([
                    'barang_id'       => $barang->id,
                    'harga_awal'      => $barang->harga_awal,
                    'tanggal_mulai'   => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'status'          => 'scheduled',
                ]);
            }

            $barang->update(['status' => 'in_auction']);
            $dijadwalkan++;
        }

        return back()->with('success', 'Lelang berhasil dijadwalkan untuk ' . $dijadwalkan . ' barang.');
    }

    public function batal(Request $request, PengajuanLelang $pengajuan)
    {
        // Ambil semua perkara_id dari pengajuan
        $perkaraIds = $pengajuan->perkaras()->pluck('id');

        if ($perkaraIds->isEmpty()) {
            return back()->with('error', 'Tidak ada perkara dalam pengajuan ini.');
        }

        // Ambil semua barang_id dari perkara tersebut
        $barangIds = Barang::whereIn('perkara_id', $perkaraIds)->pluck('id');

        if ($barangIds->isEmpty()) {
            return back()->with('error', 'Tidak ada barang dalam pengajuan ini.');
        }

        // Batalkan semua lelang scheduled dari barang-barang tersebut
        $dibatalkan = Lelang::whereIn('barang_id', $barangIds)
            ->where('status', 'scheduled')
            ->get();

        if ($dibatalkan->isEmpty()) {
            return back()->with('error', 'Tidak ada lelang terjadwal yang bisa dibatalkan.');
        }

        foreach ($dibatalkan as $lelang) {
            $lelang->update(['status' => 'cancelled']);
            $lelang->barang->update(['status' => 'available']);
        }

        return back()->with('success', 'Jadwal lelang berhasil dibatalkan untuk ' . $dibatalkan->count() . ' barang.');
    }

    // -------------------------------------------------------
    // Halaman Lelang Aktif
    // Route: GET /admin/lelang/aktif
    // Name:  admin.lelang.aktif
    // -------------------------------------------------------
    public function aktif()
    {
        $lelangs = Lelang::with([
                'barang.perkara.pengajuan.satker',  // relasi ke satker
                'penawarans' => fn($q) => $q->orderByDesc('nilai_penawaran'), // penawaran diurutkan tertinggi
            ])
            ->where('status', 'active')
            ->orderBy('tanggal_selesai', 'asc') // yang hampir berakhir tampil duluan
            ->get();
 
        return view('admin.lelang.aktif', compact('lelangs'));
    }

    public function detail(Lelang $lelang)
    {
        $lelang->load([
            'barang.perkara.pengajuan.satker',
            'barang.fotoBarang',
            'penawarans' => fn($q) => $q->orderByDesc('nilai_penawaran'),
        ]);
 
        return view('admin.lelang.detail', compact('lelang'));
    }

    public function show(Lelang $lelang)
    {
        $lelang->load([
            'barang.fotoBarang',
            'barang.perkara.pengajuan.satker',
            'penawarans' => fn($q) => $q->orderByDesc('nilai_penawaran'),
        ]);

        return view('admin.lelang.show', compact('lelang'));
    }


    public function aktifkan($id)// Aktifkan Lelang
    {
        $lelang = Lelang::findOrFail($id);

        $this->authorize('update', $lelang);

        $lelang->update(['status' => 'active']);

        $lelang->barang->update(['status' => 'in_auction']);

        return response()->json(['message' => 'Lelang aktif']);
    }

    public function tutup(Lelang $lelang)
    {
        if (!in_array($lelang->status, ['active', 'scheduled'])) {
            return back()->with('error', 'Lelang tidak dapat ditutup.');
        }
 
        $pemenang = $lelang->penawaran()->orderByDesc('harga_tawar')->first();
 
        $lelang->update([
            'status'              => 'closed',
            'pemenang_id'         => $pemenang?->id,
            'harga_final'         => $pemenang?->harga_tawar,
        ]);
 
        $lelang->barang->update(['status' => 'sold']);
 
        return back()->with('success', 'Lelang ditutup. ' .
            ($pemenang ? 'Pemenang: ' . $pemenang->nama_pembeli : 'Tidak ada penawar.'));
    }

    // Lelang Selesai — Admin Pusat
    public function selesai()
    {
        $lelangs = Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
                'pemenang',
                'penawarans.pembeli',
            ])
            ->where('status', 'closed')
            ->latest()
            ->get();

        return view('admin.lelang.selesai', compact('lelangs'));
    }

    // Lelang Selesai — Admin Satker
    public function selesaiSatker()
    {
        $lelangs = Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
                'pemenang',
                'penawarans.pembeli',
            ])
            ->where('status', 'closed')
            ->whereHas('barang.perkara.pengajuan', function($q) {
                $q->where('satker_id', auth()->user()->satker_id);
            })
            ->latest()
            ->get();

        return view('admin.lelang.selesai', compact('lelangs'));
    }

    // Ganti Pemenang — hanya Admin Satker
    public function gantiPemenang(Request $request, Lelang $lelang)
    {
        // Pastikan hanya admin satker milik satker ini
        if (auth()->user()->role !== 'admin_satker') {
            abort(403);
        }

        $milikSatker = $lelang->barang->perkara->pengajuan->satker_id === auth()->user()->satker_id;
        if (!$milikSatker) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'pembeli_id'      => 'required|exists:pembelis,id',
            'catatan_pemenang'=> 'nullable|string|max:500',
        ]);

        // Ambil urutan penawaran pembeli baru
        $penawaranBaru = $lelang->penawarans()
            ->where('pembeli_id', $request->pembeli_id)
            ->orderByDesc('nilai_penawaran')
            ->first();

        if (!$penawaranBaru) {
            return back()->with('error', 'Pembeli tidak ditemukan dalam daftar penawaran.');
        }

        // Hitung urutan pemenang baru
        $urutanBaru = ($lelang->pemenang_urutan ?? 1) + 1;

        $lelang->update([
            'pemenang_id'      => $request->pembeli_id,
            'harga_tertinggi'  => $penawaranBaru->nilai_penawaran,
            'pemenang_urutan'  => $urutanBaru,
            'catatan_pemenang' => $request->catatan_pemenang,
        ]);

        // Update status barang tetap sold
        $lelang->barang->update(['status' => 'sold']);

        return back()->with('success', 'Pemenang berhasil diganti.');
    }

    // Lelang Aktif untuk Admin Satker — hanya satker sendiri
    public function aktifSatker()
    {
        $lelangs = Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
                'penawarans',
            ])
            ->where('status', 'active')
            ->whereHas('barang.perkara.pengajuan', function($q) {
                $q->where('satker_id', auth()->user()->satker_id);
            })
            ->latest()
            ->get();

        return view('admin.lelang.aktif', compact('lelangs'));
    }

    public function ajukanLelangUlang(Lelang $lelang)
    {
        // Pastikan hanya admin satker pemilik barang
        if (auth()->user()->role !== 'admin_satker') {
            abort(403);
        }

        $milikSatker = $lelang->barang->perkara->pengajuan->satker_id 
                    === auth()->user()->satker_id;

        if (!$milikSatker) {
            abort(403, 'Akses ditolak.');
        }

        // Hanya barang unsold yang bisa diajukan ulang
        if ($lelang->barang->status !== 'unsold') {
            return back()->with('error', 'Hanya barang yang tidak terjual yang bisa diajukan ulang.');
        }

        // Reset status barang ke available
        $lelang->barang->update(['status' => 'available']);

        // Tandai lelang lama sebagai cancelled agar tidak bentrok
        // (lelang baru akan dibuat oleh admin pusat)
        $lelang->update(['status' => 'cancelled']);

        return back()->with('success', 
            'Barang "' . $lelang->barang->nama_barang . '" berhasil diajukan untuk lelang ulang. ' .
            'Admin Pusat akan menjadwalkan lelang baru.');
    }


}
