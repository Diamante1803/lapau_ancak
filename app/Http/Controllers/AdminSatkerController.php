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

    // =========================
    // FORM CREATE
    // =========================
    public function create()
    {
        return view('admin_satker.pengajuan.create');
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
        'judul_pengajuan' => 'required'
        ]);

        $pengajuan = PengajuanLelang::create([
            'satker_id' => auth()->user()->satker_id,
            'judul_pengajuan' => $request->judul_pengajuan,
            'status' => 'draft'
        ]);

        return redirect()->route('satker.pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan berhasil dibuat');
    }

    public function uploadDokumenPengajuan(Request $request, PengajuanLelang $pengajuan)
    {
        $request->validate([
            'jenis' => 'required|in:sk_panitia,izin_penjualan,surat_penetapan_harga',
            'file' => 'required',
            'file.*' => 'file|mimes:pdf|max:2048' // hapus jpg,jpeg,png
        ], [
            // Custom error message
            'file.required' => 'File wajib diupload.',
            'file.*.mimes' => 'File harus berformat PDF.',
            'file.*.max' => 'Ukuran file maksimal 2MB.',
        ]);

        // 🔥 CEK DUPLIKAT
        $cek = DokumenPengajuan::where('pengajuan_lelang_id', $pengajuan->id)
            ->where('jenis', $request->jenis)
            ->exists();

        if ($cek) {
            $jenisLabel = Str::of($request->jenis)
                ->replace('_', ' ')
                ->title();

            return back()->with('error', $jenisLabel . ' sudah diupload!');
        }

        $namaSatker = Str::slug($pengajuan->satker->nama_satker);
        $jenis = $request->jenis; // contoh: sk_panitia

        foreach ($request->file('file') as $file) {

            $ext = $file->getClientOriginalExtension();

            // format nama file
            $namaFile = $jenis . '_' . $namaSatker . '_' . time() . '.' . $ext;

            // simpan file
            $path = $file->storeAs('pengajuan', $namaFile, 'public');

            DokumenPengajuan::create([
                'pengajuan_lelang_id' => $pengajuan->id,
                'jenis' => $jenis,
                'file_path' => $path
            ]);
        }

        return back()->with('success', 'Dokumen berhasil diupload.');
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
    // EDIT
    // =========================
    public function edit(PengajuanLelang $pengajuan)
    {
        return view('admin_satker.pengajuan.edit', compact('pengajuan'));
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
            ->with('success', 'Pengajuan dan semua dokumen berhasil dihapus');
    }

    // =========================
    // SUBMIT
    // =========================
    public function submit(PengajuanLelang $pengajuan)
    {
        $this->authorize('update', $pengajuan);

        // Cek status harus draft atau revision
        if (!in_array($pengajuan->status, ['draft', 'revision'])) {
            return back()->with('error', 'Pengajuan tidak dapat dikirim ulang.');
        }

        // Cek dokumen pengajuan wajib lengkap (3 dokumen)
        $dokumen = $pengajuan->dokumenPengajuan;
        $jenisDokumen = $dokumen->pluck('jenis')->toArray();
        $wajib = ['sk_panitia', 'izin_penjualan', 'surat_penetapan_harga'];
        $kurang = array_diff($wajib, $jenisDokumen);

        if (!empty($kurang)) {
            $label = [
                'sk_panitia'             => 'SK Panitia',
                'izin_penjualan'         => 'Izin Penjualan',
                'surat_penetapan_harga'  => 'Surat Penetapan Harga Limit',
            ];
            $kurangLabel = implode(', ', array_map(fn($k) => $label[$k], $kurang));
            return back()->with('error', 'Dokumen belum lengkap: ' . $kurangLabel);
        }

        // Cek minimal ada 1 perkara
        if ($pengajuan->perkaras()->count() === 0) {
            return back()->with('error', 'Minimal harus ada 1 data perkara.');
        }

        // Cek setiap perkara punya minimal 1 dokumen perkara
        $perkaraTanpaDokumen = $pengajuan->perkaras()
            ->whereDoesntHave('dokumenPerkara')
            ->pluck('nomor_perkara');

        if ($perkaraTanpaDokumen->isNotEmpty()) {
            return back()->with('error', 
                'Perkara berikut belum ada dokumennya: ' . $perkaraTanpaDokumen->implode(', '));
        }

        // Cek setiap perkara punya minimal 1 barang
        $perkaraTanpaBarang = $pengajuan->perkaras()
            ->whereDoesntHave('barangs')
            ->pluck('nomor_perkara');

        if ($perkaraTanpaBarang->isNotEmpty()) {
            return back()->with('error', 
                'Perkara berikut belum ada barangnya: ' . $perkaraTanpaBarang->implode(', '));
        }

        // Semua validasi lolos — submit
        $pengajuan->update([
            'status'            => 'submitted',
            'tanggal_pengajuan' => now(),
        ]);

        return back()->with('success', 'Pengajuan berhasil dikirim ke Admin Pusat.');
    }

}