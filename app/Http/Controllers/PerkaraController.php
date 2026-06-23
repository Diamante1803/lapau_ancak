<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use App\Models\PengajuanLelang;
use App\Models\Perkara;
use App\Models\DokumenPerkara;

class PerkaraController extends Controller
{
    use AuthorizesRequests;

 // =========================
    // PERKARA
    // =========================
    public function storePerkara(Request $request, PengajuanLelang $pengajuan)
    {
        $this->authorize('update', $pengajuan);

        $request->validate([
            'nomor_perkara'    => 'required|string|max:255',
            'nama_tersangka'   => 'required|string|max:255',
            'tanggal_putusan'  => 'required|date',
            'dokumen'          => 'nullable|array|max:5',
            'dokumen.*'        => 'file|mimes:pdf|max:2048',
            'nama_dokumen'     => 'nullable|array',
            'nama_dokumen.*'   => 'nullable|string|max:255',
        ]);

        // Simpan perkara
        $perkara = Perkara::create([
            'pengajuan_lelang_id' => $pengajuan->id,
            'nomor_perkara' => $request->nomor_perkara,
            'nama_tersangka' => $request->nama_tersangka,
            'tanggal_putusan' => $request->tanggal_putusan,
        ]);

        // Simpan dokumen
        if ($request->hasFile('dokumen')) {

        $namaSatker = Str::slug($perkara->pengajuan->satker->nama_satker);
        $namaTersangka = Str::slug($perkara->nama_tersangka);

        foreach ($request->file('dokumen') as $i => $file) {

            $ext = $file->getClientOriginalExtension();

            // 🔥 ambil dari input user
            $namaDokumenInput = $request->nama_dokumen[$i] ?? 'dokumen';

            // 🚨 CEK DUPLIKAT DI DATABASE
            $cek = \App\Models\DokumenPerkara::where('perkara_id', $perkara->id)
                ->where('nama_dokumen', $namaDokumenInput)
                ->exists();

            if ($cek) {
                return back()->with('error', $namaDokumenInput . ' sudah diupload!');
            }

            $namaDokumen = Str::slug($namaDokumenInput);

            // format nama file
            $fileName = $namaDokumen . '_' . $namaTersangka . '_' . $namaSatker . '_' . time() . '.' . $ext;

            // simpan file
            $path = $file->storeAs('dokumen_perkara', $fileName, 'public');

            \App\Models\DokumenPerkara::create([
                'perkara_id' => $perkara->id,
                'nama_dokumen' => $namaDokumenInput,
                'file_path' => $path
            ]);
        }
    }

        return back()->with('success', 'Perkara ditambahkan');
    }

    public function updatePerkara(Request $request, Perkara $perkara)
    {
        $this->authorize('update', $perkara->pengajuan);

        $request->validate([
            'nomor_perkara' => 'required',
            'nama_tersangka' => 'required',
            'tanggal_putusan' => 'required|date',
            'dokumen'         => 'nullable|array|max:5',
            'dokumen.*'       => 'file|mimes:pdf|max:2048',
            'nama_dokumen'    => 'nullable|array',
            'nama_dokumen.*'  => 'nullable|string|max:255',
        ]);

        // ✅ update data perkara
        $perkara->update([
            'nomor_perkara' => $request->nomor_perkara,
            'nama_tersangka' => $request->nama_tersangka,
            'tanggal_putusan' => $request->tanggal_putusan,
        ]);

        // ✅ upload dokumen baru (sama seperti create)
        if ($request->hasFile('dokumen')) {

        $namaSatker = Str::slug($perkara->pengajuan->satker->nama_satker);
        $namaTersangka = Str::slug($perkara->nama_tersangka);

        foreach ($request->file('dokumen') as $i => $file) {

            $ext = $file->getClientOriginalExtension();

            // 🔥 ambil dari input user
            $namaDokumenInput = $request->nama_dokumen[$i] ?? 'dokumen';

            // 🚨 CEK DUPLIKAT DI DATABASE
            $cek = \App\Models\DokumenPerkara::where('perkara_id', $perkara->id)
                ->where('nama_dokumen', $namaDokumenInput)
                ->exists();

            if ($cek) {
                return back()->with('error', $namaDokumenInput . ' sudah diupload!');
            }

            $namaDokumen = Str::slug($namaDokumenInput);

            // format nama file
            $fileName = $namaDokumen . '_' . $namaTersangka . '_' . $namaSatker . '_' . time() . '.' . $ext;

            // simpan file
            $path = $file->storeAs('dokumen_perkara', $fileName, 'public');

            \App\Models\DokumenPerkara::create([
                'perkara_id' => $perkara->id,
                'nama_dokumen' => $namaDokumenInput,
                'file_path' => $path
            ]);
        }
    }

        return back()->with('success', 'Perkara berhasil diupdate');
    }

    public function destroyPerkara(Perkara $perkara)
    {
        $this->authorize('update', $perkara->pengajuan);

        $perkara->delete();

        return back()->with('success', 'Perkara berhasil dihapus');
    }

    public function uploadDokumenPerkara(Request $request, Perkara $perkara)
    {
        $this->authorize('update', $perkara->pengajuan);

        $request->validate([
            'dokumen'         => 'required|array|max:5',
            'dokumen.*'       => 'file|mimes:pdf|max:2048',
            'nama_dokumen'    => 'required|array',
            'nama_dokumen.*'  => 'required|string|max:255',
        ], [
            'dokumen.required'        => 'Minimal 1 dokumen wajib diupload.',
            'dokumen.max'             => 'Maksimal 5 dokumen.',
            'dokumen.*.mimes'         => 'Semua file harus berformat PDF.',
            'dokumen.*.max'           => 'Ukuran file maksimal 2MB.',
            'nama_dokumen.*.required' => 'Nama dokumen wajib diisi.',
        ]);

        $files      = $request->file('dokumen');
        $namaDokumen = $request->input('nama_dokumen');

        foreach ($files as $index => $file) {
            $ext      = $file->getClientOriginalExtension();
            $namaFile = Str::slug($namaDokumen[$index]) . '_' . time() . '.' . $ext;
            $path     = $file->storeAs('dokumen_perkara', $namaFile, 'public');

            DokumenPerkara::create([
                'perkara_id'   => $perkara->id,
                'nama_dokumen' => $namaDokumen[$index],
                'file_path'    => $path,
            ]);
        }

        return back()->with('success', 'Dokumen perkara berhasil diupload.');
    }

    public function destroyDokumenPerkara($id)
    {
        $doc = DokumenPerkara::findOrFail($id);
        $this->authorize('update', $doc->perkara->pengajuan);

        Storage::disk('public')->delete($doc->file_path);

        $doc->delete();

        return back()->with('success', 'Dokumen berhasil dihapus');
    }
}
