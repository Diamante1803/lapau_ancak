@extends('layouts.admin')

@section('content')

<div class="container-fluid">

@php
    $isPusat  = auth()->user()->role === 'admin_pusat';
    $isSatker = auth()->user()->role === 'admin_satker';
@endphp

{{-- ================= PAGE HEADING ================= --}}
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4" style="gap: 15px;">
    <div class="flex-shrink-0">
        <h1 class="h3 mb-0 font-weight-bold" style="color: #1a6b3c;">
            <i class="fas fa-tachometer-alt mr-2" style="color: #f6c90e;"></i> Dashboard
        </h1>
        <small class="text-muted">Selamat datang, <strong>{{ auth()->user()->name }}</strong></small>
    </div>

    {{-- ================= FILTER PERIODE (COMPACT) ================= --}}
    <div class="flex-grow-1 d-flex justify-content-center no-print">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex align-items-center bg-white p-2 shadow-sm border" style="border-radius: 16px; gap: 8px;">
            <div class="position-relative" style="width: 160px;">
                <input type="text" name="dari" class="interactive-field datepicker" style="font-size: 0.8rem; padding: 9px 32px 9px 12px;" value="{{ request('dari') }}" placeholder="Mulai">
                <i class="material-icons position-absolute" style="right:10px; top:11px; font-size:18px; color:var(--c-theme-primary); pointer-events: none;">calendar_today</i>
            </div>
            <span class="text-muted small mx-1"><i class="fas fa-chevron-right fa-xs"></i></span>
            <div class="position-relative" style="width: 160px;">
                <input type="text" name="sampai" class="interactive-field datepicker" style="font-size: 0.8rem; padding: 9px 32px 9px 12px;" value="{{ request('sampai') }}" placeholder="Hingga">
                <i class="material-icons position-absolute" style="right:10px; top:11px; font-size:18px; color:var(--c-theme-primary); pointer-events: none;">event_available</i>
            </div>
            <button type="submit" class="btn btn-sm text-white ml-1 shadow-sm" style="background: #1a6b3c; border-radius: 8px; height: 31px; width: 31px;" title="Filter">
                <i class="fas fa-filter fa-xs"></i>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light border ml-1 shadow-sm" style="border-radius: 8px; height: 31px; width: 31px; display: flex; align-items: center; justify-content: center;" title="Reset">
                <i class="fas fa-undo fa-xs"></i>
            </a>
        </form>
    </div>

    <div class="flex-shrink-0 text-right">
        <small class="text-muted d-block">
            <i class="fas fa-clock mr-1"></i>
            {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB
        </small>
        <small class="badge badge-light border text-muted px-2" style="font-size: 0.65rem;">
            {{ $isPusat ? 'Admin Pusat' : (optional(auth()->user()->satker)->nama_satker ?? 'Satker') }}
        </small>
    </div>
</div>

{{-- ================= BARIS 1: KPI CARDS ================= --}}
<div class="row">

    {{-- Total Pengajuan --}}
    <div class="{{ $isPusat ? 'col-xl-2 col-md-4' : 'col-xl-3 col-md-6' }} mb-4">
        <x-statistic-card
            title="Total Pengajuan"
            value="{{ number_format($stats['total_pengajuan']) }}"
            unit="Berkas"
            icon="fa-file-invoice"
            color="#1a6b3c"
            description="<i class='fas fa-building mr-1'></i> {{ $isSatker ? 'Satker Anda' : 'Semua Satker' }}"
        />
    </div>

    {{-- Menunggu Persetujuan (hanya admin pusat) --}}
    @if($isPusat)
    <div class="col-xl-2 col-md-4 mb-4">
        <x-statistic-card
            title="Menunggu"
            value="{{ number_format($stats['menunggu']) }}"
            unit="Berkas"
            icon="fa-hourglass-half"
            color="#d97706"
            description="<i class='fas fa-clock mr-1'></i>Perlu tindak lanjut"
        >
            @if($stats['menunggu'] > 0)
                <x-slot:badge>Perlu Aksi</x-slot:badge>
            @endif
        </x-statistic-card>
    </div>

    {{-- Disetujui --}}
    <div class="col-xl-2 col-md-4 mb-4">
        <x-statistic-card
            title="Disetujui"
            value="{{ number_format($stats['disetujui']) }}"
            unit="Berkas"
            icon="fa-check-circle"
            color="#15803d"
            description="<i class='fas fa-thumbs-up mr-1'></i>Lolos verifikasi"
        />
    </div>
    @endif

    {{-- Lelang Aktif --}}
    <div class="{{ $isPusat ? 'col-xl-2 col-md-4' : 'col-xl-3 col-md-6' }} mb-4">
        <a href="{{ $isPusat ? route('admin.lelang.aktif') : route('satker.lelang.aktif') }}" class="text-decoration-none">
            <x-statistic-card
                title="Lelang Aktif"
                value="{{ number_format($stats['lelang_aktif']) }}"
                unit="Lot"
                icon="fa-gavel"
                color="#0369a1"
                description="<i class='fas fa-fire mr-1'></i>Sedang berlangsung"
            >
                @if($stats['lelang_aktif'] > 0)
                    <x-slot:badge>
                        <span class="d-inline-flex align-items-center" style="gap:4px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;animation:pulse 1.5s infinite;display:inline-block;"></span>
                            LIVE
                        </span>
                    </x-slot:badge>
                @endif
            </x-statistic-card>
        </a>
    </div>

    {{-- Aset Terjual --}}
    <div class="{{ $isPusat ? 'col-xl-2 col-md-4' : 'col-xl-3 col-md-6' }} mb-4" 
        style="cursor: pointer;" data-toggle="modal" data-target="#modalSoldAssets" title="Klik untuk lihat detail aset terlelang">
        <x-statistic-card
            title="Aset Terlelang"
            value="{{ number_format($stats['barang_terjual']) }}"
            unit="Barang"
            icon="fa-box-open"
            color="#7c3aed"
            description="<i class='fas fa-handshake mr-1'></i>Memiliki pemenang"
        />
    </div>

    {{-- Total PNBP --}}
    <div class="{{ $isPusat ? 'col-xl-2 col-md-4' : 'col-xl-3 col-md-6' }} mb-4" 
        style="cursor: pointer;" data-toggle="modal" data-target="#modalUnpaidPNBP" title="Klik untuk lihat detail piutang">
        <x-statistic-card
            title="Penerimaan Negara"
            value="{{ $stats['total_nilai'] >= 1000000000 ? 'Rp ' . number_format($stats['total_nilai']/1000000000, 2, ',', '.') . ' M' : ($stats['total_nilai'] >= 1000000 ? 'Rp ' . number_format($stats['total_nilai']/1000000, 1, ',', '.') . ' Jt' : 'Rp ' . number_format($stats['total_nilai'], 0, ',', '.')) }}"
            icon="fa-wallet"
            color="#a16207"
            description="<i class='fas fa-university mr-1'></i>Kas negara"
        >
            <x-slot:badge>PNBP</x-slot:badge>
        </x-statistic-card>
    </div>

</div>

{{-- ================= BARIS 2: MONITORING SATKER (hanya admin pusat) ================= --}}
@if($isPusat)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-header border-0 d-flex justify-content-between align-items-center"
                style="background:linear-gradient(90deg,#1a6b3c,#145c32);border-radius:16px 16px 0 0;padding:14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-sitemap mr-2" style="color:#f6c90e;"></i>
                    Monitoring Satker
                </h6>
                <span style="background:rgba(255,255,255,0.15);color:white;font-size:0.75rem;padding:3px 10px;border-radius:20px;">
                    {{ $monitoringSatker->count() }} Satker
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8fff9;">
                            <tr>
                                <th class="border-0 pl-4" style="color:#1a6b3c;font-size:0.8rem;">Satker</th>
                                <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;">Pengajuan</th>
                                <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;">Lelang Aktif</th>
                                <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;">Lelang Selesai</th>
                                <th class="border-0 text-center" style="color:#1a6b3c;font-size:0.8rem;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monitoringSatker as $satker)
                            @php
                                $lastAktivitas = $satker->pengajuans->max('updated_at');
                                $hariSejak = $lastAktivitas 
                                    ? \Carbon\Carbon::parse($lastAktivitas)->diffInDays(now()) 
                                    : 999;

                                if ($hariSejak <= 7) {
                                    $statusAktif = ['label' => 'Aktif minggu ini', 'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.1)'];
                                } elseif ($hariSejak <= 30) {
                                    $statusAktif = ['label' => 'Aktif bulan ini', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.1)'];
                                } else {
                                    $statusAktif = ['label' => 'Tidak aktif > 30 hari', 'color' => '#e74a3b', 'bg' => 'rgba(231,74,59,0.1)'];
                                }
                            @endphp
                            <tr style="border-left:3px solid transparent;transition:0.2s;"
                                onmouseover="this.style.borderLeft='3px solid #1a6b3c'"
                                onmouseout="this.style.borderLeft='3px solid transparent'">

                                <td class="pl-4 align-middle">
                                    <div class="d-flex align-items-center" style="gap:10px;">
                                        <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#1a6b3c,#2d9e5f);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                            {{ strtoupper(substr($satker->nama_satker, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold small">{{ $satker->nama_satker }}</div>
                                            <div class="text-muted" style="font-size:0.7rem;">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $satker->alamat ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle text-center">
                                    <span class="font-weight-bold" style="color:#1a6b3c;">
                                        {{ $satker->pengajuans->count() }}
                                    </span>
                                </td>

                                <td class="align-middle text-center">
                                    @php $lelangAktifSatker = $satker->pengajuans->flatMap->perkaras->flatMap->barangs->filter(fn($b) => $b->status === 'in_auction')->count(); @endphp
                                    @if($lelangAktifSatker > 0)
                                    <span style="background:rgba(3,105,161,0.1);color:#0369a1;font-weight:700;padding:2px 10px;border-radius:20px;font-size:0.8rem;">
                                        {{ $lelangAktifSatker }}
                                    </span>
                                    @else
                                    <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                <td class="align-middle text-center">
                                    @php $lelangSelesaiSatker = $satker->pengajuans->flatMap->perkaras->flatMap->barangs->filter(fn($b) => $b->status === 'sold')->count(); @endphp
                                    <span class="font-weight-bold text-success">{{ $lelangSelesaiSatker }}</span>
                                </td>

                                <td class="align-middle text-center">
                                    <span style="background:{{ $statusAktif['bg'] }};color:{{ $statusAktif['color'] }};font-size:0.7rem;font-weight:700;padding:3px 10px;border-radius:20px;">
                                        {{ $statusAktif['label'] }}
                                    </span>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-building fa-2x mb-2 d-block" style="color:#d1e7d8;"></i>
                                    Belum ada satker terdaftar
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ================= BARIS 3: DATA PERKARA & BARANG ================= --}}
<div class="row mb-4">

    {{-- Data Perkara --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header border-0"
                style="background:linear-gradient(90deg,#c0392b,#a93226);border-radius:16px 16px 0 0;padding:14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-balance-scale mr-2" style="color:#f6c90e;"></i>
                    Data Perkara
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div style="font-size:1.6rem;font-weight:800;color:#c0392b;">
                            {{ number_format($statsPerkara['total']) }}
                        </div>
                        <div style="font-size:0.72rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            Total
                        </div>
                    </div>
                    <div class="col-4" style="border-left:1px solid #f5c6cb;border-right:1px solid #f5c6cb;">
                        <div style="font-size:1.6rem;font-weight:800;color:#d97706;">
                            {{ number_format($statsPerkara['aktif']) }}
                        </div>
                        <div style="font-size:0.72rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            Aktif
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:1.6rem;font-weight:800;color:#22c55e;">
                            {{ number_format($statsPerkara['selesai']) }}
                        </div>
                        <div style="font-size:0.72rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            Selesai
                        </div>
                    </div>
                </div>

                <hr style="border-color:#f5c6cb;">

                {{-- Progress bar perkara selesai --}}
                @php
                    $persenPerkara = $statsPerkara['total'] > 0 
                        ? round(($statsPerkara['selesai'] / $statsPerkara['total']) * 100) 
                        : 0;
                @endphp
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Tingkat Penyelesaian</small>
                    <small class="font-weight-bold" style="color:#c0392b;">{{ $persenPerkara }}%</small>
                </div>
                <div class="progress" style="height:8px;border-radius:20px;background:#fde8e8;">
                    <div class="progress-bar" role="progressbar"
                        style="width:{{ $persenPerkara }}%;background:linear-gradient(90deg,#c0392b,#e74a3b);border-radius:20px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Barang --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header border-0"
                style="background:linear-gradient(90deg,#f6c90e,#e0b800);border-radius:16px 16px 0 0;padding:14px 20px;">
                <h6 class="m-0 font-weight-bold" style="color:#1a6b3c;">
                    <i class="fas fa-boxes mr-2"></i>Data Barang
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div style="font-size:1.4rem;font-weight:800;color:#1e293b;">
                            {{ number_format($statsBarang['total']) }}
                        </div>
                        <div style="font-size:0.68rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            Total
                        </div>
                    </div>
                    <div class="col-3" style="border-left:1px solid #f0d060;">
                        <div style="font-size:1.4rem;font-weight:800;color:#6c757d;">
                            {{ number_format($statsBarang['belum_lelang']) }}
                        </div>
                        <div style="font-size:0.68rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            Belum
                        </div>
                    </div>
                    <div class="col-3" style="border-left:1px solid #f0d060;">
                        <div style="font-size:1.4rem;font-weight:800;color:#0369a1;">
                            {{ number_format($statsBarang['sedang_lelang']) }}
                        </div>
                        <div style="font-size:0.68rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            Proses
                        </div>
                    </div>
                    <div class="col-3" style="border-left:1px solid #f0d060;">
                        <div style="font-size:1.4rem;font-weight:800;color:#22c55e;">
                            {{ number_format($statsBarang['terjual']) }}
                        </div>
                        <div style="font-size:0.68rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            Terjual
                        </div>
                    </div>
                </div>

                <hr style="border-color:#f0d060;">

                {{-- Progress bar barang terjual --}}
                @php
                    $persenBarang = $statsBarang['total'] > 0 
                        ? round(($statsBarang['terjual'] / $statsBarang['total']) * 100) 
                        : 0;
                @endphp
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Tingkat Terjual</small>
                    <small class="font-weight-bold" style="color:#856404;">{{ $persenBarang }}%</small>
                </div>
                <div class="progress" style="height:8px;border-radius:20px;background:#fef9c3;">
                    <div class="progress-bar" role="progressbar"
                        style="width:{{ $persenBarang }}%;background:linear-gradient(90deg,#a16207,#eab308);border-radius:20px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ================= BARIS 4: AKTIVITAS & LELANG BERAKHIR ================= --}}
<div class="row">

    {{-- Aktivitas Terbaru --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header border-0 d-flex justify-content-between align-items-center"
                style="background:linear-gradient(90deg,#1a6b3c,#145c32);border-radius:16px 16px 0 0;padding:14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-history mr-2" style="color:#f6c90e;"></i>
                    Aktivitas Terbaru
                </h6>
            </div>
            <div class="card-body p-0">
                <div style="max-height:320px;overflow-y:auto;">
                    @forelse($aktivitasTerbaru as $aktivitas)
                    <div class="d-flex align-items-start px-4 py-3"
                        style="border-bottom:1px solid #f0f9f4;{{ $loop->first ? 'background:#f8fff9;' : '' }}">

                        {{-- Icon --}}
                        <div style="width:34px;height:34px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;margin-right:12px;
                            background:{{ $aktivitas['status'] === 'submitted' ? 'rgba(3,105,161,0.1)' : ($aktivitas['status'] === 'approved' ? 'rgba(34,197,94,0.1)' : ($aktivitas['status'] === 'revision' ? 'rgba(245,158,11,0.1)' : 'rgba(26,107,60,0.1)')) }}">
                            <i class="fas {{ $aktivitas['status'] === 'submitted' ? 'fa-paper-plane text-primary' : ($aktivitas['status'] === 'approved' ? 'fa-check text-success' : ($aktivitas['status'] === 'revision' ? 'fa-redo text-warning' : 'fa-file-alt')) }}"
                                style="font-size:0.8rem;color:#1a6b3c;"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="small font-weight-bold" style="color:#2d3748;line-height:1.3;">
                                {{ $aktivitas['keterangan'] }}
                            </div>
                            <div style="font-size:0.7rem;color:#6c757d;margin-top:2px;">
                                <i class="fas fa-building mr-1"></i>{{ $aktivitas['satker'] }}
                                &nbsp;·&nbsp;
                                <i class="fas fa-clock mr-1"></i>{{ $aktivitas['waktu'] }}
                            </div>
                        </div>

                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block" style="color:#d1e7d8;"></i>
                        Belum ada aktivitas
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Lelang Akan Berakhir --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header border-0 d-flex justify-content-between align-items-center"
                style="background:linear-gradient(90deg,#c0392b,#a93226);border-radius:16px 16px 0 0;padding:14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-hourglass-end mr-2" style="color:#f6c90e;"></i>
                    Lelang Akan Berakhir
                </h6>
                <span style="background:rgba(255,255,255,0.15);color:white;font-size:0.72rem;padding:3px 10px;border-radius:20px;">
                    24 jam ke depan
                </span>
            </div>
            <div class="card-body p-0">
                @forelse($lelangAkanBerakhir as $lelang)
                @php
                    $sisaWaktu  = \Carbon\Carbon::now()->diffInMinutes($lelang->tanggal_selesai, false);
                    $sisaJam    = floor($sisaWaktu / 60);
                    $sisaMenit  = $sisaWaktu % 60;
                    $urgent     = $sisaJam < 2;
                @endphp
                <div class="d-flex align-items-center px-4 py-3"
                    style="border-bottom:1px solid #fde8e8;{{ $urgent ? 'background:#fff8f8;' : '' }}">

                    {{-- Foto kecil --}}
                    <div style="width:44px;height:44px;border-radius:10px;overflow:hidden;flex-shrink:0;margin-right:12px;background:#f5c6cb;">
                        @if($lelang->barang->fotoBarang->count() > 0)
                            <img src="{{ asset('storage/'.$lelang->barang->fotoBarang->first()->file_path) }}"
                                style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-image text-muted" style="font-size:1.2rem;"></i>
                            </div>
                        @endif
                    </div>

                    <div class="flex-grow-1">
                        <div class="font-weight-bold small" style="color:#2d3748;">
                            {{ $lelang->barang->nama_barang }}
                        </div>
                        <div style="font-size:0.7rem;color:#6c757d;margin-top:1px;">
                            <i class="fas fa-building mr-1"></i>
                            {{ optional($lelang->barang->perkara->pengajuan->satker)->nama_satker ?? '-' }}
                        </div>
                    </div>

                    {{-- Countdown --}}
                    <div class="text-right">
                        <div style="font-size:0.85rem;font-weight:800;color:{{ $urgent ? '#c0392b' : '#d97706' }};">
                            {{ $sisaJam }}j {{ $sisaMenit }}m
                        </div>
                        @if($urgent)
                        <span style="background:#fde8e8;color:#c0392b;font-size:0.65rem;font-weight:700;padding:2px 6px;border-radius:20px;">
                            ⚠ Segera
                        </span>
                        @endif
                    </div>

                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#d1e7d8;"></i>
                    Tidak ada lelang yang akan berakhir
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ================= BARIS 5: TABEL PENGAJUAN TERBARU ================= --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-header border-0 d-flex justify-content-between align-items-center"
        style="background:linear-gradient(90deg,#1a6b3c,#145c32);border-radius:16px 16px 0 0;padding:14px 20px;">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-list mr-2" style="color:#f6c90e;"></i>Pengajuan Terbaru
        </h6>
        <a href="{{ $isPusat ? route('admin.pengajuan.index') : route('satker.pengajuan.index') }}"
            class="btn btn-sm font-weight-bold"
            style="background:#f6c90e;color:#1a6b3c;border-radius:6px;">
            <i class="fas fa-list fa-sm mr-1"></i>Lihat Semua
        </a>
    </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabelDashboard" class="table table-hover mb-0" width="100%" cellspacing="0">

                    <thead style="background: #f8fff9;">
                        <tr>
                            @if(auth()->user()->role == 'admin_pusat')
                                <th class="border-0 pl-4" style="color: #1a6b3c;">No</th>
                                <th class="border-0" style="color: #1a6b3c;">Satker</th>
                                <th class="border-0" style="color: #1a6b3c;">Judul Pengajuan</th>
                                <th class="border-0" style="color: #1a6b3c;">Tanggal</th>
                                <th class="border-0" style="color: #1a6b3c;">Status Pengajuan</th>
                                <th class="border-0" style="color: #1a6b3c;">Status Lelang</th>
                                <th class="border-0" style="color: #1a6b3c;">Aksi</th>
                            @else
                                <th class="border-0 pl-4" style="color: #1a6b3c;">Judul Pengajuan</th>
                                <th class="border-0" style="color: #1a6b3c;">Tanggal</th>
                                <th class="border-0" style="color: #1a6b3c;">Status Pengajuan</th>
                                <th class="border-0" style="color: #1a6b3c;">Status Lelang</th>
                                <th class="border-0" style="color: #1a6b3c;">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengajuans as $key => $item)

                        @php
                            // Hitung status lelang dari semua barang dalam pengajuan
                            $semuaBarang = $item->perkaras->flatMap->barangs;
                            $totalBarang = $semuaBarang->count();

                            $countActive    = $semuaBarang->filter(fn($b) => $b->lelang && $b->lelang->status === 'active')->count();
                            $countScheduled = $semuaBarang->filter(fn($b) => $b->lelang && $b->lelang->status === 'scheduled')->count();
                            $countSold      = $semuaBarang->filter(fn($b) => $b->status === 'sold')->count();
                            $countUnsold    = $semuaBarang->filter(fn($b) => $b->status === 'unsold')->count();
                            $countAvailable = $semuaBarang->filter(fn($b) => $b->status === 'available')->count();
                            
                            $totalFinished = $countSold + $countUnsold;

                            // Rule Prioritas
                            if ($totalBarang === 0) {
                                $statusLelang = 'no_barang';
                            } elseif ($countActive > 0) {
                                $statusLelang = 'active';
                            } elseif ($countScheduled > 0) {
                                $statusLelang = 'scheduled';
                            } elseif ($totalFinished > 0 && $totalFinished < $totalBarang) {
                                $statusLelang = 'partial_finished';
                            } elseif ($totalFinished === $totalBarang) {
                                // Jika semua selesai, cek apakah ada yang terjual
                                if ($countSold === $totalBarang) {
                                    $statusLelang = 'all_sold';
                                } elseif ($countSold > 0) {
                                    $statusLelang = 'mixed_finished';
                                } else {
                                    $statusLelang = 'all_unsold';
                                }
                            } else {
                                $statusLelang = 'available';
                            }
                        @endphp

                        <tr style="border-left: 3px solid transparent; transition: 0.2s;"
                            onmouseover="this.style.borderLeft='3px solid #1a6b3c'"
                            onmouseout="this.style.borderLeft='3px solid transparent'">

                            @if(auth()->user()->role == 'admin_pusat')
                                <td class="pl-4 align-middle">{{ $key + 1 }}</td>
                                <td class="align-middle small">
                                    <i class="fas fa-building mr-1 text-muted"></i>
                                    {{ $item->satker->nama_satker ?? '-' }}
                                </td>
                                <td class="align-middle font-weight-bold">{{ $item->judul_pengajuan }}</td>
                                <td class="align-middle text-muted small">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $item->created_at->format('d M Y') }}
                                </td>
                            @else
                                <td class="pl-4 align-middle font-weight-bold">{{ $item->judul_pengajuan }}</td>
                                <td class="align-middle text-muted small">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $item->created_at->format('d M Y') }}
                                </td>
                            @endif

                            {{-- Status Pengajuan --}}
                            <td class="align-middle">
                                @if($item->status == 'draft')
                                    <span class="badge badge-warning px-2 py-1" style="border-radius:20px;">📝 Draft</span>
                                @elseif($item->status == 'submitted')
                                    <span class="badge badge-info px-2 py-1" style="border-radius:20px;">📤 Dikirim</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge badge-success px-2 py-1" style="border-radius:20px;">✅ Disetujui</span>
                                @elseif($item->status == 'rejected')
                                    <span class="badge badge-danger px-2 py-1" style="border-radius:20px;">❌ Ditolak</span>
                                @elseif($item->status == 'revision')
                                    <span class="badge badge-secondary px-2 py-1" style="border-radius:20px;">🔄 Revisi</span>
                                @endif
                            </td>

                            {{-- Status Lelang --}}
                            <td class="align-middle">
                                @if($statusLelang === 'no_barang')
                                    <span class="badge px-2 py-1" style="background:#e9ecef;color:#6c757d;border-radius:20px;">
                                        — Belum ada barang
                                    </span>

                                @elseif($statusLelang === 'available')
                                    <span class="badge px-2 py-1" style="background:#e8f5ee;color:#1a6b3c;border-radius:20px;">
                                        🟢 Tersedia ({{ $totalBarang }})
                                    </span>
                                @elseif($statusLelang === 'scheduled')
                                    <span class="badge badge-info px-2 py-1" style="border-radius:20px;">
                                        📅 Terjadwal ({{ $countScheduled }}/{{ $totalBarang }})
                                    </span>
                                @elseif($statusLelang === 'active')
                                    <span class="badge px-2 py-1" style="background:#28a745;color:white;border-radius:20px;">
                                        🔴 Live ({{ $countActive }}/{{ $totalBarang }})
                                    </span>
                                @elseif($statusLelang === 'partial_finished')
                                    <span class="badge px-2 py-1" style="background:#17a2b8;color:white;border-radius:20px;">
                                        📦 Sebagian Selesai ({{ $totalFinished }}/{{ $totalBarang }})
                                    </span>
                                @elseif($statusLelang === 'all_sold')
                                    <span class="badge px-2 py-1" style="background:#6f42c1;color:white;border-radius:20px;">
                                        ✅ Semua Terjual ({{ $totalBarang }})
                                    </span>
                                @elseif($statusLelang === 'mixed_finished')
                                    <span class="badge px-2 py-1" style="background:#5a6268;color:white;border-radius:20px;">
                                        🏁 Selesai ({{ $countSold }} Laku)
                                    </span>
                                @elseif($statusLelang === 'all_unsold')
                                    <span class="badge badge-secondary px-2 py-1" style="border-radius:20px;">
                                        ❌ Tidak Terjual
                                    </span>
                                @endif

                                {{-- Indikator Butuh Re-jadwal --}}
                                @if($countAvailable > 0 && $totalFinished > 0)
                                    <span class="d-block mt-1 text-warning font-weight-bold" style="font-size: 0.65rem;">
                                        <i class="fas fa-redo"></i> {{ $countAvailable }} barang perlu dijadwalkan ulang
                                    </span>
                                @elseif($statusLelang === 'available' && $item->status === 'approved')
                                    <span class="d-block mt-1 text-success font-weight-bold" style="font-size: 0.65rem;">
                                        <i class="fas fa-clock"></i> Siap dijadwalkan
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="align-middle">
                                <a href="{{ auth()->user()->role === 'admin_pusat' 
                                    ? route('admin.pengajuan.show', $item->id) 
                                    : route('satker.pengajuan.step4', $item->id) }}"
                                    class="btn btn-sm"
                                    style="background: #1a6b3c; color: white; border-radius: 6px;">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block" style="color: #d1e7d8;"></i>
                                Belum ada data pengajuan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>

    </div>

