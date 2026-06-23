@extends('layouts.admin')

@section('title', 'Detail Lelang — ' . $lelang->barang->nama_barang)

@section('content')

{{-- PAGE HEADER --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="font-weight-bold mb-0" style="color: #1a6b3c;">
            <i class="fas fa-gavel mr-2" style="color: #f6c90e;"></i>
            Detail Lelang
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0" style="font-size: 0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('admin.lelang.dashboard') }}" style="color:#1a6b3c;">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.lelang.aktif') }}" style="color:#1a6b3c;">Lelang</a></li>
                <li class="breadcrumb-item active text-muted">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.lelang.aktif') }}"
        class="btn btn-sm btn-outline-secondary"
        style="border-radius: 8px;">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

@php
    $barang  = $lelang->barang;
    $perkara = $barang->perkara;
    $satker  = $perkara->pengajuan->satker ?? null;
    $fotos   = $barang->fotoBarang;
    $penawarans = $lelang->penawarans;
    $topPenawaran = $penawarans->first();

    $statusColor = match($lelang->status) {
        'active'    => ['bg' => '#d4edda', 'text' => '#1a6b3c', 'icon' => 'fa-fire',        'label' => 'Sedang Berlangsung'],
        'scheduled' => ['bg' => '#d1ecf1', 'text' => '#0c5460', 'icon' => 'fa-calendar-alt','label' => 'Terjadwal'],
        'closed'    => ['bg' => '#e2e3e5', 'text' => '#383d41', 'icon' => 'fa-check-circle', 'label' => 'Selesai'],
        'cancelled' => ['bg' => '#f8d7da', 'text' => '#721c24', 'icon' => 'fa-times-circle', 'label' => 'Dibatalkan'],
        default     => ['bg' => '#fff3cd', 'text' => '#856404', 'icon' => 'fa-clock',        'label' => 'Menunggu'],
    };
@endphp

