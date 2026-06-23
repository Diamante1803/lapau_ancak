<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use App\Models\Perkara;
use App\Models\Barang;
use App\Models\FotoBarang;
use App\Http\Controllers\AuditLogController;

class BarangController extends Controller
{
    use AuthorizesRequests;
    // =========================
    // BARANG
    // =========================
    public function storeBarang(Request $request, Perkara $perkara)
    {
        $this->authorize('create', [Barang::class, $perkara]);

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

        AuditLogController::log($perkara->pengajuan_lelang_id, 'Barang', 'created', "Menambahkan barang baru: {$request->nama_barang}");
    
        return back()->with('success', 'Barang berhasil ditambahkan.');
    }
    
    
    // ============================================================
    // BarangController.php — method update()
    // ============================================================
    
    public function update(Request $request, Barang $barang)
    {
        $this->authorize('update', $barang);

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
    
        AuditLogController::log($barang->id, 'Barang', 'updated', "Memperbarui data barang: {$barang->nama_barang}");

        return back()->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        $this->authorize('update', $barang);

        // optional: hapus foto juga
        foreach ($barang->fotoBarang ?? [] as $foto) {
            if ($foto->file_path) Storage::disk('public')->delete($foto->file_path);
            $foto->delete();
        }

        AuditLogController::log($barang->id, 'Barang', 'deleted', "Menghapus barang: {$barang->nama_barang}");

        $barang->delete();

        return back()->with('success', 'Barang berhasil dihapus');
    }

    // =========================
    // FOTO
    // =========================
    public function uploadFotoBarang(Request $request, Barang $barang)
    {
        $this->authorize('update', $barang);

        if (!$request->hasFile('foto')) {
            return back()->with('error', 'Tidak ada file.');
        }

        $request->validate([
            'foto'   => 'required|array',
            'foto.*' => 'file|mimes:jpg,jpeg,png|max:5120', // max 5MB sebelum dikompres
        ], [
            'foto.*.mimes' => 'Format foto harus JPG atau PNG.',
            'foto.*.max'   => 'Ukuran foto maksimal 5MB.',
        ]);

        $jumlahLama = $barang->fotoBarang()->count();
        $jumlahBaru = count($request->file('foto'));

        if (($jumlahLama + $jumlahBaru) > 5) {
            return back()->with('error', 'Maksimal 5 foto per barang. Saat ini sudah ada ' . $jumlahLama . ' foto.');
        }

        $namaBarang = Str::slug($barang->nama_barang);

        // Batas ukuran aman — jika di bawah ini langsung upload tanpa kompres
        $batasAman = 800 * 1024; // 800KB

        // Target ukuran setelah kompres
        $maxWidth  = 1920; // px
        $maxHeight = 1080; // px
        $quality   = 80;   // % kualitas JPG

        foreach ($request->file('foto') as $index => $file) {
            $ukuranAsli = $file->getSize();
            $ext        = strtolower($file->getClientOriginalExtension());
            $fileName   = $namaBarang . '_' . ($jumlahLama + $index + 1) . '_' . time() . '.jpg'; // selalu simpan sebagai jpg
            $path       = 'foto_barang/' . $fileName;

            if ($ukuranAsli <= $batasAman) {
                // ✅ Ukuran aman — upload langsung tanpa kompres
                $file->storeAs('foto_barang', $fileName, 'public');
            } else {
                // 🔄 Ukuran terlalu besar — kompres dulu
                $image = Image::read($file);

                // Resize jika lebar atau tinggi melebihi batas
                // scale down proporsional, tidak distorsi
                if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                    $image->scaleDown($maxWidth, $maxHeight);
                }

                // Simpan ke storage dengan kualitas terkompresi
                Storage::disk('public')->put(
                    $path,
                    $image->toJpeg($quality)
                );
            }

            FotoBarang::create([
                'barang_id' => $barang->id,
                'file_path' => $path,
            ]);
        }

        AuditLogController::log($barang->id, 'Barang', 'updated', "Upload foto baru untuk barang: {$barang->nama_barang}");

        return back()->with('success', 'Foto berhasil diupload.');
    }


    public function destroyFoto($id)
    {
        $foto = FotoBarang::findOrFail($id);
        $this->authorize('update', $foto->barang);

        // hapus file fisik
        if (Storage::disk('public')->exists($foto->file_path)) {
            Storage::disk('public')->delete($foto->file_path);
        }

        $foto->delete();

        return back()->with('success', 'Foto berhasil dihapus');
    }

}
