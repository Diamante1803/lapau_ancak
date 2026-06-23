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
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\MagicLinkMail;
use Illuminate\Support\Facades\Cache;
use App\Events\LelangStatusUpdate;
use App\Events\PenawaranBaru;
use App\Http\Controllers\AuditLogController;

class LelangController extends Controller
{
    use AuthorizesRequests;

    private function clearPublicCache()
    {
        Cache::forget('public_index_stats');
        Cache::forget('public_lelangs_aktif');
        Cache::forget('public_lelangs_mendatang');
    }

    /**
     * Display a listing of the resource.
     */


    public function dashboard(Request $request)
    {
        // Ambil semua pengajuan yang approved
        $query = PengajuanLelang::with([
                'satker',
                'perkaras.barangs.fotoBarang',
                'perkaras.barangs.lelang', // relasi lelang per barang
            ])
            ->where('status', 'approved');

        // Filter pencarian Satker (Server-side)
        if ($request->filled('search')) {
            $query->whereHas('satker', function($q) use ($request) {
                $q->where('nama_satker', 'like', '%' . $request->search . '%');
            });
        }

        $pengajuans = $query->latest()
            ->paginate(10) // Tampilkan 10 kartu per halaman
            ->withQueryString();

        return view('admin.lelang.dashboard', compact('pengajuans'));
    }

    public function jadwalkan(Request $request, PengajuanLelang $pengajuan)
    {
        // Hanya Admin Pusat yang bisa menjadwalkan lelang
        if (auth()->user()->role !== 'admin_pusat') {
            abort(403, 'Akses ditolak.');
        }

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

        // Hanya ambil barang yang statusnya 'available' (siap lelang)
        // Ini mencegah barang yang sudah 'sold' atau sedang 'in_auction' terjadwal ulang
        $barangs = Barang::whereIn('perkara_id', $perkaraIds)
            ->where('status', 'available')
            ->get();

        if ($barangs->isEmpty()) {
            return back()->with('error', 'Tidak ada barang yang tersedia untuk dijadwalkan lelang pada pengajuan ini.');
        }

        $dijadwalkan = 0;

        DB::transaction(function () use ($barangs, $request, &$dijadwalkan) {
            foreach ($barangs as $barang) {

                // Cek apakah ada lelang cancelled untuk barang ini
                $lelangLama = Lelang::where('barang_id', $barang->id)
                    ->where('status', 'cancelled')
                    ->latest()
                    ->first();

                if ($lelangLama) {
                    // ✅ Update lelang lama — tidak buat id baru
                    $lelang = $lelangLama;
                    $lelangLama->update([
                        'harga_awal'      => $barang->harga_awal,
                        'tanggal_mulai'   => $request->tanggal_mulai,
                        'tanggal_selesai' => $request->tanggal_selesai,
                        'status'          => 'scheduled',
                        'harga_tertinggi' => null,
                        'pemenang_id'     => null,
                        'pemenang_urutan' => 1,
                        'catatan_pemenang'=> null,
                    ]);
                } else {
                    // ✅ Buat lelang baru jika belum pernah ada
                    $lelang = Lelang::create([
                        'barang_id'       => $barang->id,
                        'harga_awal'      => $barang->harga_awal,
                        'tanggal_mulai'   => $request->tanggal_mulai,
                        'tanggal_selesai' => $request->tanggal_selesai,
                        'status'          => 'scheduled',
                    ]);
                }

                $barang->update(['status' => 'in_auction']);
                broadcast(new LelangStatusUpdate($lelang->id, 'scheduled'));

                $dijadwalkan++;
                
                AuditLogController::log($lelang->id, 'Lelang', 'scheduled', "Menjadwalkan lelang untuk barang: {$barang->nama_barang}");
                
                $this->clearPublicCache();
            }
        });

        return back()->with('success', 'Lelang berhasil dijadwalkan untuk ' . $dijadwalkan . ' barang.');
    }

