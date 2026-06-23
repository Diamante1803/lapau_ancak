@extends('layouts.admin')

@section('content')

@php $isPusat = auth()->user()->role === 'admin_pusat'; @endphp

{{-- ================= HEADER ================= --}}
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 header-card" style="gap: 15px;">
    <div class="flex-shrink-0">
        <h4 class="font-weight-bold mb-0" style="color:#1a6b3c;">
            <i class="fas fa-chart-bar mr-2" style="color:#f6c90e;"></i>
            Laporan Lelang Selesai
        </h4>
    </div>

    {{-- ================= FILTER COMPACT (TENGAH HEADER) ================= --}}
    <div class="flex-grow-1 d-flex justify-content-center no-print">
        <form method="GET" action="{{ $isPusat ? route('admin.laporan.index') : route('satker.laporan.index') }}" 
              class="d-flex align-items-center bg-white p-2 shadow-sm border" style="border-radius: 16px; gap: 8px;">
            
            {{-- Dari Tanggal --}}
            <div class="position-relative" style="width: 140px;">
                <input type="text" name="dari" value="{{ request('dari') }}" 
                       class="interactive-field datepicker py-2" style="font-size: 0.8rem; padding-right: 32px;" placeholder="Mulai">
                <i class="material-icons position-absolute" style="right:8px; top:9px; font-size:18px; color:var(--c-theme-primary); pointer-events: none;">calendar_today</i>
            </div>

            <span class="text-muted small"><i class="fas fa-chevron-right fa-xs"></i></span>

            {{-- Sampai Tanggal --}}
            <div class="position-relative" style="width: 140px;">
                <input type="text" name="sampai" value="{{ request('sampai') }}" 
                       class="interactive-field datepicker py-2" style="font-size: 0.8rem; padding-right: 32px;" placeholder="Hingga">
                <i class="material-icons position-absolute" style="right:8px; top:9px; font-size:18px; color:var(--c-theme-primary); pointer-events: none;">event_available</i>
            </div>

            @if($isPusat)
            {{-- Satker Dropdown --}}
            <div class="custom-dropdown-container" style="width: 180px;">
                <input type="hidden" name="satker_id" id="hidden_satker_id" value="{{ request('satker_id') }}">
                <button type="button" class="interactive-field text-left d-flex justify-content-between align-items-center py-2" 
                        style="font-size: 0.8rem;" id="btnSatkerDropdown" onclick="toggleCustomDropdown('satker_laporan')">
                    <span id="labelSatkerDropdown" class="text-truncate mr-1">
                        {{ request('satker_id') ? ($satkers->firstWhere('id', request('satker_id'))->nama_satker ?? 'Satker') : 'Pilih Satker' }}
                    </span>
                    <i class="material-icons dropdown-toggle-icon" style="font-size:16px; color:var(--c-theme-primary);" id="icon-satker_laporan">expand_more</i>
                </button>
                <div class="custom-dropdown-menu shadow-lg d-none" id="menu-satker_laporan" style="min-width: 250px;">
                    <div class="p-2 border-bottom sticky-top bg-white">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari satker..." oninput="filterDropdownList('satker_laporan', this.value)" onclick="event.stopPropagation()">
                    </div>
                    <div class="list-wrapper">
                        <div class="dropdown-item-custom py-2 px-3 cursor-pointer no-filter" onclick="selectDropdownOption('satker_laporan', '', 'Pilih Satker', 'hidden_satker_id', 'labelSatkerDropdown')">
                            <span class="small font-weight-bold">Semua Satker</span>
                        </div>
                    @foreach($satkers as $s)
                        <div class="dropdown-item-custom py-2 px-3 cursor-pointer" data-search="{{ strtolower($s->nama_satker) }}" onclick="selectDropdownOption('satker_laporan', '{{ $s->id }}', '{{ $s->nama_satker }}', 'hidden_satker_id', 'labelSatkerDropdown')">
                            <div class="small font-weight-bold">{{ $s->nama_satker }}</div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
            @endif

            <button type="submit" class="btn btn-sm text-white shadow-sm d-flex align-items-center justify-content-center" style="background: #1a6b3c; border-radius: 8px; height: 34px; width: 34px;" title="Cari">
                <i class="fas fa-search fa-xs"></i>
            </button>
            <a href="{{ $isPusat ? route('admin.laporan.index') : route('satker.laporan.index') }}" 
               class="btn btn-sm btn-light border shadow-sm d-flex align-items-center justify-content-center" style="border-radius: 8px; height: 34px; width: 34px;" title="Reset">
                <i class="fas fa-undo fa-xs"></i>
            </a>
        </form>
    </div>

    @if($lelangs->count() > 0)
    <div class="d-flex no-print" style="gap:8px;">
        {{-- Toggle All --}}
        <button id="btnToggleAll" onclick="toggleAllSatker()" class="btn btn-sm btn-light border font-weight-bold"
            style="border-radius:8px;padding:8px 16px;">
            <i class="fas fa-expand-alt mr-1"></i> Buka Semua
        </button>

        {{-- Cetak PDF --}}
        <button data-toggle="modal" data-target="#modalPilihanCetak" class="btn btn-sm font-weight-bold"
            style="background:#c0392b;color:white;border-radius:8px;padding:8px 16px;">
            <i class="fas fa-print mr-1"></i> Cetak PDF
        </button>
        <button onclick="eksporExcel()" class="btn btn-sm font-weight-bold"
            style="background:#1a6b3c;color:white;border-radius:8px;padding:8px 16px;">
            <i class="fas fa-file-excel mr-1"></i> Ekspor Excel
        </button>
    </div>
    @endif