<div class="row">

    {{-- ===================== KOLOM KIRI: INFO BARANG ===================== --}}
    <div class="col-lg-5 mb-4">

        {{-- FOTO BARANG --}}
        <div class="card shadow-sm mb-4" style="border-radius: 12px; border: none; overflow: hidden;">
            <div style="position: relative; background: #111; height: 300px;">

                @if($fotos->count() > 0)
                    @foreach($fotos as $fIndex => $foto)
                    <div class="slide-detail"
                        id="slide-detail-{{ $fIndex }}"
                        style="display: {{ $fIndex == 0 ? 'block' : 'none' }}; height: 300px;">
                        <img src="{{ asset('storage/' . $foto->file_path) }}"
                            style="width: 100%; height: 300px; object-fit: cover; opacity: 0.92; cursor: pointer;"
                            onclick="previewDokumen('{{ asset('storage/' . $foto->file_path) }}', '{{ $barang->nama_barang }}')">
                    </div>
                    @endforeach

                    @if($fotos->count() > 1)
                    <button onclick="slideDetail(-1)" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:white;border:none;width:32px;height:32px;border-radius:50%;font-size:13px;cursor:pointer;z-index:10;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="slideDetail(1)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:white;border:none;width:32px;height:32px;border-radius:50%;font-size:13px;cursor:pointer;z-index:10;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <div style="position:absolute;bottom:10px;width:100%;text-align:center;z-index:10;">
                        @foreach($fotos as $dIndex => $dot)
                        <span class="dot-detail" id="dot-detail-{{ $dIndex }}"
                            onclick="goToSlideDetail({{ $dIndex }})"
                            style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $dIndex == 0 ? 'white' : 'rgba(255,255,255,0.4)' }};margin:0 3px;cursor:pointer;transition:0.2s;">
                        </span>
                        @endforeach
                    </div>
                    <div style="position:absolute;top:10px;right:10px;">
                        <span style="background:rgba(0,0,0,0.55);color:white;border-radius:20px;font-size:0.7rem;padding:3px 10px;">
                            <i class="fas fa-images mr-1"></i>{{ $fotos->count() }} foto
                        </span>
                    </div>
                    @endif

                @else
                    <div style="height:300px;display:flex;align-items:center;justify-content:center;flex-direction:column;color:#555;">
                        <i class="fas fa-image fa-3x mb-2"></i>
                        <span style="font-size:0.85rem;">Belum ada foto</span>
                    </div>
                @endif

                {{-- STATUS BADGE OVERLAY --}}
                <div style="position:absolute;top:10px;left:10px;">
                    <span style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;">
                        <i class="fas {{ $statusColor['icon'] }} mr-1"></i>{{ $statusColor['label'] }}
                    </span>
                </div>

            </div>
        </div>

        {{-- INFO BARANG --}}
        <div class="card shadow-sm mb-4" style="border-radius: 12px; border: none;">
            <div class="card-header font-weight-bold" style="background: linear-gradient(90deg,#1a6b3c,#145c32); color:white; border-radius:12px 12px 0 0; font-size:0.9rem;">
                <i class="fas fa-box mr-2"></i>Informasi Barang
            </div>
            <div class="card-body" style="font-size: 0.88rem;">
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width:40%;">Nama Barang</td>
                            <td class="font-weight-bold" style="color:#2d3748;">{{ $barang->nama_barang }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Deskripsi</td>
                            <td>{{ $barang->deskripsi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga Awal</td>
                            <td class="font-weight-bold" style="color:#1a6b3c;">
                                Rp {{ number_format($barang->harga_awal, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Perkara</td>
                            <td><span class="badge badge-light border">{{ $perkara->nomor_perkara }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tersangka</td>
                            <td>{{ $perkara->nama_tersangka }}</td>
                        </tr>
                        @if($satker)
                        <tr>
                            <td class="text-muted">Satker</td>
                            <td>{{ $satker->nama_satker ?? '-' }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- INFO LELANG --}}
        <div class="card shadow-sm" style="border-radius: 12px; border: none;">
            <div class="card-header font-weight-bold" style="background: linear-gradient(90deg,#1a6b3c,#145c32); color:white; border-radius:12px 12px 0 0; font-size:0.9rem;">
                <i class="fas fa-gavel mr-2"></i>Informasi Lelang
            </div>
            <div class="card-body" style="font-size: 0.88rem;">
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width:40%;">Mulai</td>
                            <td class="font-weight-bold">
                                {{ \Carbon\Carbon::parse($lelang->tanggal_mulai)->format('d M Y, H:i') }} WIB
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Selesai</td>
                            <td class="font-weight-bold">
                                {{ \Carbon\Carbon::parse($lelang->tanggal_selesai)->format('d M Y, H:i') }} WIB
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Durasi</td>
                            <td>
                                @php
                                    $durasi = \Carbon\Carbon::parse($lelang->tanggal_mulai)
                                                ->diffForHumans(\Carbon\Carbon::parse($lelang->tanggal_selesai), true);
                                @endphp
                                {{ $durasi }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Penawar</td>
                            <td id="info-total-penawar">
                                <span class="font-weight-bold" style="color:#1a6b3c;">
                                    {{ $penawarans->count() }} orang
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Penawaran Tertinggi</td>
                            <td id="info-harga-tertinggi" class="font-weight-bold" style="color:#c0392b; font-size:0.95rem;">
                                @if($topPenawaran)
                                    Rp {{ number_format($topPenawaran->nilai_penawaran, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">Belum ada penawaran</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- COUNTDOWN jika active --}}
                @if($lelang->status == 'active')
                <div class="mt-3 p-3 rounded text-center"
                    style="background: linear-gradient(135deg, #d4edda, #e8f5ee); border: 1px solid #c3e6cb;">
                    <div class="small text-muted mb-1">Sisa Waktu Lelang</div>
                    <div class="d-flex justify-content-center align-items-end mt-1 js-countdown" style="gap: 8px;"
                        data-end="{{ $lelang->tanggal_selesai->toIso8601String() }}">
                        @foreach([['id'=>'cd-hari','label'=>'Hari'],['id'=>'cd-jam','label'=>'Jam'],['id'=>'cd-menit','label'=>'Menit'],['id'=>'cd-detik','label'=>'Detik']] as $unit)
                        <div class="text-center">
                            <div class="font-weight-bold {{ str_replace('cd-', 'js-cd-', $unit['id']) }}"
                                style="font-size:1.6rem; color:#1a6b3c; line-height:1; min-width:36px; letter-spacing:1px;">
                                00
                            </div>
                            <div style="font-size:0.65rem; color:#6c757d; text-transform:uppercase; letter-spacing:1px;">
                                {{ $unit['label'] }}
                            </div>
                        </div>
                        @if(!$loop->last)
                        <div class="font-weight-bold pb-3" style="color:#1a6b3c; font-size:1.2rem;">:</div>
                        @endif
                        @endforeach
                    </div>
                    <div id="countdown-done" class="font-weight-bold text-danger mt-1" style="display:none; font-size:0.9rem;">
                        <i class="fas fa-check-circle mr-1"></i>Waktu Lelang Telah Habis
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>

    {{-- ===================== KOLOM KANAN: DAFTAR PENAWAR ===================== --}}
    <div class="col-lg-7 mb-4">

        {{-- RINGKASAN STATISTIK --}}
        <div class="row mb-4">

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100 text-center" style="border-radius:12px; border:1px solid #d4edda;">
                    <div class="card-body py-3">
                        <div id="stat-total-penawar" style="font-size:1.6rem; font-weight:700; color:#1a6b3c; line-height:1;">
                            {{ $penawarans->count() }}
                        </div>
                        <div style="font-size:0.78rem; color:#6c757d; margin-top:4px;">Total Penawar</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100 text-center" style="border-radius:12px; border:1px solid #ffeeba;">
                    <div class="card-body py-3">
                        <div id="stat-harga-tertinggi" style="font-size:1.1rem; font-weight:700; color:#856404; line-height:1.3;">
                            @if($topPenawaran)
                                Rp {{ number_format($topPenawaran->nilai_penawaran, 0, ',', '.') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div style="font-size:0.78rem; color:#6c757d; margin-top:4px;">Penawaran Tertinggi</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100 text-center" style="border-radius:12px; border:1px solid #f5c6cb;">
                    <div class="card-body py-3">
                        <div id="stat-selisih" style="font-size:1.1rem; font-weight:700; color:#c0392b; line-height:1.3;">
                            @if($penawarans->count() > 0)
                                @php $selisih = $topPenawaran->nilai_penawaran - $barang->harga_awal; @endphp
                                +Rp {{ number_format($selisih, 0, ',', '.') }}
                            @else
                                <span class="text-muted">Rp 0</span>
                            @endif
                        </div>
                        <div style="font-size:0.78rem; color:#6c757d; margin-top:4px;">Selisih dari Harga Awal</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABEL PENAWAR --}}
        <div class="card shadow-sm" style="border-radius: 12px; border: none;">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background: linear-gradient(90deg,#1a6b3c,#145c32); color:white; border-radius:12px 12px 0 0;">
                <span class="font-weight-bold" style="font-size:0.9rem;">
                    <i class="fas fa-users mr-2"></i>Daftar Penawar
                </span>
                <span id="badge-total-penawaran" class="badge" style="background:rgba(255,255,255,0.2); color:white; border-radius:20px; font-size:0.75rem;">
                    {{ $penawarans->count() }} penawaran
                </span>
            </div>
            <div class="card-body p-0">

                {{-- Wrapper selalu ada agar polling bisa inject --}}
                <div id="list-penawaran">
                    @if($penawarans->count() > 0)
                    <div class="table-responsive">
                        <table id="tabelPenawaran" class="table table-hover mb-0" style="font-size:0.875rem; width:100%;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="border-0 pl-4" style="width:50px;color:#6c757d;font-weight:600;font-size:0.78rem;" data-no-sort>NO</th>
                                    <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">PENAWAR</th>
                                    <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">NILAI PENAWARAN</th>
                                    <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">WAKTU</th>
                                    <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">STATUS</th>
                                    @if(auth()->user()->role === 'admin_pusat' && $lelang->status === 'active')
                                    <th class="border-0 text-center" style="color:#6c757d;font-weight:600;font-size:0.78rem;">AKSI</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penawarans as $rank => $penawaran)
                                @php $isTop = $rank === 0; @endphp
                                <tr style="{{ $isTop ? 'background:#f0fff4;' : '' }}">

                                    {{-- RANK --}}
                                    <td class="pl-4 align-middle">
                                        @if($rank === 0)
                                            <span style="background:#f6c90e;color:#5a4000;border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;">
                                                <i class="fas fa-trophy"></i>
                                            </span>
                                        @elseif($rank === 1)
                                            <span style="background:#e0e0e0;color:#555;border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;">2</span>
                                        @elseif($rank === 2)
                                            <span style="background:#f4a460;color:#fff;border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;">3</span>
                                        @else
                                            <span class="text-muted" style="font-size:0.82rem;padding-left:6px;">{{ $rank + 1 }}</span>
                                        @endif
                                    </td>

                                    {{-- PENAWAR --}}
                                    <td class="align-middle" data-sort="{{ strtolower($penawaran->pembeli->nama ?? 'anonim') }}">
                                        <div class="d-flex align-items-center">
                                            <div style="width:34px;height:34px;border-radius:50%;background:{{ $isTop ? '#1a6b3c' : '#dee2e6' }};display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                                                <i class="fas fa-user" style="color:{{ $isTop ? 'white' : '#6c757d' }};font-size:0.8rem;"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold" style="color:#2d3748;font-size:0.875rem;">
                                                    {{ $penawaran->pembeli->nama ?? 'Anonim' }}
                                                </div>
                                                <div class="text-muted" style="font-size:0.75rem;">
                                                    {{ $penawaran->pembeli->email ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- NILAI --}}
                                    <td class="align-middle" data-sort="{{ (float) $penawaran->nilai_penawaran }}">
                                        <div class="font-weight-bold" style="color:{{ $isTop ? '#1a6b3c' : '#2d3748' }};font-size:{{ $isTop ? '0.95rem' : '0.875rem' }};">
                                            Rp {{ number_format($penawaran->nilai_penawaran, 0, ',', '.') }}
                                        </div>
                                        @if($isTop)
                                        <small style="color:#1a6b3c;font-size:0.72rem;">
                                            <i class="fas fa-arrow-up mr-1"></i>Tertinggi
                                        </small>
                                        @endif
                                    </td>

                                    {{-- WAKTU --}}
                                    <td class="align-middle text-muted" style="font-size:0.8rem;" data-sort="{{ $penawaran->created_at->timestamp }}">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($penawaran->created_at)->format('d M Y') }}<br>
                                        <span style="font-size:0.75rem;">{{ \Carbon\Carbon::parse($penawaran->created_at)->format('H:i') }} WIB</span>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="align-middle">
                                        @if($isTop && $lelang->status == 'closed')
                                            <span class="badge badge-success" style="border-radius:6px;font-size:0.72rem;padding:4px 8px;">
                                                <i class="fas fa-check mr-1"></i>Pemenang
                                            </span>
                                        @elseif($isTop)
                                            <span class="badge badge-warning text-dark" style="border-radius:6px;font-size:0.72rem;padding:4px 8px;">
                                                <i class="fas fa-star mr-1"></i>Tertinggi
                                            </span>
                                        @else
                                            <span class="badge badge-light border" style="border-radius:6px;font-size:0.72rem;padding:4px 8px;color:#6c757d;">
                                                Kalah
                                            </span>
                                        @endif
                                    </td>

                                    {{-- AKSI — hanya admin pusat, hanya penawaran tertinggi, hanya saat active --}}
                                    @if(auth()->user()->role === 'admin_pusat' && $lelang->status === 'active')
                                    <td class="align-middle text-center">
                                        @if($isTop)
                                        @php
                                            $nilaiFormatted = number_format($penawaran->nilai_penawaran, 0, ',', '.');
                                            $namaPenawar    = addslashes($penawaran->pembeli->nama ?? '-');
                                        @endphp

                                        <form id="form-hapus-bid-{{ $penawaran->id }}"
                                            action="{{ route('admin.lelang.hapusPenawaranTertinggi', $lelang->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm"
                                                style="background:#fff3cd;color:#856404;border-radius:6px;padding:4px 10px;font-size:0.75rem;"
                                                title="Hapus penawaran tidak wajar"
                                                onclick="swalSubmitForm('form-hapus-bid-{{ $penawaran->id }}', {
                                                    title: 'Hapus Penawaran Tertinggi?',
                                                    text: 'Penawaran Rp {{ $nilaiFormatted }} oleh {{ $namaPenawar }} akan dihapus. Penawaran berikutnya otomatis menjadi tertinggi.',
                                                    icon: 'warning',
                                                    confirmText: 'Ya, Hapus',
                                                    confirmColor: '#856404'
                                                })">
                                                <i class="fas fa-user-minus mr-1"></i>Hapus
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted" style="font-size:0.75rem;">—</span>
                                        @endif
                                    </td>
                                    @endif

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#d1e7d8;"></i>
                        <div class="font-weight-bold mb-1">Belum ada penawaran</div>
                        <small>Penawaran akan muncul di sini setelah lelang aktif</small>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- AKSI ADMIN (jika active atau scheduled) --}}
        @if(auth()->user()->role === 'admin_pusat')
        @if(in_array($lelang->status, ['active', 'scheduled']))
        <div class="card shadow-sm mt-4" style="border-radius: 12px; border: none;">
            <div class="card-header font-weight-bold" style="background: #fff3cd; color: #856404; border-radius:12px 12px 0 0; font-size:0.9rem; border-bottom: 1px solid #ffeeba;">
                <i class="fas fa-tools mr-2"></i>Aksi Admin
            </div>            
            <div class="card-body d-flex flex-wrap gap-2" style="gap: 10px;">

                @if($lelang->status == 'scheduled')
                <form action="{{ route('admin.lelang.aktivasi', $lelang->id) }}" method="POST" class="mr-2" id="form-aktifkan-lelang">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm font-weight-bold"
                        style="background:#1a6b3c; color:white; border-radius:8px; padding: 6px 16px;"
                        onclick="swalSubmitForm('form-aktifkan-lelang', {
                            title: 'Aktifkan Lelang?',
                            text: 'Lelang akan segera dimulai dan penawar dapat mengirimkan bid.',
                            icon: 'question',
                            confirmText: 'Ya, Aktifkan',
                            confirmColor: '#1a6b3c'
                        })">
                        <i class="fas fa-play mr-1"></i> Aktifkan Lelang
                    </button>
                </form>
                @endif

                <form action="{{ route('admin.lelang.tutup', $lelang->id) }}" method="POST" class="mr-2" id="form-tutup-lelang">
                    @csrf @method('PATCH')
                    <button type="button" class="btn btn-sm font-weight-bold btn-secondary"
                        style="border-radius:8px; padding: 6px 16px;"
                        onclick="swalSubmitForm('form-tutup-lelang', {
                            title: 'Tutup Lelang?',
                            text: 'Lelang akan ditutup dan pemenang akan ditentukan berdasarkan penawaran tertinggi.',
                            icon: 'warning',
                            confirmText: 'Ya, Tutup Lelang',
                            confirmColor: '#6c757d'
                        })">
                        <i class="fas fa-stop mr-1"></i> Tutup Lelang
                    </button>
                </form>

                <form action="{{ route('admin.lelang.batalAktif', $lelang->id) }}" method="POST" id="form-batal-lelang">
                    @csrf
                    <button type="button" class="btn btn-sm font-weight-bold btn-outline-danger"
                        style="border-radius:8px;padding:6px 16px;"
                        onclick="swalSubmitForm('form-batal-lelang', {
                            title: 'Batalkan Lelang?',
                            text: 'Lelang akan dibatalkan dan barang kembali ke status tersedia.',
                            icon: 'error',
                            confirmText: 'Ya, Batalkan',
                            confirmColor: '#dc3545'
                        })">
                        <i class="fas fa-ban mr-1"></i> Batalkan Lelang
                    </button>
                </form>
                
            </div>            
        </div>
        @endif
        @endif

    </div>