    public function batal(Request $request, PengajuanLelang $pengajuan)
    {
        // Hanya Admin Pusat yang bisa membatalkan jadwal
        if (auth()->user()->role !== 'admin_pusat') {
            abort(403, 'Akses ditolak.');
        }

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

        DB::transaction(function () use ($dibatalkan) {
            foreach ($dibatalkan as $lelang) {
                $lelang->update(['status' => 'cancelled']);
                $lelang->barang->update(['status' => 'available']);
                broadcast(new LelangStatusUpdate($lelang->id, 'cancelled'));

                AuditLogController::log($lelang->id, 'Lelang', 'cancelled', "Membatalkan jadwal lelang barang: {$lelang->barang->nama_barang}");
                
                $this->clearPublicCache();
            }
        });

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
                'penawarans' => fn($q) => $q->with('pembeli')->orderByDesc('nilai_penawaran'), // penawaran diurutkan tertinggi dengan data pembeli
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

    public function aktifkan(Lelang $lelang)// Aktifkan Lelang
    {
        if (auth()->user()->role !== 'admin_pusat') {
            abort(403, 'Akses ditolak.');
        }

        $this->authorize('update', $lelang);

        if ($lelang->status !== 'scheduled') {
            return back()->with('error', 'Hanya lelang terjadwal yang bisa diaktifkan.');
        }

        DB::transaction(function () use ($lelang) {
            $lelang->update(['status' => 'active']);
            $lelang->barang->update(['status' => 'in_auction']);
            broadcast(new LelangStatusUpdate($lelang->id, 'active'));

            AuditLogController::log($lelang->id, 'Lelang', 'active', "Mengaktifkan lelang barang: {$lelang->barang->nama_barang}");
            
            $this->clearPublicCache();
        });

        return back()->with('success', 'Lelang berhasil diaktifkan.');
    }

    public function tutup(Lelang $lelang)
    {
        if (auth()->user()->role !== 'admin_pusat') {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($lelang->status, ['active', 'scheduled'])) {
            return back()->with('error', 'Lelang tidak dapat ditutup.');
        }
 
        $pemenang = null;

        DB::transaction(function () use ($lelang, &$pemenang) {
            $pemenang = $lelang->penawarans()->orderByDesc('nilai_penawaran')->first();

            $lelang->update([
                'status'              => 'closed',
                'pemenang_id'         => $pemenang?->pembeli_id,
                'harga_tertinggi'     => $pemenang?->nilai_penawaran,
                'pemenang_urutan'     => 1,
            ]);

            $lelang->barang->update([
                'status' => $pemenang ? 'sold' : 'unsold',
            ]);

            broadcast(new LelangStatusUpdate($lelang->id, 'closed'));

            AuditLogController::log($lelang->id, 'Lelang', 'closed', "Menutup lelang barang: {$lelang->barang->nama_barang}. Pemenang ID: " . ($pemenang->pembeli_id ?? 'N/A'));
            
            $this->clearPublicCache();
        });

        return back()->with('success', 'Lelang ditutup secara manual. ' .
             ($pemenang ? 'Pemenang: ' . ($pemenang->pembeli->nama ?? 'Peserta') : 'Tidak ada penawar.'));
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
        // Urutan dihitung berdasarkan peringkat nilai penawaran tertinggi dari setiap pembeli unik.
        // Kita menghitung berapa banyak pembeli yang memiliki penawaran lebih tinggi dari pembeli terpilih.
        $urutanBaru = $lelang->penawarans()
            ->select('pembeli_id', DB::raw('MAX(nilai_penawaran) as nilai_tertinggi'))
            ->groupBy('pembeli_id')
            ->having('nilai_tertinggi', '>', $penawaranBaru->nilai_penawaran)
            ->get()
            ->count() + 1;

        DB::transaction(function () use ($lelang, $request, $penawaranBaru, $urutanBaru) {
            $lelang->update([
                'pemenang_id'      => $request->pembeli_id,
                'harga_tertinggi'  => $penawaranBaru->nilai_penawaran,
                'pemenang_urutan'  => $urutanBaru,
                'catatan_pemenang' => $request->catatan_pemenang,
            ]);

            // Update status barang tetap sold
            $lelang->barang->update(['status' => 'sold']);

            AuditLogController::log($lelang->id, 'Lelang', 'updated', "Mengganti pemenang lelang barang: {$lelang->barang->nama_barang}");
        });

        return back()->with('success', 'Pemenang berhasil diganti.');
    }

    // Lelang Aktif untuk Admin Satker — hanya satker sendiri
    public function aktifSatker()
    {
        $lelangs = Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
                'penawarans.pembeli',
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

        DB::transaction(function () use ($lelang) {
            // Reset status barang ke available
            $lelang->barang->update(['status' => 'available']);

            // Tandai lelang lama sebagai cancelled agar tidak bentrok
            // (lelang baru akan dibuat oleh admin pusat)
            $lelang->update(['status' => 'cancelled']);

            broadcast(new LelangStatusUpdate($lelang->id, 'cancelled'));

            AuditLogController::log($lelang->id, 'Lelang', 'revision', "Mengajukan lelang ulang untuk barang: {$lelang->barang->nama_barang}");
            
            $this->clearPublicCache();
        });

        return back()->with('success', 
            'Barang "' . $lelang->barang->nama_barang . '" berhasil diajukan untuk lelang ulang. ' .
            'Admin Pusat akan menjadwalkan lelang baru.');
    }

    public function hapusPenawaranTertinggi(Lelang $lelang)
    {
        if (auth()->user()->role !== 'admin_pusat') {
            abort(403, 'Hanya Admin Pusat yang dapat menghapus penawaran.');
        }

        // Ambil penawaran tertinggi
        $tertinggi = $lelang->penawarans()
            ->orderByDesc('nilai_penawaran')
            ->first();

        if (!$tertinggi) {
            return back()->with('error', 'Tidak ada penawaran untuk dihapus.');
        }

        $namaPembeli = $tertinggi->pembeli?->nama ?? '-';
        $nilaiHapus  = number_format($tertinggi->nilai_penawaran, 0, ',', '.');

        $berikutnya = null;
        DB::transaction(function () use ($tertinggi, $lelang, &$berikutnya, $namaPembeli) {
            // Hapus penawaran tertinggi
            $tertinggi->delete();

            // Update harga_tertinggi di tabel lelang ke penawaran berikutnya
            $berikutnya = $lelang->penawarans()
                ->orderByDesc('nilai_penawaran')
                ->first();

            $lelang->update([
                'harga_tertinggi' => $berikutnya?->nilai_penawaran ?? null,
                'pemenang_id'     => $berikutnya?->pembeli_id ?? null,
            ]);

            // Broadcast update harga terbaru ke public agar penawar lain tahu jika bid tertinggi dihapus
            broadcast(new PenawaranBaru(
                $lelang->id,
                (float) ($lelang->harga_tertinggi ?? $lelang->harga_awal),
                $lelang->harga_tertinggi ? 'Rp ' . number_format($lelang->harga_tertinggi, 0, ',', '.') : 'Belum ada',
                (float) (($lelang->harga_tertinggi ?? $lelang->harga_awal) + 10000),
                $lelang->penawarans()->count()
            ))->toOthers();

            AuditLogController::log($lelang->id, 'Lelang', 'updated', "Menghapus penawaran tertinggi oleh {$namaPembeli} pada barang: {$lelang->barang->nama_barang}");
            
            $this->clearPublicCache();
        });

        return back()->with('success', 
            "Penawaran Rp {$nilaiHapus} oleh {$namaPembeli} berhasil dihapus. " .
            ($berikutnya ? "Pemenang sementara sekarang: " . ($berikutnya->pembeli->nama ?? '-') : "Belum ada penawar.")
        );
    }

    public function batalAktif(Lelang $lelang)
    {
        // Security Fix: Guard administrative action
        if (auth()->user()->role !== 'admin_pusat') {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($lelang->status, ['scheduled', 'active'])) {
            return back()->with('error', 'Lelang tidak dapat dibatalkan.');
        }

        $statusAsal = $lelang->status;
        $namaBarang = $lelang->barang->nama_barang;

        DB::transaction(function () use ($lelang, $statusAsal, $namaBarang) {
            // Jika sempat aktif, bersihkan semua jejak lelang
            if ($statusAsal === 'active') {
                // Hapus semua penawaran
                $lelang->penawarans()->delete();

                // Reset pemenang & harga tertinggi
                $lelang->update([
                    'status'          => 'cancelled',
                    'pemenang_id'     => null,
                    'harga_tertinggi' => null,
                ]);
            } else {
                $lelang->update(['status' => 'cancelled']);
            }

            // Reset status barang
            $lelang->barang->update(['status' => 'available']);

            broadcast(new LelangStatusUpdate($lelang->id, 'cancelled'));

            AuditLogController::log($lelang->id, 'Lelang', 'cancelled', "Membatalkan lelang aktif/terjadwal barang: {$namaBarang}");
            
            $this->clearPublicCache();
        });

        $label = $statusAsal === 'active' ? 'berlangsung' : 'terjadwal';

        return back()->with('success',
            'Lelang "' . $namaBarang . '" yang sedang ' . $label . ' berhasil dibatalkan' .
            ($statusAsal === 'active' ? ' beserta seluruh penawaran yang masuk.' : '.')
        );
    }

    public function tabelPenawaran(Lelang $lelang)
    {
        $penawarans = $lelang->penawarans()
            ->with('pembeli')
            ->orderByDesc('nilai_penawaran')
            ->get();

        return view('partials.tabel-penawaran', compact('lelang', 'penawarans'));
    }

}
