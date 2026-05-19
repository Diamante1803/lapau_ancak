<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\PengajuanLelang;
use App\Models\Perkara;
use App\Models\Barang;
use App\Models\FotoBarang;
use App\Models\DokumenPengajuan;
use App\Models\DokumenPerkara;

class AdminSatkerController extends Controller
{
    use AuthorizesRequests;
    // =========================
    // DASHBOARD + LIST
    // =========================
    public function dashboard()
    {
        // $pengajuans = PengajuanLelang::where('satker_id', auth()->user()->satker_id)
        //     ->latest()
        //     ->take(5) // ambil 5 terbaru saja
        //     ->get();

        // return view('admin.dashboard', compact('pengajuans'));
    }

    public function index(Request $request)
    {
        $query = PengajuanLelang::with([
                'dokumenPengajuan',
                'perkaras.dokumenPerkara',
                'perkaras.barangs.fotoBarang',
            ])
            ->where('satker_id', Auth::user()->satker_id);

        if ($request->search) {
            $query->where('judul_pengajuan', 'like', '%' . $request->search . '%');
        }

        $pengajuans = $query->latest()->get();

        return view('admin.pengajuan.index', compact('pengajuans'));
    }

    private function stepStatus(PengajuanLelang $pengajuan): array
    {
        $dokumen      = $pengajuan->dokumenPengajuan->pluck('jenis')->toArray();
        $wajib        = ['sk_panitia', 'izin_penjualan', 'surat_penetapan_harga'];
        $step1Lengkap = !empty($pengajuan->judul_pengajuan)
                        && empty(array_diff($wajib, $dokumen));
    
        $perkaras           = $pengajuan->perkaras()->with(['dokumenPerkara', 'barangs'])->get();
        $step2Lengkap       = $step1Lengkap
                            && $perkaras->count() > 0
                            && $perkaras->every(fn($p) => $p->dokumenPerkara->count() > 0);
    
        $totalBarang  = $perkaras->sum(fn($p) => $p->barangs->count());
        $step3Lengkap = $step2Lengkap && $totalBarang > 0;
    
        return [
            1 => $step1Lengkap,
            2 => $step2Lengkap,
            3 => $step3Lengkap,
        ];
    }
    
    // ============================================================
    // WIZARD — CREATE (redirect ke step 1 pengajuan baru)
    // ============================================================
    
    public function create()
    {
        return view('admin_satker.pengajuan.wizard.step1-create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
        ], [
            'judul_pengajuan.required' => 'Judul pengajuan tidak boleh kosong.',
        ]);
    
        $pengajuan = PengajuanLelang::create([
            'satker_id'       => auth()->user()->satker_id,
            'judul_pengajuan' => $request->judul_pengajuan,
            'status'          => 'draft',
        ]);
    
