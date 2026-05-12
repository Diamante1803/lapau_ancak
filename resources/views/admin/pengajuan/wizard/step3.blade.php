@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0">

    {{-- ================= WIZARD PROGRESS BAR (STICKY) ================= --}}
    <div class="wizard-sticky bg-white border-bottom mb-4">
        <div class="container-fluid px-4 py-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('satker.pengajuan.step2', $pengajuan) }}" class="btn btn-sm mr-3"
                        style="background:rgba(26,107,60,0.1);color:#1a6b3c;border:1px solid #1a6b3c;border-radius:8px;">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <div>
                        <h6 class="mb-0 font-weight-bold" style="color:#1a6b3c;">
                            <i class="fas fa-boxes mr-2" style="color:#f6c90e;"></i>
                            {{ $pengajuan->judul_pengajuan }}
                        </h6>
                        <small class="text-muted">Pengajuan Lelang — Langkah 3 dari 4</small>
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
                        2 => ['label' => 'Perkara',         'icon' => 'fa-balance-scale'],
                        3 => ['label' => 'Barang & Foto',   'icon' => 'fa-boxes'],
                        4 => ['label' => 'Review & Submit', 'icon' => 'fa-paper-plane'],
                    ];
                    $currentStep = 3;
                @endphp
                @foreach($stepItems as $num => $item)
                @php
                    $isDone   = $steps[$num] ?? false;
                    $isActive = $num === $currentStep;
                    $isLocked = $num > $currentStep && !($steps[$num - 1] ?? false);
                @endphp
                <div class="d-flex align-items-center flex-fill">
                    @if($isLocked)
                    <div class="d-flex align-items-center" style="opacity:0.4;cursor:not-allowed;">
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

        {{-- ================= LOOP PERKARA — BARANG ================= --}}
        @foreach($pengajuan->perkaras as $perkara)

        <div class="card shadow mb-4" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="card-header d-flex align-items-center"
                style="background: linear-gradient(90deg, #f9d100 0%, #e08c00 100%); padding: 14px 20px;">
                <i class="fas fa-boxes mr-2" style="color:#1a6b3c;"></i>
                <span class="font-weight-bold" style="color:#1a6b3c;">
                    Barang — {{ $perkara->nama_tersangka }}
                </span>
                <span class="badge ml-2" style="background:#1a6b3c;color:white;border-radius:20px;font-size:0.7rem;">
                    {{ $perkara->nomor_perkara }}
                </span>
                <span class="badge ml-auto" style="background:white;color:#1a6b3c;border-radius:20px;">
                    {{ $perkara->barangs->count() }} barang
                </span>
            </div>

            <div class="card-body">

                {{-- Form Tambah Barang --}}
                @if($canEditSatker)
                {{-- Tombol Toggle --}}
                <button type="button" id="btn-toggle-barang-{{ $perkara->id }}"
                    onclick="toggleFormBarang({{ $perkara->id }})"
                    class="btn btn-sm font-weight-bold mb-3"
                    style="background:#f6c90e;color:#1a6b3c;border-radius:8px;padding:6px 16px;">
                    <i class="fas fa-plus mr-1" id="icon-form-barang-{{ $perkara->id }}"></i>
                    <span id="label-form-barang-{{ $perkara->id }}">Tambah Barang</span>
                </button>

                {{-- Form (hidden by default) --}}
                <div id="wrap-form-barang-{{ $perkara->id }}"
                    class="form-barang-wrap"
                    style="overflow:hidden; max-height:0; transition:max-height 0.35s ease;">

                    <form method="POST" action="{{ route('satker.perkara.barang.store', $perkara) }}"
                        id="formBarang-{{ $perkara->id }}">
                        @csrf
                        <input type="hidden" name="perkara_id" value="{{ $perkara->id }}">

                        <div class="card mb-4" style="border:1px dashed #f6c90e;border-radius:10px;background:#fffdf0;">
                            <div class="card-body">
                                <h6 class="font-weight-bold mb-3" style="color:#856404;">
                                    <i class="fas fa-plus-circle mr-2"></i>Tambah Barang Baru
                                </h6>

                                {{-- Error banner --}}
                                @if($errors->any() && old('perkara_id') == $perkara->id)
                                <div class="alert alert-danger py-2 mb-3"
                                    style="border-left:4px solid #e74a3b;border-radius:8px;font-size:0.82rem;">
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
                                                style="border-radius:6px;">
                                            @if(old('perkara_id') == $perkara->id)
                                                @error('nama_barang')<small class="text-danger d-block">{{ $message }}</small>@enderror
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
                                                placeholder="0" min="1" max="35000000"
                                                value="{{ old('perkara_id') == $perkara->id ? old('harga_awal') : '' }}"
                                                style="border-radius:6px;"
                                                oninput="validateHargaLimit(this)">
                                            <small id="harga-limit-msg-{{ $perkara->id }}"
                                                style="color:#c0392b;display:none;">
                                                <i class="fas fa-exclamation-circle mr-1"></i>Harga limit tidak boleh melebihi Rp 35.000.000
                                            </small>
                                            @if(old('perkara_id') == $perkara->id)
                                                @error('harga_awal')<small class="text-danger d-block">{{ $message }}</small>@enderror
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
                                                style="border-radius:6px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <button type="button"
                                                onclick="toggleCatatanInternal({{ $perkara->id }})"
                                                style="background:none;border:none;padding:0;color:#1a6b3c;font-size:0.82rem;font-weight:600;cursor:pointer;">
                                                <i class="fas fa-plus-circle mr-1" id="icon-catatan-{{ $perkara->id }}"></i>
                                                <span id="label-catatan-{{ $perkara->id }}">Barang Gabungan? Tambah Catatan Internal</span>
                                            </button>
                                            <div id="wrap-catatan-{{ $perkara->id }}" style="display:none;margin-top:10px;">
                                                <div class="d-flex align-items-center mb-1" style="gap:6px;">
                                                    <label class="small font-weight-bold text-muted mb-0">Catatan Internal</label>
                                                    <span class="badge"
                                                        style="background:#e8f4fd;color:#1a6b3c;font-size:0.68rem;border-radius:10px;padding:2px 8px;">
                                                        <i class="fas fa-lock mr-1"></i>Hanya terlihat Admin
                                                    </span>
                                                </div>
                                                <textarea name="catatan_internal"
                                                    class="form-control form-control-sm" rows="2"
                                                    placeholder="Contoh: Hasil penggabungan dari perkara No. 123/2025 dan 456/2025"
                                                    style="border-radius:6px;">{{ old('perkara_id') == $perkara->id ? old('catatan_internal') : '' }}</textarea>
                                                <small class="text-muted d-block mt-1" style="font-size:0.75rem;">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Perkara tanpa barang tersendiri tetap bisa diajukan selama barang gabungannya tercatat di sini.
                                                </small>
                                            </div>
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
                                        style="border-radius:6px;">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                @endif

                {{-- Tabel Barang --}}
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: linear-gradient(90deg, #1a6b3c, #145c32);">
                            <tr>
                                <th style="color: white; border-top: none;">Nama</th>
                                <th style="color: white; border-top: none;">Harga Limit</th>
                                <th style="color: white; border-top: none;">Deskripsi</th>
                                <th style="color: white; border-top: none;">Catatan Internal</th>
                                <th style="color: white; border-top: none;">Foto Barang</th>
                                @if($canEditSatker)
                                <th style="color: white; border-top: none;" width="120">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perkara->barangs as $barang)
                            <tr style="border-left:3px solid transparent;transition:0.2s;"
                                onmouseover="this.style.borderLeft='3px solid #f6c90e'"
                                onmouseout="this.style.borderLeft='3px solid transparent'">

                                <td class="align-middle font-weight-bold">{{ $barang->nama_barang }}</td>
                                <td class="align-middle font-weight-bold" style="color:#1a6b3c;">
                                    Rp {{ number_format($barang->harga_awal, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-muted small">{{ $barang->deskripsi ?? '-' }}</td>
                                <td class="align-middle">
                                    @if($barang->catatan_internal)
                                        <span class="badge"
                                            style="background:#fff3cd;color:#856404;border-radius:6px;font-size:0.72rem;
                                                   white-space:normal;text-align:left;display:inline-block;max-width:160px;padding:4px 7px;">
                                            <i class="fas fa-lock mr-1" style="font-size:0.65rem;"></i>
                                            {{ $barang->catatan_internal }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                {{-- Foto --}}
                                <td class="align-middle">
                                    <div class="mb-2 d-flex flex-wrap" style="gap:6px;">
                                        @forelse($barang->fotoBarang ?? [] as $foto)
                                        <div class="photo-box" style="position:relative;display:inline-block;">
                                            <img src="{{ asset('storage/'.$foto->file_path) }}"
                                                style="width:60px;height:60px;object-fit:cover;border-radius:8px;
                                                       border:2px solid #e0eeea;cursor:pointer;transition:0.2s;"
                                                onmouseover="this.style.borderColor='#1a6b3c'"
                                                onmouseout="this.style.borderColor='#e0eeea'"
                                                onclick="previewDokumen('{{ asset('storage/'.$foto->file_path) }}','Foto Barang')">
                                            @if($canEditSatker)
                                            <form action="{{ route('satker.barang.foto.destroy', $foto->id) }}"
                                                method="POST" style="position:absolute;top:2px;right:2px;">
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
                                    @if($canEditSatker)
                                    <form method="POST"
                                        action="{{ route('satker.barang.uploadFoto', $barang) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="file" name="foto[]" multiple accept="image/*"
                                                class="form-control form-control-sm"
                                                style="border-radius:6px 0 0 6px;font-size:0.78rem;">
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
                                        style="background:#fff3cd;color:#856404;border-radius:6px;width:34px;"
                                        onclick="$('#modalEditBarang-{{ $barang->id }}').modal('show')"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="form-barang-{{ $barang->id }}"
                                        action="{{ route('satker.barang.destroy', $barang->id) }}"
                                        method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm"
                                            style="background:#fde8e8;color:#e74a3b;border-radius:6px;width:34px;"
                                            title="Hapus"
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
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2 d-block" style="color:#f0d060;opacity:0.5;"></i>
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
                                        <label class="small font-weight-bold" style="color:#1a6b3c;">
                                            <i class="fas fa-box mr-1"></i>Nama Barang
                                        </label>
                                        <input type="text" name="nama_barang" class="form-control"
                                            value="{{ $barang->nama_barang }}" style="border-radius:8px;" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold" style="color:#1a6b3c;">
                                            <i class="fas fa-align-left mr-1"></i>Deskripsi
                                        </label>
                                        <textarea name="deskripsi" rows="2" class="form-control"
                                            placeholder="Deskripsi (opsional) — ditampilkan ke pembeli"
                                            style="border-radius:8px;">{{ $barang->deskripsi }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold" style="color:#1a6b3c;">
                                            <i class="fas fa-lock mr-1"></i>Catatan Internal
                                            <span class="badge"
                                                style="background:#e8f4fd;color:#1a6b3c;font-size:0.7rem;border-radius:10px;padding:2px 8px;">
                                                Hanya terlihat Admin
                                            </span>
                                        </label>
                                        <textarea name="catatan_internal" rows="2" class="form-control"
                                            placeholder="Contoh: Hasil peleburan barang dari beberapa perkara..."
                                            style="border-radius:8px;">{{ $barang->catatan_internal }}</textarea>
                                        <small class="text-muted" style="font-size:0.75rem;">
                                            <i class="fas fa-info-circle mr-1"></i>Tidak ditampilkan ke pembeli.
                                        </small>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold" style="color:#1a6b3c;">
                                            <i class="fas fa-tag mr-1"></i>Harga Limit
                                        </label>
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
                                        style="border-radius:6px;">
                                        <i class="fas fa-times mr-1"></i>Batal
                                    </button>
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

            </div>
        </div>
        @endforeach

        {{-- ================= NAVIGASI BAWAH ================= --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('satker.pengajuan.step2', $pengajuan) }}" class="btn btn-sm btn-secondary"
                style="border-radius:8px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            @if($steps[3])
            <a href="{{ route('satker.pengajuan.step4', $pengajuan) }}"
                class="btn btn-sm font-weight-bold"
                style="background:#1a6b3c;color:white;border-radius:8px;padding:8px 24px;">
                Review & Submit <i class="fas fa-arrow-right ml-1"></i>
            </a>
            @else
            <button disabled class="btn btn-sm font-weight-bold"
                style="background:#ccc;color:white;border-radius:8px;padding:8px 24px;cursor:not-allowed;"
                title="Pastikan minimal ada 1 barang dalam pengajuan">
                Review & Submit <i class="fas fa-arrow-right ml-1"></i>
            </button>
            @endif
        </div>

    </div>
</div>

<script>
function toggleFormBarang(perkaraId) {
    const wrap  = document.getElementById('wrap-form-barang-' + perkaraId);
    const icon  = document.getElementById('icon-form-barang-' + perkaraId);
    const label = document.getElementById('label-form-barang-' + perkaraId);

    const isOpen = wrap.dataset.open === 'true';

    if (isOpen) {
        wrap.style.maxHeight = '0';
        wrap.dataset.open    = 'false';
        icon.className       = 'fas fa-plus mr-1';
        label.textContent    = 'Tambah Barang';
    } else {
        wrap.style.maxHeight = '1000px';
        wrap.dataset.open    = 'true';
        icon.className       = 'fas fa-times mr-1';
        label.textContent    = 'Tutup Form';
    }
}

// Auto buka jika ada validation error
document.addEventListener('DOMContentLoaded', function () {
    const perkaraId = '{{ old("perkara_id") }}';
    if (perkaraId) {
        const wrap = document.getElementById('wrap-form-barang-' + perkaraId);
        if (wrap) {
            wrap.style.maxHeight = '1000px';
            wrap.dataset.open    = 'true';
            const icon  = document.getElementById('icon-form-barang-' + perkaraId);
            const label = document.getElementById('label-form-barang-' + perkaraId);
            if (icon)  icon.className    = 'fas fa-times mr-1';
            if (label) label.textContent = 'Tutup Form';
        }
    }
});

function toggleCatatanInternal(perkaraId) {
    const wrap  = document.getElementById('wrap-catatan-'  + perkaraId);
    const icon  = document.getElementById('icon-catatan-'  + perkaraId);
    const label = document.getElementById('label-catatan-' + perkaraId);
    const isHidden = wrap.style.display === 'none';
    wrap.style.display  = isHidden ? 'block' : 'none';
    icon.className      = isHidden ? 'fas fa-minus-circle mr-1' : 'fas fa-plus-circle mr-1';
    label.textContent   = isHidden ? 'Sembunyikan Catatan Internal' : 'Barang Gabungan? Tambah Catatan Internal';
}

// Auto expand jika old value terisi saat validasi gagal
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('textarea[name="catatan_internal"]').forEach(function (el) {
        if (el.value.trim() !== '') {
            const wrap = el.closest('[id^="wrap-catatan-"]');
            if (wrap) {
                const id = wrap.id.replace('wrap-catatan-', '');
                wrap.style.display = 'block';
                const icon  = document.getElementById('icon-catatan-'  + id);
                const label = document.getElementById('label-catatan-' + id);
                if (icon)  icon.className    = 'fas fa-minus-circle mr-1';
                if (label) label.textContent = 'Sembunyikan Catatan Internal';
            }
        }
    });

    // Scroll ke form yang error
    @if($errors->any() && old('perkara_id'))
    const perkaraId = "{{ old('perkara_id') }}";
    const form = document.getElementById('formBarang-' + perkaraId);
    if (form) form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    @endif
});
</script>

@endsection