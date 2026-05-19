@extends('layouts.admin')

@section('content')

@php
    $user = auth()->user();

    $isSatker = $user->role === 'admin_satker';
    $isPusat  = $user->role === 'admin_pusat';

    $isDraft     = $pengajuan->status === 'draft';
    $isRevision  = $pengajuan->status === 'revision';
    $isSubmitted = $pengajuan->status === 'submitted';
    $isApproved  = $pengajuan->status === 'approved';

    $canEditSatker = $isSatker && ($isDraft || $isRevision);
    $isReadonly    = ($isSatker && !($isDraft || $isRevision)) || $isPusat;

    $sk    = $pengajuan->dokumenPengajuan->where('jenis','sk_panitia')->first();
    $izin  = $pengajuan->dokumenPengajuan->where('jenis','izin_penjualan')->first();
    $harga = $pengajuan->dokumenPengajuan->where('jenis','surat_penetapan_harga')->first();
@endphp

{{-- ═══════════════════════════════════════════════════════════
     STICKY HEADER
═══════════════════════════════════════════════════════════ --}}
<div class="detail-sticky-header">
    <div class="detail-sticky-inner">

        {{-- Kiri: back + judul --}}
        <div class="d-flex align-items-center" style="gap:12px; min-width:0;">
            <a href="{{ route('admin.dashboard') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div style="min-width:0;">
                <div class="detail-title">
                    <i class="fas fa-file-alt mr-1" style="color:#f6c90e;"></i>
                    Detail Pengajuan
                </div>
                <div class="detail-subtitle text-truncate">{{ $pengajuan->judul_pengajuan }}</div>
            </div>
        </div>

        {{-- Tengah: mini progress --}}
        <div class="d-none d-md-flex align-items-center" style="gap:6px;">
            @php
                $done  = ($sk ? 1 : 0) + ($izin ? 1 : 0) + ($harga ? 1 : 0);
                $pct   = round($done / 3 * 100);
                $pCnt  = $pengajuan->perkaras->count();
                $bCnt  = $pengajuan->perkaras->sum(fn($p) => $p->barangs->count());
            @endphp
            <div class="mini-stat">
                <span class="mini-stat-val">{{ $done }}/3</span>
                <span class="mini-stat-lbl">Dokumen</span>
            </div>
            <div class="mini-sep"></div>
            <div class="mini-stat">
                <span class="mini-stat-val">{{ $pCnt }}</span>
                <span class="mini-stat-lbl">Perkara</span>
            </div>
            <div class="mini-sep"></div>
            <div class="mini-stat">
                <span class="mini-stat-val">{{ $bCnt }}</span>
                <span class="mini-stat-lbl">Barang</span>
            </div>
        </div>

        {{-- Kanan: badge + aksi --}}
        <div class="d-flex align-items-center" style="gap:8px; flex-shrink:0;">
            <x-badge-status-pengajuan :status="$pengajuan->status" />

            @if($isSatker && $canEditSatker)
            <form method="POST" action="{{ route('satker.pengajuan.submit', $pengajuan) }}" id="formSubmitHeader">
                @csrf
                <button type="button" class="btn-aksi-hijau"
                    onclick="swalSubmitForm('formSubmitHeader', {
                        title: 'Kirim Pengajuan?',
                        text: 'Pengajuan akan dikirim ke Admin Pusat untuk direview.',
                        icon: 'question',
                        confirmText: 'Ya, Kirim',
                        confirmColor: '#1a6b3c'
                    })">
                    <i class="fas fa-paper-plane mr-1"></i> Submit
                </button>
            </form>
            @endif

            @if($isPusat && $isSubmitted)
            <form method="POST" action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}" id="formApproveHeader">
                @csrf
                <button type="button" class="btn-aksi-hijau"
                    onclick="swalSubmitForm('formApproveHeader', {
                        title: 'Setujui Pengajuan?',
                        text: 'Pengajuan ini akan disetujui.',
                        icon: 'question',
                        confirmText: 'Ya, Setujui',
                        confirmColor: '#1a6b3c'
                    })">
                    <i class="fas fa-check-circle mr-1"></i> Setujui
                </button>
            </form>
            <button class="btn-aksi-kuning" data-toggle="modal" data-target="#modalRevisi">
                <i class="fas fa-redo mr-1"></i> Revisi
            </button>
            <form method="POST" action="{{ route('admin.pengajuan.destroy', $pengajuan->id) }}" id="formHapusHeader">
                @csrf @method('DELETE')
                <button type="button" class="btn-aksi-merah"
                    onclick="swalSubmitForm('formHapusHeader', {
                        title: 'Hapus Pengajuan?',
                        text: 'Semua data terkait akan ikut terhapus permanen.',
                        icon: 'warning',
                        confirmText: 'Ya, Hapus',
                        confirmColor: '#e74a3b'
                    })">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
            </form>
            @endif

            @if($isSatker && $isDraft)
            <form action="{{ route('satker.pengajuan.destroy', $pengajuan->id) }}"
                method="POST" id="formHapusDraft">
                @csrf @method('DELETE')
                <button type="button" class="btn-aksi-merah"
                    onclick="swalSubmitForm('formHapusDraft', {
                        title: 'Hapus Pengajuan?',
                        text: 'Pengajuan beserta semua datanya akan dihapus permanen.',
                        icon: 'warning',
                        confirmText: 'Ya, Hapus',
                        confirmColor: '#e74a3b'
                    })">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     CONTENT AREA
