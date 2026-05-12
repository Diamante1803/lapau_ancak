@extends('layouts.admin')

@section('content')

@php $isPusat = auth()->user()->role === 'admin_pusat'; @endphp

{{-- ================= HEADER ================= --}}
<div class="d-flex align-items-center justify-content-between mb-4 header-card">
    <div>
        <h4 class="font-weight-bold mb-0" style="color:#1a6b3c;">
            <i class="fas fa-chart-bar mr-2" style="color:#f6c90e;"></i>
            Laporan Lelang Selesai
        </h4>
    </div>
    @if($lelangs->count() > 0)
    <div class="d-flex no-print" style="gap:8px;">
        {{-- Cetak PDF --}}
        <button onclick="window.print()" class="btn btn-sm font-weight-bold"
            style="background:#c0392b;color:white;border-radius:8px;padding:8px 16px;">
            <i class="fas fa-print mr-1"></i> Cetak PDF
        </button>
        {{-- Ekspor Excel --}}
        <button onclick="eksporExcel()" class="btn btn-sm font-weight-bold"
            style="background:#1a6b3c;color:white;border-radius:8px;padding:8px 16px;">
            <i class="fas fa-file-excel mr-1"></i> Ekspor Excel
        </button>
    </div>
    @endif
</div>

{{-- ================= ALERT ================= --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3"
    style="border-radius:10px;border:none;font-size:0.875rem;">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

{{-- ================= PRINT HEADER ================= --}}
<div id="print-header" style="display:none;text-align:center;margin-bottom:16px;border-bottom:2px solid #1a6b3c;padding-bottom:10px;">
    <div style="font-size:15px;font-weight:700;color:#1a6b3c;letter-spacing:1px;">
        LAPORAN LELANG BARANG SITAAN
    </div>
    <div style="font-size:12px;color:#2d3748;margin-top:3px;">
        @if(!$isPusat)
            {{ auth()->user()->satker->nama_satker ?? '-' }}
        @elseif(request('satker_id') && isset($satkers))
            {{ $satkers->find(request('satker_id'))->nama_satker ?? 'Semua Satker' }}
        @else
            Semua Satker
        @endif
    </div>
    <div style="font-size:11px;color:#555;margin-top:3px;">
        @if(request('dari') && request('sampai'))
            Periode: {{ \Carbon\Carbon::parse(request('dari'))->format('d M Y') }}
            — {{ \Carbon\Carbon::parse(request('sampai'))->format('d M Y') }}
        @else
            Semua Periode
        @endif
    </div>
    <div style="font-size:10px;color:#888;margin-top:2px;">
        Dicetak: {{ now()->format('d M Y, H:i') }} WIB
    </div>
</div>

{{-- ================= FILTER ================= --}}
<div class="card shadow-sm mb-4 filter-card" style="border-radius:12px;border:none;">
    <div class="card-header font-weight-bold"
        style="background:linear-gradient(90deg,#1a6b3c,#145c32);color:white;border-radius:12px 12px 0 0;font-size:0.9rem;">
        <i class="fas fa-filter mr-2"></i>Filter Laporan
    </div>
    <div class="card-body" style="background:#f8fff9;">
        <form method="GET" action="{{ $isPusat ? route('admin.laporan.index') : route('satker.laporan.index') }}">
            <div class="row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-muted">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control form-control-sm"
                        style="border-radius:8px;" value="{{ request('dari') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-muted">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control form-control-sm"
                        style="border-radius:8px;" value="{{ request('sampai') }}">
                </div>
                @if($isPusat)
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-muted">Satker</label>
                    <select name="satker_id" class="form-control form-control-sm" style="border-radius:8px;">
                        <option value="">Semua Satker</option>
                        @foreach($satkers as $satker)
                        <option value="{{ $satker->id }}"
                            {{ request('satker_id') == $satker->id ? 'selected' : '' }}>
                            {{ $satker->nama_satker }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3 mb-2 d-flex" style="gap:8px;">
                    <button type="submit" class="btn btn-sm font-weight-bold flex-fill"
                        style="background:#1a6b3c;color:white;border-radius:8px;">
                        <i class="fas fa-search mr-1"></i>Tampilkan
                    </button>
                    <a href="{{ $isPusat ? route('admin.laporan.index') : route('satker.laporan.index') }}"
                        class="btn btn-sm btn-secondary flex-fill" style="border-radius:8px;">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= STATISTIK ================= --}}
<div class="row mb-4 stat-cards">
    <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:12px;border:1px solid #d4edda;">
            <div class="card-body d-flex align-items-center py-3">
                <div style="width:42px;height:42px;border-radius:10px;background:#e8f5ee;
                    display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-gavel" style="color:#1a6b3c;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#1a6b3c;line-height:1;">
                        {{ $lelangs->count() }}
                    </div>
                    <div style="font-size:0.72rem;color:#6c757d;">Total Lelang Selesai</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:12px;border:1px solid #ffeeba;">
            <div class="card-body d-flex align-items-center py-3">
                <div style="width:42px;height:42px;border-radius:10px;background:#fff8e1;
                    display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-money-bill-wave" style="color:#f39c12;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.9rem;font-weight:700;color:#856404;line-height:1.3;">
                        Rp {{ number_format($totalNilaiBilling, 0, ',', '.') }}
                    </div>
                    <div style="font-size:0.72rem;color:#6c757d;">Total Nilai Terbayar</div>
                    <div style="font-size:0.7rem;color:#adb5bd;">
                        dari Rp {{ number_format($totalNilai, 0, ',', '.') }} total terjual
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:12px;border:1px solid #bee5eb;">
            <div class="card-body d-flex align-items-center py-3">
                <div style="width:42px;height:42px;border-radius:10px;background:#e8f6f9;
                    display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-file-alt" style="color:#17a2b8;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#0c5460;line-height:1;">
                        {{ $sudahBAST }}
                    </div>
                    <div style="font-size:0.72rem;color:#6c757d;">Sudah Upload BAST</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:12px;border:1px solid #f5c6cb;">
            <div class="card-body d-flex align-items-center py-3">
                <div style="width:42px;height:42px;border-radius:10px;background:#fde8e8;
                    display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-times-circle" style="color:#c0392b;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#c0392b;line-height:1;">
                        {{ $belumBAST }}
                    </div>
                    <div style="font-size:0.72rem;color:#6c757d;">Belum Upload BAST</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= TABEL PER SATKER ================= --}}
