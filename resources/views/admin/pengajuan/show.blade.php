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

    // ✅ Satker boleh edit saat draft & revision
    $canEditSatker = $isSatker && ($isDraft || $isRevision);

    // ✅ Readonly kondisi:
    // - Satker tapi sudah submitted / approved
    // - Admin pusat selalu readonly (tidak boleh edit detail)
    $isReadonly = ($isSatker && !($isDraft || $isRevision)) || $isPusat;
@endphp

<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm mr-3"
                style="background: rgba(26,107,60,0.1); color: #1a6b3c; border: 1px solid #1a6b3c; border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <div>
                <h1 class="h4 mb-0 font-weight-bold" style="color: #1a6b3c;">
                    <i class="fas fa-file-alt mr-2" style="color: #f6c90e;"></i>
                    Detail Pengajuan
                </h1>
                <small class="text-muted">{{ $pengajuan->judul_pengajuan }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center mt-2 mt-sm-0" style="gap: 8px;">

            {{-- ================= ADMIN SATKER ================= --}}
            @if($isSatker)

                {{-- Submit hanya saat draft --}}
                @if($canEditSatker)
                <form method="POST" action="{{ route('satker.pengajuan.submit', $pengajuan) }}">
                    @csrf
                    <button class="btn btn-sm font-weight-bold shadow-sm"
                        style="background: linear-gradient(135deg, #1a6b3c, #145c32); color: white; border-radius: 8px;">
                        <i class="fas fa-paper-plane mr-1"></i> Submit ke Pusat
                    </button>
                </form>
                @endif

            {{-- ================= ADMIN PUSAT ================= --}}
            @elseif($isPusat && $isSubmitted)

                <div class="d-flex flex-wrap gap-2" style="gap: 8px;">

                    {{-- APPROVE --}}
                    <form method="POST" action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}">
                        @csrf
                        <button class="btn btn-sm font-weight-bold shadow-sm"
                            style="background: #1a6b3c; color: white; border-radius: 8px; padding: 6px 16px;"
                            onmouseover="this.style.background='#145c32'"
                            onmouseout="this.style.background='#1a6b3c'"
                            onclick="return confirm('Setujui pengajuan ini?')">
                            <i class="fas fa-check-circle mr-1"></i> Setujui
                        </button>
                    </form>

                    {{-- REVISI --}}
                    <button class="btn btn-sm font-weight-bold shadow-sm"
                        style="background: #f6c90e; color: #1a6b3c; border-radius: 8px; padding: 6px 16px;"
                        onmouseover="this.style.background='#e0b800'"
                        onmouseout="this.style.background='#f6c90e'"
                        data-toggle="modal" data-target="#modalRevisi">
                        <i class="fas fa-redo mr-1"></i> Minta Revisi
                    </button>

                    {{-- DELETE --}}
                    <form method="POST" action="{{ route('admin.pengajuan.destroy', $pengajuan->id) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm font-weight-bold shadow-sm"
                            style="background: #e74a3b; color: white; border-radius: 8px; padding: 6px 16px;"
                            onmouseover="this.style.background='#c0392b'"
                            onmouseout="this.style.background='#e74a3b'"
                            onclick="return confirm('Hapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.')">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>

                </div>

            @endif
        </div>
    </div>

    {{-- ================= ALERT ================= --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert"
        style="border-left: 4px solid #e74a3b; border-radius: 8px;">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif
    @if(session('success'))
    <div id="autoAlert" class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"
        style="border-left: 4px solid #1a6b3c; border-radius: 8px;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <script>
        setTimeout(function () {
            let alertBox = document.getElementById('autoAlert');
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s";
                alertBox.style.opacity = "0";
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 4000);
    </script>
    @endif

    {{-- ================= INFO PENGAJUAN ================= --}}
    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">
        <div class="card-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
            <span class="font-weight-bold text-white">
                <i class="fas fa-info-circle mr-2" style="color: #f6c90e;"></i>Informasi Pengajuan
            </span>

            {{-- Hapus — hanya admin_satker --}}
            @if($isSatker && $isDraft)
            <form action="{{ route('satker.pengajuan.destroy', $pengajuan->id) }}"
                method="POST"
                onsubmit="return confirm('Yakin ingin menghapus pengajuan ini beserta semua datanya?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" style="border-radius: 6px;">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
            </form>
            @endif
        </div>

        <div class="card-body" style="background: #f8fff9;">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2">
                        <span class="text-muted small">Judul Pengajuan</span><br>
                        <strong style="color: #1a6b3c;">{{ $pengajuan->judul_pengajuan }}</strong>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <span class="text-muted small">Status</span><br>
                        @if($pengajuan->status == 'draft')
                            <span class="badge badge-warning px-3 py-1" style="border-radius: 20px;">📝 Draft</span>
                        @elseif($pengajuan->status == 'submitted')
                            <span class="badge badge-info px-3 py-1" style="border-radius: 20px;">📤 Dikirim</span>
                        @elseif($pengajuan->status == 'approved')
                            <span class="badge badge-success px-3 py-1" style="border-radius: 20px;">✅ Disetujui</span>
                        @elseif($pengajuan->status == 'rejected')
                            <span class="badge badge-danger px-3 py-1" style="border-radius: 20px;">❌ Ditolak</span>
                        @elseif($pengajuan->status == 'revision')
                            <span class="badge badge-secondary px-3 py-1" style="border-radius: 20px;">🔄 Revisi</span>
                        @endif
                    </p>
                </div>
                @if($pengajuan->catatan_revisi && count($pengajuan->catatan_revisi) > 0)
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px; overflow: hidden;">

                    <div class="card-header" style="background: linear-gradient(90deg, #856404, #a07800); padding: 12px 20px;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-history mr-2" style="color: #f6c90e;"></i>
                            Riwayat Revisi
                            <span class="badge ml-2"
                                style="background: rgba(255,255,255,0.2); color: white; border-radius: 20px; font-size: 0.7rem; padding: 3px 8px;">
                                {{ count($pengajuan->catatan_revisi) }}x revisi
                            </span>
                        </h6>
                    </div>

                    <div class="card-body p-0">
                        @foreach(array_reverse($pengajuan->catatan_revisi) as $idx => $revisi)
                        <div class="px-4 py-3 {{ $idx > 0 ? 'border-top' : '' }}"
                            style="{{ $idx == 0 ? 'background: #fffdf0;' : 'background: white;' }}">

                            <div class="d-flex align-items-start" style="gap: 12px;">

                                {{-- Badge revisi ke- --}}
                                <div style="
                                    min-width: 32px; height: 32px; border-radius: 50%;
                                    background: {{ $idx == 0 ? '#f6c90e' : '#e9ecef' }};
                                    color: {{ $idx == 0 ? '#856404' : '#6c757d' }};
                                    display: flex; align-items: center; justify-content: center;
                                    font-weight: bold; font-size: 0.75rem; flex-shrink: 0;">
                                    {{ $revisi['ke_revisi'] }}
                                </div>

                                <div>
                                    <div class="font-weight-bold small mb-1" style="color: #856404;">
                                        Revisi ke-{{ $revisi['ke_revisi'] }}
                                        @if($idx == 0)
                                        <span class="badge badge-warning ml-1" style="font-size: 0.65rem; border-radius: 20px;">
                                            Terbaru
                                        </span>
                                        @endif
                                    </div>
                                    <p class="mb-1 small" style="color: #4a4a4a;">
                                        {{ $revisi['catatan'] }}
                                    </p>
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
            </div>
        </div>
    </div>

    {{-- ================= DOKUMEN PENGAJUAN ================= --}}
    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-folder-open mr-2" style="color: #f6c90e;"></i>Dokumen Pengajuan
            </h6>
        </div>

        <div class="card-body">

            {{-- Form Upload — hanya admin_satker --}}
            @if($canEditSatker)
            <form method="POST" action="{{ route('satker.pengajuan.uploadDokumen', $pengajuan) }}"
                enctype="multipart/form-data">
                @csrf

                <div class="card mb-4" style="border: 1px dashed #b2d8c0; border-radius: 10px; background: #f8fff9;">
                    <div class="card-body">
                        <h6 class="font-weight-bold mb-3" style="color: #1a6b3c;">
                            <i class="fas fa-upload mr-2"></i>Upload Dokumen Baru
                        </h6>

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-muted">Jenis Dokumen</label>
                                    <select name="jenis" class="form-control form-control-sm @error('jenis') is-invalid @enderror"
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
                                        class="form-control form-control-sm @error('file') is-invalid @enderror"
                                        style="border-radius: 6px;">
                                    <small class="text-muted">PDF, maks. 2MB</small>
                                    @error('file')<small class="text-danger d-block">{{ $message }}</small>@enderror
                                    @if($errors->has('file.*'))
                                        @foreach($errors->get('file.*') as $fileErrors)
                                            @foreach($fileErrors as $err)
                                                <small class="text-danger d-block">{{ $err }}</small>
                                            @endforeach
                                        @endforeach
                                    @endif
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
                $sk     = $pengajuan->dokumenPengajuan->where('jenis','sk_panitia')->first();
                $izin   = $pengajuan->dokumenPengajuan->where('jenis','izin_penjualan')->first();
                $harga  = $pengajuan->dokumenPengajuan->where('jenis','surat_penetapan_harga')->first();
                $total  = 3;
                $done   = 0;
                if($sk)    $done++;
                if($izin)  $done++;
                if($harga) $done++;
                $percent = round(($done / $total) * 100);
            @endphp

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small font-weight-bold" style="color: #1a6b3c;">
                        <i class="fas fa-tasks mr-1"></i>Kelengkapan Dokumen
                    </span>
                    <span class="small font-weight-bold" style="color: #1a6b3c;">{{ $done }}/{{ $total }} dokumen</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 20px; background: #e0eeea;">
                    <div class="progress-bar
                            @if($percent < 40) bg-danger
                            @elseif($percent < 70) bg-warning
                            @else bg-success
                            @endif"
                        role="progressbar"
                        style="width: {{ $percent }}%; border-radius: 20px;">
                    </div>
                </div>
                <small class="text-muted">{{ $percent }}% lengkap</small>
            </div>

            {{-- List Dokumen --}}
            <div class="row">

                {{-- SK Panitia --}}
                <div class="col-md-4 mb-3">
                    <div class="card h-100" style="border: 1px solid {{ $sk ? '#b2d8c0' : '#e0e0e0' }}; border-radius: 10px; background: {{ $sk ? '#f8fff9' : '#fafafa' }};">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-pdf fa-lg mr-2" style="color: {{ $sk ? '#1a6b3c' : '#ccc' }};"></i>
                                <span class="font-weight-bold small">SK Panitia</span>
                                @if($sk)
                                    <span class="badge badge-success ml-auto" style="border-radius: 20px; font-size: 0.65rem;">✓ Ada</span>
                                @else
                                    <span class="badge badge-light ml-auto" style="border-radius: 20px; font-size: 0.65rem; color: #999;">Belum</span>
                                @endif
                            </div>
                            @if($sk)
                                <button class="btn btn-sm btn-block mb-1"
                                    style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px; font-size: 0.8rem;"
                                    onclick="previewDokumen('{{ asset('storage/'.$sk->file_path) }}','SK Panitia')">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </button>
                                @if($canEditSatker)
                                <form action="{{ route('satker.dokumen.destroy', $sk->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-block"
                                        style="background: #fde8e8; color: #e74a3b; border-radius: 6px; font-size: 0.8rem;"
                                        onclick="return confirm('Hapus dokumen ini?')">
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

                {{-- Izin Penjualan --}}
                <div class="col-md-4 mb-3">
                    <div class="card h-100" style="border: 1px solid {{ $izin ? '#b2d8c0' : '#e0e0e0' }}; border-radius: 10px; background: {{ $izin ? '#f8fff9' : '#fafafa' }};">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-pdf fa-lg mr-2" style="color: {{ $izin ? '#1a6b3c' : '#ccc' }};"></i>
                                <span class="font-weight-bold small">Izin Penjualan</span>
                                @if($izin)
                                    <span class="badge badge-success ml-auto" style="border-radius: 20px; font-size: 0.65rem;">✓ Ada</span>
                                @else
                                    <span class="badge badge-light ml-auto" style="border-radius: 20px; font-size: 0.65rem; color: #999;">Belum</span>
                                @endif
                            </div>
                            @if($izin)
                                <button class="btn btn-sm btn-block mb-1"
                                    style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px; font-size: 0.8rem;"
                                    onclick="previewDokumen('{{ asset('storage/'.$izin->file_path) }}','Izin Penjualan')">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </button>
                                @if($canEditSatker)
                                <form action="{{ route('satker.dokumen.destroy', $izin->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-block"
                                        style="background: #fde8e8; color: #e74a3b; border-radius: 6px; font-size: 0.8rem;"
                                        onclick="return confirm('Hapus dokumen ini?')">
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

                {{-- Surat Penetapan Harga --}}
                <div class="col-md-4 mb-3">
                    <div class="card h-100" style="border: 1px solid {{ $harga ? '#b2d8c0' : '#e0e0e0' }}; border-radius: 10px; background: {{ $harga ? '#f8fff9' : '#fafafa' }};">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-pdf fa-lg mr-2" style="color: {{ $harga ? '#1a6b3c' : '#ccc' }};"></i>
                                <span class="font-weight-bold small">Surat Penetapan Harga Limit</span>
                                @if($harga)
                                    <span class="badge badge-success ml-auto" style="border-radius: 20px; font-size: 0.65rem;">✓ Ada</span>
                                @else
                                    <span class="badge badge-light ml-auto" style="border-radius: 20px; font-size: 0.65rem; color: #999;">Belum</span>
                                @endif
                            </div>
                            @if($harga)
                                <button class="btn btn-sm btn-block mb-1"
                                    style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px; font-size: 0.8rem;"
                                    onclick="previewDokumen('{{ asset('storage/'.$harga->file_path) }}','Surat Penetapan Harga Limit')">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </button>
                                @if($canEditSatker)
                                <form action="{{ route('satker.dokumen.destroy', $harga->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-block"
                                        style="background: #fde8e8; color: #e74a3b; border-radius: 6px; font-size: 0.8rem;"
                                        onclick="return confirm('Hapus dokumen ini?')">
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

            </div>
        </div>
    </div>

    {{-- ================= PERKARA ================= --}}
    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(90deg, #c0392b, #a93226); padding: 14px 20px;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-balance-scale mr-2" style="color: #f6c90e;"></i>Data Perkara
            </h6>
        </div>

        <div class="card-body">

            @if($canEditSatker)
            <form method="POST" action="{{ route('satker.pengajuan.perkara.store', $pengajuan) }}" enctype="multipart/form-data">
                @csrf

                <div class="card mb-4" style="border: 1px dashed #f5c6cb; border-radius: 10px; background: #fff8f8;">
                    <div class="card-body">
                        <h6 class="font-weight-bold mb-3" style="color: #c0392b;">
                            <i class="fas fa-plus-circle mr-2"></i>Tambah Perkara Baru
                        </h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Nomor Perkara</label>
                                    <input type="text" name="nomor_perkara"
                                        class="form-control form-control-sm"
                                        placeholder="Nomor Perkara" required
                                        style="border-radius: 6px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Nama Tersangka</label>
                                    <input type="text" name="nama_tersangka"
                                        class="form-control form-control-sm"
                                        placeholder="Nama Tersangka" required
                                        style="border-radius: 6px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Tanggal Putusan</label>
                                    <input type="date" name="tanggal_putusan"
                                        class="form-control form-control-sm" required
                                        style="border-radius: 6px;">
                                </div>
                            </div>
                        </div>

                        <label class="small font-weight-bold text-muted">Upload Dokumen Perkara</label>

                        @error('dokumen')<small class="text-danger d-block mb-1">{{ $message }}</small>@enderror
                        @if($errors->has('dokumen.*'))
                            @foreach($errors->get('dokumen.*') as $e)
                                @foreach($e as $err)<small class="text-danger d-block mb-1">{{ $err }}</small>@endforeach
                            @endforeach
                        @endif

                        <div id="dokumen-wrapper">
                            <div class="input-group mb-2">
                                <input type="file" name="dokumen[]"
                                    class="form-control form-control-sm @error('dokumen') is-invalid @enderror"
                                    accept=".pdf" required>
                                <input type="text" name="nama_dokumen[]"
                                    class="form-control form-control-sm"
                                    placeholder="Nama Dokumen" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm"
                                        style="background: #c0392b; color: white; border-radius: 0 6px 6px 0;"
                                        onclick="tambahDokumen()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="error-dokumen" class="mt-1 mb-2" style="display:none;">
                            <small class="text-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <span id="error-dokumen-text"></span>
                            </small>
                        </div>
                        <small class="text-muted d-block mb-3">Format PDF, maksimal 5 dokumen & 2MB per file</small>

                        <button class="btn btn-sm font-weight-bold"
                            style="background: #c0392b; color: white; border-radius: 6px;">
                            <i class="fas fa-save mr-1"></i> Tambah Perkara
                        </button>
                    </div>
                </div>
            </form>
            @endif

            {{-- Tabel Perkara --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(90deg, #c0392b, #a93226);">
                        <tr>
                            <th class="text-white border-0" width="4%">No</th>
                            <th class="text-white border-0">Nomor Perkara</th>
                            <th class="text-white border-0">Tersangka</th>
                            <th class="text-white border-0">Tgl. Putusan</th>
                            <th class="text-white border-0">Dokumen</th>
                            @if($canEditSatker)
                            <th class="text-white border-0" width="12%">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan->perkaras as $i => $p)
                        <tr style="border-left: 3px solid transparent; transition: 0.2s;"
                            onmouseover="this.style.borderLeft='3px solid #c0392b'"
                            onmouseout="this.style.borderLeft='3px solid transparent'">
                            <td class="align-middle">{{ $i+1 }}</td>
                            <td class="align-middle font-weight-bold" style="color: #c0392b;">
                                {{ $p->nomor_perkara }}
                            </td>
                            <td class="align-middle">{{ $p->nama_tersangka }}</td>
                            <td class="align-middle text-muted small">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ \Carbon\Carbon::parse($p->tanggal_putusan)->format('d-m-Y') }}
                            </td>
                            <td class="align-middle">
                                @forelse($p->dokumenPerkara as $doc)
                                <div class="mb-1 d-flex align-items-center" style="gap: 4px;">
                                    <i class="fas fa-file-alt text-danger small"></i>
                                    <span class="small mr-1">{{ $doc->nama_dokumen }}</span>
                                    <button type="button" class="btn btn-sm"
                                        style="padding: 2px 8px; background: #fde8e8; color: #c0392b; border-radius: 4px; font-size: 0.75rem;"
                                        onclick="previewDokumen('{{ asset('storage/'.$doc->file_path) }}','{{ $doc->nama_dokumen }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($canEditSatker)
                                    <form action="{{ route('satker.pengajuan.perkara.dokumen.destroy', $doc->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                            style="padding: 2px 8px; background: #fde8e8; color: #e74a3b; border-radius: 4px; font-size: 0.75rem;"
                                            onclick="return confirm('Yakin hapus dokumen ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                @empty
                                    <span class="text-muted small">Belum ada dokumen</span>
                                @endforelse
                            </td>
                            @if($canEditSatker)
                            <td class="align-middle text-center">
                                <button class="btn btn-sm mr-1"
                                    style="background: #fff3cd; color: #856404; border-radius: 6px;"
                                    data-toggle="modal" data-target="#editPerkara{{ $p->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('satker.pengajuan.perkara.destroy', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm"
                                        style="background: #fde8e8; color: #e74a3b; border-radius: 6px;"
                                        onclick="return confirm('Yakin hapus perkara ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-balance-scale fa-2x mb-2 d-block" style="color: #f5c6cb;"></i>
                                Belum ada data perkara
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= LOOP PERKARA — BARANG ================= --}}
    @foreach($pengajuan->perkaras as $perkara)

    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">
        <div class="card-header d-flex align-items-center"
            style="background: linear-gradient(90deg, #f6c90e, #e0b800); padding: 14px 20px;">
            <i class="fas fa-boxes mr-2" style="color: #1a6b3c;"></i>
            <span class="font-weight-bold" style="color: #1a6b3c;">
                Barang — Perkara {{ $perkara->nama_tersangka }}
            </span>
            <span class="badge ml-auto" style="background: #1a6b3c; color: white; border-radius: 20px;">
                {{ $perkara->barangs->count() }} barang
            </span>
        </div>

        <div class="card-body">

            {{-- Form Tambah Barang --}}
            @if($canEditSatker)
            <form method="POST" action="{{ route('satker.perkara.barang.store', $perkara) }}"
                id="formBarang-{{ $perkara->id }}">
                @csrf

                <input type="hidden" name="perkara_id" value="{{ $perkara->id }}">

                <div class="card mb-4" style="border: 1px dashed #f6c90e; border-radius: 10px; background: #fffdf0;">
                    <div class="card-body">
                        <h6 class="font-weight-bold mb-3" style="color: #856404;">
                            <i class="fas fa-plus-circle mr-2"></i>Tambah Barang
                        </h6>

                        {{-- Error banner khusus perkara ini --}}
                        @if($errors->any() && old('perkara_id') == $perkara->id)
                        <div class="alert alert-danger py-2 mb-3"
                            style="border-left: 4px solid #e74a3b; border-radius: 8px; font-size: 0.82rem;">
                            <i class="fas fa-exclamation-circle mr-1"></i>
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
                                        class="form-control form-control-sm @if($errors->any() && old('perkara_id') == $perkara->id) @error('nama_barang') is-invalid @enderror @endif"
                                        placeholder="Nama Barang"
                                        value="{{ old('perkara_id') == $perkara->id ? old('nama_barang') : '' }}"
                                        style="border-radius: 6px;">
                                    @if(old('perkara_id') == $perkara->id)
                                        @error('nama_barang')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">
                                        Harga Limit (Rp)
                                        <span class="text-muted font-weight-normal">— maks. Rp 35.000.000</span>
                                    </label>
                                    <input type="number" name="harga_awal"
                                        class="form-control form-control-sm @if($errors->any() && old('perkara_id') == $perkara->id) @error('harga_awal') is-invalid @enderror @endif"
                                        placeholder="0"
                                        min="1"
                                        max="35000000"
                                        value="{{ old('perkara_id') == $perkara->id ? old('harga_awal') : '' }}"
                                        style="border-radius: 6px;"
                                        oninput="validateHargaLimit(this)">
                                    <small id="harga-limit-msg-{{ $perkara->id }}" style="color:#c0392b; display:none;">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Harga limit tidak boleh melebihi Rp 35.000.000
                                    </small>
                                    @if(old('perkara_id') == $perkara->id)
                                        @error('harga_awal')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Deskripsi</label>
                                    <input type="text" name="deskripsi"
                                        class="form-control form-control-sm"
                                        placeholder="Deskripsi singkat kondisi barang (ditampilkan ke pembeli)"
                                        value="{{ old('perkara_id') == $perkara->id ? old('deskripsi') : '' }}"
                                        style="border-radius: 6px;">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold text-muted">
                                        Catatan Internal
                                        <span class="badge"
                                            style="background: #e8f4fd; color: #1a6b3c; font-size: 0.7rem; border-radius: 10px; padding: 2px 8px;">
                                            <i class="fas fa-lock mr-1"></i>Hanya terlihat oleh Admin
                                        </span>
                                    </label>
                                    <textarea name="catatan_internal"
                                        class="form-control form-control-sm"
                                        rows="2"
                                        placeholder="Contoh : Hasil penggabungan dari perkara..."
                                        style="border-radius: 6px;">{{ old('perkara_id') == $perkara->id ? old('catatan_internal') : '' }}</textarea>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Catatan ini tidak akan ditampilkan kepada pembeli.
                                    </small>
                                </div>
                            </div>

                        </div>

                        <div class="mt-3">
                            <button class="btn btn-sm font-weight-bold"
                                style="background: #f6c90e; color: #1a6b3c; border-radius: 6px;">
                                <i class="fas fa-plus mr-1"></i> Tambah Barang
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @endif

            {{-- Tabel Barang --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f8fff9;">
                        <tr>
                            <th style="color: #1a6b3c; border-top: none;">Nama</th>
                            <th style="color: #1a6b3c; border-top: none;">Harga Limit</th>
                            <th style="color: #1a6b3c; border-top: none;">Deskripsi</th>
                            <th style="color: #1a6b3c; border-top: none;">Catatan Internal</th>
                            <th style="color: #1a6b3c; border-top: none;">Foto Barang</th>
                            @if($canEditSatker)
                            <th style="color: #1a6b3c; border-top: none;" width="120">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perkara->barangs as $barang)
                        <tr style="border-left: 3px solid transparent; transition: 0.2s;"
                            onmouseover="this.style.borderLeft='3px solid #f6c90e'"
                            onmouseout="this.style.borderLeft='3px solid transparent'">

                            <td class="align-middle font-weight-bold">{{ $barang->nama_barang }}</td>

                            <td class="align-middle" style="color: #1a6b3c; font-weight: bold;">
                                Rp {{ number_format($barang->harga_awal, 0, ',', '.') }}
                            </td>

                            <td class="align-middle text-muted small">{{ $barang->deskripsi ?? '-' }}</td>

                            <td class="align-middle">
                                @if($barang->catatan_internal)
                                    <span class="badge"
                                        style="background: #fff3cd; color: #856404; border-radius: 6px; font-size: 0.72rem; white-space: normal; text-align: left; display: inline-block; max-width: 180px; padding: 4px 7px;">
                                        <i class="fas fa-lock mr-1" style="font-size: 0.65rem;"></i>
                                        {{ $barang->catatan_internal }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td class="align-middle">
                                {{-- Mini Gallery Foto --}}
                                <div class="mb-2 d-flex flex-wrap" style="gap: 6px;">
                                    @forelse($barang->fotoBarang ?? [] as $foto)
                                    <div class="photo-box" style="position:relative; display:inline-block;">
                                        <img src="{{ asset('storage/' . $foto->file_path) }}"
                                            style="width:70px; height:70px; object-fit:cover; border-radius:8px; border: 2px solid #e0eeea; cursor:pointer; transition: 0.2s;"
                                            onmouseover="this.style.borderColor='#1a6b3c'"
                                            onmouseout="this.style.borderColor='#e0eeea'"
                                            onclick="previewDokumen('{{ asset('storage/'.$foto->file_path) }}', 'Foto Barang')">
                                        @if($canEditSatker)
                                        <form action="{{ route('satker.barang.foto.destroy', $foto->id) }}"
                                            method="POST"
                                            style="position:absolute; top:2px; right:2px;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="photo-delete"
                                                onclick="return confirm('Hapus foto ini?')">×</button>
                                        </form>
                                        @endif
                                    </div>
                                    @empty
                                        <small class="text-muted">Belum ada foto</small>
                                    @endforelse
                                </div>

                                {{-- Upload Foto —  hanya admin_satker --}}
                                @if($canEditSatker)
                                <form method="POST"
                                    action="{{ route('satker.barang.uploadFoto', $barang) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <input type="file" name="foto[]" multiple
                                            accept="image/*"
                                            class="form-control form-control-sm"
                                            style="border-radius: 6px 0 0 6px; font-size: 0.78rem;">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm"
                                                style="background: #1a6b3c; color: white; border-radius: 0 6px 6px 0;">
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
                                    style="background: #fff3cd; color: #856404; border-radius: 6px; width: 34px;"
                                    data-toggle="modal"
                                    data-target="#modalEditBarang-{{ $barang->id }}"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('satker.barang.destroy', $barang->id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm"
                                        style="background: #fde8e8; color: #e74a3b; border-radius: 6px; width: 34px;"
                                        onclick="return confirm('Hapus barang ini?')"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @endif

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x mb-2 d-block" style="color: #f0d060;"></i>
                                Belum ada barang
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Edit Barang --}}
            @foreach($perkara->barangs as $barang)
            <div class="modal fade" id="modalEditBarang-{{ $barang->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                        <div class="modal-header"
                            style="background: linear-gradient(90deg, #f6c90e, #e0b800);">
                            <h5 class="modal-title font-weight-bold" style="color: #1a6b3c;">
                                <i class="fas fa-edit mr-2"></i>Edit Barang
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('satker.barang.update', $barang->id) }}">
                            @csrf @method('PUT')

                            <div class="modal-body" style="background: #fffdf0;">
                                <div class="form-group">
                                    <label class="small font-weight-bold" style="color: #1a6b3c;">
                                        <i class="fas fa-box mr-1"></i>Nama Barang
                                    </label>
                                    <input type="text" name="nama_barang"
                                        class="form-control"
                                        value="{{ $barang->nama_barang }}"
                                        style="border-radius: 8px;" required>
                                </div>

                                <div class="form-group">
                                    <label class="small font-weight-bold" style="color: #1a6b3c;">
                                        <i class="fas fa-align-left mr-1"></i>Deskripsi
                                    </label>
                                    <textarea name="deskripsi" rows="2"
                                        class="form-control"
                                        placeholder="Deskripsi (opsional) — ditampilkan ke pembeli"
                                        style="border-radius: 8px;">{{ $barang->deskripsi }}</textarea>
                                </div>

                                {{-- ✅ FIELD BARU: Catatan Internal di Modal Edit --}}
                                <div class="form-group">
                                    <label class="small font-weight-bold" style="color: #1a6b3c;">
                                        <i class="fas fa-lock mr-1"></i>Catatan Internal
                                        <span class="badge"
                                            style="background: #e8f4fd; color: #1a6b3c; font-size: 0.7rem; border-radius: 10px; padding: 2px 8px;">
                                            Hanya terlihat oleh Admin
                                        </span>
                                    </label>
                                    <textarea name="catatan_internal" rows="2"
                                        class="form-control"
                                        placeholder="Contoh: Hasil peleburan barang dari 5 perkara..."
                                        style="border-radius: 8px;">{{ $barang->catatan_internal }}</textarea>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Tidak ditampilkan kepada pembeli.
                                    </small>
                                </div>
                                {{-- END FIELD BARU --}}

                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold" style="color: #1a6b3c;">
                                        <i class="fas fa-tag mr-1"></i>Harga Limit
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                style="background: #f6c90e; border-color: #f6c90e; color: #1a6b3c; font-weight: bold;">Rp</span>
                                        </div>
                                        <input type="number" name="harga_awal"
                                            class="form-control"
                                            value="{{ $barang->harga_awal }}"
                                            min="0" required
                                            style="border-radius: 0 8px 8px 0;">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer" style="background: #fffdf0;">
                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"
                                    style="border-radius: 6px;">
                                    <i class="fas fa-times mr-1"></i>Batal
                                </button>
                                <button type="submit" class="btn btn-sm font-weight-bold"
                                    style="background: #f6c90e; color: #1a6b3c; border-radius: 6px;">
                                    <i class="fas fa-save mr-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    @endforeach

</div>

<div class="modal fade" id="modalRevisi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.pengajuan.revisi', $pengajuan->id) }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Revisi Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <label>Catatan Revisi</label>
                    <textarea name="catatan_revisi" class="form-control" rows="4" required></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-warning">Kirim Revisi</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- ================= MODAL PREVIEW DOKUMEN ================= --}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
            <div class="modal-header" style="background: linear-gradient(90deg, #1a6b3c, #145c32);">
                <h5 class="modal-title text-white font-weight-bold" id="modalTitle">
                    <i class="fas fa-eye mr-2" style="color: #f6c90e;"></i>Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center" style="background: #f8fff9;">
                <iframe id="previewFrame" width="100%" height="500px" style="display:none; border-radius: 8px;"></iframe>
                <img id="previewImage" src="" style="max-width:100%; display:none; border-radius: 8px;" />
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL EDIT PERKARA ================= --}}
@foreach($pengajuan->perkaras as $p)
<div class="modal fade" id="editPerkara{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">

            <form method="POST"
                action="{{ route('satker.pengajuan.perkara.update', $p->id) }}"
                enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="modal-header" style="background: linear-gradient(90deg, #1a6b3c, #145c32);">
                    <h5 class="modal-title text-white font-weight-bold">
                        <i class="fas fa-edit mr-2" style="color: #f6c90e;"></i>Edit Perkara
                    </h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body" style="background: #f8fff9;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Nomor Perkara</label>
                                <input type="text" name="nomor_perkara"
                                    value="{{ $p->nomor_perkara }}"
                                    class="form-control" style="border-radius: 8px;" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Nama Tersangka</label>
                                <input type="text" name="nama_tersangka"
                                    value="{{ $p->nama_tersangka }}"
                                    class="form-control" style="border-radius: 8px;" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Tanggal Putusan</label>
                                <input type="date" name="tanggal_putusan"
                                    value="{{ $p->tanggal_putusan ? \Carbon\Carbon::parse($p->tanggal_putusan)->format('Y-m-d') : '' }}"
                                    class="form-control" style="border-radius: 8px;" required>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <label class="small font-weight-bold text-muted">Dokumen Terlampir</label>
                    <ul class="list-group mb-3">
                        @forelse($p->dokumenPerkara ?? [] as $doc)
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            style="border-radius: 8px; margin-bottom: 4px; border: 1px solid #e0eeea; background: #f8fff9;">
                            <span><i class="fas fa-file-pdf text-danger mr-2"></i>{{ $doc->nama_dokumen }}</span>
                            <button type="button" class="btn btn-sm"
                                style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px; font-size: 0.78rem;"
                                onclick="previewDokumen('{{ asset('storage/'.$doc->file_path) }}','{{ $doc->nama_dokumen }}')">
                                <i class="fas fa-eye mr-1"></i> Lihat
                            </button>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted" style="border-radius: 8px;">
                            Tidak ada dokumen
                        </li>
                        @endforelse
                    </ul>

                    <label class="small font-weight-bold text-muted">Tambah Dokumen Baru</label>

                    <div id="dokumen-wrapper-edit-{{ $p->id }}">
                        <div class="input-group mb-2">
                            <input type="file" name="dokumen[]"
                                class="form-control form-control-sm"
                                accept=".pdf">
                            <input type="text" name="nama_dokumen[]"
                                class="form-control form-control-sm"
                                placeholder="Nama Dokumen">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-success"
                                    onclick="tambahDokumenEdit({{ $p->id }})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="error-dokumen-edit-{{ $p->id }}" class="mt-1 mb-2" style="display:none;">
                        <small class="text-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="error-dokumen-edit-text-{{ $p->id }}"></span>
                        </small>
                    </div>
                    <small class="text-muted">Format PDF, maksimal 5 dokumen & 2MB per file</small>
                </div>

                <div class="modal-footer" style="background: #f8fff9;">
                    <button class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius: 6px;">Batal</button>
                    <button class="btn btn-sm font-weight-bold"
                        style="background: #1a6b3c; color: white; border-radius: 6px;">
                        <i class="fas fa-save mr-1"></i> Update Perkara
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endforeach

