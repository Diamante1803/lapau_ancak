@extends('layouts.admin')

@section('content')

@php
    $canEdit = in_array($pengajuan->status, ['draft', 'revision']);
@endphp

<div class="container-fluid px-0">

    {{-- ================= WIZARD PROGRESS BAR (STICKY) ================= --}}
    <div class="wizard-sticky bg-white border-bottom mb-4">
        <div class="container-fluid px-4 py-3">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('satker.pengajuan.index') }}" class="btn btn-sm mr-3"
                        style="background: rgba(26,107,60,0.1); color: #1a6b3c; border: 1px solid #1a6b3c; border-radius: 8px;">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <div>
                        <h6 class="mb-0 font-weight-bold" style="color: #1a6b3c;">
                            <i class="fas fa-file-alt mr-2" style="color: #f6c90e;"></i>
                            {{ $pengajuan->judul_pengajuan }}
                        </h6>
                        <small class="text-muted">Pengajuan Lelang — Langkah 1 dari 4</small>
                    </div>
                </div>
                {{-- Badge Status --}}
                <x-badge-status-pengajuan :status="$pengajuan->status" />
            </div>

            {{-- Steps --}}
            <div class="d-flex align-items-center" style="gap: 0;">
                @php
                    $stepItems = [
                        1 => ['label' => 'Info & Dokumen',    'icon' => 'fa-file-alt'],
                        2 => ['label' => 'Perkara',           'icon' => 'fa-balance-scale'],
                        3 => ['label' => 'Barang & Foto',     'icon' => 'fa-boxes'],
                        4 => ['label' => 'Review & Submit',   'icon' => 'fa-paper-plane'],
                    ];
                    $currentStep = 1;
                @endphp

                @foreach($stepItems as $num => $item)
                @php
                    $isDone    = $num < $currentStep || ($steps[$num] ?? false);
                    $isActive  = $num === $currentStep;
                    $isLocked  = $num > $currentStep && !($steps[$num - 1] ?? false);
                @endphp

                {{-- Step Item --}}
                <div class="d-flex align-items-center flex-fill">
                    @if($isLocked)
                    {{-- Locked: tidak bisa diklik --}}
                    <div class="d-flex align-items-center" style="opacity: 0.4; cursor: not-allowed;">
                        <div style="width:34px; height:34px; border-radius:50%;
                            background: #e9ecef; color: #6c757d;
                            display:flex; align-items:center; justify-content:center;
                            font-size:0.8rem; font-weight:bold; flex-shrink:0;">
                            {{ $num }}
                        </div>
                        <span class="ml-2 small d-none d-md-inline" style="color: #6c757d; white-space:nowrap;">
                            {{ $item['label'] }}
                        </span>
                    </div>
                    @elseif($isActive)
                    {{-- Active --}}
                    <div class="d-flex align-items-center">
                        <div style="width:34px; height:34px; border-radius:50%;
                            background: #1a6b3c; color: white;
                            display:flex; align-items:center; justify-content:center;
                            font-size:0.8rem; font-weight:bold; flex-shrink:0;
                            box-shadow: 0 0 0 3px rgba(26,107,60,0.2);">
                            {{ $num }}
                        </div>
                        <span class="ml-2 small font-weight-bold d-none d-md-inline"
                            style="color: #1a6b3c; white-space:nowrap;">
                            {{ $item['label'] }}
                        </span>
                    </div>
                    @else
                    {{-- Done / Accessible --}}
                    <a href="{{ route('satker.pengajuan.step' . $num, $pengajuan) }}"
                        class="d-flex align-items-center text-decoration-none">
                        <div style="width:34px; height:34px; border-radius:50%;
                            background: {{ $isDone ? '#f6c90e' : '#e9ecef' }};
                            color: {{ $isDone ? '#1a6b3c' : '#6c757d' }};
                            display:flex; align-items:center; justify-content:center;
                            font-size:0.8rem; font-weight:bold; flex-shrink:0;">
                            @if($isDone) <i class="fas fa-check" style="font-size:0.7rem;"></i>
                            @else {{ $num }} @endif
                        </div>
                        <span class="ml-2 small d-none d-md-inline"
                            style="color: {{ $isDone ? '#1a6b3c' : '#6c757d' }}; white-space:nowrap;">
                            {{ $item['label'] }}
                        </span>
                    </a>
                    @endif

                    {{-- Connector line --}}
                    @if(!$loop->last)
                    <div class="flex-fill mx-2" style="height:2px;
                        background: {{ $isDone ? '#f6c90e' : '#e9ecef' }};
                        min-width: 20px;">
                    </div>
                    @endif
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <div class="container-fluid px-4">

        {{-- ================= ALERT ================= --}}
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"
            style="border-left: 4px solid #e74a3b; border-radius: 8px;">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        @endif
        @if(session('success'))
        <div id="autoAlert" class="alert alert-success alert-dismissible fade show"
            style="border-left: 4px solid #1a6b3c; border-radius: 8px;">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <script>
            setTimeout(() => {
                let a = document.getElementById('autoAlert');
                if (a) { a.style.opacity = '0'; setTimeout(() => a.remove(), 500); }
            }, 4000);
        </script>
        @endif

        {{-- ================= RIWAYAT REVISI ================= --}}
        @if($pengajuan->catatan_revisi && count($pengajuan->catatan_revisi) > 0)
        <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(90deg, #856404, #a07800); padding: 12px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-history mr-2" style="color: #f6c90e;"></i>
                    Riwayat Revisi
                    <span class="badge ml-2"
                        style="background: rgba(255,255,255,0.2); color: white; border-radius: 20px; font-size: 0.7rem;">
                        {{ count($pengajuan->catatan_revisi) }}x revisi
                    </span>
                    @if($pengajuan->status != 'revision')
                    <span class="badge ml-1"
                        style="background: rgba(255,255,255,0.15); color: #ffe082; font-size: 0.65rem; border-radius: 20px;">
                        sudah disubmit ulang
                    </span>
                    @endif
                </h6>
            </div>
            <div class="card-body p-0">
                @foreach(array_reverse($pengajuan->catatan_revisi) as $idx => $revisi)
                <div class="px-4 py-3 {{ $idx > 0 ? 'border-top' : '' }}"
                    style="{{ $idx == 0 ? 'background: #fffdf0;' : 'background: white;' }}">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div style="min-width:32px; height:32px; border-radius:50%;
                            background: {{ $idx == 0 ? '#f6c90e' : '#e9ecef' }};
                            color: {{ $idx == 0 ? '#856404' : '#6c757d' }};
                            display:flex; align-items:center; justify-content:center;
                            font-weight:bold; font-size:0.75rem; flex-shrink:0;">
                            {{ $revisi['ke_revisi'] }}
                        </div>
                        <div>
                            <div class="font-weight-bold small mb-1" style="color: #856404;">
                                Revisi ke-{{ $revisi['ke_revisi'] }}
                                @if($idx == 0)
                                <span class="badge badge-warning ml-1" style="font-size:0.65rem; border-radius:20px;">Terbaru</span>
                                @endif
                            </div>
                            <p class="mb-1 small" style="color: #4a4a4a;">{{ $revisi['catatan'] }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                {{ \Carbon\Carbon::parse($revisi['tanggal'])->format('d M Y, H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ================= FORM INFO PENGAJUAN ================= --}}
        <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-info-circle mr-2" style="color: #f6c90e;"></i>
                    Informasi Pengajuan
                </h6>
            </div>
            <div class="card-body" style="background: #f8fff9;">
                @if($canEdit)
                <form method="POST" action="{{ route('satker.pengajuan.saveStep1', $pengajuan) }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold" style="color: #1a6b3c;">
                            <i class="fas fa-heading mr-1"></i> Judul Pengajuan
                        </label>
                        <input type="text" name="judul_pengajuan"
                            class="form-control @error('judul_pengajuan') is-invalid @enderror"
                            value="{{ old('judul_pengajuan', $pengajuan->judul_pengajuan) }}"
                            placeholder="Contoh: Pengajuan Lelang Barang Rampasan Q1 2025"
                            style="border-radius: 8px;">
                        @error('judul_pengajuan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-sm font-weight-bold"
                        style="background: #1a6b3c; color: white; border-radius: 8px; padding: 8px 20px;">
                        <i class="fas fa-save mr-1"></i> Simpan Judul
                    </button>
                </form>
                @else
                <p class="mb-0">
                    <span class="text-muted small">Judul Pengajuan</span><br>
                    <strong style="color: #1a6b3c; font-size: 1.05rem;">{{ $pengajuan->judul_pengajuan }}</strong>
                </p>
                @endif
            </div>
        </div>

        {{-- ================= DOKUMEN PENGAJUAN ================= --}}
        <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-folder-open mr-2" style="color: #f6c90e;"></i>
                    Dokumen Pengajuan
                    <span class="badge ml-2"
                        style="background: rgba(255,255,255,0.2); color: white; border-radius: 20px; font-size: 0.7rem;">
                        {{ $pengajuan->dokumenPengajuan->count() }}/3 dokumen
                    </span>
                </h6>
            </div>
            <div class="card-body">

                {{-- Form Upload --}}
                @if($canEdit)
                <form method="POST" action="{{ route('satker.pengajuan.uploadDokumen', $pengajuan) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-4" style="border: 1px dashed #b2d8c0; border-radius: 10px; background: #f8fff9;">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3" style="color: #1a6b3c;">
                                <i class="fas fa-upload mr-2"></i>Upload Dokumen
                            </h6>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-muted">Jenis Dokumen</label>
                                        <select name="jenis"
                                            class="form-control form-control-sm @error('jenis') is-invalid @enderror"
                                            style="border-radius: 6px;">
                                            <option value="sk_panitia">SK Panitia</option>
                                            <option value="izin_penjualan">Izin Penjualan</option>
                                            <option value="surat_penetapan_harga">Surat Penetapan Harga Limit</option>
                                        </select>
                                        @error('jenis')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-muted">File PDF</label>
                                        <input type="file" name="file[]" multiple
                                            accept=".pdf"
                                            class="form-control form-control-sm @error('file') is-invalid @enderror"
                                            style="border-radius: 6px;">
                                        <small class="text-muted">PDF, maks. 2MB</small>
                                        @error('file')<small class="text-danger d-block">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-sm btn-block mb-2 font-weight-bold"
                                        style="background: #1a6b3c; color: white; border-radius: 6px;">
                                        <i class="fas fa-upload mr-1"></i> Upload
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @endif

                {{-- Progress Kelengkapan --}}
                @php
                    $sk      = $pengajuan->dokumenPengajuan->where('jenis','sk_panitia')->first();
                    $izin    = $pengajuan->dokumenPengajuan->where('jenis','izin_penjualan')->first();
                    $harga   = $pengajuan->dokumenPengajuan->where('jenis','surat_penetapan_harga')->first();
                    $done    = collect([$sk, $izin, $harga])->filter()->count();
                    $percent = round(($done / 3) * 100);
                @endphp

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small font-weight-bold" style="color:#1a6b3c;">
                            <i class="fas fa-tasks mr-1"></i>Kelengkapan Dokumen
                        </span>
                        <span class="small font-weight-bold" style="color:#1a6b3c;">{{ $done }}/3</span>
                    </div>
                    <div class="progress" style="height:10px; border-radius:20px; background:#e0eeea;">
                        <div class="progress-bar
                                @if($percent < 40) bg-danger
                                @elseif($percent < 100) bg-warning
                                @else bg-success @endif"
                            style="width:{{ $percent }}%; border-radius:20px; transition: width 0.5s;">
                        </div>
                    </div>
                    <small class="text-muted">{{ $percent }}% lengkap</small>
                </div>

                {{-- 3 Card Dokumen --}}
                <div class="row">
                    @foreach([
                        ['key' => 'sk_panitia',            'label' => 'SK Panitia',                 'doc' => $sk],
                        ['key' => 'izin_penjualan',         'label' => 'Izin Penjualan',              'doc' => $izin],
                        ['key' => 'surat_penetapan_harga',  'label' => 'Surat Penetapan Harga Limit', 'doc' => $harga],
                    ] as $item)
                    @php $doc = $item['doc']; @endphp
                    <div class="col-md-4 mb-3">
                        <div class="card h-100"
                            style="border: 1px solid {{ $doc ? '#b2d8c0' : '#e0e0e0' }};
                                   border-radius: 10px;
                                   background: {{ $doc ? '#f8fff9' : '#fafafa' }};">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file-pdf fa-lg mr-2"
                                        style="color: {{ $doc ? '#1a6b3c' : '#ccc' }};"></i>
                                    <span class="font-weight-bold small">{{ $item['label'] }}</span>
                                    @if($doc)
                                        <span class="badge badge-success ml-auto"
                                            style="border-radius:20px; font-size:0.65rem;">✓ Ada</span>
                                    @else
                                        <span class="badge badge-light ml-auto"
                                            style="border-radius:20px; font-size:0.65rem; color:#999;">Belum</span>
                                    @endif
                                </div>
                                @if($doc)
                                    <button class="btn btn-sm btn-block mb-1"
                                        style="background:#e8f5ee; color:#1a6b3c; border-radius:6px; font-size:0.8rem;"
                                        onclick="previewDokumen('{{ asset('storage/'.$doc->file_path) }}','{{ $item['label'] }}')">
                                        <i class="fas fa-eye mr-1"></i> Lihat
                                    </button>
                                    @if($canEdit)
                                    <form id="form-dok-{{ $doc->id }}"
                                        action="{{ route('satker.dokumen.destroy', $doc->id) }}"
                                        method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-block"
                                            style="background:#fde8e8; color:#e74a3b; border-radius:6px; font-size:0.8rem;"
                                            onclick="swalSubmitForm('form-dok-{{ $doc->id }}', {
                                                title: 'Hapus Dokumen?',
                                                text: 'File {{ $item['label'] }} akan dihapus permanen.',
                                                icon: 'warning',
                                                confirmText: 'Ya, Hapus',
                                                confirmColor: '#e74a3b'
                                            })">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                    @endif
                                @else
                                    <p class="text-muted small mb-0 text-center py-2">Belum diupload</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- ================= NAVIGASI BAWAH ================= --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('satker.pengajuan.index') }}" class="btn btn-sm btn-secondary"
                style="border-radius: 8px;">
                <i class="fas fa-times mr-1"></i> Batal
            </a>
            @if($steps[1])
            <a href="{{ route('satker.pengajuan.step2', $pengajuan) }}"
                class="btn btn-sm font-weight-bold"
                style="background: #1a6b3c; color: white; border-radius: 8px; padding: 8px 24px;">
                Lanjut ke Perkara <i class="fas fa-arrow-right ml-1"></i>
            </a>
            @else
            <button disabled class="btn btn-sm font-weight-bold"
                style="background: #ccc; color: white; border-radius: 8px; padding: 8px 24px;
                       cursor: not-allowed;" title="Lengkapi judul dan 3 dokumen terlebih dahulu">
                Lanjut ke Perkara <i class="fas fa-arrow-right ml-1"></i>
            </button>
            @endif
        </div>

    </div>
</div>

@endsection