@extends('layouts.admin')

@section('content')

@php
    $canEdit = in_array($pengajuan->status, ['draft', 'revision']);
@endphp

<div class="container-fluid px-0">

    {{-- ================= WIZARD PROGRESS BAR (STICKY) ================= --}}
    <div class="wizard-sticky bg-white border-bottom mb-4">
        <div class="container-fluid px-4 py-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('satker.pengajuan.step1', $pengajuan) }}" class="btn btn-sm mr-3"
                        style="background: rgba(26,107,60,0.1); color: #1a6b3c; border: 1px solid #1a6b3c; border-radius: 8px;">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <div>
                        <h6 class="mb-0 font-weight-bold" style="color: #1a6b3c;">
                            <i class="fas fa-balance-scale mr-2" style="color: #f6c90e;"></i>
                            {{ $pengajuan->judul_pengajuan }}
                        </h6>
                        <small class="text-muted">Pengajuan Lelang — Langkah 2 dari 4</small>
                    </div>
                </div>
                {{-- Badge Status --}}
                <x-badge-status-pengajuan :status="$pengajuan->status" />
            </div>

            {{-- Steps --}}
            <div class="d-flex align-items-center">
                @php
                    $stepItems   = [
                        1 => ['label' => 'Info & Dokumen',  'icon' => 'fa-file-alt'],
                        2 => ['label' => 'Putusan',         'icon' => 'fa-balance-scale'],
                        3 => ['label' => 'Barang & Foto',   'icon' => 'fa-boxes'],
                        4 => ['label' => 'Review & Submit', 'icon' => 'fa-paper-plane'],
                    ];
                    $currentStep = 2;
                @endphp
                @foreach($stepItems as $num => $item)
                @php
                    $isDone   = $steps[$num] ?? false;
                    $isActive = $num === $currentStep;
                    $isLocked = $num > $currentStep && !($steps[$num - 1] ?? false);
                @endphp
                <div class="d-flex align-items-center flex-fill">
                    @if($isLocked)
                    <div class="d-flex align-items-center" style="opacity:0.4; cursor:not-allowed;">
                        <div style="width:34px;height:34px;border-radius:50%;background:#e9ecef;color:#6c757d;
                            display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:bold;flex-shrink:0;">
                            {{ $num }}
                        </div>
                        <span class="ml-2 small d-none d-md-inline" style="color:#6c757d;white-space:nowrap;">{{ $item['label'] }}</span>
                    </div>
                    @elseif($isActive)
                    <div class="d-flex align-items-center">
                        <div style="width:34px;height:34px;border-radius:50%;background:#1a6b3c;color:white;
                            display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:bold;
                            flex-shrink:0;box-shadow:0 0 0 3px rgba(26,107,60,0.2);">
                            {{ $num }}
                        </div>
                        <span class="ml-2 small font-weight-bold d-none d-md-inline" style="color:#1a6b3c;white-space:nowrap;">{{ $item['label'] }}</span>
                    </div>
                    @else
                    <a href="{{ route('satker.pengajuan.step' . $num, $pengajuan) }}"
                        class="d-flex align-items-center text-decoration-none">
                        <div style="width:34px;height:34px;border-radius:50%;
                            background:{{ $isDone ? '#f6c90e' : '#e9ecef' }};
                            color:{{ $isDone ? '#1a6b3c' : '#6c757d' }};
                            display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:bold;flex-shrink:0;">
                            @if($isDone)<i class="fas fa-check" style="font-size:0.7rem;"></i>@else{{ $num }}@endif
                        </div>
                        <span class="ml-2 small d-none d-md-inline"
                            style="color:{{ $isDone ? '#1a6b3c' : '#6c757d' }};white-space:nowrap;">{{ $item['label'] }}</span>
                    </a>
                    @endif
                    @if(!$loop->last)
                    <div class="flex-fill mx-2" style="height:2px;background:{{ $isDone ? '#f6c90e' : '#e9ecef' }};min-width:20px;"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">

        {{-- Alert --}}
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" style="border-left:4px solid #e74a3b;border-radius:8px;">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        @endif
        @if(session('success'))
        <div id="autoAlert" class="alert alert-success alert-dismissible fade show" style="border-left:4px solid #1a6b3c;border-radius:8px;">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <script>setTimeout(() => { let a = document.getElementById('autoAlert'); if(a){a.style.opacity='0';setTimeout(()=>a.remove(),500);} }, 4000);</script>
        @endif

        {{-- ================= RIWAYAT REVISI ================= --}}
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
                        <div class="d-flex overflow-auto" style="gap:0;">
                            @foreach($pengajuan->catatan_revisi as $idx => $revisi)
                            <div class="flex-shrink-0 px-4 py-3"
                                style="min-width:220px; max-width:260px; border-right:1px solid #f0e6c8;
                                    {{ $idx == count($pengajuan->catatan_revisi) - 1 ? 'background:#fffdf0;' : 'background:white;' }}">

                                <div class="d-flex align-items-center mb-2" style="gap:8px;">
                                    <div style="
                                        width:28px; height:28px; border-radius:50%; flex-shrink:0;
                                        background:{{ $idx == count($pengajuan->catatan_revisi) - 1 ? '#f6c90e' : '#e9ecef' }};
                                        color:{{ $idx == count($pengajuan->catatan_revisi) - 1 ? '#856404' : '#6c757d' }};
                                        display:flex; align-items:center; justify-content:center;
                                        font-weight:bold; font-size:0.72rem;">
                                        {{ $revisi['ke_revisi'] }}
                                    </div>
                                    <div class="font-weight-bold small" style="color:#856404;">
                                        Revisi ke-{{ $revisi['ke_revisi'] }}
                                        @if($idx == count($pengajuan->catatan_revisi) - 1)
                                        <span class="badge badge-warning ml-1" style="font-size:0.62rem;border-radius:20px;">
                                            Terbaru
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <p class="mb-1 small" style="color:#4a4a4a; font-size:0.82rem; line-height:1.4;">
                                    {{ $revisi['catatan'] }}
                                </p>
                                <small class="text-muted" style="font-size:0.72rem;">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ \Carbon\Carbon::parse($revisi['tanggal'])->format('d M Y, H:i') }}
                                </small>

                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

        {{-- ================= FORM TAMBAH PERKARA ================= --}}
        @if($canEdit)
        <div class="card shadow mb-4" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="card-header" style="background:linear-gradient(90deg,#c0392b,#a93226);padding:14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2" style="color:#f6c90e;"></i>Tambah Putusan Perkara Baru
                </h6>
            </div>
            <div class="card-body" style="background:#fff8f8;">
                <form method="POST" action="{{ route('satker.pengajuan.perkara.store', $pengajuan) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Nomor Putusan Perkara</label>
                                <input type="text" name="nomor_perkara"
                                    class="form-control form-control-sm" placeholder="Nomor Putusan Perkara"
                                    value="{{ old('nomor_perkara') }}" required style="border-radius:6px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Nama Tersangka</label>
                                <input type="text" name="nama_tersangka"
                                    class="form-control form-control-sm" placeholder="Nama Tersangka"
                                    value="{{ old('nama_tersangka') }}" required style="border-radius:6px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Tanggal Putusan</label>
                                <input type="date" name="tanggal_putusan"
                                    class="form-control form-control-sm"
                                    value="{{ old('tanggal_putusan') }}" required style="border-radius:6px;">
                            </div>
                        </div>
                    </div>

                    <label class="small font-weight-bold text-muted">Upload Dokumen Putusan Perkara</label>
                    @error('dokumen')<small class="text-danger d-block mb-1">{{ $message }}</small>@enderror

                    <div id="dokumen-wrapper">
                        <div class="input-group mb-2">
                            <input type="file" name="dokumen[]"
                                class="form-control form-control-sm" accept=".pdf" required>
                            <input type="text" name="nama_dokumen[]"
                                class="form-control form-control-sm" placeholder="Nama Dokumen" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm"
                                    style="background:#c0392b;color:white;border-radius:0 6px 6px 0;"
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
                        style="background:#c0392b;color:white;border-radius:6px;">
                        <i class="fas fa-save mr-1"></i> Tambah Putusan Perkara
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- ================= LIST PERKARA (ACCORDION) ================= --}}
        <div class="card shadow mb-4" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="card-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);padding:14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-balance-scale mr-2" style="color:#f6c90e;"></i>
                    Data Putusan Perkara
                    <span class="badge ml-2"
                        style="background:rgba(255,255,255,0.2);color:white;border-radius:20px;font-size:0.7rem;">
                        {{ $pengajuan->perkaras->count() }} perkara
                    </span>
                </h6>
            </div>
            <div class="card-body p-0">

                @forelse($pengajuan->perkaras as $i => $p)
                <div class="border-bottom" style="{{ $i === 0 ? '' : '' }}">

                    {{-- Accordion Header --}}
                    <div class="d-flex align-items-center justify-content-between px-4 py-3"
                        style="cursor:pointer; background: {{ $i % 2 === 0 ? '#f8fff9' : 'white' }};"
                        onclick="togglePerkara({{ $p->id }})">
                        <div class="d-flex align-items-center" style="gap:12px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:#c0392b;color:white;
                                display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:0.8rem;flex-shrink:0;">
                                {{ $i + 1 }}
                            </div>
                            <div>
                                <div class="font-weight-bold" style="color:#c0392b;">{{ $p->nomor_perkara }}</div>
                                <small class="text-muted">{{ $p->nama_tersangka }} —
                                    {{ \Carbon\Carbon::parse($p->tanggal_putusan)->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            {{-- Badge dokumen --}}
                            <span class="badge {{ $p->dokumenPerkara->count() > 0 ? 'badge-success' : 'badge-danger' }}"
                                style="border-radius:20px;font-size:0.7rem;">
                                {{ $p->dokumenPerkara->count() }} dok
                            </span>
                            @if($canEdit)
                            {{-- Edit --}}
                            <button type="button" class="btn btn-sm"
                                style="background:#fff3cd;color:#856404;border-radius:6px;width:30px;height:30px;padding:0;"
                                onclick="event.stopPropagation(); $('#editPerkara{{ $p->id }}').modal('show');">
                                <i class="fas fa-edit" style="font-size:0.75rem;"></i>
                            </button>
                            {{-- Hapus --}}
                            <form id="form-perkara-{{ $p->id }}"
                                action="{{ route('satker.pengajuan.perkara.destroy', $p->id) }}"
                                method="POST" style="display:inline;" onclick="event.stopPropagation()">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm"
                                    style="background:#fde8e8;color:#e74a3b;border-radius:6px;width:30px;height:30px;padding:0;"
                                    onclick="event.stopPropagation(); swalSubmitForm('form-perkara-{{ $p->id }}', {
                                        title: 'Hapus PutusanPerkara?',
                                        text: 'Putusan Perkara beserta seluruh dokumen dan barangnya akan dihapus permanen.',
                                        icon: 'warning',
                                        confirmText: 'Ya, Hapus',
                                        confirmColor: '#e74a3b'
                                    })">
                                    <i class="fas fa-trash" style="font-size:0.75rem;"></i>
                                </button>
                            </form>
                            @endif
                            {{-- Chevron --}}
                            <i class="fas fa-chevron-down text-muted transition-transform"
                                id="chevron-{{ $p->id }}"
                                style="font-size:0.8rem;transition:transform 0.3s;"></i>
                        </div>
                    </div>

                    {{-- Accordion Body --}}
                    <div id="perkara-body-{{ $p->id }}" class="perkara-body" style="background:#fdfdfd;">
                        <div class="px-4 py-3">

                            {{-- Dokumen Perkara --}}
                            <h6 class="small font-weight-bold mb-2" style="color:#1a6b3c;">
                                <i class="fas fa-paperclip mr-1"></i> Dokumen Terlampir
                            </h6>

                            @forelse($p->dokumenPerkara as $doc)
                            <div class="d-flex align-items-center mb-2 p-2"
                                style="background:#f8fff9;border-radius:8px;border:1px solid #e0eeea;">
                                <i class="fas fa-file-pdf text-danger mr-2"></i>
                                <span class="small flex-fill">{{ $doc->nama_dokumen }}</span>
                                <button type="button" class="btn btn-sm mr-1"
                                    style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;font-size:0.75rem;"
                                    onclick="previewDokumen('{{ asset('storage/'.$doc->file_path) }}','{{ $doc->nama_dokumen }}')">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </button>
                                @if($canEdit)
                                <form id="form-perkara-dok-{{ $doc->id }}"
                                    action="{{ route('satker.pengajuan.perkara.dokumen.destroy', $doc->id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm"
                                        style="background:#fde8e8;color:#e74a3b;border-radius:6px;font-size:0.75rem;"
                                        onclick="swalSubmitForm('form-perkara-dok-{{ $doc->id }}', {
                                            title: 'Hapus Dokumen?',
                                            text: '{{ $doc->nama_dokumen }} akan dihapus permanen.',
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
                            <div class="text-center py-2 text-muted small">
                                <i class="fas fa-exclamation-circle text-warning mr-1"></i>
                                Belum ada dokumen — wajib upload minimal 1 dokumen
                            </div>
                            @endforelse

                            {{-- Upload Dokumen Tambahan --}}
                            @if($canEdit)
                            <form method="POST"
                                action="{{ route('satker.pengajuan.perkara.uploadDokumen', $p) }}"
                                enctype="multipart/form-data" class="mt-3">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="file" name="dokumen[]" multiple
                                        accept=".pdf"
                                        class="form-control form-control-sm"
                                        style="border-radius:6px 0 0 6px;">
                                    <input type="text" name="nama_dokumen[]"
                                        class="form-control form-control-sm"
                                        placeholder="Nama dokumen">
                                    <div class="input-group-append">
                                        <button class="btn btn-sm"
                                            style="background:#1a6b3c;color:white;border-radius:0 6px 6px 0;">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">PDF, maks. 2MB</small>
                            </form>
                            @endif

                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-balance-scale fa-2x mb-2 d-block" style="color:#f5c6cb;opacity:0.5;"></i>
                    Belum ada data putusan perkara — tambahkan putusan di atas
                </div>
                @endforelse

            </div>
        </div>

        {{-- ================= NAVIGASI BAWAH ================= --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('satker.pengajuan.step1', $pengajuan) }}" class="btn btn-sm btn-secondary"
                style="border-radius:8px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            @if($steps[2])
            <form method="POST" action="{{ route('satker.pengajuan.saveStep2', $pengajuan) }}">
                @csrf
                <button type="submit" class="btn btn-sm font-weight-bold"
                    style="background:#1a6b3c;color:white;border-radius:8px;padding:8px 24px;">
                    Lanjut ke Barang <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </form>
            @else
            <button disabled class="btn btn-sm font-weight-bold"
                style="background:#ccc;color:white;border-radius:8px;padding:8px 24px;cursor:not-allowed;"
                title="Pastikan minimal ada 1 putusan perkara dan setiap putusan perkara memiliki dokumen">
                Lanjut ke Barang <i class="fas fa-arrow-right ml-1"></i>
            </button>
            @endif
        </div>

    </div>