        return redirect()->route('satker.pengajuan.step1', $pengajuan)
            ->with('success', 'Pengajuan berhasil dibuat. Lengkapi dokumen pengajuan.');
    }

    public function uploadDokumenPengajuan(Request $request, PengajuanLelang $pengajuan)
    {
        $request->validate([
            'sk_panitia'            => 'nullable|file|mimes:pdf|max:2048',
            'izin_penjualan'        => 'nullable|file|mimes:pdf|max:2048',
            'surat_penetapan_harga' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            '*.mimes' => 'File harus berformat PDF.',
            '*.max'   => 'Ukuran file maksimal 2MB.',
        ]);

        // Pastikan minimal 1 file diupload
        if (!$request->hasFile('files.sk_panitia') &&
            !$request->hasFile('files.izin_penjualan') &&
            !$request->hasFile('files.surat_penetapan_harga')) {
            return back()->with('error', 'Pilih minimal 1 file untuk diupload.');
        }

        $namaSatker = Str::slug($pengajuan->satker->nama_satker);
        $uploaded   = [];

        $jenisList = ['sk_panitia', 'izin_penjualan', 'surat_penetapan_harga'];

        foreach ($jenisList as $jenis) {
            if (!$request->hasFile('files.' . $jenis)) continue;

            // Cek duplikat
            $sudahAda = DokumenPengajuan::where('pengajuan_lelang_id', $pengajuan->id)
                ->where('jenis', $jenis)
                ->exists();

            if ($sudahAda) {
                $label = Str::of($jenis)->replace('_', ' ')->title();
                return back()->with('error', $label . ' sudah diupload sebelumnya.');
            }

            $file     = $request->file('files.' . $jenis);
            $ext      = $file->getClientOriginalExtension();
            $namaFile = $jenis . '_' . $namaSatker . '_' . time() . '.' . $ext;
            $path     = $file->storeAs('pengajuan', $namaFile, 'public');

            DokumenPengajuan::create([
                'pengajuan_lelang_id' => $pengajuan->id,
                'jenis'               => $jenis,
                'file_path'           => $path,
            ]);

            $uploaded[] = Str::of($jenis)->replace('_', ' ')->title();
        }

        $namaUpload = implode(', ', $uploaded);
        return back()->with('success', $namaUpload . ' berhasil diupload.');
    }

    public function destroyDokumenPengajuan(DokumenPengajuan $dokumen)
    {
        // hapus file dari storage
        if (Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        // hapus dari database
        $dokumen->delete();

        return back()->with('success', 'Dokumen berhasil dihapus');
    }

    // =========================
    // DETAIL
    // =========================
    public function show(PengajuanLelang $pengajuan)
    {
        $this->authorize('view', $pengajuan);

        return view('admin.pengajuan.show', compact('pengajuan'));
    }
    
    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, PengajuanLelang $pengajuan)
    {
        $this->authorize('update', $pengajuan);

        $pengajuan->update([
            'judul_pengajuan' => $request->judul_pengajuan
        ]);

        return redirect()->route('satker.pengajuan.index')
            ->with('success', 'Pengajuan berhasil diupdate');
    }

    // =========================
    // DELETE
    // =========================
    public function destroy(PengajuanLelang $pengajuan)
    {
        $pengajuan->load([
            'dokumenPengajuan',
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

        return back()->with('success', 'Pengajuan dan semua dokumen berhasil dihapus');
    }

    // =========================
    // SUBMIT
    // =========================
    public function submit(PengajuanLelang $pengajuan)
    {
        $this->authorize('update', $pengajuan);

        if (!in_array($pengajuan->status, ['draft', 'revision'])) {
            return back()->with('error', 'Pengajuan tidak dapat dikirim ulang.');
        }

        // Cek dokumen pengajuan wajib lengkap
        $dokumen      = $pengajuan->dokumenPengajuan;
        $jenisDokumen = $dokumen->pluck('jenis')->toArray();
        $wajib        = ['sk_panitia', 'izin_penjualan', 'surat_penetapan_harga'];
        $kurang       = array_diff($wajib, $jenisDokumen);

        if (!empty($kurang)) {
            $label = [
                'sk_panitia'            => 'SK Panitia',
                'izin_penjualan'        => 'Izin Penjualan',
                'surat_penetapan_harga' => 'Surat Penetapan Harga Limit',
            ];
            $kurangLabel = implode(', ', array_map(fn($k) => $label[$k], $kurang));
            return back()->with('error', 'Dokumen belum lengkap: ' . $kurangLabel);
        }

        // Cek minimal ada 1 perkara
        if ($pengajuan->perkaras()->count() === 0) {
            return back()->with('error', 'Minimal harus ada 1 data perkara.');
        }

        // Cek setiap perkara punya minimal 1 dokumen
        $perkaraTanpaDokumen = $pengajuan->perkaras()
            ->whereDoesntHave('dokumenPerkara')
            ->pluck('nomor_perkara');

        if ($perkaraTanpaDokumen->isNotEmpty()) {
            return back()->with('error',
                'Perkara berikut belum ada dokumennya: ' . $perkaraTanpaDokumen->implode(', '));
        }

        // ✅ Cek barang — dengan pengecualian perkara gabungan
        $perkaras        = $pengajuan->perkaras()->with('barangs')->get();
        $totalBarangSemua = $perkaras->sum(fn($p) => $p->barangs->count());

        // Jika tidak ada barang sama sekali → tolak
        if ($totalBarangSemua === 0) {
            return back()->with('error', 'Minimal harus ada 1 barang dalam pengajuan.');
        }

        // Cek perkara yang tidak punya barang
        $perkaraTanpaBarang = $perkaras->filter(fn($p) => $p->barangs->count() === 0);

        if ($perkaraTanpaBarang->isNotEmpty()) {

            // Cek apakah ada barang gabungan di perkara lain
            // (barang dengan catatan_internal terisi = barang gabungan)
            $adaBarangGabungan = $perkaras->some(
                fn($p) => $p->barangs->some(
                    fn($b) => !empty($b->catatan_internal)
                )
            );

            if (!$adaBarangGabungan) {
                // Tidak ada barang gabungan → wajib tiap perkara punya barang
                $nomorPerkara = $perkaraTanpaBarang->pluck('nomor_perkara')->implode(', ');
                return back()->with('error',
                    'Perkara berikut belum ada barangnya: ' . $nomorPerkara .
                    '. Jika barang merupakan gabungan perkara lain, isi kolom Catatan Internal pada barang tersebut.');
            }

            // Ada barang gabungan → izinkan submit tapi beri warning
            $pengajuan->update([
                'status'            => 'submitted',
                'tanggal_pengajuan' => now(),
                'catatan_revisi'    => $pengajuan->catatan_revisi, // preserve riwayat
            ]);

            return back()->with('success',
                'Pengajuan berhasil dikirim. Catatan: ' .
                $perkaraTanpaBarang->count() .
                ' perkara tidak memiliki barang tersendiri (diasumsikan barang gabungan).');
        }

        // Semua perkara punya barang → submit normal
        $pengajuan->update([
            'status'            => 'submitted',
            'tanggal_pengajuan' => now(),
        ]);

        return back()->with('success', 'Pengajuan berhasil dikirim ke Admin Pusat.');
    }

    // ============================================================
    // STEP 1 — Info Pengajuan & Dokumen
    // ============================================================
    
    public function step1(PengajuanLelang $pengajuan)
    {
        $this->authorize('view', $pengajuan);
    
        $pengajuan->load('dokumenPengajuan');
        $steps = $this->stepStatus($pengajuan);
    
        return view('admin.pengajuan.wizard.step1', compact('pengajuan', 'steps'));
    }
    
    public function saveStep1(Request $request, PengajuanLelang $pengajuan)
    {
        $this->authorize('update', $pengajuan);
    
        $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
        ], [
            'judul_pengajuan.required' => 'Judul pengajuan tidak boleh kosong.',
        ]);
    
        $pengajuan->update([
            'judul_pengajuan' => $request->judul_pengajuan,
        ]);
    
        // Cek apakah step 1 sudah lengkap untuk navigasi otomatis
        $steps = $this->stepStatus($pengajuan->fresh(['dokumenPengajuan']));
    
        if ($steps[1]) {
            return redirect()->route('satker.pengajuan.step2', $pengajuan)
                ->with('success', 'Info pengajuan disimpan. Lanjut ke Perkara.');
        }
    
        return back()->with('success', 'Judul pengajuan berhasil disimpan. Lengkapi dokumen untuk melanjutkan.');
    }
    
    // ============================================================
    // STEP 2 — Perkara & Dokumen Perkara
    // ============================================================
    
    public function step2(PengajuanLelang $pengajuan)
    {
        $this->authorize('view', $pengajuan);
    
        // Guard: step 1 harus lengkap dulu
        $steps = $this->stepStatus($pengajuan->load('dokumenPengajuan'));
        if (!$steps[1]) {
            return redirect()->route('satker.pengajuan.step1', $pengajuan)
                ->with('error', 'Lengkapi info dan dokumen pengajuan terlebih dahulu.');
        }
    
        $pengajuan->load([
            'perkaras.dokumenPerkara',
            'perkaras.barangs',
        ]);
    
        return view('admin.pengajuan.wizard.step2', compact('pengajuan', 'steps'));
    }
    
    public function saveStep2(Request $request, PengajuanLelang $pengajuan)
    {
        // Ganti authorize dengan pengecekan manual
        $user = auth()->user();

        if ($user->role === 'admin_satker' && $user->satker_id !== $pengajuan->satker_id) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        // Step 2 tidak ada form khusus
        $pengajuan->load(['dokumenPengajuan', 'perkaras.dokumenPerkara', 'perkaras.barangs']);
        $steps = $this->stepStatus($pengajuan);

        if (!$steps[2]) {
            return back()->with('error', 'Pastikan minimal ada 1 perkara dan setiap perkara memiliki dokumen.');
        }

        return redirect()->route('satker.pengajuan.step3', $pengajuan)
            ->with('success', 'Lanjut ke input barang.');
    }
    
    // ============================================================
    // STEP 3 — Barang & Foto
    // ============================================================
    
    public function step3(PengajuanLelang $pengajuan)
    {
        $this->authorize('view', $pengajuan);
    
        // Guard: step 2 harus lengkap dulu
        $pengajuan->load(['dokumenPengajuan', 'perkaras.dokumenPerkara', 'perkaras.barangs']);
        $steps = $this->stepStatus($pengajuan);
    
        if (!$steps[2]) {
            return redirect()->route('satker.pengajuan.step2', $pengajuan)
                ->with('error', 'Lengkapi data perkara terlebih dahulu.');
        }
    
        $pengajuan->load([
            'perkaras.barangs.fotoBarang',
            'perkaras.dokumenPerkara',
        ]);
    
        $canEditSatker = in_array($pengajuan->status, ['draft', 'revision']);
    
        return view('admin.pengajuan.wizard.step3', compact('pengajuan', 'steps', 'canEditSatker'));
    }
    
    // ============================================================
    // STEP 4 — Review & Submit
    // ============================================================
    
    public function step4(PengajuanLelang $pengajuan)
    {
        $this->authorize('view', $pengajuan);
    
        // Guard: step 3 harus lengkap dulu
        $pengajuan->load([
            'dokumenPengajuan',
            'perkaras.dokumenPerkara',
            'perkaras.barangs.fotoBarang',
        ]);
        $steps = $this->stepStatus($pengajuan);
    
        if (!$steps[3]) {
            return redirect()->route('satker.pengajuan.step3', $pengajuan)
                ->with('error', 'Pastikan minimal ada 1 barang dalam pengajuan.');
        }
    
        return view('admin.pengajuan.wizard.step4', compact('pengajuan', 'steps'));
    }

}