{{-- ================= SCRIPTS ================= --}}
<script>
// Preview dokumen
function previewDokumen(url, nama) {
    let img   = document.getElementById('previewImage');
    let frame = document.getElementById('previewFrame');
    let title = document.getElementById('modalTitle');
    title.innerHTML = '<i class="fas fa-eye mr-2" style="color:#f6c90e;"></i>' + (nama ?? 'Dokumen');
    if (url.match(/\.(jpeg|jpg|png)$/i)) {
        img.src = url; img.style.display = 'block'; frame.style.display = 'none';
    } else {
        frame.src = url; frame.style.display = 'block'; img.style.display = 'none';
    }
    $('#previewModal').modal('show');
}

// Validasi upload file pengajuan
document.querySelector('input[name="file[]"]').addEventListener('change', function() {
    const maxSize = 2 * 1024 * 1024;
    let errors = [];
    Array.from(this.files).forEach(file => {
        if (file.type !== 'application/pdf') errors.push(`"${file.name}" bukan PDF.`);
        if (file.size > maxSize) errors.push(`"${file.name}" melebihi 2MB.`);
    });
    if (errors.length > 0) { alert(errors.join('\n')); this.value = ''; }
});

// Tambah/hapus dokumen perkara
const MAX_DOKUMEN = 5;
const MAX_SIZE    = 2 * 1024 * 1024;