</div>

{{-- ================= MODAL EDIT PERKARA ================= --}}
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
                                <label class="small font-weight-bold text-muted">Nomor Putusan Perkara</label>
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
                        <li class="list-group-item text-center text-muted" style="border-radius:8px;">Tidak ada dokumen</li>
                        @endforelse
                    </ul>
                    <label class="small font-weight-bold text-muted">Tambah Dokumen Baru</label>
                    <div id="dokumen-wrapper-edit-{{ $p->id }}"
                        data-existing="{{ $p->dokumenPerkara->count() }}">
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
                    <small class="text-muted">Format PDF, maksimal 5 dokumen & 2MB per file</small>
                </div>
                <div class="modal-footer" style="background:#f8fff9;">
                    <button class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:6px;">Batal</button>
                    <button class="btn btn-sm font-weight-bold"
                        style="background:#1a6b3c;color:white;border-radius:6px;">
                        <i class="fas fa-save mr-1"></i> Update Putusan Perkara
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
    .perkara-body {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.35s ease;
}
.perkara-body.open {
    max-height: 1000px; /* cukup besar untuk konten apapun */
}
</style>

<script>
// ================================================
// ACCORDION PERKARA
// ================================================
function togglePerkara(id) {
    const body    = document.getElementById('perkara-body-' + id);
    const chevron = document.getElementById('chevron-' + id);
    const isOpen  = body.classList.contains('open');

    // Tutup semua dulu
    document.querySelectorAll('.perkara-body').forEach(el => {
        el.classList.remove('open');
    });
    document.querySelectorAll('[id^="chevron-"]').forEach(el => {
        el.style.transform = 'rotate(0deg)';
    });

    // Buka yang diklik (kalau sebelumnya tertutup)
    if (!isOpen) {
        body.classList.add('open');
        chevron.style.transform = 'rotate(180deg)';
    }
}