@if($lelangs->count() > 0)

@foreach($grouped as $satkerNama => $group)
<div class="card shadow-sm mb-3" style="border-radius:12px;border:none;">

    {{-- Header Satker --}}
    <div class="card-header d-flex align-items-center justify-content-between"
        style="background:linear-gradient(90deg,#1a6b3c,#145c32);border-radius:12px 12px 0 0;padding:12px 20px;">
        <span class="font-weight-bold text-white" style="font-size:0.9rem;">
            <i class="fas fa-building mr-2" style="color:#f6c90e;"></i>
            {{ $satkerNama }}
        </span>
        <div class="d-flex align-items-center" style="gap:8px;">
            <span class="badge" style="background:rgba(255,255,255,0.2);color:white;border-radius:20px;font-size:0.72rem;">
                {{ $group['items']->count() }} lelang
            </span>
            <span class="badge" style="background:rgba(246,201,14,0.3);color:#f6c90e;border-radius:20px;font-size:0.72rem;">
                ▲ {{ $group['kenaikan'] }}%
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelLaporan" class="table table-hover mb-0" style="font-size:0.82rem;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="border-0 pl-4" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 16px;width:36px;">#</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">NAMA BARANG</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">HARGA LIMIT</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">HARGA TERJUAL</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">PEMENANG</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">TGL SELESAI</th>
                        <th class="border-0 text-center" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">LAPORAN</th>
                        @if(!$isPusat)
                        <th data-no-sort class="border-0 text-center no-print" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;width:60px;">AKSI</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['items'] as $i => $lelang)
                    @php
                        $barang  = $lelang->barang;
                        $perkara = $barang->perkara;
                        $laporan = $lelang->laporan;
                        $terjual = $lelang->harga_tertinggi ?? 0;
                        $limit   = $lelang->harga_awal;
                        $persen  = $limit > 0
                            ? round((($terjual - $limit) / $limit) * 100, 2)
                            : 0;
                    @endphp
                    <tr>
                        <td class="pl-4 align-middle text-muted" style="padding:10px 16px;">{{ $i + 1 }}</td>

                        {{-- NAMA BARANG --}}
                        <td class="align-middle" style="padding:10px 12px;">
                            <div class="font-weight-bold" style="color:#2d3748;font-size:0.82rem;">
                                {{ $barang->nama_barang }}
                            </div>
                            <small class="text-muted">{{ $perkara->nomor_perkara }} — {{ $perkara->nama_tersangka }}</small>
                        </td>

                        {{-- HARGA LIMIT --}}
                        <td class="align-middle" style="padding:10px 12px;color:#6c757d;font-size:0.82rem;">
                            Rp {{ number_format($limit, 0, ',', '.') }}
                        </td>

                        {{-- HARGA TERJUAL + PERSENTASE --}}
                        <td class="align-middle" style="padding:10px 12px;">
                            <div class="font-weight-bold" style="color:#1a6b3c;font-size:0.85rem;">
                                Rp {{ number_format($terjual, 0, ',', '.') }}
                            </div>
                            @if($persen > 0)
                            <small style="color:#27ae60;font-size:0.7rem;">
                                <i class="fas fa-arrow-up"></i> {{ $persen }}% dari limit
                            </small>
                            @elseif($persen < 0)
                            <small style="color:#e74a3b;font-size:0.7rem;">
                                <i class="fas fa-arrow-down"></i> {{ abs($persen) }}% dari limit
                            </small>
                            @else
                            <small class="text-muted" style="font-size:0.7rem;">Sama dengan limit</small>
                            @endif
                        </td>

                        {{-- PEMENANG --}}
                        <td class="align-middle" style="padding:10px 12px;">
                            @if($lelang->pemenang)
                            <div style="font-size:0.82rem;color:#2d3748;">{{ $lelang->pemenang->nama }}</div>
                            <small class="text-muted">{{ $lelang->pemenang->no_hp }}</small>
                            @else
                            <span class="text-muted" style="font-size:0.8rem;">—</span>
                            @endif
                        </td>

                        {{-- TGL SELESAI --}}
                        <td class="align-middle text-muted" style="padding:10px 12px;font-size:0.8rem;">
                            {{ \Carbon\Carbon::parse($lelang->tanggal_selesai)->format('d M Y') }}
                        </td>

                        {{-- STATUS LAPORAN --}}
                        <td class="align-middle text-center" style="padding:10px 12px;">
                            @if(!$laporan)
                                <span class="badge badge-danger" style="border-radius:20px;font-size:0.7rem;">
                                    Belum ada
                                </span>
                            @elseif($laporan->status === 'lengkap')
                                <span class="badge badge-success" style="border-radius:20px;font-size:0.7rem;">
                                    Lengkap
                                </span>
                                <div class="mt-1 d-flex justify-content-center bast-link" style="gap:4px;">
                                    @if($laporan->file_bast)
                                    <a href="{{ asset('storage/'.$laporan->file_bast) }}" target="_blank"
                                        style="font-size:0.68rem;color:#1a6b3c;">
                                        <i class="fas fa-file-alt"></i> BAST
                                    </a>
                                    @endif
                                    @if($laporan->file_bukti_bayar)
                                    <span style="color:#ccc;font-size:0.68rem;">|</span>
                                    <a href="{{ asset('storage/'.$laporan->file_bukti_bayar) }}" target="_blank"
                                        style="font-size:0.68rem;color:#1a6b3c;">
                                        <i class="fas fa-receipt"></i> Billing
                                    </a>
                                    @endif
                                </div>
                            @else
                                <span class="badge badge-warning" style="border-radius:20px;font-size:0.7rem;color:#856404;">
                                    Sebagian
                                </span>
                                <div class="mt-1" style="font-size:0.68rem;color:#6c757d;">
                                    BAST: {{ $laporan->file_bast ? '✓' : '✗' }}
                                    | Billing: {{ $laporan->file_bukti_bayar ? '✓' : '✗' }}
                                </div>
                            @endif
                        </td>

                        {{-- AKSI (satker only) --}}
                        @if(!$isPusat)
                        <td class="align-middle text-center no-print" style="padding:10px 12px;">
                            <button class="btn btn-sm"
                                style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;padding:4px 10px;font-size:0.75rem;"
                                onclick="$('#modalLaporan-{{ $lelang->id }}').modal('show')"
                                title="{{ $laporan ? 'Edit Laporan' : 'Upload Laporan' }}">
                                <i class="fas fa-{{ $laporan ? 'edit' : 'upload' }}"></i>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach

                    {{-- ── TOTAL PER SATKER ── --}}
                    <tr style="background:#f0faf4;border-top:2px solid #1a6b3c;">
                        <td colspan="{{ $isPusat ? 2 : 2 }}" class="pl-4 align-middle font-weight-bold"
                            style="padding:10px 16px;color:#1a6b3c;font-size:0.82rem;">
                            Total {{ $satkerNama }}
                        </td>
                        <td class="align-middle font-weight-bold" style="padding:10px 12px;color:#6c757d;font-size:0.82rem;">
                            Rp {{ number_format($group['total_limit'], 0, ',', '.') }}
                        </td>
                        <td class="align-middle" style="padding:10px 12px;">
                            <div class="font-weight-bold" style="color:#1a6b3c;font-size:0.85rem;">
                                Rp {{ number_format($group['total_terjual'], 0, ',', '.') }}
                            </div>
                            <small style="color:#27ae60;font-size:0.7rem;">
                                <i class="fas fa-arrow-up"></i> {{ $group['kenaikan'] }}% dari limit
                            </small>
                        </td>
                        <td colspan="{{ $isPusat ? 3 : 4 }}" class="align-middle text-muted text-center bast-info"
                            style="padding:10px 12px;font-size:0.75rem;">
                            {{ $group['items']->count() }} barang terjual ·
                            {{ $group['sudah_bast'] }} BAST lengkap ·
                            {{ $group['belum_bast'] }} belum
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

