@extends('layouts.admin')

@section('content')

@php
    $isPusat  = auth()->user()->role === 'admin_pusat';
    $isSatker = auth()->user()->role === 'admin_satker';
    $lelangs = $lelangs->filter(fn($l) => now()->lessThan($l->tanggal_selesai));

    // Detail penawar untuk modal
    $biddersDetail = $lelangs->flatMap->penawarans
        ->groupBy('pembeli_id')
        ->map(function ($bids) {
            $pembeli = $bids->first()->pembeli;
            return [
                'nama'  => $pembeli->nama ?? 'Anonim',
                'kontak'=> ($pembeli->no_hp ?? '-') . ' / ' . ($pembeli->email ?? '-'),
                'items' => $bids->map(fn($b) => $b->lelang->barang->nama_barang)->unique(),
                'total_bids' => $bids->count()
            ];
        });
@endphp

<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0 font-weight-bold" style="color: #1a6b3c;">
                <i class="fas fa-gavel mr-2" style="color: #f6c90e;"></i>
                Lelang Aktif
            </h1>
            <small class="text-muted">Barang yang sedang dalam proses lelang secara real-time</small>
        </div>

        <div class="d-flex align-items-center mt-2 mt-sm-0" style="gap: 8px;">
            {{-- Badge total aktif --}}
            <span class="badge px-3 py-2"
                style="background: linear-gradient(135deg, #1a6b3c, #145c32); color: white; border-radius: 8px; font-size: 0.82rem;">
                <i class="fas fa-circle mr-1" style="color: #4ade80; font-size: 0.5rem; vertical-align: middle;"></i>
                {{ $lelangs->count() }} barang aktif
            </span>

            {{-- Refresh --}}
            <button onclick="window.location.reload()"
                class="btn btn-sm"
                style="background: rgba(26,107,60,0.1); color: #1a6b3c; border: 1px solid #1a6b3c; border-radius: 8px;">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
        </div>
    </div>

    {{-- ================= STAT MINI CARDS ================= --}}
    <div class="row mb-4 stat-cards-wrapper">

        {{-- Total Aktif --}}
        <div class="col-md-3 col-sm-6 mb-3">
            <x-statistic-card
                title="Lelang Berjalan"
                value="{{ number_format($lelangs->count(), 0, ',', '.') }}"
                unit="Lot"
                icon="fa-gavel"
                color="#1a6b3c"
            >
                <x-slot:badge>
                    <span class="d-inline-flex align-items-center" style="gap:4px;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;animation:pulse 1.5s infinite;display:inline-block;"></span>
                        LIVE
                    </span>
                </x-slot:badge>
            </x-statistic-card>
        </div>

        {{-- Segera Berakhir (< 24 jam) --}}
        <div class="col-md-3 col-sm-6 mb-3">
            @php
                $urgentLelangs = $lelangs->filter(function($l) {
                    return now()->diffInHours($l->tanggal_selesai, false) <= 24;
                });
                $segeraBerakhir = $urgentLelangs->count();
            @endphp
            <x-statistic-card
                title="Segera Berakhir"
                value="{{ number_format($segeraBerakhir, 0, ',', '.') }}"
                unit="Lot"
                icon="fa-hourglass-half"
                color="#f59e0b"
            >
                @if($segeraBerakhir > 0)
                    <x-slot:badge>Urgent</x-slot:badge>
                @endif
            </x-statistic-card>
        </div>

        {{-- Total Nilai Terkini --}}
        <div class="col-md-3 col-sm-6 mb-3">
            @php
                // Menjumlahkan nilai penawaran tertinggi dari lelang yang aktif (mengabaikan harga limit jika belum ada bid)
                $totalValuasiAktif = $lelangs->sum(function($l) {
                    return $l->penawarans->max('nilai_penawaran') ?? 0;
                });
            @endphp
            <x-statistic-card
                title="Total Penawaran Tertinggi"
                value="Rp {{ number_format($totalValuasiAktif, 0, ',', '.') }}"
                icon="fa-money-bill-wave"
                color="#0284c7"
            />
        </div>

        {{-- Total Penawar (Partisipan) --}}
        <div class="col-md-3 col-sm-6 mb-3" style="cursor: pointer;" data-toggle="modal" data-target="#modalDetailPenawar" 
            title="Klik untuk melihat detail partisipan penawar">
            <x-statistic-card
                title="Total Penawar"
                value="{{ number_format($biddersDetail->count(), 0, ',', '.') }}"
                unit="Orang"
                icon="fa-users"
                color="#991b1b"
            />
        </div>

    </div>

    {{-- ================= TABEL LELANG AKTIF ================= --}}
    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">

        <div class="card-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-table mr-2" style="color: #f6c90e;"></i>
                Daftar Barang Lelang Aktif
            </h6>
            <small class="text-white" style="opacity: 0.6;">
                <i class="fas fa-clock mr-1"></i>
                Update terakhir: {{ now()->format('H:i') }} WIB
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabelLelangAktif" class="table table-hover mb-0">
                    <thead style="background: #f8fff9;">
                        <tr>
                            <th class="border-0 pl-4" style="color:#1a6b3c;font-size:0.8rem;" width="4%">No</th>
                            <th class="border-0" style="color:#1a6b3c;font-size:0.8rem;">Nama Barang</th>
                            <th class="border-0" style="color:#1a6b3c;font-size:0.8rem;">Tersangka</th>
                            <th class="border-0" style="color:#1a6b3c;font-size:0.8rem;">Satker</th>
                            <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;">Harga Awal</th>
                            <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;">Penawaran Tertinggi</th>
                            <th class="border-0" style="color:#1a6b3c;font-size:0.8rem;">Penawar Tertinggi</th>
                            <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;">Jumlah Penawaran</th>
                            <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;">Sisa Waktu</th>
                            <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;" width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lelangs as $i => $lelang)

                        @php
                            $selesai       = \Carbon\Carbon::parse($lelang->tanggal_selesai);
                            $sisaJam       = now()->diffInHours($selesai, false);
                            $sisaMenit     = now()->diffInMinutes($selesai, false);
                            $hariSisa      = max(0, floor($sisaJam / 24));
                            $jamSisa       = max(0, $sisaJam % 24);
                            $isUrgent      = $sisaJam <= 24 && $sisaJam >= 0;
                            $isExpired     = $sisaMenit < 0;

                            $penawaranTertinggi = $lelang->penawarans->sortByDesc('nilai_penawaran')->first();
                            $hargaTertinggi     = $penawaranTertinggi?->nilai_penawaran ?? null;
                            $nasikhPembeli      = $penawaranTertinggi?->pembeli->nama ?? null;
                            $jumlahPenawaran    = $lelang->penawarans->count();

                            $kenaikan = $hargaTertinggi
                                ? round((($hargaTertinggi - $lelang->harga_awal) / $lelang->harga_awal) * 100, 1)
                                : 0;
                        @endphp

                        <tr style="border-left: 3px solid transparent; transition: 0.2s;
                            {{ $isUrgent && !$isExpired ? 'background: #fffdf0;' : '' }}"
                            onmouseover="this.style.borderLeft='3px solid #1a6b3c'"
                            onmouseout="this.style.borderLeft='3px solid transparent'">

                            <td class="pl-4 align-middle text-muted small">{{ $i + 1 }}</td>

                            {{-- Nama Barang --}}
                            <td class="align-middle">
                                <div class="font-weight-bold" style="color: #1a6b3c; font-size: 0.88rem;">
                                    {{ $lelang->barang->nama_barang }}
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Mulai {{ \Carbon\Carbon::parse($lelang->tanggal_mulai)->format('d M Y') }}
                                </small>
                            </td>

                            {{-- Tersangka --}}
                            <td class="align-middle">
                                <div class="small font-weight-bold" style="color: #8b1a1a;">
                                    <i class="fas fa-user mr-1" style="color: #c9706e; font-size: 0.7rem;"></i>
                                    {{ $lelang->barang->perkara->nama_tersangka ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    {{ $lelang->barang->perkara->nomor_perkara ?? '' }}
                                </small>
                            </td>

                            {{-- Satker --}}
                            <td class="align-middle small text-muted">
                                <i class="fas fa-building mr-1"></i>
                                {{ $lelang->barang->perkara->pengajuan->satker->nama_satker ?? '-' }}
                            </td>

                            {{-- Harga Awal --}}
                            <td class="align-middle text-center">
                                <span class="small text-muted">
                                    Rp {{ number_format($lelang->harga_awal, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Penawaran Tertinggi --}}
                            <td class="align-middle text-center">
                                @if($hargaTertinggi)
                                <div class="font-weight-bold" style="color: #1a6b3c; font-size: 0.9rem;">
                                    Rp {{ number_format($hargaTertinggi, 0, ',', '.') }}
                                </div>
                                @if($kenaikan > 0)
                                <span class="badge" style="background: rgba(26,107,60,0.1); color: #1a6b3c; border-radius: 20px; font-size: 0.65rem;">
                                    <i class="fas fa-arrow-up mr-1"></i>+{{ $kenaikan }}%
                                </span>
                                @endif
                                @else
                                <span class="text-muted small">
                                    <i class="fas fa-minus"></i> Belum ada
                                </span>
                                @endif
                            </td>

                            {{-- Penawar Tertinggi --}}
                            <td class="align-middle">
                                @if($nasikhPembeli)
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#1a6b3c,#2d9e5f);display:flex;align-items:center;justify-content:center;color:white;font-size:0.7rem;font-weight:bold;flex-shrink:0;">
                                        {{ strtoupper(substr($nasikhPembeli, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="small font-weight-bold text-dark">{{ $nasikhPembeli }}</div>
                                        <small class="text-muted">{{ $penawaranTertinggi?->email_pembeli ?? '' }}</small>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted small">
                                    <i class="fas fa-user-slash mr-1"></i>Belum ada
                                </span>
                                @endif
                            </td>

                            {{-- Jumlah Penawaran --}}
                            <td class="align-middle text-center">
                                @if($jumlahPenawaran > 0)
                                <span class="badge badge-pill px-3"
                                    style="background: rgba(26,107,60,0.1); color: #1a6b3c; font-size: 0.78rem;">
                                    {{ $jumlahPenawaran }} penawaran
                                </span>
                                @else
                                <span class="badge badge-pill px-3"
                                    style="background: #f0f0f0; color: #999; font-size: 0.78rem;">
                                    0 penawaran
                                </span>
                                @endif
                            </td>

                            {{-- Sisa Waktu --}}
                            <td class="align-middle text-center">
                                @if($isExpired)
                                <span class="badge badge-pill badge-danger px-3"
                                    style="border-radius: 20px; font-size: 0.75rem;">
                                    <i class="fas fa-times-circle mr-1"></i>Berakhir
                                </span>
                                @elseif($isUrgent)
                                <div class="countdown-badge urgent"
                                    data-end="{{ $selesai->toIso8601String() }}"
                                    style="display:inline-block;">
                                    <span class="badge badge-pill px-3 py-2"
                                        style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border-radius: 20px; font-size: 0.75rem; animation: pulse 1.5s ease-in-out infinite;">
                                        <i class="fas fa-fire mr-1"></i>
                                        <span class="timer-text">{{ $hariSisa }}h {{ $jamSisa }}j</span>
                                    </span>
                                </div>
                                @else
                                <div class="countdown-badge"
                                    data-end="{{ $selesai->toIso8601String() }}">
                                    <span class="badge badge-pill px-3 py-1"
                                        style="background: #e8f5ee; color: #1a6b3c; border-radius: 20px; font-size: 0.75rem; border: 1px solid #b2d8c0;">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span class="timer-text">{{ $hariSisa }}h {{ $jamSisa }}j</span>
                                    </span>
                                </div>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="align-middle text-center">
                                <a href="{{ auth()->user()->role === 'admin_pusat' 
                                    ? route('admin.lelang.detail', $lelang->id) 
                                    : route('satker.lelang.detail', $lelang->id) }}"
                                    class="btn btn-sm"
                                    style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px; width: 34px;"
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-gavel fa-3x mb-3 d-block" style="color: #d1e7d8;"></i>
                                <span class="d-block font-weight-bold mb-1">Tidak ada lelang aktif saat ini</span>
                                <small>Lelang akan muncul setelah Admin Pusat menjadwalkan barang</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer card --}}
        @if($lelangs->count() > 0)
        <div class="card-footer py-2 px-4 d-flex justify-content-between align-items-center"
            style="background: #f8fff9; border-top: 1px solid #e0eeea; border-radius: 0 0 12px 12px;">
            <small class="text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Halaman ini menampilkan semua barang dengan status <strong>aktif</strong>
            </small>
            <small class="text-muted">
                <i class="fas fa-sync-alt mr-1"></i>
                Countdown diperbarui otomatis setiap menit
            </small>
        </div>
        @endif

    </div>

</div>

{{-- Modal Detail Penawar --}}
<div class="modal fade" id="modalDetailPenawar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0" style="padding: 24px;">
                <h5 class="modal-title font-weight-bold" style="color:#1e293b;">
                    <i class="fas fa-users mr-2 text-danger"></i> 
                    Partisipan Penawar Aktif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p class="text-muted small mb-4">Daftar penawar yang sedang aktif berpartisipasi dalam lelang-lelang di atas.</p>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background:#f8fafc; color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:1px;">
                                <th class="border-0 px-3">Penawar</th>
                                <th class="border-0">Barang yang Ditawar</th>
                                <th class="border-0 text-center pr-3">Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($biddersDetail as $detail)
                            <tr>
                                <td class="px-3 align-top">
                                    <div class="font-weight-bold text-dark">{{ $detail['nama'] }}</div>
                                    <div class="text-muted small">{{ $detail['kontak'] }}</div>
                                </td>
                                <td class="align-top">
                                    @foreach($detail['items'] as $itemName)
                                        <div class="small text-dark mb-1">• {{ $itemName }}</div>
                                    @endforeach
                                </td>
                                <td class="text-center align-middle pr-3">
                                    <span class="badge badge-pill bg-success-light text-success px-3">{{ $detail['total_bids'] }} Bid</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada penawaran masuk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding: 24px;">
                <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" style="border-radius:10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= SCRIPTS ================= --}}
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.85; transform: scale(1.03); }
}

