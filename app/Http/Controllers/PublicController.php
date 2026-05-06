<?php

namespace App\Http\Controllers;

use App\Models\Lelang;
use App\Models\Satker;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $satkers = Satker::has('pengajuans')->get();

        $stats = [
            'total'      => Lelang::whereIn('status', ['scheduled', 'active', 'closed'])->count(),
            'aktif'      => Lelang::where('status', 'active')->count(),
            'mendatang'  => Lelang::where('status', 'scheduled')->count(), // ✅ ganti terjual
        ];

        $lelangsAktif = Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
            ])
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        // ✅ Tambah query lelang mendatang
        $lelangsMendatang = Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
            ])
            ->where('status', 'scheduled')
            ->orderBy('tanggal_mulai', 'asc')
            ->take(6)
            ->get();

        return view('public.index', compact('satkers', 'stats', 'lelangsAktif', 'lelangsMendatang'));
    }

    public function detail(Lelang $lelang)
    {
        $lelang->load([
            'barang.fotoBarang',
            'barang.perkara.pengajuan.satker',
            'penawarans.pembeli',
        ]);

        return view('public.detail', compact('lelang'));
    }
}