</div>

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

{{-- ================= STATISTIK ================= --}}
<div class="row mb-4 stat-cards">
    {{-- Total Lelang Selesai --}}
    <div class="col-6 col-md-3 mb-4">
        <x-statistic-card
            title="Total Lelang"
            value="{{ number_format($lelangs->count()) }}"
            unit="Lot"
            icon="fa-gavel"
            color="#1a6b3c"
        />
    </div>

    {{-- Realisasi PNBP --}}
    <div class="col-6 col-md-3 mb-4">
        <x-statistic-card
            title="Realisasi PNBP"
            value="Rp {{ number_format($totalNilaiBilling, 0, ',', '.') }}"
            icon="fa-money-bill-wave"
            color="#f39c12"
            description="Dari terjual Rp {{ number_format($totalNilai, 0, ',', '.') }}"
        />
    </div>

    {{-- Sudah BAST --}}
    <div class="col-6 col-md-3 mb-4">
        <x-statistic-card
            title="BAST Selesai"
            value="{{ number_format($sudahBAST) }}"
            unit="Lot"
            icon="fa-file-alt"
            color="#17a2b8"
        />
    </div>

    {{-- Belum BAST --}}
    <div class="col-6 col-md-3 mb-4">
        <x-statistic-card
            title="Belum BAST"
            value="{{ number_format($belumBAST) }}"
            unit="Lot"
            icon="fa-times-circle"
            color="#c0392b"
        />
    </div>
</div>

{{-- ================= TABEL PER SATKER ================= --}}
@if($lelangs->count() > 0)

