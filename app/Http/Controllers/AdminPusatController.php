<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;  
use App\Models\Barang;
use App\Models\Satker;
use App\Models\User;
use App\Models\PengajuanLelang;
use App\Models\Lelang;

class AdminPusatController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        $user     = auth()->user();
        $isPusat  = $user->role === 'admin_pusat';
        $isSatker = $user->role === 'admin_satker';

        // Query base — filter per satker jika admin_satker
        $basePengajuan = PengajuanLelang::query();
        $baseLelang    = Lelang::query();

        if ($isSatker) {
            $basePengajuan->where('satker_id', $user->satker_id);
            $baseLelang->whereHas('barang.perkara.pengajuan', function($q) use ($user) {
                $q->where('satker_id', $user->satker_id);
            });
        }

        $stats = [
            // Total pengajuan (semua status)
            'total_pengajuan' => (clone $basePengajuan)->count(),

            // Lelang aktif saat ini
            'lelang_aktif' => (clone $baseLelang)->where('status', 'active')->count(),

            // Barang terjual (lelang closed + ada pemenang)
            'barang_terjual' => (clone $baseLelang)
                ->where('status', 'closed')
                ->whereNotNull('pemenang_id')
                ->count(),

            // Total nilai penjualan final (harga_tertinggi saat closed)
            'total_nilai' => (clone $baseLelang)
                ->where('status', 'closed')
                ->whereNotNull('pemenang_id')
                ->sum('harga_tertinggi'),
        ];

        $pengajuans = (clone $basePengajuan)
            ->with([
                'satker',
                'perkaras.barangs.lelang', // tambah ini
            ])
            ->latest()
            ->take(5)
            ->get();

        $totalPengajuan = $stats['total_pengajuan'];

        return view('admin.dashboard', compact('stats', 'pengajuans', 'totalPengajuan'));
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

    public function create()
    {
        $satkers = \App\Models\Satker::all();
        return view('tambah', compact('satkers'));
    }

    public function store(Request $request)
    {
        

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
            'dokumen',
            'perkaras.dokumenPerkaras',
            'perkaras.barangs.FotoBarangs'
        ]);

        DB::transaction(function () use ($pengajuan) {

            // 1. Dokumen pengajuan
            foreach ($pengajuan->dokumen ?? [] as $doc) {
                if ($doc->file_path && Storage::exists($doc->file_path)) {
                    Storage::delete($doc->file_path);
                }
                $doc->delete();
            }

            // 2. Perkara → Barang → Dokumen Barang
            foreach ($pengajuan->perkaras ?? [] as $perkara) {

                // 🔸 Barang
                foreach ($perkara->barangs ?? [] as $barang) {

                    foreach ($barang->fotoBarangs ?? [] as $docBarang) {
                        if ($docBarang->file_path && Storage::exists($docBarang->file_path)) {
                            Storage::delete($docBarang->file_path);
                        }
                        $docBarang->delete();
                    }

                    $barang->delete();
                }

                // 🔸 Dokumen perkara
                foreach ($perkara->dokumenPerkaras ?? [] as $docPerkara) {
                    if ($docPerkara->file_path && Storage::exists($docPerkara->file_path)) {
                        Storage::delete($docPerkara->file_path);
                    }
                    $docPerkara->delete();
                }

                $perkara->delete();
            }

            // 3. Hapus pengajuan
            $pengajuan->delete();
        });

        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan dan semua relasi berhasil dihapus');
    }

    public function satkerIndex()
    {
        $satkers = Satker::latest()->get();

        return view('admin.satker.index', compact('satkers'));
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