function tampilError(pesan) {
    const box  = document.getElementById('error-dokumen');
    const text = document.getElementById('error-dokumen-text');
    text.innerText = pesan;
    box.style.display = 'block';
    setTimeout(() => box.style.display = 'none', 4000);
}

function tambahDokumen() {
    const wrapper = document.getElementById('dokumen-wrapper');
    if (wrapper.querySelectorAll('.input-group').length >= MAX_DOKUMEN) {
        tampilError('Maksimal ' + MAX_DOKUMEN + ' dokumen saja.'); return;
    }
    wrapper.insertAdjacentHTML('beforeend', `
    <div class="input-group mb-2">
        <input type="file" name="dokumen[]" class="form-control form-control-sm" accept=".pdf" required>
        <input type="text" name="nama_dokumen[]" class="form-control form-control-sm" placeholder="Nama Dokumen" required>
        <div class="input-group-append">
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusDokumen(this)"><i class="fas fa-minus"></i></button>
        </div>
    </div>`);
}

function hapusDokumen(btn) { btn.closest('.input-group').remove(); }

document.getElementById('dokumen-wrapper').addEventListener('change', function(e) {
    if (e.target.type !== 'file') return;
    const file = e.target.files[0]; if (!file) return;
    if (file.type !== 'application/pdf') { tampilError(`"${file.name}" bukan file PDF.`); e.target.value = ''; return; }
    if (file.size > MAX_SIZE) { tampilError(`"${file.name}" melebihi batas 2MB.`); e.target.value = ''; }
});