{{-- ================= GRAND TOTAL ================= --}}
@php
    $grandLimit   = $grouped->sum('total_limit');
    $grandTerjual = $grouped->sum('total_terjual');
    $grandKenaikan = $grandLimit > 0
        ? round((($grandTerjual - $grandLimit) / $grandLimit) * 100, 2)
        : 0;
@endphp
<div class="card shadow-sm mb-4 no-print" style="border-radius:12px;border:2px solid #1a6b3c;overflow:hidden;">
    <div class="card-body" style="background:linear-gradient(135deg,#1a6b3c,#145c32);padding:16px 24px;">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div style="color:rgba(255,255,255,0.7);font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;">
                    Total Seluruh Penjualan
                </div>
                <div style="font-size:0.82rem;color:rgba(255,255,255,0.85);margin-top:2px;">
                    {{ $lelangs->count() }} lelang dari {{ $grouped->count() }} satker
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div style="color:rgba(255,255,255,0.7);font-size:0.72rem;">Total Harga Limit</div>
                <div style="font-size:1rem;font-weight:700;color:rgba(255,255,255,0.9);">
                    Rp {{ number_format($grandLimit, 0, ',', '.') }}
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div style="color:rgba(255,255,255,0.7);font-size:0.72rem;">Total Nilai Terjual</div>
                <div style="font-size:1.2rem;font-weight:700;color:#f6c90e;">
                    Rp {{ number_format($grandTerjual, 0, ',', '.') }}
                </div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.7);">
                    ▲ {{ $grandKenaikan }}% dari total limit
                </div>
            </div>
        </div>
    </div>