═══════════════════════════════════════════════════════════ --}}
<div class="container-fluid detail-content-area">

    {{-- Alert --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4"
        style="border-left:4px solid #e74a3b;border-radius:8px;">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif
    @if(session('success'))
    <div id="autoAlert" class="alert alert-success alert-dismissible fade show shadow-sm mb-4"
        style="border-left:4px solid #1a6b3c;border-radius:8px;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <script>
        setTimeout(function () {
            let a = document.getElementById('autoAlert');
            if (a) { a.style.transition='opacity 0.5s'; a.style.opacity='0'; setTimeout(()=>a.remove(),500); }
        }, 4000);
    </script>
    @endif

    {{-- ══ ROW ATAS: Info + Dokumen ══ --}}
    <div class="row mb-4">

        {{-- Info Pengajuan --}}
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="detail-card h-100">
                <div class="detail-card-header">
                    <i class="fas fa-info-circle mr-2" style="color:#f6c90e;"></i>
                    Informasi Pengajuan
                </div>
                <div class="detail-card-body">
                    <div class="info-row">
                        <span class="info-lbl">Judul</span>
                        <span class="info-val font-weight-bold" style="color:#1a6b3c;">
                            {{ $pengajuan->judul_pengajuan }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Status</span>
                        <span class="info-val">
                            <x-badge-status-pengajuan :status="$pengajuan->status" />
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Satker</span>
                        <span class="info-val">{{ $pengajuan->satker->nama_satker ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Dibuat</span>
                        <span class="info-val">{{ $pengajuan->created_at->format('d M Y') }}</span>
                    </div>
                    @if($pengajuan->tanggal_pengajuan)
                    <div class="info-row">
                        <span class="info-lbl">Dikirim</span>
                        <span class="info-val">
                            {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d M Y, H:i') }}
                        </span>
                    </div>
                    @endif

                    {{-- Ringkasan --}}
                    <div class="mt-3 pt-3" style="border-top:1px solid #e0eeea;">
                        <div class="d-flex justify-content-between" style="gap:8px;">
                            <div class="summary-pill">
                                <div class="summary-pill-val">{{ $pengajuan->perkaras->count() }}</div>
                                <div class="summary-pill-lbl">Perkara</div>
                            </div>
                            <div class="summary-pill">
                                <div class="summary-pill-val">
                                    {{ $pengajuan->perkaras->sum(fn($p) => $p->barangs->count()) }}
                                </div>
                                <div class="summary-pill-lbl">Barang</div>
                            </div>
                            <div class="summary-pill">
                                <div class="summary-pill-val">{{ $done }}/3</div>
                                <div class="summary-pill-lbl">Dokumen</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dokumen Pengajuan --}}
        <div class="col-lg-8">
            <div class="detail-card h-100">
                <div class="detail-card-header">
                    <i class="fas fa-folder-open mr-2" style="color:#f6c90e;"></i>
                    Dokumen Pengajuan
                    <span class="ml-auto badge"
                        style="background:rgba(255,255,255,0.2);color:white;border-radius:20px;font-size:0.7rem;">
                        {{ $done }}/3 lengkap
                    </span>
                </div>
                <div class="detail-card-body">

                    {{-- Progress bar --}}
                    <div class="mb-3">
                        <div class="progress" style="height:6px;border-radius:20px;background:#e0eeea;">
                            <div class="progress-bar {{ $pct < 40 ? 'bg-danger' : ($pct < 100 ? 'bg-warning' : 'bg-success') }}"
                                style="width:{{ $pct }}%;border-radius:20px;"></div>
                        </div>
                        <small class="text-muted">{{ $pct }}% lengkap</small>
                    </div>

                    {{-- Upload form --}}
                    @if($canEditSatker)
                    <form method="POST" action="{{ route('satker.pengajuan.uploadDokumen', $pengajuan) }}"
                        enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div style="border:1px dashed #b2d8c0;border-radius:10px;background:#f8fff9;padding:14px;">
                            <div class="font-weight-bold small mb-3" style="color:#1a6b3c;">
                                <i class="fas fa-upload mr-1"></i> Upload Dokumen
                            </div>
                            <div class="row">
                                @foreach([
                                    ['key'=>'sk_panitia',            'label'=>'SK Panitia',                  'doc'=>$sk],
                                    ['key'=>'izin_penjualan',        'label'=>'Izin Penjualan',              'doc'=>$izin],
                                    ['key'=>'surat_penetapan_harga', 'label'=>'Surat Penetapan Harga Limit', 'doc'=>$harga],
                                ] as $item)
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-muted d-flex align-items-center justify-content-between">
                                            {{ $item['label'] }}
                                            @if($item['doc'])
                                            <span style="color:#1a6b3c;font-size:0.68rem;">
                                                <i class="fas fa-check-circle"></i> Ada
                                            </span>
                                            @else
                                            <span class="text-danger" style="font-size:0.68rem;">Belum</span>
                                            @endif
                                        </label>
                                        <input type="file" name="{{ $item['key'] }}" accept=".pdf"
                                            class="form-control form-control-sm"
                                            style="border-radius:6px;"
                                            {{ $item['doc'] ? 'disabled' : '' }}>
                                        @if($item['doc'])
                                        <small>
                                            <a href="{{ asset('storage/'.$item['doc']->file_path) }}"
                                                target="_blank" style="color:#1a6b3c;font-size:0.72rem;">
                                                <i class="fas fa-eye mr-1"></i>Lihat file
                                            </a>
                                        </small>
                                        @else
                                        <small class="text-muted" style="font-size:0.7rem;">PDF, maks. 2MB</small>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @if(!$sk || !$izin || !$harga)
                            <button type="submit" class="btn btn-sm font-weight-bold"
                                style="background:#1a6b3c;color:white;border-radius:6px;padding:5px 16px;">
                                <i class="fas fa-upload mr-1"></i> Upload
                            </button>
                            @endif
                        </div>
                    </form>
                    @endif

                    {{-- Grid 3 dokumen --}}
                    <div class="row">
                        @foreach([
                            ['label'=>'SK Panitia',                  'doc'=>$sk,    'key'=>'sk_panitia'],
                            ['label'=>'Izin Penjualan',              'doc'=>$izin,  'key'=>'izin_penjualan'],
                            ['label'=>'Surat Penetapan Harga Limit', 'doc'=>$harga, 'key'=>'surat_penetapan_harga'],
                        ] as $item)
                        <div class="col-md-4 mb-2">
                            <div class="dok-tile {{ $item['doc'] ? 'dok-tile--ada' : 'dok-tile--kosong' }}">
                                <i class="fas fa-file-pdf fa-lg mb-1"
                                    style="color:{{ $item['doc'] ? '#1a6b3c' : '#ccc' }};"></i>
                                <div class="dok-tile-label">{{ $item['label'] }}</div>
                                @if($item['doc'])
                                <div class="mt-2 d-flex" style="gap:4px;">
                                    <button type="button" class="btn btn-sm flex-fill"
                                        style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;font-size:0.75rem;"
                                        onclick="previewDokumen('{{ asset('storage/'.$item['doc']->file_path) }}','{{ $item['label'] }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($canEditSatker)
                                    <form id="form-dok-{{ $item['doc']->id }}"
                                        action="{{ route('satker.dokumen.destroy', $item['doc']->id) }}"
                                        method="POST" style="flex:1;">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm w-100"
                                            style="background:#fde8e8;color:#e74a3b;border-radius:6px;font-size:0.75rem;"
                                            onclick="swalSubmitForm('form-dok-{{ $item['doc']->id }}', {
                                                title: 'Hapus Dokumen?',
                                                text: '{{ $item['label'] }} akan dihapus permanen.',
                                                icon: 'warning',
                                                confirmText: 'Ya, Hapus',
                                                confirmColor: '#e74a3b'
                                            })">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                @else
                                <div class="text-muted small mt-1" style="font-size:0.72rem;">Belum diupload</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ══ RIWAYAT REVISI ══ --}}
    @if($pengajuan->catatan_revisi && count($pengajuan->catatan_revisi) > 0)
    <div class="detail-card mb-4">
        <div class="detail-card-header" style="background:linear-gradient(90deg,#856404,#a07800);">
            <i class="fas fa-history mr-2" style="color:#f6c90e;"></i>
            Riwayat Revisi
            <span class="ml-2 badge"
                style="background:rgba(255,255,255,0.2);color:white;border-radius:20px;font-size:0.7rem;">
                {{ count($pengajuan->catatan_revisi) }}x
            </span>
        </div>
        <div class="detail-card-body p-0">
            <div class="d-flex overflow-auto" style="gap:0;">
                @foreach($pengajuan->catatan_revisi as $idx => $revisi)
                @php $isLatest = $idx === count($pengajuan->catatan_revisi) - 1; @endphp
                <div class="revisi-item {{ $isLatest ? 'revisi-item--latest' : '' }}">
                    <div class="revisi-badge">{{ $revisi['ke_revisi'] }}</div>
                    <div class="revisi-title">
                        Revisi ke-{{ $revisi['ke_revisi'] }}
                        @if($isLatest)
                        <span class="badge badge-warning ml-1" style="font-size:0.6rem;border-radius:20px;">Terbaru</span>
                        @endif
                    </div>
                    <p class="revisi-text">{{ $revisi['catatan'] }}</p>
                    <div class="revisi-time">
                        <i class="fas fa-clock mr-1"></i>
                        {{ \Carbon\Carbon::parse($revisi['tanggal'])->format('d M Y, H:i') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ══ ACCORDION PUTUSAN PERKARA ══ --}}
    <div class="mb-2 d-flex align-items-center justify-content-between">
        <h6 class="font-weight-bold mb-0" style="color:#1a6b3c;">
            <i class="fas fa-balance-scale mr-2" style="color:#c0392b;"></i>
            Putusan Perkara
            <span class="badge ml-1"
                style="background:#e8f5ee;color:#1a6b3c;border-radius:20px;font-size:0.72rem;">
                {{ $pengajuan->perkaras->count() }} perkara
            </span>
        </h6>
        <div style="gap:6px;" class="d-flex">
            <button type="button" onclick="bukaSemuaPerkara()"
                class="btn btn-sm" style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;font-size:0.78rem;">
                <i class="fas fa-expand-alt mr-1"></i>Buka Semua
            </button>
            <button type="button" onclick="tutupSemuaPerkara()"
                class="btn btn-sm" style="background:#f8f9fa;color:#6c757d;border-radius:6px;font-size:0.78rem;">
                <i class="fas fa-compress-alt mr-1"></i>Tutup Semua
            </button>
        </div>
    </div>

    {{-- Form tambah perkara --}}
    @if($canEditSatker)
    <div class="detail-card mb-3">
        <div class="detail-card-header" style="background:linear-gradient(90deg,#c0392b,#a93226);">
            <i class="fas fa-plus-circle mr-2"></i>Tambah Putusan Perkara Baru
        </div>
        <div class="detail-card-body">
            <form method="POST" action="{{ route('satker.pengajuan.perkara.store', $pengajuan) }}"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">Nomor Perkara</label>
                            <input type="text" name="nomor_perkara"
                                class="form-control form-control-sm" placeholder="Nomor Perkara"
                                required style="border-radius:6px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">Nama Tersangka</label>
                            <input type="text" name="nama_tersangka"
                                class="form-control form-control-sm" placeholder="Nama Tersangka"
                                required style="border-radius:6px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">Tanggal Putusan</label>
                            <input type="date" name="tanggal_putusan"
                                class="form-control form-control-sm" required style="border-radius:6px;">
                        </div>
                    </div>
                </div>

                <label class="small font-weight-bold text-muted">Dokumen Perkara</label>
                <div id="dokumen-wrapper">
                    <div class="input-group mb-2">
                        <input type="file" name="dokumen[]" class="form-control form-control-sm" accept=".pdf" required>
                        <input type="text" name="nama_dokumen[]" class="form-control form-control-sm" placeholder="Nama Dokumen" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-sm"
                                style="background:#c0392b;color:white;border-radius:0 6px 6px 0;"
                                onclick="tambahDokumen()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="error-dokumen" style="display:none;" class="mb-2">
                    <small class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>
                        <span id="error-dokumen-text"></span></small>
                </div>
                <small class="text-muted d-block mb-3">PDF, maks. 5 dokumen & 2MB/file</small>
                <button type="submit" class="btn btn-sm font-weight-bold"
                    style="background:#c0392b;color:white;border-radius:6px;">
                    <i class="fas fa-save mr-1"></i> Tambah Putusan Perkara
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Loop accordion per perkara --}}
    @foreach($pengajuan->perkaras as $i => $perkara)
    @php
        $totalBarangPerkara = $perkara->barangs->count();
        $totalDokPerkara    = $perkara->dokumenPerkara->count();
        $dokOk              = $totalDokPerkara > 0;
        $barangOk           = $totalBarangPerkara > 0;
    @endphp

    <div class="perkara-accordion mb-3" id="acc-{{ $perkara->id }}">

        {{-- Accordion Header --}}
        <div class="perkara-acc-header" onclick="toggleAcc({{ $perkara->id }})">
            <div class="d-flex align-items-center" style="gap:12px; flex:1; min-width:0;">

                {{-- Nomor --}}
                <div class="perkara-num">{{ $i + 1 }}</div>

                {{-- Info --}}
                <div style="min-width:0;">
                    <div class="perkara-acc-nama">{{ $perkara->nomor_perkara }}</div>
                    <div class="perkara-acc-sub">
                        <i class="fas fa-user mr-1"></i>{{ $perkara->nama_tersangka }}
                        <span class="mx-1">·</span>
                        <i class="fas fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($perkara->tanggal_putusan)->format('d M Y') }}
                    </div>
                </div>
            </div>

            {{-- Badges --}}
            <div class="d-flex align-items-center" style="gap:6px; flex-shrink:0;">
                <span class="perkara-badge {{ $dokOk ? 'perkara-badge--ok' : 'perkara-badge--err' }}">
                    <i class="fas fa-paperclip mr-1"></i>{{ $totalDokPerkara }} dok
                </span>
                <span class="perkara-badge {{ $barangOk ? 'perkara-badge--ok' : 'perkara-badge--warn' }}">
                    <i class="fas fa-box mr-1"></i>{{ $totalBarangPerkara }} barang
                </span>

                @if($canEditSatker)
                {{-- Edit --}}
                <button type="button" class="btn btn-sm perkara-btn-edit"
                    onclick="event.stopPropagation(); $('#editPerkara{{ $perkara->id }}').modal('show');">
                    <i class="fas fa-edit"></i>
                </button>
                {{-- Hapus --}}
                <form id="form-perkara-{{ $perkara->id }}"
                    action="{{ route('satker.pengajuan.perkara.destroy', $perkara->id) }}"
                    method="POST" style="display:inline;" onclick="event.stopPropagation()">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-sm perkara-btn-hapus"
                        onclick="event.stopPropagation(); swalSubmitForm('form-perkara-{{ $perkara->id }}', {
                            title: 'Hapus Putusan Perkara?',
                            text: 'Semua dokumen dan barang dalam perkara ini akan ikut terhapus.',
                            icon: 'warning',
                            confirmText: 'Ya, Hapus',
                            confirmColor: '#e74a3b'
                        })">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @endif

                <i class="fas fa-chevron-down perkara-chevron" id="chevron-{{ $perkara->id }}"></i>
            </div>
        </div>

        {{-- Accordion Body --}}
        <div class="perkara-acc-body" id="body-{{ $perkara->id }}">
            <div class="perkara-acc-inner">

                {{-- ── DOKUMEN PERKARA ── --}}
                <div class="section-block">
                    <div class="section-block-title">
                        <i class="fas fa-paperclip mr-1"></i>Dokumen Putusan Perkara
                    </div>

                    @forelse($perkara->dokumenPerkara as $doc)
                    <div class="dok-perkara-row">
                        <i class="fas fa-file-pdf text-danger mr-2 small"></i>
                        <span class="small flex-fill">{{ $doc->nama_dokumen }}</span>
                        <button type="button" class="btn btn-sm mr-1"
                            style="background:#e8f5ee;color:#1a6b3c;border-radius:5px;font-size:0.73rem;padding:2px 8px;"
                            onclick="previewDokumen('{{ asset('storage/'.$doc->file_path) }}','{{ $doc->nama_dokumen }}')">
                            <i class="fas fa-eye mr-1"></i>Lihat
                        </button>
                        @if($canEditSatker)
                        <form id="form-dok-p-{{ $doc->id }}"
                            action="{{ route('satker.pengajuan.perkara.dokumen.destroy', $doc->id) }}"
                            method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm"
                                style="background:#fde8e8;color:#e74a3b;border-radius:5px;font-size:0.73rem;padding:2px 8px;"
                                onclick="swalSubmitForm('form-dok-p-{{ $doc->id }}', {
                                    title: 'Hapus Dokumen?',
                                    text: '{{ addslashes($doc->nama_dokumen) }} akan dihapus.',
                                    icon: 'warning',
                                    confirmText: 'Ya, Hapus',
                                    confirmColor: '#e74a3b'
                                })">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div class="text-danger small">
                        <i class="fas fa-exclamation-circle mr-1"></i>Belum ada dokumen
                    </div>
                    @endforelse

                    {{-- Upload dokumen tambahan --}}
                    @if($canEditSatker)
                    <form method="POST"
                        action="{{ route('satker.pengajuan.perkara.uploadDokumen', $perkara) }}"
                        enctype="multipart/form-data" class="mt-2">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="file" name="dokumen[]" multiple accept=".pdf"
                                class="form-control form-control-sm" style="border-radius:6px 0 0 6px;">
                            <input type="text" name="nama_dokumen[]"
                                class="form-control form-control-sm" placeholder="Nama dokumen">
                            <div class="input-group-append">
                                <button class="btn btn-sm"
                                    style="background:#1a6b3c;color:white;border-radius:0 6px 6px 0;">
                                    <i class="fas fa-upload"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted" style="font-size:0.7rem;">PDF, maks. 2MB</small>
                    </form>
                    @endif
                </div>

                {{-- ── BARANG ── --}}
                <div class="section-block">
                    <div class="section-block-title">
                        <i class="fas fa-boxes mr-1"></i>Barang
                        <span class="badge ml-1"
                            style="background:#e8f5ee;color:#1a6b3c;border-radius:20px;font-size:0.68rem;">
                            {{ $totalBarangPerkara }} barang
                        </span>
                    </div>

                    {{-- Tambah Barang Toggle --}}
                    @if($canEditSatker)
                    <button type="button" id="btn-toggle-barang-{{ $perkara->id }}"
                        onclick="toggleFormBarang({{ $perkara->id }})"
                        class="btn btn-sm font-weight-bold mb-3"
                        style="background:#f6c90e;color:#1a6b3c;border-radius:8px;padding:5px 14px;">
                        <i class="fas fa-plus mr-1" id="icon-form-barang-{{ $perkara->id }}"></i>
                        <span id="label-form-barang-{{ $perkara->id }}">Tambah Barang</span>
                    </button>

                    <div id="wrap-form-barang-{{ $perkara->id }}"
                        style="overflow:hidden;max-height:0;transition:max-height 0.35s ease;">
                        <form method="POST" action="{{ route('satker.perkara.barang.store', $perkara) }}"
                            id="formBarang-{{ $perkara->id }}" class="mb-3">
                            @csrf
                            <input type="hidden" name="perkara_id" value="{{ $perkara->id }}">
                            <div style="border:1px dashed #f6c90e;border-radius:10px;background:#fffdf0;padding:14px;">

                                @if($errors->any() && old('perkara_id') == $perkara->id)
                                <div class="alert alert-danger py-2 mb-3"
                                    style="border-left:4px solid #e74a3b;border-radius:8px;font-size:0.82rem;">
                                    @foreach($errors->all() as $error)
                                        <span class="d-block">{{ $error }}</span>
                                    @endforeach
                                </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-muted">Nama Barang</label>
                                            <input type="text" name="nama_barang"
                                                class="form-control form-control-sm"
                                                value="{{ old('perkara_id') == $perkara->id ? old('nama_barang') : '' }}"
                                                placeholder="Nama Barang" style="border-radius:6px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-muted">
                                                Harga Limit (Rp)
                                                <span class="font-weight-normal text-muted">— maks. Rp 35.000.000</span>
                                            </label>
                                            <input type="number" name="harga_awal"
                                                class="form-control form-control-sm"
                                                min="1" max="35000000"
                                                value="{{ old('perkara_id') == $perkara->id ? old('harga_awal') : '' }}"
                                                placeholder="0" style="border-radius:6px;"
                                                oninput="validateHargaLimit(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-muted">Deskripsi</label>
                                            <input type="text" name="deskripsi"
                                                class="form-control form-control-sm"
                                                placeholder="Deskripsi singkat kondisi barang"
                                                value="{{ old('perkara_id') == $perkara->id ? old('deskripsi') : '' }}"
                                                style="border-radius:6px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="button"
                                            onclick="toggleCatatanInternal({{ $perkara->id }})"
                                            style="background:none;border:none;padding:0;color:#1a6b3c;font-size:0.82rem;font-weight:600;cursor:pointer;">
                                            <i class="fas fa-plus-circle mr-1" id="icon-catatan-{{ $perkara->id }}"></i>
                                            <span id="label-catatan-{{ $perkara->id }}">Barang Gabungan? Tambah Catatan Internal</span>
                                        </button>
                                        <div id="wrap-catatan-{{ $perkara->id }}" style="display:none;margin-top:8px;">
                                            <textarea name="catatan_internal" rows="2"
                                                class="form-control form-control-sm"
                                                placeholder="Contoh: Hasil penggabungan dari perkara No. 123/2025"
                                                style="border-radius:6px;">{{ old('perkara_id') == $perkara->id ? old('catatan_internal') : '' }}</textarea>
                                            <small class="text-muted" style="font-size:0.72rem;">
                                                <i class="fas fa-info-circle mr-1"></i>Tidak ditampilkan ke pembeli.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex" style="gap:8px;">
                                    <button type="submit" class="btn btn-sm font-weight-bold"
                                        style="background:#f6c90e;color:#1a6b3c;border-radius:6px;">
                                        <i class="fas fa-save mr-1"></i> Simpan Barang
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary"
                                        onclick="toggleFormBarang({{ $perkara->id }})"
                                        style="border-radius:6px;">Batal</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Tabel Barang --}}
                    @if($totalBarangPerkara > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.82rem;">
                            <thead style="background:#f8fff9;">
                                <tr>
                                    <th class="border-0" style="color:#1a6b3c;">Nama Barang</th>
                                    <th class="border-0" style="color:#1a6b3c;">Harga Limit</th>
                                    <th class="border-0" style="color:#1a6b3c;">Deskripsi</th>
                                    <th class="border-0" style="color:#1a6b3c;">Catatan Internal</th>
                                    <th class="border-0" style="color:#1a6b3c;">Foto</th>
                                    @if($canEditSatker)
                                    <th class="border-0" style="color:#1a6b3c;width:80px;">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perkara->barangs as $barang)
                                <tr>
                                    <td class="align-middle font-weight-bold">{{ $barang->nama_barang }}</td>
                                    <td class="align-middle font-weight-bold" style="color:#1a6b3c;">
                                        Rp {{ number_format($barang->harga_awal, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle text-muted small">{{ $barang->deskripsi ?? '-' }}</td>
                                    <td class="align-middle">
                                        @if($barang->catatan_internal)
                                        <span class="badge"
                                            style="background:#fff3cd;color:#856404;border-radius:6px;font-size:0.7rem;white-space:normal;max-width:160px;display:inline-block;padding:3px 6px;">
                                            <i class="fas fa-lock mr-1" style="font-size:0.6rem;"></i>
                                            {{ $barang->catatan_internal }}
                                        </span>
                                        @else
                                        <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-wrap" style="gap:4px;">
                                            @forelse($barang->fotoBarang ?? [] as $foto)
                                            <div style="position:relative;display:inline-block;">
                                                <img src="{{ asset('storage/'.$foto->file_path) }}"
                                                    style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:2px solid #e0eeea;cursor:pointer;"
                                                    onclick="previewDokumen('{{ asset('storage/'.$foto->file_path) }}','Foto Barang')">
                                                @if($canEditSatker)
                                                <form action="{{ route('satker.barang.foto.destroy', $foto->id) }}"
                                                    method="POST" style="position:absolute;top:1px;right:1px;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="photo-delete"
                                                        onclick="return confirm('Hapus foto?')">×</button>
                                                </form>
                                                @endif
                                            </div>
                                            @empty
                                            <small class="text-muted">Belum ada foto</small>
                                            @endforelse
                                        </div>
                                        @if($canEditSatker)
                                        <form method="POST"
                                            action="{{ route('satker.barang.uploadFoto', $barang) }}"
                                            enctype="multipart/form-data" class="mt-1">
                                            @csrf
                                            <div class="input-group input-group-sm">
                                                <input type="file" name="foto[]" multiple accept="image/*"
                                                    class="form-control form-control-sm"
                                                    style="border-radius:6px 0 0 6px;font-size:0.75rem;">
                                                <div class="input-group-append">
                                                    <button class="btn btn-sm"
                                                        style="background:#1a6b3c;color:white;border-radius:0 6px 6px 0;">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        @endif
                                    </td>
                                    @if($canEditSatker)
                                    <td class="align-middle text-center">
                                        <button type="button" class="btn btn-sm mb-1"
                                            style="background:#fff3cd;color:#856404;border-radius:6px;width:32px;"
                                            data-toggle="modal"
                                            data-target="#modalEditBarang-{{ $barang->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form id="form-barang-{{ $barang->id }}"
                                            action="{{ route('satker.barang.destroy', $barang->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm"
                                                style="background:#fde8e8;color:#e74a3b;border-radius:6px;width:32px;"
                                                onclick="swalSubmitForm('form-barang-{{ $barang->id }}', {
                                                    title: 'Hapus Barang?',
                                                    text: '{{ addslashes($barang->nama_barang) }} akan dihapus permanen.',
                                                    icon: 'warning',
                                                    confirmText: 'Ya, Hapus',
                                                    confirmColor: '#e74a3b'
                                                })">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3 text-muted small">
                        <i class="fas fa-box-open fa-2x mb-2 d-block" style="color:#f0d060;opacity:0.5;"></i>
                        Belum ada barang di perkara ini
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Barang --}}
    @foreach($perkara->barangs as $barang)
    <div class="modal fade" id="modalEditBarang-{{ $barang->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
                <div class="modal-header" style="background:linear-gradient(90deg,#f6c90e,#e0b800);">
                    <h5 class="modal-title font-weight-bold" style="color:#1a6b3c;">
                        <i class="fas fa-edit mr-2"></i>Edit Barang
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form method="POST" action="{{ route('satker.barang.update', $barang->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-body" style="background:#fffdf0;">
                        <div class="form-group">
                            <label class="small font-weight-bold" style="color:#1a6b3c;">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control"
                                value="{{ $barang->nama_barang }}" style="border-radius:8px;" required>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold" style="color:#1a6b3c;">Deskripsi</label>
                            <textarea name="deskripsi" rows="2" class="form-control"
                                style="border-radius:8px;">{{ $barang->deskripsi }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold" style="color:#1a6b3c;">
                                Catatan Internal
                                <span class="badge" style="background:#e8f4fd;color:#1a6b3c;font-size:0.68rem;border-radius:10px;padding:2px 7px;">
                                    Hanya Admin
                                </span>
                            </label>
                            <textarea name="catatan_internal" rows="2" class="form-control"
                                style="border-radius:8px;">{{ $barang->catatan_internal }}</textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold" style="color:#1a6b3c;">Harga Limit</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"
                                        style="background:#f6c90e;border-color:#f6c90e;color:#1a6b3c;font-weight:bold;">Rp</span>
                                </div>
                                <input type="number" name="harga_awal" class="form-control"
                                    value="{{ $barang->harga_awal }}" min="0" required
                                    style="border-radius:0 8px 8px 0;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background:#fffdf0;">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"
                            style="border-radius:6px;">Batal</button>
                        <button type="submit" class="btn btn-sm font-weight-bold"
                            style="background:#f6c90e;color:#1a6b3c;border-radius:6px;">
                            <i class="fas fa-save mr-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @endforeach
    {{-- END loop perkara --}}

</div>
{{-- END content area --}}

{{-- ═══ MODAL REVISI ═══ --}}
<div class="modal fade" id="modalRevisi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
            <form method="POST" action="{{ route('admin.pengajuan.revisi', $pengajuan->id) }}">
                @csrf
                <div class="modal-header" style="background:linear-gradient(90deg,#856404,#a07800);">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="fas fa-redo mr-2" style="color:#f6c90e;"></i>Minta Revisi
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label class="small font-weight-bold text-muted">Catatan Revisi</label>
                    <textarea name="catatan_revisi" class="form-control" rows="4"
                        placeholder="Jelaskan apa yang perlu diperbaiki..." required
                        style="border-radius:8px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:6px;">Batal</button>
                    <button type="submit" class="btn btn-sm font-weight-bold"
                        style="background:#f6c90e;color:#1a6b3c;border-radius:6px;">
                        <i class="fas fa-paper-plane mr-1"></i>Kirim Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL EDIT PERKARA ═══ --}}