// Tambah/hapus dokumen edit perkara
function tampilErrorEdit(id, pesan) {
    const box  = document.getElementById('error-dokumen-edit-' + id);
    const text = document.getElementById('error-dokumen-edit-text-' + id);
    if (!box || !text) return;
    text.innerText = pesan;
    box.style.display = 'block';
    setTimeout(() => box.style.display = 'none', 4000);
}

function tambahDokumenEdit(id) {
    const wrapper = document.getElementById('dokumen-wrapper-edit-' + id);
    if (!wrapper) return;
    if (wrapper.querySelectorAll('.input-group').length >= 5) {
        tampilErrorEdit(id, 'Maksimal 5 dokumen saja.'); return;
    }
    wrapper.insertAdjacentHTML('beforeend', `
    <div class="input-group mb-2">
        <input type="file" name="dokumen[]" class="form-control form-control-sm" accept=".pdf">
        <input type="text" name="nama_dokumen[]" class="form-control form-control-sm" placeholder="Nama Dokumen">
        <div class="input-group-append">
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusDokumenEdit(this)"><i class="fas fa-minus"></i></button>
        </div>
    </div>`);
}

function hapusDokumenEdit(btn) { btn.closest('.input-group').remove(); }

document.addEventListener('change', function(e) {
    if (e.target.type !== 'file') return;
    const wrapper = e.target.closest('[id^="dokumen-wrapper-edit-"]');
    if (!wrapper) return;
    const id = wrapper.id.replace('dokumen-wrapper-edit-', '');
    const file = e.target.files[0]; if (!file) return;
    if (file.type !== 'application/pdf') { tampilErrorEdit(id, `"${file.name}" bukan PDF.`); e.target.value = ''; return; }
    if (file.size > MAX_SIZE) { tampilErrorEdit(id, `"${file.name}" melebihi 2MB.`); e.target.value = ''; }
});

function validateHargaLimit(input) {
    const maxLimit = 35000000;
    const msg = document.getElementById('harga-limit-msg');

    if (input.value > maxLimit) {
        input.value = maxLimit;
        msg.textContent = 'Harga limit maksimal Rp 35.000.000';
        msg.style.display = 'block';
        setTimeout(() => msg.style.display = 'none', 4000);
    } else if (input.value <= 0 || input.value === '') {
        input.value = '';
        msg.textContent = 'Harga limit tidak boleh kosong atau 0';
        msg.style.display = 'block';
        setTimeout(() => msg.style.display = 'none', 4000);
    } else {
        msg.style.display = 'none';
    }
}

@if($errors->any() && old('perkara_id'))
    $(document).ready(function() {
        const perkaraId = "{{ old('perkara_id') }}";
        const form = document.getElementById('formBarang-' + perkaraId);
        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
@endif
</script>

<style>
.photo-delete {
    opacity: 0;
    transition: 0.2s;
    background: rgba(231,74,59,0.85);
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 14px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.photo-box:hover .photo-delete { opacity: 1; }
</style>

@endsection