@foreach($grouped as $satkerNama => $group)
@php $satkerId = 'satker-' . $loop->index; @endphp
<div class="card shadow-sm mb-3" style="border-radius:12px;border:none;">

    {{-- Header Satker --}}
    <div class="card-header d-flex align-items-center justify-content-between cursor-pointer"
        style="background:linear-gradient(90deg,#1a6b3c,#145c32);border-radius:12px 12px 0 0;padding:12px 20px;"
        onclick="toggleSatker('{{ $satkerId }}')">
        
        <div id="chevron-{{ $satkerId }}" class="mr-2 no-print" style="transition: transform 0.3s;">
            <i class="fas fa-chevron-down text-white small"></i>
        </div>

        <span class="font-weight-bold text-white flex-grow-1" style="font-size:0.9rem;">
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

    <div class="card-body p-0 body-satker" id="body-{{ $satkerId }}" style="overflow: hidden; max-height: 0; transition: max-height 0.4s ease, opacity 0.3s ease; opacity: 0;">
        <div class="table-responsive">
            <table id="tabel-{{ $satkerId }}" class="table table-hover mb-0 table-laporan-satker" style="font-size:0.82rem;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="border-0 pl-4 col-no" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 16px;width:36px;">#</th>
                        <th class="border-0 col-barang" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">NAMA BARANG</th>
                        <th class="border-0 col-limit" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">HARGA LIMIT</th>
                        <th class="border-0 col-terjual" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">HARGA TERJUAL</th>
                        <th class="border-0 col-pemenang" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">PEMENANG</th>
                        <th class="border-0 col-tgl" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">TGL SELESAI</th>
                        <th class="border-0 text-center col-laporan" style="color:#6c757d;font-weight:600;font-size:0.75rem;padding:10px 12px;">LAPORAN</th>
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
                        <td class="pl-4 align-middle text-muted col-no" style="padding:10px 16px;">{{ $i + 1 }}</td>

                        {{-- NAMA BARANG --}}
                        <td class="align-middle col-barang" style="padding:10px 12px;">
                            <div class="font-weight-bold" style="color:#2d3748;font-size:0.82rem;">
                                {{ $barang->nama_barang }}
                            </div>
                            <small class="text-muted">{{ $perkara->nomor_perkara }} — {{ $perkara->nama_tersangka }}</small>
                        </td>

                        {{-- HARGA LIMIT --}}
                        <td class="align-middle col-limit" style="padding:10px 12px;color:#6c757d;font-size:0.82rem;">
                            Rp {{ number_format($limit, 0, ',', '.') }}
                        </td>

                        {{-- HARGA TERJUAL + PERSENTASE --}}
                        <td class="align-middle col-terjual" style="padding:10px 12px;">
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
                        <td class="align-middle col-pemenang" style="padding:10px 12px;">
                            @if($lelang->pemenang)
                            <div style="font-size:0.82rem;color:#2d3748;">{{ $lelang->pemenang->nama }}</div>
                            <small class="text-muted">{{ $lelang->pemenang->no_hp }}</small>
                            @else
                            <span class="text-muted" style="font-size:0.8rem;">—</span>
                            @endif
                        </td>

                        {{-- TGL SELESAI --}}
                        <td class="align-middle text-muted col-tgl" style="padding:10px 12px;font-size:0.8rem;">
                            {{ \Carbon\Carbon::parse($lelang->tanggal_selesai)->format('d M Y') }}
                        </td>

                        {{-- STATUS LAPORAN --}}
                        <td class="align-middle text-center col-laporan" style="padding:10px 12px;">
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
                        <td colspan="{{ $isPusat ? 2 : 2 }}" class="pl-4 align-middle font-weight-bold col-barang"
                            style="padding:10px 16px;color:#1a6b3c;font-size:0.82rem;">
                            Total {{ $satkerNama }}
                        </td>
                        <td class="align-middle font-weight-bold col-limit" style="padding:10px 12px;color:#6c757d;font-size:0.82rem;">
                            Rp {{ number_format($group['total_limit'], 0, ',', '.') }}
                        </td>
                        <td class="align-middle col-terjual" style="padding:10px 12px;">
                            <div class="font-weight-bold" style="color:#1a6b3c;font-size:0.85rem;">
                                Rp {{ number_format($group['total_terjual'], 0, ',', '.') }}
                            </div>
                            <small style="color:#27ae60;font-size:0.7rem;">
                                <i class="fas fa-arrow-up"></i> {{ $group['kenaikan'] }}% dari limit
                            </small>
                        </td>
                        <td colspan="{{ $isPusat ? 3 : 4 }}" class="align-middle text-muted text-center bast-info col-pemenang"
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

