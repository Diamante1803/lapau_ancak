@extends('layouts.admin')

@section('title', 'Laporan Lelang')

@section('content')

@php $isPusat = auth()->user()->role === 'admin_pusat'; @endphp

{{-- HEADER --}}
<div class="d-flex align-items-center header-card justify-content-between mb-4">
    <div>
        <h4 class="font-weight-bold mb-0" style="color: #1a6b3c;">
            <i class="fas fa-chart-bar mr-2" style="color: #f6c90e;"></i>
            Laporan Lelang Selesai
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0" style="font-size: 0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color:#1a6b3c;">Dashboard</a></li>
                <li class="breadcrumb-item active text-muted">Laporan</li>
            </ol>
        </nav>
    </div>
</div>

{{-- ALERT --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;border:none;font-size:0.875rem;">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

{{-- PRINT HEADER --}}
<div id="print-header" style="text-align:center; margin-bottom:14px; border-bottom:2px solid #1a6b3c; padding-bottom:10px;">
    <div style="font-size:15px; font-weight:700; color:#1a6b3c; letter-spacing:1px;">
        LAPORAN LELANG BARANG SITAAN
    </div>
    <div style="font-size:12px; font-weight:600; color:#2d3748; margin-top:3px;">
        @if($isPusat && isset($satkers) && request('satker_id'))
            {{ $satkers->find(request('satker_id'))->nama_satker ?? 'Semua Satker' }}
        @elseif(!$isPusat)
            {{ auth()->user()->satker->nama_satker ?? '-' }}
        @else
            Semua Satker
        @endif
    </div>
    <div style="font-size:11px; color:#555; margin-top:3px;">
        @if(request('dari') && request('sampai'))
            Periode: {{ \Carbon\Carbon::parse(request('dari'))->format('d M Y') }} — {{ \Carbon\Carbon::parse(request('sampai'))->format('d M Y') }}
        @else
            Semua Periode
        @endif
        @if(isset($satkers) && request('satker_id'))
            &nbsp;|&nbsp; Satker: {{ $satkers->find(request('satker_id'))->nama_satker ?? '-' }}
        @endif
    </div>
    <div style="font-size:10px; color:#888; margin-top:2px;">
        Dicetak: {{ now()->format('d M Y, H:i') }} WIB
    </div>
</div>

{{-- STATISTIK KHUSUS PRINT --}}
<div id="print-statistik" style="
    display:none;
    gap:10px;
    margin-bottom:14px;
    width:100%;
">
    {{-- Total Lelang --}}
    <div style="flex:1; border:1.5px solid #1a6b3c; border-radius:6px; padding:8px 12px; text-align:center;">
        <div style="font-size:18px; font-weight:700; color:#1a6b3c;">{{ $lelangs->count() }}</div>
        <div style="font-size:9px; color:#555; text-transform:uppercase; letter-spacing:1px;">Total Lelang Selesai</div>
    </div>
    {{-- Total Nilai --}}
    <div style="flex:2; border:1.5px solid #1a6b3c; border-radius:6px; padding:8px 12px; text-align:center;">
        <div style="font-size:14px; font-weight:700; color:#1a6b3c;">
            Rp {{ number_format($totalNilai, 0, ',', '.') }}
        </div>
        <div style="font-size:9px; color:#555; text-transform:uppercase; letter-spacing:1px;">Total Nilai Terjual</div>
    </div>
    {{-- Sudah BAST --}}
    <div style="flex:1; border:1.5px solid #1a6b3c; border-radius:6px; padding:8px 12px; text-align:center;">
        <div style="font-size:18px; font-weight:700; color:#1a6b3c;">{{ $sudahBAST }}</div>
        <div style="font-size:9px; color:#555; text-transform:uppercase; letter-spacing:1px;">Sudah BAST</div>
    </div>
    {{-- Belum BAST --}}
    <div style="flex:1; border:1.5px solid #c0392b; border-radius:6px; padding:8px 12px; text-align:center;">
        <div style="font-size:18px; font-weight:700; color:#c0392b;">{{ $belumBAST }}</div>
        <div style="font-size:9px; color:#555; text-transform:uppercase; letter-spacing:1px;">Belum BAST</div>
    </div>
</div>

{{-- FILTER --}}
<div class="card shadow-sm mb-4 filter-card" style="border-radius:12px;border:none;">
    <div class="card-header font-weight-bold"
        style="background:linear-gradient(90deg,#1a6b3c,#145c32);color:white;border-radius:12px 12px 0 0;font-size:0.9rem;">
        <i class="fas fa-filter mr-2"></i>Filter Laporan
    </div>
    <div class="card-body" style="background:#f8fff9;">
        <form method="GET" action="{{ route('admin.laporan.index') }}">
            <div class="row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-muted">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control form-control-sm"
                        style="border-radius:8px;"
                        value="{{ request('dari') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-muted">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control form-control-sm"
                        style="border-radius:8px;"
                        value="{{ request('sampai') }}">
                </div>
                @if($isPusat)
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-muted">Satker</label>
                    <select name="satker_id" class="form-control form-control-sm" style="border-radius:8px;">
                        <option value="">Semua Satker</option>
                        @foreach($satkers as $satker)
                        <option value="{{ $satker->id }}" {{ request('satker_id') == $satker->id ? 'selected' : '' }}>
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
                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-secondary flex-fill"
                        style="border-radius:8px;">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- STATISTIK --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:12px;border:none;background:linear-gradient(135deg,#1a6b3c,#2ecc71);">
            <div class="card-body d-flex align-items-center py-3">
                <div style="width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-gavel" style="color:white;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:white;line-height:1;">{{ $lelangs->count() }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.85);">Total Lelang Selesai</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:12px;border:none;background:linear-gradient(135deg,#f6c90e,#f39c12);">
            <div class="card-body d-flex align-items-center py-3">
                <div style="width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-money-bill-wave" style="color:white;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;color:white;line-height:1.2;">
                        Rp {{ number_format($totalNilai, 0, ',', '.') }}
                    </div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.85);">Total Nilai Terjual</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:12px;border:none;background:linear-gradient(135deg,#0c5460,#17a2b8);">
            <div class="card-body d-flex align-items-center py-3">
                <div style="width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-file-check" style="color:white;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:white;line-height:1;">{{ $sudahBAST }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.85);">Sudah Upload BAST</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:12px;border:none;background:linear-gradient(135deg,#c0392b,#e74c3c);">
            <div class="card-body d-flex align-items-center py-3">
                <div style="width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-times-circle" style="color:white;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:white;line-height:1;">{{ $belumBAST }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.85);">Belum Upload BAST</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABEL --}}
<div class="card shadow-sm" style="border-radius:12px;border:none;">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background:linear-gradient(90deg,#1a6b3c,#145c32);color:white;border-radius:12px 12px 0 0;">
        <span class="font-weight-bold" style="font-size:0.9rem;">
            <i class="fas fa-list mr-2"></i>Data Lelang Selesai
        </span>
        @if($lelangs->count() > 0)
        <button onclick="window.print()" class="btn btn-sm font-weight-bold"
            style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3);border-radius:8px;font-size:0.78rem;">
            <i class="fas fa-print mr-1"></i>Cetak PDF
        </button>
        @endif
    </div>

    <div class="card-body p-0">
        @if($lelangs->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.82rem;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="border-0 pl-4" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:12px 16px;width:40px;">#</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;">BARANG</th>
                        @if($isPusat)
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;">SATKER</th>
                        @endif
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;">PERKARA</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;">HARGA LIMIT</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;">HARGA TERJUAL</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;">PEMENANG</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.75rem;">TGL SELESAI</th>
                        <th class="border-0 text-center" style="color:#6c757d;font-weight:600;font-size:0.75rem;">LAPORAN</th>
                        @if(!$isPusat)
                        <th class="border-0 text-center" style="color:#6c757d;font-weight:600;font-size:0.75rem;width:70px;">AKSI</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($lelangs as $i => $lelang)
                    @php
                        $barang   = $lelang->barang;
                        $perkara  = $barang->perkara;
                        $satker   = $perkara->pengajuan->satker;
                        $laporan  = $lelang->laporan;
                        $selisih  = ($lelang->harga_tertinggi ?? 0) - $lelang->harga_awal;
                    @endphp
                    <tr>
                        <td class="pl-4 align-middle text-muted" style="padding:12px 16px;">{{ $i + 1 }}</td>

                        {{-- BARANG --}}
                        <td class="align-middle" style="padding:12px;">
                            <div class="font-weight-bold" style="color:#2d3748;">{{ $barang->nama_barang }}</div>
                            <small class="text-muted">{{ Str::limit($barang->deskripsi, 35) ?? '-' }}</small>
                        </td>

                        {{-- SATKER (pusat only) --}}
                        @if($isPusat)
                        <td class="align-middle" style="padding:12px;">
                            <span class="badge" style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;font-size:0.75rem;padding:4px 8px;">
                                {{ $satker->nama_satker ?? '-' }}
                            </span>
                        </td>
                        @endif

                        {{-- PERKARA --}}
                        <td class="align-middle" style="padding:12px;">
                            <div style="font-size:0.8rem;color:#2d3748;">{{ $perkara->nomor_perkara }}</div>
                            <small class="text-muted">{{ $perkara->nama_tersangka }}</small>
                        </td>

                        {{-- HARGA LIMIT --}}
                        <td class="align-middle" style="padding:12px;color:#6c757d;font-size:0.82rem;">
                            Rp {{ number_format($lelang->harga_awal, 0, ',', '.') }}
                        </td>

                        {{-- HARGA TERJUAL --}}
                        <td class="align-middle" style="padding:12px;">
                            <div class="font-weight-bold" style="color:#1a6b3c;">
                                Rp {{ number_format($lelang->harga_tertinggi ?? 0, 0, ',', '.') }}
                            </div>
                            @if($selisih > 0)
                            <small style="color:#2ecc71;font-size:0.72rem;">
                                <i class="fas fa-arrow-up"></i> +Rp {{ number_format($selisih, 0, ',', '.') }}
                            </small>
                            @endif
                        </td>

                        {{-- PEMENANG --}}
                        <td class="align-middle" style="padding:12px;">
                            @if($lelang->pemenang)
                            <div style="font-size:0.82rem;color:#2d3748;">{{ $lelang->pemenang->nama }}</div>
                            <small class="text-muted">{{ $lelang->pemenang->no_hp }}</small>
                            @else
                            <span class="text-muted" style="font-size:0.8rem;">—</span>
                            @endif
                        </td>

                        {{-- TGL SELESAI --}}
                        <td class="align-middle text-muted" style="padding:12px;font-size:0.8rem;">
                            {{ \Carbon\Carbon::parse($lelang->tanggal_selesai)->format('d M Y') }}
                        </td>

                        {{-- STATUS LAPORAN --}}
                        <td class="align-middle text-center" style="padding:12px;">
                            @if(!$laporan)
                                <span class="badge badge-danger" style="border-radius:20px;font-size:0.72rem;">
                                    ❌ Belum ada
                                </span>
                            @elseif($laporan->status === 'lengkap')
                                <span class="badge badge-success" style="border-radius:20px;font-size:0.72rem;">
                                    ✅ Lengkap
                                </span>
                                {{-- Preview link --}}
                                <div class="mt-1 d-flex justify-content-center bast-link" style="gap:4px;">
                                    @if($laporan->file_bast)
                                    <a href="{{ asset('storage/' . $laporan->file_bast) }}" target="_blank"
                                        title="Lihat BAST"
                                        style="font-size:0.68rem;color:#1a6b3c;">
                                        <i class="fas fa-file-alt"></i> BAST
                                    </a>
                                    @endif
                                    @if($laporan->file_bukti_bayar)
                                    <span style="color:#ccc;font-size:0.68rem;">|</span>
                                    <a href="{{ asset('storage/' . $laporan->file_bukti_bayar) }}" target="_blank"
                                        title="Lihat Bukti Bayar"
                                        style="font-size:0.68rem;color:#1a6b3c;">
                                        <i class="fas fa-receipt"></i> Billing
                                    </a>
                                    @endif
                                </div>
                            @else
                                <span class="badge badge-warning" style="border-radius:20px;font-size:0.72rem;color:#856404;">
                                    ⚠️ Sebagian
                                </span>
                                <div class="mt-1" style="font-size:0.68rem;color:#6c757d;">
                                    BAST: {{ $laporan->file_bast ? '✓' : '✗' }}
                                    | Billing: {{ $laporan->file_bukti_bayar ? '✓' : '✗' }}
                                </div>
                            @endif
                        </td>

                        {{-- AKSI (satker only) --}}
                        @if(!$isPusat)
                        <td class="align-middle text-center" style="padding:12px;">
                            <button class="btn btn-sm"
                                style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;padding:4px 10px;font-size:0.75rem;"
                                data-toggle="modal"
                                data-target="#modalLaporan-{{ $lelang->id }}"
                                title="{{ $laporan ? 'Edit Laporan' : 'Upload Laporan' }}">
                                <i class="fas fa-{{ $laporan ? 'edit' : 'upload' }}"></i>
                            </button>
                        </td>
                        @endif
                    </tr>

                    {{-- ========== MODAL LAPORAN (satker only) ========== --}}
                    @if(!$isPusat)
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

                                        {{-- ── BAGIAN BAST ── --}}
                                        <div class="mb-3 p-3 rounded" style="background:white;border:1px solid #e3e6f0;">
                                            <div class="font-weight-bold mb-2" style="color:#1a6b3c;font-size:0.8rem;">
                                                <i class="fas fa-file-alt mr-1"></i> Berita Acara Serah Terima (BAST)
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold text-muted">Nomor BAST</label>
                                                <input type="text" name="nomor_bast" class="form-control form-control-sm"
                                                    style="border-radius:8px;"
                                                    placeholder="Contoh: BAST/001/V/2026"
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
                                                    File BAST
                                                    @if(!$laporan?->file_bast) <span class="text-danger">*</span> @endif
                                                </label>
                                                <input type="file" name="file_bast" class="form-control form-control-sm"
                                                    style="border-radius:8px;"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    {{ !$laporan?->file_bast ? 'required' : '' }}>
                                                <small class="text-muted">PDF / JPG / PNG, maks 5MB</small>
                                                @if($laporan?->file_bast)
                                                <div class="mt-1">
                                                    <small style="color:#1a6b3c;">
                                                        <i class="fas fa-check-circle mr-1"></i>Sudah ada —
                                                        <a href="{{ asset('storage/' . $laporan->file_bast) }}" target="_blank" style="color:#1a6b3c;">lihat file</a>
                                                        (upload baru untuk mengganti)
                                                    </small>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- ── BAGIAN BILLING ── --}}
                                        <div class="mb-3 p-3 rounded" style="background:white;border:1px solid #e3e6f0;">
                                            <div class="font-weight-bold mb-2" style="color:#1a6b3c;font-size:0.8rem;">
                                                <i class="fas fa-receipt mr-1"></i> Bukti Pembayaran / Billing
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold text-muted">Nomor Billing</label>
                                                <input type="text" name="nomor_billing" class="form-control form-control-sm"
                                                    style="border-radius:8px;"
                                                    placeholder="Contoh: BIL/2026/0001"
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
                                                    File Bukti Bayar
                                                    @if(!$laporan?->file_bukti_bayar) <span class="text-danger">*</span> @endif
                                                </label>
                                                <input type="file" name="file_bukti_bayar" class="form-control form-control-sm"
                                                    style="border-radius:8px;"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    {{ !$laporan?->file_bukti_bayar ? 'required' : '' }}>
                                                <small class="text-muted">PDF / JPG / PNG, maks 5MB</small>
                                                @if($laporan?->file_bukti_bayar)
                                                <div class="mt-1">
                                                    <small style="color:#1a6b3c;">
                                                        <i class="fas fa-check-circle mr-1"></i>Sudah ada —
                                                        <a href="{{ asset('storage/' . $laporan->file_bukti_bayar) }}" target="_blank" style="color:#1a6b3c;">lihat file</a>
                                                        (upload baru untuk mengganti)
                                                    </small>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- CATATAN --}}
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold text-muted">Catatan <span class="font-weight-normal">(opsional)</span></label>
                                            <textarea name="catatan" class="form-control form-control-sm"
                                                style="border-radius:8px;" rows="2"
                                                placeholder="Catatan tambahan jika ada...">{{ $laporan->catatan ?? '' }}</textarea>
                                        </div>

                                    </div>
                                    <div class="modal-footer" style="background:#f8fff9;">
                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:6px;">
                                            Batal
                                        </button>
                                        <button type="submit" class="btn btn-sm font-weight-bold"
                                            style="background:#1a6b3c;color:white;border-radius:6px;padding:6px 14px;">
                                            <i class="fas fa-save mr-1"></i>Simpan Laporan
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- ========== END MODAL ========== --}}

                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-chart-bar fa-3x mb-3 d-block" style="color:#d1e7d8;"></i>
            <div class="font-weight-bold mb-1">Belum ada data lelang selesai</div>
            <small>{{ request('dari') ? 'Tidak ada data pada rentang waktu yang dipilih.' : 'Data akan muncul setelah lelang selesai.' }}</small>
        </div>
        @endif
    </div>