</div>

{{-- Modal Detail Belum Bayar (Piutang) --}}
<div class="modal fade" id="modalUnpaidPNBP" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0" style="padding: 24px;">
                <h5 class="modal-title font-weight-bold" style="color:#1e293b;">
                    <i class="fas fa-exclamation-triangle mr-2 text-warning"></i> 
                    Detail Belum Bayar (Piutang)
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p class="text-muted small mb-4">Daftar satker yang memiliki barang terjual namun belum diinput bukti bayar billingnya oleh admin satker.</p>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background:#f8fafc; color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:1px;">
                                <th class="border-0 px-3">Satuan Kerja</th>
                                <th class="border-0">Detail Barang</th>
                                <th class="border-0 text-right pr-3">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unpaidDetails as $detail)
                            <tr>
                                <td class="px-3 align-top font-weight-bold" style="color:#1e293b;">
                                    {{ $detail['nama_satker'] }}
                                    <div class="text-muted font-weight-normal small">{{ $detail['jumlah_lot'] }} Lot</div>
                                </td>
                                <td class="align-top">
                                    @foreach($detail['daftar_barang'] as $namaBarang)
                                        <div class="small text-dark mb-1">• {{ $namaBarang }}</div>
                                    @endforeach
                                </td>
                                <td class="text-right align-middle pr-3 font-weight-bold text-danger">
                                    Rp {{ number_format($detail['total_nilai'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Semua tagihan sudah dibayar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($unpaidDetails->count() > 0)
                        <tfoot style="border-top: 2px solid #e2e8f0;">
                            <tr style="background:#f8fafc;">
                                <th class="px-3 py-2 text-muted font-weight-normal small" colspan="2">Total PNBP Sudah Terbayar (Realisasi)</th>
                                <th class="text-right py-2 pr-3 text-success font-weight-bold">
                                    Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}
                                </th>
                            </tr>
                            <tr style="background:#f8fafc;">
                                <th class="px-3 py-2 text-muted font-weight-normal small" colspan="2">Total Piutang (Belum Terbayar)</th>
                                <th class="text-right py-2 pr-3 text-danger font-weight-bold">
                                    Rp {{ number_format($unpaidDetails->sum('total_nilai'), 0, ',', '.') }}
                                </th>
                            </tr>
                            <tr style="background:#f1f5f9;">
                                <th class="px-3 py-3 font-weight-bold text-dark" colspan="2">TOTAL PNBP SEHARUSNYA (POTENSI)</th>
                                <th class="text-right py-3 pr-3 font-weight-bold text-primary" style="font-size:1.1rem;">
                                    Rp {{ number_format($stats['pnbp_seharusnya'], 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding: 24px;">
                <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" style="border-radius:10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Aset Terjual --}}
<div class="modal fade" id="modalSoldAssets" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0" style="padding: 24px;">
                <h5 class="modal-title font-weight-bold" style="color:#1e293b;">
                    <i class="fas fa-check-circle mr-2 text-success"></i> 
                    Detail Aset Terlelang
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p class="text-muted small mb-4">Daftar barang yang telah berhasil terlelang dan memiliki pemenang sah.</p>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background:#f8fafc; color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:1px;">
                                <th class="border-0 px-3">Satuan Kerja</th>
                                <th class="border-0">Detail Barang & Pemenang</th>
                                <th class="border-0 text-right pr-3">Harga Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($soldDetails as $detail)
                            <tr>
                                <td class="px-3 align-top font-weight-bold" style="color:#1e293b;">
                                    {{ $detail['nama_satker'] }}
                                    <div class="text-muted font-weight-normal small">{{ $detail['jumlah_lot'] }} Lot</div>
                                </td>
                                <td class="align-top">
                                    @foreach($detail['daftar_barang'] as $item)
                                        <div class="mb-2">
                                            <div class="small font-weight-bold text-dark">• {{ $item['nama'] }}</div>
                                            <div class="small text-muted ml-3">Pemenang: <span class="text-primary">{{ $item['pemenang'] }}</span></div>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-right align-middle pr-3 font-weight-bold text-success">
                                    Rp {{ number_format($detail['total_nilai'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada aset yang terlelang.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($soldDetails->count() > 0)
                        <tfoot style="border-top: 2px solid #e2e8f0;">
                            <tr style="background:#f1f5f9;">
                                <th class="px-3 py-3 font-weight-bold text-dark" colspan="2">TOTAL REKAPITULASI TERLELANG</th>
                                <th class="text-right py-3 pr-3 font-weight-bold text-success" style="font-size:1.1rem;">
                                    Rp {{ number_format($soldDetails->sum('total_nilai'), 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding: 24px;">
                <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" style="border-radius:10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

</div> {{-- Penutup container-fluid yang tertinggal --}}

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.3); }
}

.modern-date-group {
    transition: all 0.3s;
    border-radius: 8px;
}
.modern-date-group:focus-within {
    background-color: #f0fdf4;
    box-shadow: 0 0 0 2px rgba(26, 107, 60, 0.1);
}
.modern-date-input {
    cursor: pointer;
    background: transparent !important;
    color: #2d3748 !important;
    font-weight: 600;
}
.modern-date-input::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.6;
    transition: 0.2s;
}
.modern-date-input::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
    transform: scale(1.1);
}
</style>

@endsection
@push('scripts')
    
<script>
    document.addEventListener('DOMContentLoaded', function () {
    LapauTable.init('tabelDashboard', {
        searchable: false,
        pageSize:  10,
        sortDir:   'desc',
    });
});
</script>
@endpush