{{-- ================= MODAL PILIHAN CETAK ================= --}}
<div class="modal fade no-print" id="modalPilihanCetak" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-print mr-2" style="color:#f6c90e;"></i>
                    Opsi Cetak Laporan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4" style="background:#f8fff9;">
                <p class="text-muted small mb-3">Pilih kolom yang ingin ditampilkan dalam laporan PDF:</p>
                
                <div class="row">
                    @php
                        $pilihanKolom = [
                            'col-no'       => 'Nomor (#)',
                            'col-barang'   => 'Nama Barang',
                            'col-limit'    => 'Harga Limit',
                            'col-terjual'  => 'Harga Terjual',
                            'col-pemenang' => 'Nama Pemenang',
                            'col-tgl'      => 'Tanggal Selesai',
                            'col-laporan'  => 'Status Dokumen'
                        ];
                    @endphp

                    @foreach($pilihanKolom as $id => $label)
                    <div class="col-md-6 mb-2">
                        <div class="custom-control custom-checkbox shadow-sm bg-white p-2 px-3" style="border-radius:10px; border:1px solid #e0eeea;">
                            <input type="checkbox" class="custom-control-input check-kolom" id="check-{{ $id }}" data-column="{{ $id }}" checked>
                            <label class="custom-control-label small font-weight-bold text-dark cursor-pointer" for="check-{{ $id }}">
                                {{ $label }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="alert alert-info mt-3 py-2 mb-0" style="border-radius:8px; font-size:0.75rem; border:none; background:rgba(26,107,60,0.08); color:#1a6b3c;">
                    <i class="fas fa-info-circle mr-1"></i>
                    Kertas akan otomatis diset ke mode <strong>Landscape</strong> untuk hasil terbaik.
                </div>
            </div>
            <div class="modal-footer" style="background:#f8fff9; border-top:1px solid #e0eeea;">
                <button type="button" class="btn btn-sm btn-light border font-weight-bold px-3" data-dismiss="modal" style="border-radius:8px;">Batal</button>
                <button type="button" onclick="jalankanCetak()" class="btn btn-sm font-weight-bold text-white px-4" 
                    style="background:#1a6b3c; border-radius:8px;">
                    <i class="fas fa-file-pdf mr-1"></i> Mulai Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .print-hidden-manual { display: none !important; }
</style>

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
    .lt-info, .lt-pag-wrap, .body-satker,
    .lt-pagination, .lt-bottom-bar {
        display: none !important;
    }

    #print-header { display: block !important; }

    body { font-size: 10px; margin: 0; padding: 0; background: white !important; }
    .container-fluid { padding: 0 !important; }

    .body-satker { 
        max-height: none !important; 
        opacity: 1 !important; 
        display: block !important; 
    }

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

    .print-hidden-manual { display: none !important; }
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

{{-- Gunakan xlsx-js-style agar fitur alignment/styling tidak merusak proses download --}}
<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
<script>
const data = {!! json_encode($excelData) !!};

function eksporExcel() {
    const wb   = XLSX.utils.book_new();
    const rows = [];
    const merges = [];

    rows.push([
        'No', 'Satker', 'Nama Barang', 'Nomor Perkara', 'Tersangka',
        'Harga Limit', 'Harga Terjual', 'Kenaikan (%)',
        'Pemenang', 'No HP Pemenang', 'Tgl Selesai', 'Status Laporan'
    ]);

    let globalNo = 1; // Nomor urut global per Satker
    let useAltColor = false;
    const rowBgColors = {};

    data.forEach(group => {
        const bgColor = useAltColor ? "F7FBF7" : "FFFFFF"; // Hijau sangat tipis vs Putih
        const startRow = rows.length; // Catat baris awal sebelum memasukkan item

        group.items.forEach(item => {
            rows.push([
                globalNo,
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
            rowBgColors[rows.length] = bgColor;
        });

        const endRow = rows.length - 1; // Catat baris terakhir item
        // Jika dalam grup ada lebih dari 1 item, lakukan merge pada kolom No (0) dan Satker (1)
        if (endRow > startRow) {
            merges.push({
                s: { r: startRow, c: 0 },
                e: { r: endRow,   c: 0 }
            });
            merges.push({
                s: { r: startRow, c: 1 }, // s: Start (r: row index, c: col index)
                e: { r: endRow,   c: 1 }  // e: End
            });
        }

        globalNo++; // Lanjutkan nomor untuk satker berikutnya

        const totalLimit   = group.items.reduce((s, i) => s + parseFloat(i.harga_limit),   0);
        const totalTerjual = group.items.reduce((s, i) => s + parseFloat(i.harga_terjual), 0);
        const kenaikan     = totalLimit > 0
            ? Math.round(((totalTerjual - totalLimit) / totalLimit) * 10000) / 100
            : 0;
        
        const totalRowIdx = rows.length;
        rows.push([
            '', `TOTAL ${group.nama_satker}`, '', '', '',
            totalLimit, totalTerjual, kenaikan,
            '', '', '', ''
        ]);
        // Merge kolom label (B-E) dan kolom sisa (I-L)
        merges.push({ s: { r: totalRowIdx, c: 1 }, e: { r: totalRowIdx, c: 4 } });
        merges.push({ s: { r: totalRowIdx, c: 8 }, e: { r: totalRowIdx, c: 11 } });
        rowBgColors[rows.length] = bgColor;

        rows.push([]);

        useAltColor = !useAltColor; // Ganti warna untuk grup satker berikutnya
    });

    const allItems     = data.flatMap(g => g.items);
    const grandLimit   = allItems.reduce((s, i) => s + parseFloat(i.harga_limit),   0);
    const grandTerjual = allItems.reduce((s, i) => s + parseFloat(i.harga_terjual), 0);
    const grandKenaikan = grandLimit > 0
        ? Math.round(((grandTerjual - grandLimit) / grandLimit) * 10000) / 100
        : 0;

    const grandTotalRowIdx = rows.length;
    rows.push([
        '', 'GRAND TOTAL', '', '', '',
        grandLimit, grandTerjual, grandKenaikan,
        '', '', '', ''
    ]);
    merges.push({ s: { r: grandTotalRowIdx, c: 1 }, e: { r: grandTotalRowIdx, c: 4 } });
    merges.push({ s: { r: grandTotalRowIdx, c: 8 }, e: { r: grandTotalRowIdx, c: 11 } });

    const ws = XLSX.utils.aoa_to_sheet(rows);
    ws['!merges'] = merges; // Terapkan konfigurasi merge ke worksheet

    ws['!cols'] = [
        { wch: 4  }, { wch: 30 }, { wch: 30 }, { wch: 20 }, { wch: 20 },
        { wch: 18 }, { wch: 18 }, { wch: 12 }, { wch: 25 }, { wch: 15 },
        { wch: 12 }, { wch: 14 },
    ];

    // Atur Tinggi Baris (hpt = height in points)
    const rowHeights = [];
    for (let r = 0; r < rows.length; r++) {
        if (r === 0) {
            rowHeights.push({ hpt: 35 }); // Header lebih tinggi
        } else {
            rowHeights.push({ hpt: 22 }); // Baris data dengan padding
        }
    }
    ws['!rows'] = rowHeights;

    Object.keys(ws).forEach(cell => {
        if (cell[0] === '!') return;
        const col = cell.replace(/[0-9]/g, '');
        const rowNum = parseInt(cell.replace(/\D/g, ''));

        // Deteksi apakah baris ini adalah baris Total atau Grand Total melalui kolom B
        const bCell = ws['B' + rowNum];
        const isGrandTotal = bCell && bCell.v && String(bCell.v).includes('GRAND TOTAL');
        const isTotalRow = bCell && bCell.v && String(bCell.v).includes('TOTAL');
        
        // Inisialisasi objek style jika belum ada
        if (!ws[cell].s) ws[cell].s = {};

        // 1. Terapkan Border ke seluruh sel (tabel keseluruhan)
        ws[cell].s.border = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "thin", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        };

        // 2. Styling Judul Kolom (Baris 1)
        if (rowNum === 1) {
            ws[cell].s.font = { bold: true, color: { rgb: "FFFFFF" } };
            ws[cell].s.fill = { 
                patternType: "solid", 
                fgColor: { rgb: "1A6B3C" } // Hijau tema Lapau Ancak
            };
            ws[cell].s.alignment = { horizontal: 'center', vertical: 'center' };
        } else {
            // Default alignment untuk isi
            ws[cell].s.alignment = ws[cell].s.alignment || { vertical: 'center' };
            
            // Kolom No, Tgl Selesai, dan Status -> Center
            if (['A', 'K', 'L'].includes(col)) {
                ws[cell].s.alignment.horizontal = 'center';
            }

            if (isTotalRow) {
                ws[cell].s.font = { bold: true };
                if (isGrandTotal) {
                    ws[cell].s.fill = { 
                        patternType: "solid", 
                        fgColor: { rgb: "F6C90E" } // Kuning kontras untuk Grand Total
                    };
                    ws[cell].s.font.color = { rgb: "1A6B3C" };
                } else {
                    ws[cell].s.fill = { 
                        patternType: "solid", 
                        fgColor: { rgb: "D1E7D8" } // Hijau lebih tegas untuk baris total satker
                    };
                }
                // Border tebal untuk baris total agar terpisah dari data
                ws[cell].s.border.top = { style: "medium", color: { rgb: "000000" } };
                ws[cell].s.border.bottom = { style: "medium", color: { rgb: "000000" } };
            } else {
                // Terapkan background warna selang-seling (hanya untuk baris data)
                if (rowBgColors[rowNum]) {
                    ws[cell].s.fill = { 
                        patternType: "solid", 
                        fgColor: { rgb: rowBgColors[rowNum] }
                    };
                }
            }
        }

        // Atur agar kolom No (A) dan Satker (B) menjadi align top
        if (['A', 'B'].includes(col) && rowNum > 1) {
            ws[cell].s.alignment.vertical = 'top';
            ws[cell].s.alignment.wrapText = true;
        }

        // Format Angka Currency Rp. dan Rata Kanan
        if (['F', 'G'].includes(col) && typeof ws[cell].v === 'number') {
            ws[cell].z = '"Rp "#,##0';
            ws[cell].s.alignment.horizontal = 'right';
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
function jalankanCetak() {
    // 1. Ambil semua checkbox yang TIDAK dicentang
    const unchecked = document.querySelectorAll('.check-kolom:not(:checked)');
    
    // 2. Reset semua kolom ke tampilan normal dulu
    document.querySelectorAll('[class*="col-"]').forEach(el => el.classList.remove('print-hidden-manual'));

    // 3. Sembunyikan kolom yang dipilih user
    unchecked.forEach(cb => {
        const colClass = cb.dataset.column;
        document.querySelectorAll('.' + colClass).forEach(el => {
            el.classList.add('print-hidden-manual');
        });
    });

    // 4. Tutup modal dan panggil print
    $('#modalPilihanCetak').modal('hide');
    setTimeout(() => {
        window.print();
    }, 500);
}
</script>
<script>
function toggleSatker(id) {
    const body = document.getElementById('body-' + id);
    const chevron = document.getElementById('chevron-' + id);
    if (!body) return;

    const isOpen = body.style.maxHeight !== '0px';

    if (isOpen) {
        body.style.maxHeight = '0px';
        body.style.opacity = '0';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        body.style.maxHeight = body.scrollHeight + 'px';
        body.style.opacity = '1';
        chevron.style.transform = 'rotate(180deg)';
    }
}

let allOpen = false;
function toggleAllSatker() {
    allOpen = !allOpen;
    const bodies = document.querySelectorAll('.body-satker');
    const chevrons = document.querySelectorAll('[id^="chevron-"]');
    const btn = document.getElementById('btnToggleAll');

    bodies.forEach(body => {
        body.style.maxHeight = allOpen ? body.scrollHeight + 'px' : '0px';
        body.style.opacity = allOpen ? '1' : '0';
    });

    chevrons.forEach(ch => {
        ch.style.transform = allOpen ? 'rotate(180deg)' : 'rotate(0deg)';
    });

    btn.innerHTML = allOpen ? '<i class="fas fa-compress-alt mr-1"></i> Tutup Semua' : '<i class="fas fa-expand-alt mr-1"></i> Buka Semua';
}

document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi semua tabel satker yang ada
    document.querySelectorAll('.table-laporan-satker').forEach(table => {
        LapauTable.init(table.id, {
            pageSize: 10,
            sortDir: 'desc'
        });
    });
});
</script>
@endpush