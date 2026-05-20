<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Models\Barang;
use App\Models\Satker;

class SatkerController extends Controller
{
    //
    use AuthorizesRequests;
    public function index()
    {
        $satkers = Satker::with('users')->latest()->get();

        return view('admin.satker', compact('satkers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satker' => 'required',
            'alamat'      => 'nullable|string|max:255', 
        ]);

        Satker::create([
            'nama_satker' => $request->nama_satker,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.satker.index')
            ->with('success', 'Satker berhasil ditambahkan');
    }

    public function update(Request $request, Satker $satker)
    {
        $request->validate([
            'nama_satker' => 'required',
            'alamat' => 'required',
        ]);

        $satker->update([
            'nama_satker' => $request->nama_satker,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.satker.index')
            ->with('success', 'Satker berhasil diperbarui');
    }

    public function destroy(Satker $satker)
    {
        $satker->delete();

        return redirect()->route('admin.satker.index')
            ->with('success', 'Satker berhasil dihapus');
    }

}