</div>

{{-- PRINT STYLE --}}
<style>
@media print {
    /* Sembunyikan semua elemen UI */
    .sidebar, .topbar, nav, .breadcrumb,
    .card-header button, .card-header span.badge,
    .modal, .modal-backdrop,
    form, button, a.btn,
    .alert, .col-md-3.mb-3,
    .filter-card,.header-card,.badge i { display: none !important; }
    thead tr th:last-child,.bast-link { display: none !important; }
    tbody tr td:last-child { display: none !important; }

    /* Tampilkan elemen khusus print */
    #print-header { display: block !important; }
    #print-statistik { display: flex !important; }

    /* Reset layout */
    body { font-size: 11px; margin: 0; padding: 0; background: white !important; }
    .container-fluid, .row, .col-md-3 { padding: 0 !important; margin: 0 !important; }

    /* Card tanpa shadow */
    .card {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
        border-radius: 0 !important;
        margin: 0 !important;
    }
    .card-header {
        background: #1a6b3c !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Tabel rapi */
    .table { width: 100% !important; border-collapse: collapse !important; }
    .table th, .table td {
        border: 1px solid #ccc !important;
        padding: 5px 8px !important;
        font-size: 10px !important;
        vertical-align: middle !important;
    }
    .table thead th {
        background: #f0f0f0 !important;
        font-weight: bold !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .badge {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    tr { page-break-inside: avoid; }
}
@page {
    size: A4 landscape;
    margin: 15mm;
}

/* Tersembunyi di layar, muncul saat print */
#print-header { display: none; }
#print-statistik { display: none; }
</style>

@endsection