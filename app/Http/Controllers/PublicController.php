<?php

namespace App\Http\Controllers;

use App\Models\Lelang;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    public function index()
    {
        // 1. Cache Daftar Satker (60 Menit)
        $satkers = Cache::remember('public_satkers_list', now()->addHours(1), function() {
            return Satker::has('pengajuans')->get();
        });

        // 2. Cache Statistik Utama (30 Menit)
        $stats = Cache::remember('public_index_stats', now()->addMinutes(30), function() {
            return [
                'total'     => Lelang::whereIn('status', ['scheduled', 'active', 'closed'])->count(),
                'aktif'     => Lelang::where('status', 'active')->count(),
                'mendatang' => Lelang::where('status', 'scheduled')->count(),
            ];
        });

        $gracePeriodDays = 2; // sama dengan GRACE_PERIOD_DAYS di JS
        $graceLimit = now()->subDays($gracePeriodDays);

        // 3. Cache Lelang Aktif & Grace Period (10 Menit)
        $lelangsAktif = Cache::remember('public_lelangs_aktif', now()->addMinutes(10), function() use ($graceLimit) {
            return Lelang::with([
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
        });

        // 4. Cache Lelang Mendatang (30 Menit)
        $lelangsMendatang = Cache::remember('public_lelangs_mendatang', now()->addMinutes(30), function() {
            return Lelang::with([
                'barang.fotoBarang',
                'barang.perkara.pengajuan.satker',
            ])
            ->where('status', 'scheduled')
            ->orderBy('tanggal_mulai', 'asc')
            ->get();
        });

        return view('public.index', compact('satkers', 'stats', 'lelangsAktif', 'lelangsMendatang'));
    }

    public function satker(Satker $satker)
    {
        $satkers = Satker::has('pengajuans')->get();

        $baseQuery = Lelang::whereHas('barang.perkara.pengajuan', function ($q) use ($satker) {
            $q->where('satker_id', $satker->id);
        });

        $stats = [
            'total'     => (clone $baseQuery)->whereIn('status', ['scheduled', 'active', 'closed'])->count(),
            'aktif'     => (clone $baseQuery)->where('status', 'active')->count(),
            'mendatang' => (clone $baseQuery)->where('status', 'scheduled')->count(),
        ];

        $gracePeriodDays = 2;
        $graceLimit = now()->subDays($gracePeriodDays);

        $lelangsAktif = (clone $baseQuery)
            ->with([
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

        $lelangsMendatang = (clone $baseQuery)
            ->with([
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