// ================================================
// TAMBAH / HAPUS DOKUMEN PERKARA BARU
// ================================================
const MAX_DOKUMEN = 5;
const MAX_SIZE    = 2 * 1024 * 1024;

function tampilError(pesan) {
    const box  = document.getElementById('error-dokumen');
    const text = document.getElementById('error-dokumen-text');
    text.innerText    = pesan;
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
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusDokumen(this)">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>`);
}

function hapusDokumen(btn) { btn.closest('.input-group').remove(); }

document.getElementById('dokumen-wrapper')?.addEventListener('change', function (e) {
    if (e.target.type !== 'file') return;
    const file = e.target.files[0]; if (!file) return;
    if (file.type !== 'application/pdf') { tampilError(`"${file.name}" bukan file PDF.`); e.target.value = ''; return; }
    if (file.size > MAX_SIZE) { tampilError(`"${file.name}" melebihi batas 2MB.`); e.target.value = ''; }
});

// ================================================
// TAMBAH / HAPUS DOKUMEN EDIT PERKARA
// ================================================
function tambahDokumenEdit(id) {
    const wrapper = document.getElementById('dokumen-wrapper-edit-' + id);
    if (!wrapper) return;

    const existing = parseInt(wrapper.dataset.existing || 0);
    const currentNew = wrapper.querySelectorAll('.input-group').length;

    const total = existing + currentNew;

    if (total >= 5) {
        alert('Maksimal total 5 dokumen (termasuk yang sudah ada)');
        return;
    }

    wrapper.insertAdjacentHTML('beforeend', `
        <div class="input-group mb-2">
            <input type="file" name="dokumen[]" class="form-control form-control-sm" accept=".pdf">
            <input type="text" name="nama_dokumen[]" class="form-control form-control-sm" placeholder="Nama Dokumen">
            <div class="input-group-append">
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusDokumenEdit(this, ${id})">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
    `);
}

function hapusDokumenEdit(btn) { btn.closest('.input-group').remove(); }
</script>

@endsection