</div>

@else
{{-- Empty state --}}
<div class="card shadow-sm" style="border-radius:12px;border:none;">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-chart-bar fa-3x mb-3 d-block" style="color:#d1e7d8;"></i>
        <div class="font-weight-bold mb-1">Belum ada data lelang selesai</div>
        <small>{{ request('dari') ? 'Tidak ada data pada rentang waktu yang dipilih.' : 'Data akan muncul setelah lelang selesai.' }}</small>
    </div>
</div>
@endif

{{-- ================= MODAL LAPORAN (satker only) ================= --}}
@if(!$isPusat)
@foreach($lelangs as $lelang)
@php $laporan = $lelang->laporan; $barang = $lelang->barang; @endphp
<div class="modal fade" id="modalLaporan-{{ $lelang->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);">
                <h5 class="modal-title font-weight-bold text-white" style="font-size:0.9rem;">
                    <i class="fas fa-file-upload mr-2" style="color:#f6c90e;"></i>
                    {{ $laporan ? 'Edit Laporan' : 'Upload Laporan' }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('satker.laporan.upload', $lelang->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="background:#f8fff9;font-size:0.875rem;">

                    {{-- Info Barang --}}
                    <div class="mb-3 p-2 rounded" style="background:white;border:1px solid #e3e6f0;">
                        <div class="font-weight-bold" style="color:#2d3748;font-size:0.82rem;">
                            {{ $barang->nama_barang }}
                        </div>
                        <small class="text-muted">
                            Selesai: {{ \Carbon\Carbon::parse($lelang->tanggal_selesai)->format('d M Y') }}
                        </small>
                    </div>

                    {{-- BAST --}}
                    <div class="mb-3 p-3 rounded" style="background:white;border:1px solid #e3e6f0;">
                        <div class="font-weight-bold mb-2" style="color:#1a6b3c;font-size:0.8rem;">
                            <i class="fas fa-file-alt mr-1"></i> Berita Acara Serah Terima (BAST)
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold text-muted">Nomor BAST</label>
                            <input type="text" name="nomor_bast" class="form-control form-control-sm"
                                style="border-radius:8px;" placeholder="Contoh: BAST/001/V/2026"
                                value="{{ $laporan->nomor_bast ?? '' }}">
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold text-muted">Tanggal BAST</label>
                            <input type="date" name="tanggal_bast" class="form-control form-control-sm"
                                style="border-radius:8px;"
                                value="{{ optional($laporan)->tanggal_bast?->format('Y-m-d') ?? '' }}">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted">
                                File BAST @if(!$laporan?->file_bast)<span class="text-danger">*</span>@endif
                            </label>
                            <input type="file" name="file_bast" class="form-control form-control-sm"
                                style="border-radius:8px;" accept=".pdf,.jpg,.jpeg,.png"
                                {{ !$laporan?->file_bast ? 'required' : '' }}>
                            <small class="text-muted">PDF / JPG / PNG, maks 5MB</small>
                            @if($laporan?->file_bast)
                            <small style="color:#1a6b3c;display:block;margin-top:4px;">
                                <i class="fas fa-check-circle mr-1"></i>Sudah ada —
                                <a href="{{ asset('storage/'.$laporan->file_bast) }}" target="_blank"
                                    style="color:#1a6b3c;">lihat file</a>
                                (upload baru untuk mengganti)
                            </small>
                            @endif
                        </div>
                    </div>

                    {{-- BILLING --}}
                    <div class="mb-3 p-3 rounded" style="background:white;border:1px solid #e3e6f0;">
                        <div class="font-weight-bold mb-2" style="color:#1a6b3c;font-size:0.8rem;">
                            <i class="fas fa-receipt mr-1"></i> Bukti Pembayaran / Billing
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold text-muted">Nomor Billing</label>
                            <input type="text" name="nomor_billing" class="form-control form-control-sm"
                                style="border-radius:8px;" placeholder="Contoh: BIL/2026/0001"
                                value="{{ $laporan->nomor_billing ?? '' }}">
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold text-muted">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" class="form-control form-control-sm"
                                style="border-radius:8px;"
                                value="{{ optional($laporan)->tanggal_bayar?->format('Y-m-d') ?? '' }}">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted">
                                File Bukti Bayar @if(!$laporan?->file_bukti_bayar)<span class="text-danger">*</span>@endif
                            </label>
                            <input type="file" name="file_bukti_bayar" class="form-control form-control-sm"
                                style="border-radius:8px;" accept=".pdf,.jpg,.jpeg,.png"
                                {{ !$laporan?->file_bukti_bayar ? 'required' : '' }}>
                            <small class="text-muted">PDF / JPG / PNG, maks 5MB</small>
                            @if($laporan?->file_bukti_bayar)
                            <small style="color:#1a6b3c;display:block;margin-top:4px;">
                                <i class="fas fa-check-circle mr-1"></i>Sudah ada —
                                <a href="{{ asset('storage/'.$laporan->file_bukti_bayar) }}" target="_blank"
                                    style="color:#1a6b3c;">lihat file</a>
                                (upload baru untuk mengganti)
                            </small>
                            @endif
                        </div>
                    </div>

                    {{-- CATATAN --}}
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted">
                            Catatan <span class="font-weight-normal">(opsional)</span>
                        </label>
                        <textarea name="catatan" class="form-control form-control-sm"
                            style="border-radius:8px;" rows="2"
                            placeholder="Catatan tambahan jika ada...">{{ $laporan->catatan ?? '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background:#f8fff9;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"
                        style="border-radius:6px;">Batal</button>
                    <button type="submit" class="btn btn-sm font-weight-bold"
                        style="background:#1a6b3c;color:white;border-radius:6px;padding:6px 14px;">
                        <i class="fas fa-save mr-1"></i>Simpan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif

{{-- ================= PRINT STYLE ================= --}}
<style>
@media print {
    .sidebar, .topbar, nav, .breadcrumb,
    .filter-card, .header-card, .stat-cards,
    .modal, .no-print, button, a.btn,
    .alert, .lt-toolbar, .bast-link, .bast-info,
    .lt-info, .lt-pag-wrap,
    .lt-pagination, .lt-bottom-bar {
        display: none !important;
    }

    #print-header { display: block !important; }

    body { font-size: 10px; margin: 0; padding: 0; background: white !important; }
    .container-fluid { padding: 0 !important; }

    .card {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
        border-radius: 0 !important;
        margin-bottom: 8px !important;
    }
    .card-header {
        background: #1a6b3c !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        padding: 6px 12px !important;
    }
    .table { width: 100% !important; border-collapse: collapse !important; }
    .table th, .table td {
        border: 1px solid #ccc !important;
        padding: 4px 8px !important;
        font-size: 9px !important;
        vertical-align: middle !important;
    }
    .table thead th {
        background: #f0f0f0 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    tr[style*="background:#f0faf4"] {
        background: #f0faf4 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    tr { page-break-inside: avoid; }
}
@page { size: A4 landscape; margin: 12mm; }
#print-header { display: none; }
</style>

@endsection

@push('scripts')
@php
$excelData = $grouped->map(function($group) {
    return [
        'nama_satker' => $group['nama_satker'],
        'items' => $group['items']->map(function($lelang) use ($group) {
            $terjual = $lelang->harga_tertinggi ?? 0;
            $limit   = $lelang->harga_awal;
            $persen  = $limit > 0
                ? round((($terjual - $limit) / $limit) * 100, 2)
                : 0;
            return [
                'satker'         => $group['nama_satker'],
                'nama_barang'    => $lelang->barang->nama_barang,
                'nomor_perkara'  => $lelang->barang->perkara->nomor_perkara,
                'tersangka'      => $lelang->barang->perkara->nama_tersangka,
                'harga_limit'    => $limit,
                'harga_terjual'  => $terjual,
                'kenaikan'       => $persen,
                'pemenang'       => $lelang->pemenang->nama ?? '-',
                'no_hp'          => $lelang->pemenang->no_hp ?? '-',
                'tgl_selesai'    => \Carbon\Carbon::parse($lelang->tanggal_selesai)->format('d/m/Y'),
                'status_laporan' => $lelang->laporan
                    ? ($lelang->laporan->status === 'lengkap' ? 'Lengkap' : 'Sebagian')
                    : 'Belum ada',
            ];
        })->values()->all(),
    ];
})->values()->all();
@endphp

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
const data = {!! json_encode($excelData) !!};

function eksporExcel() {
    const wb   = XLSX.utils.book_new();
    const rows = [];

    rows.push([
        'No', 'Satker', 'Nama Barang', 'Nomor Perkara', 'Tersangka',
        'Harga Limit', 'Harga Terjual', 'Kenaikan (%)',
        'Pemenang', 'No HP Pemenang', 'Tgl Selesai', 'Status Laporan'
    ]);

    let no = 1;
    data.forEach(group => {
        group.items.forEach(item => {
            rows.push([
                no++,
                item.satker,
                item.nama_barang,
                item.nomor_perkara,
                item.tersangka,
                parseFloat(item.harga_limit),    // ✅ konversi ke number
                parseFloat(item.harga_terjual),  // ✅ konversi ke number
                parseFloat(item.kenaikan),
                item.pemenang,
                item.no_hp,
                item.tgl_selesai,
                item.status_laporan,
            ]);
        });

        const totalLimit   = group.items.reduce((s, i) => s + parseFloat(i.harga_limit),   0);
        const totalTerjual = group.items.reduce((s, i) => s + parseFloat(i.harga_terjual), 0);
        const kenaikan     = totalLimit > 0
            ? Math.round(((totalTerjual - totalLimit) / totalLimit) * 10000) / 100
            : 0;

        rows.push([
            '', `TOTAL ${group.nama_satker}`, '', '', '',
            totalLimit, totalTerjual, kenaikan,
            '', '', '', ''
        ]);
        rows.push([]);
    });

    const allItems     = data.flatMap(g => g.items);
    const grandLimit   = allItems.reduce((s, i) => s + parseFloat(i.harga_limit),   0);
    const grandTerjual = allItems.reduce((s, i) => s + parseFloat(i.harga_terjual), 0);
    const grandKenaikan = grandLimit > 0
        ? Math.round(((grandTerjual - grandLimit) / grandLimit) * 10000) / 100
        : 0;

    rows.push([
        '', 'GRAND TOTAL', '', '', '',
        grandLimit, grandTerjual, grandKenaikan,
        '', '', '', ''
    ]);

    const ws = XLSX.utils.aoa_to_sheet(rows);

    ws['!cols'] = [
        { wch: 4  }, { wch: 30 }, { wch: 30 }, { wch: 20 }, { wch: 20 },
        { wch: 18 }, { wch: 18 }, { wch: 12 }, { wch: 25 }, { wch: 15 },
        { wch: 12 }, { wch: 14 },
    ];

    Object.keys(ws).forEach(cell => {
        if (cell[0] === '!') return;
        const col = cell.replace(/[0-9]/g, '');
        if (['F', 'G'].includes(col) && typeof ws[cell].v === 'number') {
            ws[cell].z = '#,##0';
        }
        if (col === 'H' && typeof ws[cell].v === 'number') {
            ws[cell].z = '0.00"%"';
        }
    });

    XLSX.utils.book_append_sheet(wb, ws, 'Laporan Lelang');

    const tgl = new Date().toLocaleDateString('id-ID').replace(/\//g, '-');
    XLSX.writeFile(wb, `Laporan_Lelang_${tgl}.xlsx`);
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    LapauTable.init('tabelLaporan', {
        pageSize: 10,
        sortDir: 'desc'
    });

});
</script>
@endpush