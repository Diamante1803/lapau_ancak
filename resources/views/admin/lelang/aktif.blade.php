@extends('layouts.admin')

@section('content')

@php
    $isPusat  = auth()->user()->role === 'admin_pusat';
    $isSatker = auth()->user()->role === 'admin_satker';
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

    {{-- ================= ALERT ================= --}}
    @if(session('success'))
    <div id="autoAlert" class="alert alert-success alert-dismissible fade show shadow-sm"
        style="border-left: 4px solid #1a6b3c; border-radius: 8px;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <script>
        setTimeout(function () {
            let a = document.getElementById('autoAlert');
            if (a) { a.style.transition = 'opacity 0.5s'; a.style.opacity = '0'; setTimeout(() => a.remove(), 500); }
        }, 4000);
    </script>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm"
        style="border-left: 4px solid #e74a3b; border-radius: 8px;">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    {{-- ================= STAT MINI CARDS ================= --}}
    <div class="row mb-4">

        {{-- Total Aktif --}}
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm h-100 py-2"
                style="border: none; border-radius: 12px; border-left: 4px solid #1a6b3c;">
                <div class="card-body d-flex align-items-center">
                    <div class="mr-3">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #1a6b3c;">Lelang Berjalan</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $lelangs->count() }}</div>
                    </div>
                    <div class="ml-auto">
                        <div style="width:42px;height:42px;border-radius:50%;background:rgba(26,107,60,0.1);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-gavel" style="color:#1a6b3c;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Segera Berakhir (< 24 jam) --}}
        <div class="col-md-3 col-sm-6 mb-3">
            @php
                $segeraBerakhir = $lelangs->filter(function($l) {
                    return now()->diffInHours($l->tanggal_selesai, false) <= 24
                        && now()->diffInHours($l->tanggal_selesai, false) >= 0;
                })->count();
            @endphp
            <div class="card shadow-sm h-100 py-2"
                style="border: none; border-radius: 12px; border-left: 4px solid #f6c90e;">
                <div class="card-body d-flex align-items-center">
                    <div class="mr-3">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #856404;">Segera Berakhir</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $segeraBerakhir }}</div>
                        <div class="text-xs text-muted">Sisa &lt; 24 jam</div>
                    </div>
                    <div class="ml-auto">
                        <div style="width:42px;height:42px;border-radius:50%;background:rgba(246,201,14,0.12);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-hourglass-half" style="color:#f6c90e;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Nilai Terkini --}}
        <div class="col-md-3 col-sm-6 mb-3">
            @php
                $totalNilai = $lelangs->sum(function($l) {
                    return $l->penawarans->max('harga_tawar') ?? $l->harga_awal;
                });
            @endphp
            <div class="card shadow-sm h-100 py-2"
                style="border: none; border-radius: 12px; border-left: 4px solid #36b9cc;">
                <div class="card-body d-flex align-items-center">
                    <div class="mr-3">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #36b9cc;">Total Nilai Terkini</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format($totalNilai, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="ml-auto">
                        <div style="width:42px;height:42px;border-radius:50%;background:rgba(54,185,204,0.1);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-money-bill-wave" style="color:#36b9cc;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Penawaran Masuk --}}
        <div class="col-md-3 col-sm-6 mb-3">
            @php
                $totalPenawaran = $lelangs->sum(fn($l) => $l->penawarans->count());
            @endphp
            <div class="card shadow-sm h-100 py-2"
                style="border: none; border-radius: 12px; border-left: 4px solid #8b1a1a;">
                <div class="card-body d-flex align-items-center">
                    <div class="mr-3">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #8b1a1a;">Total Penawaran</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalPenawaran }}</div>
                        <div class="text-xs text-muted">Dari semua barang</div>
                    </div>
                    <div class="ml-auto">
                        <div style="width:42px;height:42px;border-radius:50%;background:rgba(139,26,26,0.08);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-hand-paper" style="color:#8b1a1a;"></i>
                        </div>
                    </div>
                </div>
            </div>
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
            style="background: #f8fff9; border-top: 1px solid #e0eeea;">
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

{{-- ================= SCRIPTS ================= --}}
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.85; transform: scale(1.03); }
}
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