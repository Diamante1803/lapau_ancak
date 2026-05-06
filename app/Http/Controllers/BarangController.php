<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use App\Models\Perkara;
use App\Models\Barang;
use App\Models\FotoBarang;

class BarangController extends Controller
{
    use AuthorizesRequests;
    // =========================
    // BARANG
    // =========================
    public function storeBarang(Request $request, Perkara $perkara)
    {
        $request->validate([
            'nama_barang'      => 'required|string|max:255',
            'deskripsi'        => 'nullable|string|max:500',
            'harga_awal'       => 'required|numeric|min:1|max:35000000',
            'catatan_internal' => 'nullable|string|max:1000',
        ], [
            'nama_barang.required' => 'Nama barang tidak boleh kosong.',
            'harga_awal.required'  => 'Harga limit tidak boleh kosong.',
            'harga_awal.min'       => 'Harga limit tidak boleh 0 atau kurang.',
            'harga_awal.max'       => 'Harga limit tidak boleh melebihi Rp 35.000.000.',
        ]);
    
        Barang::create([
            'perkara_id'       => $perkara->id,
            'nama_barang'      => $request->nama_barang,
            'deskripsi'        => $request->deskripsi,
            'harga_awal'       => $request->harga_awal ?? 0,
            'catatan_internal' => $request->catatan_internal,
        ]);
    
        return back()->with('success', 'Barang berhasil ditambahkan.');
    }
    
    
    // ============================================================
    // BarangController.php — method update()
    // ============================================================
    
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang'      => 'required|string|max:255',
            'deskripsi'        => 'nullable|string',
            'harga_awal'       => 'required|numeric|min:0',
            'catatan_internal' => 'nullable|string|max:1000',
            'foto'             => 'nullable|array',
            'foto.*'           => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        $barang->update([
            'nama_barang'      => $request->nama_barang,
            'deskripsi'        => $request->deskripsi,
            'harga_awal'       => $request->harga_awal,
            'catatan_internal' => $request->catatan_internal,
        ]);
    
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $ext      = $foto->getClientOriginalExtension();
                $namaFile = Str::slug($barang->nama_barang) . '_' . time() . '.' . $ext;
                $path     = $foto->storeAs('foto_barang', $namaFile, 'public');
    
                FotoBarang::create([
                    'barang_id' => $barang->id,
                    'file_path' => $path,
                ]);
            }
        }
    
        return back()->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        // optional: hapus foto juga
        foreach ($barang->fotos ?? [] as $foto) {
            if ($foto->path && Storage::exists($foto->path)) {
                Storage::delete($foto->path);
            }
            $foto->delete();
        }

        $barang->delete();

        return back()->with('success', 'Barang berhasil dihapus');
    }

    // =========================
    // FOTO
    // =========================
    public function uploadFotoBarang(Request $request, Barang $barang)
    {
        if (!$request->hasFile('foto')) {
        return back()->with('error', 'Tidak ada file');
    }

    $jumlahLama = $barang->fotoBarang()->count();
    $jumlahBaru = count($request->file('foto'));

    // 🔥 cek total maksimal 5 (gabungan lama + baru)
    if (($jumlahLama + $jumlahBaru) > 5) {
        return back()->with('error', 'Maksimal 5 foto per barang');
    }

    $namaBarang = \Illuminate\Support\Str::slug($barang->nama_barang);

    foreach ($request->file('foto') as $index => $file) {

        $ext = $file->getClientOriginalExtension();

        // 🔥 format nama file: nama_barang_urut_time.ext
        $fileName = $namaBarang . '_' . ($jumlahLama + $index + 1) . '_' . time() . '.' . $ext;

        $path = $file->storeAs('foto_barang', $fileName, 'public');

        FotoBarang::create([
            'barang_id' => $barang->id,
            'file_path' => $path
        ]);
    }

    return back()->with('success', 'Foto berhasil diupload');
    }

    public function destroyFoto($id)
    {
        $foto = FotoBarang::findOrFail($id);

        // hapus file fisik
        if (Storage::disk('public')->exists($foto->file_path)) {
            Storage::disk('public')->delete($foto->file_path);
        }

        $foto->delete();

        return back()->with('success', 'Foto berhasil dihapus');
    }

}