</div>

<style>
    @keyframes flash-green {
        0% { background-color: #f0fff4; }
        35% { background-color: #86efac; }
        70% { background-color: #bbf7d0; }
        100% { background-color: transparent; }
    }
    #tabelPenawaran tbody tr.flash-bid,
    #tabelPenawaran tbody tr.flash-bid > td {
        animation: flash-green 2.4s ease-in-out forwards;
    }
    #tabelPenawaran tbody tr.flash-bid {
        box-shadow: inset 4px 0 0 #22c55e;
    }
</style>

@endsection
@push('scripts')
<script>
    // ─── SLIDESHOW FOTO ───────────────────────────────────────────────────────
    let currentSlide = 0;
    const totalSlides = {{ $fotos->count() }};

    function slideDetail(dir) {
        currentSlide = (currentSlide + dir + totalSlides) % totalSlides;
        goToSlideDetail(currentSlide);
    }

    function goToSlideDetail(index) {
        document.querySelectorAll('.slide-detail').forEach((el, i) => {
            el.style.display = i === index ? 'block' : 'none';
        });
        document.querySelectorAll('.dot-detail').forEach((dot, i) => {
            dot.style.background = i === index ? 'white' : 'rgba(255,255,255,0.4)';
        });
        currentSlide = index;
    }

    // ─── REVERB (WEBSOCKETS) INTEGRATION ─────────────────────────────────────
    const HARGA_AWAL_LELANG = {{ (float) $barang->harga_awal }};

    function formatRupiah(value) {
        const number = Number(value || 0);
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function normalizeBidPayload(payload) {
        const hargaTertinggi = payload.hargaTertinggi ?? payload.harga_tertinggi ?? 0;
        const jumlahPenawaran = payload.jumlahPenawaran ?? payload.jumlah_penawaran ?? 0;

        return {
            hargaTertinggi: Number(hargaTertinggi || 0),
            hargaFormatted: payload.hargaFormatted ?? payload.harga_formatted ?? formatRupiah(hargaTertinggi),
            jumlahPenawaran: Number(jumlahPenawaran || 0),
        };
    }

    function updateAdminUI(payload) {
        const data = normalizeBidPayload(payload);
        const selisih = Math.max(data.hargaTertinggi - HARGA_AWAL_LELANG, 0);
        const jumlahText = `${data.jumlahPenawaran} penawaran`;
        const penawarText = `${data.jumlahPenawaran} orang`;

        const updates = {
            'stat-total-penawar': data.jumlahPenawaran,
            'stat-harga-tertinggi': data.hargaTertinggi > 0 ? data.hargaFormatted : '—',
            'stat-selisih': data.hargaTertinggi > 0 ? '+Rp ' + selisih.toLocaleString('id-ID') : 'Rp 0',
            'badge-total-penawaran': jumlahText,
        };

        Object.entries(updates).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        });

        const infoTotal = document.getElementById('info-total-penawar');
        if (infoTotal) {
            infoTotal.innerHTML = `<span class="font-weight-bold" style="color:#1a6b3c;">${penawarText}</span>`;
        }

        const infoHarga = document.getElementById('info-harga-tertinggi');
        if (infoHarga) {
            infoHarga.textContent = data.hargaTertinggi > 0 ? data.hargaFormatted : 'Belum ada penawaran';
        }
    }

    function initEcho() {
        if (typeof window.Echo === 'undefined') {
            setTimeout(initEcho, 500);
            return;
        }

        const lelangId = {{ $lelang->id }};
        console.log('Admin Echo initialized, joining channel: lelang.' + lelangId);
        
        window.Echo.channel('lelang.' + lelangId).listen('.penawaran.baru', (e) => {
            console.log('Admin real-time update received:', e);
            updateAdminUI(e);
            // Beri sedikit delay agar DB selesai menulis sebelum fetch tabel
            setTimeout(refreshPenawaranTable, 300);
        });
    }

    let isRefreshing = false;
    const TABEL_URL = '{{ route('admin.lelang.tabel-penawaran', $lelang->id) }}';

    async function refreshPenawaranTable() {
        if (isRefreshing) return;
        isRefreshing = true;

        try {
            // Tambahkan cache breaker agar browser selalu mengambil data terbaru dari server
            const res = await fetch(`${TABEL_URL}?t=${new Date().getTime()}`);
            if (!res.ok) throw new Error('Fetch failed');

            const html = await res.text();
            const listEl  = document.getElementById('list-penawaran');
            
            if (listEl) {
                // 1. Ganti konten HTML secara total (Force replace)
                listEl.innerHTML = html;

                // 2. Re-inisialisasi LapauTable agar pagination & sorting aktif kembali
                initPenawaranTable();

                // 3. Efek Flash pada baris pertama (data tertinggi baru)
                setTimeout(() => {
                    const firstRow = document.querySelector('#tabelPenawaran tbody tr:first-child');
                    if (firstRow) {
                        firstRow.classList.add('flash-bid');
                    }
                }, 250); // Delay sedikit lebih lama agar library selesai manipulasi DOM
            }

        } catch (err) {
            console.error('Refresh tabel gagal:', err);
        } finally {
            isRefreshing = false;
        }
    }

    // Polling Fallback jika Reverb bermasalah (setiap 8 detik)
    function startPolling() {
        setInterval(async () => {
            @if($lelang->status === 'active')
            try {
                const res = await fetch(`/lelang/{{ $lelang->id }}/polling`);
                const data = await res.json();
                if (data.success) {
                    const currentHighest = document.getElementById('stat-harga-tertinggi')?.textContent.replace(/[^0-9]/g, '');
                    if (data.harga_tertinggi && parseInt(data.harga_tertinggi) > (parseInt(currentHighest) || 0)) {
                        updateAdminUI({
                            harga_formatted: 'Rp ' + Number(data.harga_tertinggi).toLocaleString('id-ID'),
                            jumlah_penawaran: data.jumlah_penawaran ?? 0,
                            harga_tertinggi: data.harga_tertinggi
                        });
                        refreshPenawaranTable();
                    }
                }
            } catch (err) { console.log('Polling error:', err); }
            @endif
        }, 8000);
    }

document.addEventListener('DOMContentLoaded', function () {
    initPenawaranTable();
    
    @if($lelang->status === 'active')
    initEcho();
    startPolling();
    @endif
});

function initPenawaranTable() {
    if (!window.LapauTable || !document.getElementById('tabelPenawaran')) return;

    window.LapauTable.init('tabelPenawaran', {
        pageSize:  10,
        searchable: false,
        sortDir:   'desc',
        sortCol: 2, // Kolom Nilai Penawaran
    });
}
</script>

@endpush
