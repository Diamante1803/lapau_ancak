<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;  
use App\Models\Barang;
use App\Models\Perkara;
use App\Models\Satker;
use App\Models\User;
use App\Models\PengajuanLelang;
use App\Models\Lelang;
use App\Models\LaporanLelang;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Cache;

class AdminPusatController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function dashboard(Request $request)
    {
        $user     = auth()->user();
        $isPusat  = $user->role === 'admin_pusat';
        $isSatker = $user->role === 'admin_satker';

        $dari   = $request->query('dari');
        $sampai = $request->query('sampai');

        $basePengajuan = PengajuanLelang::query();
        $baseLelang    = Lelang::query();
        $basePerkara   = Perkara::query();
        $baseBarang    = Barang::query();

        if ($isSatker) {
            $basePengajuan->where('satker_id', $user->satker_id);
            $baseLelang->whereHas('barang.perkara.pengajuan', fn($q) => 
                $q->where('satker_id', $user->satker_id));
            $basePerkara->whereHas('pengajuan', fn($q) => 
                $q->where('satker_id', $user->satker_id));
            $baseBarang->whereHas('perkara.pengajuan', fn($q) => 
                $q->where('satker_id', $user->satker_id));
        }

        // Helper closure untuk standarisasi filter tanggal di berbagai query
        $filterDates = function($query, $column = 'created_at') use ($dari, $sampai) {
            if ($dari && $sampai) {
                return $query->whereBetween($column, [$dari . ' 00:00:00', $sampai . ' 23:59:59']);
            }
            return $query;
        };

        // KPI Stats
        $cacheKey = 'admin_stats_' . ($isSatker ? $user->satker_id : 'pusat') . '_' . $dari . '_' . $sampai;
        
        $stats = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($filterDates, $basePengajuan, $baseLelang) {
            return [
                'total_pengajuan' => $filterDates(clone $basePengajuan)->count(),
                'menunggu'        => $filterDates(clone $basePengajuan)->where('status', 'submitted')->count(),
                'disetujui'       => $filterDates(clone $basePengajuan, 'updated_at')->where('status', 'approved')->count(),
                'lelang_aktif'    => $filterDates(clone $baseLelang, 'tanggal_mulai')->where('status', 'active')->count(),
                'barang_terjual'  => $filterDates(clone $baseLelang, 'tanggal_selesai')->where('status', 'closed')->whereNotNull('pemenang_id')->count(),
                'total_nilai'     => $filterDates(clone $baseLelang, 'tanggal_selesai')
                                        ->where('status', 'closed')
                                        ->whereNotNull('pemenang_id')
                                        ->whereHas('laporan', fn($q) => $q->whereNotNull('file_bukti_bayar'))
                                        ->sum('harga_tertinggi'),
                'pnbp_seharusnya' => $filterDates(clone $baseLelang, 'tanggal_selesai')
                                        ->where('status', 'closed')
                                        ->whereNotNull('pemenang_id')
                                        ->sum('harga_tertinggi'),
            ];
        });

        // Detail Belum Bayar (Piutang)
        $unpaidDetails = $filterDates(clone $baseLelang, 'tanggal_selesai')
            ->where('status', 'closed')
            ->whereNotNull('pemenang_id')
            ->whereDoesntHave('laporan', fn($q) => $q->whereNotNull('file_bukti_bayar'))
            ->with(['barang.perkara.pengajuan.satker'])
            ->get()
            ->groupBy(fn($l) => optional($l->barang->perkara->pengajuan->satker)->nama_satker ?? 'Tanpa Satker')
            ->map(fn($items, $name) => [
                'nama_satker' => $name,
                'jumlah_lot'  => $items->count(),
                'total_nilai' => $items->sum('harga_tertinggi'),
                'daftar_barang' => $items->map(fn($l) => $l->barang->nama_barang)->toArray(),
            ]);

        // Detail Aset Terjual
        $soldDetails = $filterDates(clone $baseLelang, 'tanggal_selesai')
            ->where('status', 'closed')
            ->whereNotNull('pemenang_id')
            ->with(['barang.perkara.pengajuan.satker', 'pemenang'])
            ->get()
            ->groupBy(fn($l) => optional($l->barang->perkara->pengajuan->satker)->nama_satker ?? 'Tanpa Satker')
            ->map(fn($items, $name) => [
                'nama_satker' => $name,
                'jumlah_lot'  => $items->count(),
                'total_nilai' => $items->sum('harga_tertinggi'),
                'daftar_barang' => $items->map(fn($l) => [
                    'nama' => $l->barang->nama_barang,
                    'pemenang' => optional($l->pemenang)->nama ?? 'N/A',
                    'nilai' => $l->harga_tertinggi
                ])->toArray(),
            ]);

        // Data Perkara
        $statsPerkara = [
            'total'   => $filterDates(clone $basePerkara)->count(),
            'aktif'   => (clone $basePerkara)->whereHas('barangs', fn($q) => $q->where('status', 'in_auction'))->count(),
            'selesai' => $filterDates(clone $basePerkara, 'updated_at')->whereHas('barangs', fn($q) => $q->where('status', 'sold'))->count(),
        ];

        // Data Barang
        $statsBarang = [
            'total'         => $filterDates(clone $baseBarang)->count(),
            'belum_lelang'  => $filterDates(clone $baseBarang)->where('status', 'available')->count(),
            'sedang_lelang' => (clone $baseBarang)->where('status', 'in_auction')->count(),
            'terjual'       => $filterDates(clone $baseBarang, 'updated_at')->where('status', 'sold')->count(),
        ];

        // Monitoring Satker (hanya admin pusat)
        $monitoringSatker = $isPusat 
            ? Satker::with(['pengajuans.perkaras.barangs'])->get()
            : collect();

        // Aktivitas terbaru
        $aktivitasRaw = $filterDates(clone $basePengajuan, 'updated_at')
            ->with('satker')
            ->whereIn('status', ['submitted', 'approved', 'revision'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        $aktivitasTerbaru = $aktivitasRaw->map(function($p) {
            $keterangan = match($p->status) {
                'submitted' => 'Pengajuan baru dikirim: ' . $p->judul_pengajuan,
                'approved'  => 'Pengajuan disetujui: ' . $p->judul_pengajuan,
                'revision'  => 'Pengajuan diminta revisi: ' . $p->judul_pengajuan,
                default     => $p->judul_pengajuan,
            };
            return [
                'keterangan' => $keterangan,
                'satker'     => optional($p->satker)->nama_satker ?? '-',
                'waktu'      => $p->updated_at->diffForHumans(),
                'status'     => $p->status,
            ];
        });

        // Lelang akan berakhir (24 jam ke depan)
        $lelangAkanBerakhir = (clone $baseLelang)
            ->with(['barang.fotoBarang', 'barang.perkara.pengajuan.satker'])
            ->where('status', 'active')
            ->where('tanggal_selesai', '<=', now()->addHours(24))
            ->orderBy('tanggal_selesai')
            ->take(5)
            ->get();

        // Pengajuan terbaru untuk tabel
        $totalPengajuan = $stats['total_pengajuan'];
        $pengajuans = $filterDates(clone $basePengajuan)
            ->with(['satker', 'perkaras.barangs.lelang'])
            ->when($isPusat, fn($q) => $q->whereNotIn('status', ['draft']))
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'statsPerkara', 'statsBarang',
            'monitoringSatker', 'aktivitasTerbaru',
            'lelangAkanBerakhir', 'pengajuans', 'totalPengajuan', 'unpaidDetails',
            'soldDetails'
        ));
    }
    
    public function index(Request $request)
    {
        // $query = PengajuanLelang::where('status', 'submitted');

        $user = auth()->user();

        if ($user->role === 'admin_pusat') {
            $query = PengajuanLelang::with('dokumenPengajuan')
                ->whereIn('status', ['submitted', 'revision', 'approved']);
        } else {
            $query = PengajuanLelang::with('dokumenPengajuan')
                ->where('satker_id', $user->satker_id);
        }

        $pengajuans = $query->latest()->get();

        return view('admin.pengajuan.index', compact('pengajuans'));
    }

    public function show(PengajuanLelang $pengajuan)
    {
        $this->authorize('view', $pengajuan);

        $user = auth()->user();

        // ADMIN PUSAT hanya boleh lihat jika submitted ATAU sudah diproses
        if ($user->role === 'admin_pusat') {
            abort_if(!in_array($pengajuan->status, ['submitted', 'approved', 'revision']), 403);
        }

        // ADMIN SATKER boleh lihat miliknya sendiri
        if ($user->role === 'admin_satker') {
            abort_if($pengajuan->satker_id !== $user->satker_id, 403);
        }

        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin_pusat') {
            abort(403, 'Hanya Admin Pusat yang dapat membuat user baru.');
        }
        

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'satker_id' => 'required|exists:satkers,id',
            'kontak' => 'nullable|string|max:20',
        ]);

        return DB::transaction(function () use ($request) {

            $satker = Satker::lockForUpdate()->findOrFail($request->satker_id);

            if ($satker->admin_user_id) {
                return back()->withErrors('Satker sudah memiliki admin');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin_satker',
                'satker_id' => $satker->id,
                'kontak' => $request->kontak
            ]);

            $satker->update([
                'admin_user_id' => $user->id
            ]);

            return redirect()->back()->with('success', 'Admin satker berhasil dibuat');
        });

    }

    public function destroy(PengajuanLelang $pengajuan)
    {
        $pengajuan->load([
            'dokumenPengajuan',
            'perkaras.dokumenPerkara',
            'perkaras.barangs.fotoBarang'
        ]);

        DB::transaction(function () use ($pengajuan) {

            // 1. Dokumen pengajuan
            foreach ($pengajuan->dokumenPengajuan ?? [] as $doc) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }

            // 2. Perkara → Barang → Dokumen Barang
            foreach ($pengajuan->perkaras ?? [] as $perkara) {

                // 🔸 Barang
                foreach ($perkara->barangs ?? [] as $barang) {

                    foreach ($barang->fotoBarang ?? [] as $docBarang) {
                        if ($docBarang->file_path && Storage::disk('public')->exists($docBarang->file_path)) {
                            Storage::disk('public')->delete($docBarang->file_path);
                        }
                        $docBarang->delete();
                    }

                    $barang->delete();
                }

                // 🔸 Dokumen perkara
                foreach ($perkara->dokumenPerkara ?? [] as $docPerkara) {
                    if ($docPerkara->file_path && Storage::disk('public')->exists($docPerkara->file_path)) {
                        Storage::disk('public')->delete($docPerkara->file_path);
                    }
                    $docPerkara->delete();
                }

                $perkara->delete();
            }

            // 3. Hapus pengajuan
            AuditLogController::log($pengajuan->id, 'PengajuanLelang', 'deleted', "Menghapus pengajuan lelang: {$pengajuan->judul_pengajuan}");

            $pengajuan->delete();
        });

        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan dan semua relasi berhasil dihapus');
    }

    public function satkerStore(Request $request)
    {
        $request->validate([
            'nama_satker' => 'required',
            'penanggung_jawab' => 'required',
            'no_hp' => 'required',
        ]);

        Satker::create([
            'nama_satker' => $request->nama_satker,
            'penanggung_jawab' => $request->penanggung_jawab,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('admin.satker.index')
            ->with('success', 'Satker berhasil ditambahkan');
    }

    public function indexPengajuan()// Tampilkan Semua Pengajuan Lelang
    {
        return PengajuanLelang::with('satker')->latest()->get();
    }

    public function approve(PengajuanLelang $pengajuan)
    {
        // 🔐 Pakai policy (lebih clean)
        $this->authorize('approve', $pengajuan);

        // 🔒 Validasi status
        if ($pengajuan->status !== 'submitted') {
            return back()->with('error', 'Hanya pengajuan dengan status submitted yang bisa di-approve');
        }

        // 💾 Update status
        $pengajuan->update([
            'status' => 'approved',
            'catatan_revisi' => null // bersihin kalau ada sisa revisi
        ]);

        AuditLogController::log($pengajuan->id, 'PengajuanLelang', 'approved', "Menyetujui pengajuan lelang: {$pengajuan->judul_pengajuan}");

        return back()->with('success', 'Pengajuan berhasil disetujui');
    }

    public function revisi(Request $request, PengajuanLelang $pengajuan)
    {
        // 🔒 VALIDASI ROLE
        if (auth()->user()->role !== 'admin_pusat') {
            abort(403, 'Akses ditolak');
        }

        // 🔒 VALIDASI STATUS
        if ($pengajuan->status !== 'submitted') {
            return back()->with('error', 'Pengajuan belum bisa direvisi.');
        }

        // ✅ VALIDASI INPUT
        $request->validate([
            'catatan_revisi' => 'required|string|max:1000',
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi.',
        ]);

        // 💾 Ambil riwayat lama, tambahkan entri baru
        $riwayat = $pengajuan->catatan_revisi ?? [];

        $riwayat[] = [
            'catatan'   => $request->catatan_revisi,
            'tanggal'   => now()->toDateTimeString(),
            'oleh'      => auth()->user()->name,
            'ke_revisi' => count($riwayat) + 1,
        ];

        $pengajuan->update([
            'status'         => 'revision',
            'catatan_revisi' => $riwayat,
        ]);

        AuditLogController::log($pengajuan->id, 'PengajuanLelang', 'revision', "Mengembalikan pengajuan untuk revisi: {$pengajuan->judul_pengajuan}");

        return redirect()
            ->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan dikembalikan untuk revisi.');
    }

    public function buatLelang(Request $request, $barangId)// Buat Lelang Baru dari Barang yang Diajukan
    {
        $this->authorize('create', Lelang::class);

        $barang = Barang::findOrFail($barangId);

        return Lelang::create([
            'barang_id' => $barangId,
            'tanggal_mulai' => $request->mulai,
            'tanggal_selesai' => $request->selesai,
            'harga_awal' => $barang->harga_awal,
            'status' => 'scheduled'
        ]);
    }

}