.stat-card {
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    background: #ffffff;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px -5px rgba(0,0,0,0.08) !important;
}
.stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
.bg-gradient-success { background: linear-gradient(135deg, #1a6b3c 0%, #22c55e 100%); }
.bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
.bg-gradient-info    { background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); }
.bg-gradient-danger  { background: linear-gradient(135deg, #991b1b 0%, #ef4444 100%); }

.bg-success-light { background: rgba(34, 197, 94, 0.1); }
.bg-warning-light { background: rgba(245, 158, 11, 0.1); }
.border-warning-soft { border: 1px solid rgba(245, 158, 11, 0.2) !important; }

.card-bg-icon {
    position: absolute;
    right: -10px;
    bottom: -10px;
    font-size: 4rem;
    opacity: 0.03;
    transform: rotate(-15deg);
    pointer-events: none;
}
.animate-pulse-soft { animation: pulse-soft 2s infinite; }
@keyframes pulse-soft {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
.font-weight-600 { font-weight: 600; }
.font-weight-800 { font-weight: 800; }
</style>

<script>
// ============================
// Live countdown timer
// ============================
function updateCountdowns() {
    document.querySelectorAll('.countdown-badge').forEach(function(badge) {
        const endTime   = new Date(badge.dataset.end);
        const now       = new Date();
        const diffMs    = endTime - now;
        const timerEl   = badge.querySelector('.timer-text');

        if (!timerEl) return;

        if (diffMs <= 0) {
            badge.innerHTML = `
                <span class="badge badge-pill badge-danger px-3" style="border-radius:20px;font-size:0.75rem;">
                    <i class="fas fa-times-circle mr-1"></i>Berakhir
                </span>`;
            return;
        }

        const totalSecs = Math.floor(diffMs / 1000);
        const hari      = Math.floor(totalSecs / 86400);
        const jam       = Math.floor((totalSecs % 86400) / 3600);
        const menit     = Math.floor((totalSecs % 3600) / 60);
        const detik     = totalSecs % 60;

        if (hari > 0) {
            timerEl.textContent = hari + 'h ' + jam + 'j';
        } else if (jam > 0) {
            timerEl.textContent = jam + 'j ' + menit + 'm';
        } else {
            timerEl.textContent = menit + 'm ' + detik + 'd';
        }
    });
}

// Jalankan langsung & update tiap detik
updateCountdowns();
setInterval(updateCountdowns, 1000);

// ============================
// Auto-refresh halaman tiap 5 menit
// ============================
setTimeout(function() {
    window.location.reload();
}, 5 * 60 * 1000);

document.addEventListener('DOMContentLoaded', function () {
    LapauTable.init('tabelLelangAktif', {
        pageSize:  10,
        sortDir:   'desc',
    });
});
</script>

@endsection