@foreach($pengajuan->perkaras as $p)
<div class="modal fade" id="editPerkara{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
            <form method="POST" action="{{ route('satker.pengajuan.perkara.update', $p->id) }}"
                enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);">
                    <h5 class="modal-title text-white font-weight-bold">
                        <i class="fas fa-edit mr-2" style="color:#f6c90e;"></i>Edit Putusan Perkara
                    </h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="background:#f8fff9;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Nomor Perkara</label>
                                <input type="text" name="nomor_perkara" value="{{ $p->nomor_perkara }}"
                                    class="form-control" style="border-radius:8px;" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Nama Tersangka</label>
                                <input type="text" name="nama_tersangka" value="{{ $p->nama_tersangka }}"
                                    class="form-control" style="border-radius:8px;" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Tanggal Putusan</label>
                                <input type="date" name="tanggal_putusan"
                                    value="{{ $p->tanggal_putusan ? \Carbon\Carbon::parse($p->tanggal_putusan)->format('Y-m-d') : '' }}"
                                    class="form-control" style="border-radius:8px;" required>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <label class="small font-weight-bold text-muted">Dokumen Terlampir</label>
                    <ul class="list-group mb-3">
                        @forelse($p->dokumenPerkara ?? [] as $doc)
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            style="border-radius:8px;margin-bottom:4px;border:1px solid #e0eeea;background:#f8fff9;">
                            <span><i class="fas fa-file-pdf text-danger mr-2"></i>{{ $doc->nama_dokumen }}</span>
                            <button type="button" class="btn btn-sm"
                                style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;font-size:0.78rem;"
                                onclick="previewDokumen('{{ asset('storage/'.$doc->file_path) }}','{{ $doc->nama_dokumen }}')">
                                <i class="fas fa-eye mr-1"></i> Lihat
                            </button>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted" style="border-radius:8px;">
                            Tidak ada dokumen
                        </li>
                        @endforelse
                    </ul>
                    <label class="small font-weight-bold text-muted">Tambah Dokumen Baru</label>
                    <div id="dokumen-wrapper-edit-{{ $p->id }}">
                        <div class="input-group mb-2">
                            <input type="file" name="dokumen[]" class="form-control form-control-sm" accept=".pdf">
                            <input type="text" name="nama_dokumen[]" class="form-control form-control-sm" placeholder="Nama Dokumen">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-success"
                                    onclick="tambahDokumenEdit({{ $p->id }})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="error-dokumen-edit-{{ $p->id }}" style="display:none;" class="mt-1 mb-2">
                        <small class="text-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="error-dokumen-edit-text-{{ $p->id }}"></span>
                        </small>
                    </div>
                    <small class="text-muted">PDF, maks. 5 dokumen & 2MB/file</small>
                </div>
                <div class="modal-footer" style="background:#f8fff9;">
                    <button class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:6px;">Batal</button>
                    <button type="submit" class="btn btn-sm font-weight-bold"
                        style="background:#1a6b3c;color:white;border-radius:6px;">
                        <i class="fas fa-save mr-1"></i>Update Perkara
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- ═══ MODAL PREVIEW ═══ --}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);">
                <h5 class="modal-title text-white font-weight-bold" id="modalTitle">
                    <i class="fas fa-eye mr-2" style="color:#f6c90e;"></i>Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center" style="background:#f8fff9;">
                <iframe id="previewFrame" width="100%" height="500px"
                    style="display:none;border-radius:8px;"></iframe>
                <img id="previewImage" src=""
                    style="max-width:100%;display:none;border-radius:8px;" />
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     CSS
═══════════════════════════════════════════════════════════ --}}
<style>
/* ── STICKY HEADER ── */
.detail-sticky-header {
    position: sticky;
    top: 0;
    z-index: 1030;
    background: white;
    border-bottom: 2px solid #e0eeea;
    box-shadow: 0 2px 12px rgba(26,107,60,0.08);
}
.detail-sticky-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 24px;
    flex-wrap: wrap;
}
.btn-back {
    width: 34px; height: 34px;
    border-radius: 8px;
    background: rgba(26,107,60,0.1);
    color: #1a6b3c;
    border: 1px solid #1a6b3c;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
    flex-shrink: 0;
    transition: background 0.2s;
}
.btn-back:hover { background: rgba(26,107,60,0.2); color: #1a6b3c; }
.detail-title   { font-size: 0.95rem; font-weight: 700; color: #1a6b3c; }
.detail-subtitle{ font-size: 0.75rem; color: #6c757d; }

/* Mini stats */
.mini-stat     { text-align: center; }
.mini-stat-val { display: block; font-size: 1rem; font-weight: 700; color: #1a6b3c; line-height: 1; }
.mini-stat-lbl { display: block; font-size: 0.65rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
.mini-sep      { width: 1px; height: 28px; background: #e0eeea; }

/* Aksi buttons */
.btn-aksi-hijau, .btn-aksi-kuning, .btn-aksi-merah {
    border: none; border-radius: 8px; padding: 6px 14px;
    font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: opacity 0.2s;
}
.btn-aksi-hijau  { background: linear-gradient(135deg,#1a6b3c,#145c32); color: white; }
.btn-aksi-kuning { background: #f6c90e; color: #1a6b3c; }
.btn-aksi-merah  { background: #e74a3b; color: white; }
.btn-aksi-hijau:hover, .btn-aksi-kuning:hover, .btn-aksi-merah:hover { opacity: 0.85; }

/* ── CONTENT AREA ── */
.detail-content-area { padding: 24px; }

/* ── CARDS ── */
.detail-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 8px rgba(0,0,0,0.08);
}
.detail-card-header {
    background: linear-gradient(90deg,#1a6b3c,#145c32);
    color: white;
    padding: 12px 18px;
    font-size: 0.88rem;
    font-weight: 700;
    display: flex;
    align-items: center;
}
.detail-card-body { padding: 16px 18px; background: white; }

/* Info rows */
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 6px 0;
    border-bottom: 1px solid #f0f0f0;
    gap: 8px;
    font-size: 0.82rem;
}
.info-row:last-child { border-bottom: none; }
.info-lbl { color: #6c757d; flex-shrink: 0; }
.info-val  { text-align: right; }

/* Summary pills */
.summary-pill {
    flex: 1;
    text-align: center;
    background: #f8fff9;
    border: 1px solid #e0eeea;
    border-radius: 10px;
    padding: 8px 4px;
}
.summary-pill-val { font-size: 1.2rem; font-weight: 700; color: #1a6b3c; line-height: 1; }
.summary-pill-lbl { font-size: 0.65rem; color: #6c757d; text-transform: uppercase; }

/* Dokumen tiles */
.dok-tile {
    border-radius: 10px;
    padding: 12px;
    text-align: center;
    border: 1px solid;
}
.dok-tile--ada    { background: #f8fff9; border-color: #b2d8c0; }
.dok-tile--kosong { background: #fafafa; border-color: #e0e0e0; }
.dok-tile-label   { font-size: 0.75rem; font-weight: 600; color: #2d3748; margin-top: 4px; }

/* Riwayat revisi */
.revisi-item {
    min-width: 200px; max-width: 240px;
    padding: 14px 16px;
    border-right: 1px solid #f0e6c8;
    background: white;
    flex-shrink: 0;
}
.revisi-item--latest { background: #fffdf0; }
.revisi-badge {
    width: 26px; height: 26px; border-radius: 50%;
    background: #e9ecef; color: #6c757d;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.72rem;
    margin-bottom: 6px;
}
.revisi-item--latest .revisi-badge { background: #f6c90e; color: #856404; }
.revisi-title { font-weight: 700; font-size: 0.78rem; color: #856404; margin-bottom: 4px; }
.revisi-text  { font-size: 0.78rem; color: #4a4a4a; line-height: 1.4; margin-bottom: 4px; }
.revisi-time  { font-size: 0.7rem; color: #999; }

/* ── ACCORDION PERKARA ── */
.perkara-accordion {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(0,0,0,0.07);
    border: 1px solid #e0eeea;
}
.perkara-acc-header {
    display: flex; align-items: center;
    padding: 12px 16px;
    background: white;
    cursor: pointer;
    transition: background 0.2s;
    gap: 8px;
}
.perkara-acc-header:hover { background: #f8fff9; }
.perkara-num {
    width: 30px; height: 30px; border-radius: 50%;
    background: #c0392b; color: white;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.78rem;
    flex-shrink: 0;
}
.perkara-acc-nama { font-weight: 700; font-size: 0.88rem; color: #c0392b; }
.perkara-acc-sub  { font-size: 0.72rem; color: #6c757d; }

.perkara-badge {
    font-size: 0.68rem; font-weight: 600;
    padding: 3px 8px; border-radius: 20px;
}
.perkara-badge--ok   { background: #e8f5ee; color: #1a6b3c; }
.perkara-badge--err  { background: #fde8e8; color: #c0392b; }
.perkara-badge--warn { background: #fff3cd; color: #856404; }

.perkara-btn-edit, .perkara-btn-hapus {
    width: 28px; height: 28px; padding: 0;
    border-radius: 6px; font-size: 0.72rem;
    display: flex; align-items: center; justify-content: center;
}
.perkara-btn-edit  { background: #fff3cd; color: #856404; border: none; }
.perkara-btn-hapus { background: #fde8e8; color: #e74a3b; border: none; }
.perkara-chevron {
    font-size: 0.78rem; color: #6c757d;
    transition: transform 0.3s ease;
    flex-shrink: 0;
}

/* Accordion body smooth */
.perkara-acc-body {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.4s ease;
    background: #fdfdfd;
    border-top: 1px solid transparent;
}
.perkara-acc-body.open {
    max-height: 3000px;
    border-top-color: #e0eeea;
}
.perkara-acc-inner { padding: 16px; }

/* Section blocks */
.section-block { margin-bottom: 20px; }
.section-block:last-child { margin-bottom: 0; }
.section-block-title {
    font-size: 0.8rem; font-weight: 700; color: #1a6b3c;
    border-bottom: 1px solid #e0eeea;
    padding-bottom: 6px; margin-bottom: 10px;
}
.dok-perkara-row {
    display: flex; align-items: center;
    padding: 6px 10px; border-radius: 8px;
    background: #f8fff9; border: 1px solid #e0eeea;
    margin-bottom: 6px; gap: 6px;
}

/* Photo delete btn */
.photo-delete {
    width: 16px; height: 16px; border-radius: 50%;
    background: rgba(0,0,0,0.6); color: white;
    border: none; cursor: pointer; font-size: 10px;
    display: flex; align-items: center; justify-content: center;
    line-height: 1;
}
</style>

{{-- ═══════════════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
// ── ACCORDION ──
function toggleAcc(id) {
    const body    = document.getElementById('body-' + id);
    const chevron = document.getElementById('chevron-' + id);
    const isOpen  = body.classList.contains('open');
    body.classList.toggle('open');
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

function bukaSemuaPerkara() {
    document.querySelectorAll('.perkara-acc-body').forEach(b => b.classList.add('open'));
    document.querySelectorAll('.perkara-chevron').forEach(c => c.style.transform = 'rotate(180deg)');
}

function tutupSemuaPerkara() {
    document.querySelectorAll('.perkara-acc-body').forEach(b => b.classList.remove('open'));
    document.querySelectorAll('.perkara-chevron').forEach(c => c.style.transform = 'rotate(0deg)');
}

// Buka perkara pertama otomatis
document.addEventListener('DOMContentLoaded', function () {
    const first = document.querySelector('.perkara-acc-body');
    if (first) {
        const id = first.id.replace('body-', '');
        toggleAcc(id);
    }

    // Auto-buka jika ada error barang
    const perkaraId = '{{ old("perkara_id") }}';
    if (perkaraId) {
        const body = document.getElementById('body-' + perkaraId);
        if (body && !body.classList.contains('open')) toggleAcc(perkaraId);

        // Auto-buka form tambah barang
        const wrap = document.getElementById('wrap-form-barang-' + perkaraId);
        if (wrap) {
            wrap.style.maxHeight = '1000px';
            wrap.dataset.open = 'true';
            const icon  = document.getElementById('icon-form-barang-' + perkaraId);
            const label = document.getElementById('label-form-barang-' + perkaraId);
            if (icon)  icon.className    = 'fas fa-times mr-1';
            if (label) label.textContent = 'Tutup Form';
        }
    }
});

// ── FORM TOGGLE BARANG ──
function toggleFormBarang(perkaraId) {
    const wrap  = document.getElementById('wrap-form-barang-' + perkaraId);
    const icon  = document.getElementById('icon-form-barang-' + perkaraId);
    const label = document.getElementById('label-form-barang-' + perkaraId);
    const isOpen = wrap.dataset.open === 'true';

    wrap.style.maxHeight = isOpen ? '0' : '1000px';
    wrap.dataset.open    = isOpen ? 'false' : 'true';
    icon.className       = isOpen ? 'fas fa-plus mr-1' : 'fas fa-times mr-1';
    label.textContent    = isOpen ? 'Tambah Barang' : 'Tutup Form';
}

// ── CATATAN INTERNAL ──
function toggleCatatanInternal(perkaraId) {
    const wrap  = document.getElementById('wrap-catatan-' + perkaraId);
    const icon  = document.getElementById('icon-catatan-' + perkaraId);
    const label = document.getElementById('label-catatan-' + perkaraId);
    const isHidden = wrap.style.display === 'none';
    wrap.style.display = isHidden ? 'block' : 'none';
    icon.className     = isHidden ? 'fas fa-minus-circle mr-1' : 'fas fa-plus-circle mr-1';
    label.textContent  = isHidden ? 'Sembunyikan Catatan Internal' : 'Barang Gabungan? Tambah Catatan Internal';
}

// ── PREVIEW DOKUMEN ──
function previewDokumen(url, judul) {
    const isPdf = url.toLowerCase().endsWith('.pdf');
    document.getElementById('modalTitle').innerHTML =
        '<i class="fas fa-eye mr-2" style="color:#f6c90e;"></i>' + judul;
    document.getElementById('previewFrame').style.display = isPdf ? 'block' : 'none';
    document.getElementById('previewImage').style.display = isPdf ? 'none' : 'block';
    if (isPdf) document.getElementById('previewFrame').src = url;
    else document.getElementById('previewImage').src = url;
    $('#previewModal').modal('show');
}

// ── DOKUMEN PERKARA TAMBAH/HAPUS ──
const MAX_DOK = 5;
const MAX_MB  = 2 * 1024 * 1024;

function tampilError(pesan) {
    const box = document.getElementById('error-dokumen');
    document.getElementById('error-dokumen-text').innerText = pesan;
    box.style.display = 'block';
    setTimeout(() => box.style.display = 'none', 4000);
}

function tambahDokumen() {
    const w = document.getElementById('dokumen-wrapper');
    if (w.querySelectorAll('.input-group').length >= MAX_DOK) {
        tampilError('Maksimal ' + MAX_DOK + ' dokumen.'); return;
    }
    w.insertAdjacentHTML('beforeend', `
        <div class="input-group mb-2">
            <input type="file" name="dokumen[]" class="form-control form-control-sm" accept=".pdf" required>
            <input type="text" name="nama_dokumen[]" class="form-control form-control-sm" placeholder="Nama Dokumen" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.input-group').remove()">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>`);
}

function tambahDokumenEdit(id) {
    const w = document.getElementById('dokumen-wrapper-edit-' + id);
    if (!w || w.querySelectorAll('.input-group').length >= 5) {
        const box = document.getElementById('error-dokumen-edit-' + id);
        document.getElementById('error-dokumen-edit-text-' + id).innerText = 'Maksimal 5 dokumen.';
        if (box) { box.style.display = 'block'; setTimeout(() => box.style.display = 'none', 4000); }
        return;
    }
    w.insertAdjacentHTML('beforeend', `
        <div class="input-group mb-2">
            <input type="file" name="dokumen[]" class="form-control form-control-sm" accept=".pdf">
            <input type="text" name="nama_dokumen[]" class="form-control form-control-sm" placeholder="Nama Dokumen">
            <div class="input-group-append">
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.input-group').remove()">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>`);
}

// Validasi file PDF & ukuran
document.addEventListener('change', function (e) {
    if (e.target.type !== 'file') return;
    Array.from(e.target.files).forEach(file => {
        if (file.type !== 'application/pdf') {
            alert('"' + file.name + '" bukan PDF.'); e.target.value = ''; return;
        }
        if (file.size > MAX_MB) {
            alert('"' + file.name + '" melebihi 2MB.'); e.target.value = '';
        }
    });
});

// Validasi harga limit
function validateHargaLimit(input) {
    const val = parseInt(input.value);
    const msgId = 'harga-limit-msg-' + input.closest('form')?.querySelector('[name="perkara_id"]')?.value;
    // fallback jika tidak ditemukan
    input.style.borderColor = (val > 35000000) ? '#e74a3b' : '';
}
</script>
@endpush

@endsection