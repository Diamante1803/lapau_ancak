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
            'total'     => Lelang::whereIn('status', ['scheduled', 'active', 'closed'])->count(),
            'aktif'     => Lelang::where('status', 'active')->count(),
            'mendatang' => Lelang::where('status', 'scheduled')->count(),
        ];

        $gracePeriodDays = 2; // sama dengan GRACE_PERIOD_DAYS di JS
        $graceLimit = now()->subDays($gracePeriodDays);

        $lelangsAktif = Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
            ])
            ->where(function ($q) use ($graceLimit) {
                $q->where('status', 'active')
                ->orWhere(function ($q2) use ($graceLimit) {
                    $q2->where('status', 'closed')
                        ->where('tanggal_selesai', '>=', $graceLimit);
                });
            })
            ->orderByRaw("
                CASE 
                    WHEN status = 'active' THEN 1
                    WHEN status = 'closed' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('tanggal_selesai', 'asc')
            ->get();

        $lelangsMendatang = Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
            ])
            ->where('status', 'scheduled')
            ->orderBy('tanggal_mulai', 'asc')
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