@extends('layouts.admin')

@section('title', 'Data Satker')

@section('content')

@php
    $totalSatker  = $satkers->count();
    $denganPJ     = $satkers->filter(fn($s) => $s->users->isNotEmpty())->count();
    $tanpaPJ      = $totalSatker - $denganPJ;
@endphp

{{-- HEADER --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="font-weight-bold mb-0" style="color: #1a6b3c;">
            <i class="fas fa-building mr-2" style="color: #f6c90e;"></i>
            Data Satker
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0" style="font-size: 0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color:#1a6b3c;">Dashboard</a></li>
                <li class="breadcrumb-item active text-muted">Satker</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-sm font-weight-bold"
        style="background: linear-gradient(135deg,#1a6b3c,#145c32); color:white; border-radius:8px; padding: 8px 18px;"
        data-toggle="modal" data-target="#modalTambahSatker">
        <i class="fas fa-plus mr-1"></i> Tambah Satker
    </button>
</div>

{{-- STATISTIK --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <x-statistic-card
            title="Total Satker"
            value="{{ $totalSatker }}"
            icon="fa-building"
            color="#1a6b3c"
        />
    </div>
    <div class="col-md-4 mb-3">
        <x-statistic-card
            title="Memiliki Penanggung Jawab"
            value="{{ $denganPJ }}"
            icon="fa-user-check"
            color="#f39c12"
        />
    </div>
    <div class="col-md-4 mb-3">
        <x-statistic-card
            title="Belum Ada Penanggung Jawab"
            value="{{ $tanpaPJ }}"
            icon="fa-user-times"
            color="#c0392b"
        />
    </div>
</div>

{{-- ALERT --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px; border:none; font-size:0.875rem;">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:10px; border:none; font-size:0.875rem;">
    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

{{-- TABEL --}}
<div class="card shadow-sm" style="border-radius:12px; border:none;">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background:linear-gradient(90deg,#1a6b3c,#145c32); color:white; border-radius:12px 12px 0 0;">
        <span class="font-weight-bold" style="font-size:0.9rem;">
            <i class="fas fa-list mr-2"></i>Daftar Satuan Kerja
        </span>
        <span style="background:rgba(255,255,255,0.2);color:white;border-radius:20px;font-size:0.75rem;padding:3px 12px;">
            {{ $totalSatker }} satker
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelSatker" class="table table-hover mb-0" style="font-size:0.875rem;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="border-0 pl-4" style="width:50px;color:#6c757d;font-weight:600;font-size:0.78rem;padding:12px 16px;">#</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">NAMA SATKER</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">PENANGGUNG JAWAB</th>
                        <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">ALAMAT</th>
                        <th class="border-0 text-center" style="color:#6c757d;font-weight:600;font-size:0.78rem;width:80px;" data-no-sort>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($satkers as $key => $satker)
                    <tr>
                        <td class="pl-4 align-middle text-muted" style="padding:14px 16px;">{{ $key + 1 }}</td>

                        {{-- NAMA SATKER --}}
                        <td class="align-middle" style="padding:14px 12px;">
                            <div class="d-flex align-items-center">
                                <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#d4edda,#a8d5b5);display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                                    <i class="fas fa-building" style="color:#1a6b3c;font-size:0.85rem;"></i>
                                </div>
                                <div class="font-weight-bold" style="color:#2d3748;">
                                    {{ $satker->nama_satker }}
                                </div>
                            </div>
                        </td>

                        {{-- PENANGGUNG JAWAB --}}
                        <td class="align-middle" style="padding:14px 12px;">
                            @if($satker->users->isNotEmpty())
                            <div class="d-flex align-items-center">
                                <div style="width:30px;height:30px;border-radius:50%;background:#1a6b3c;display:flex;align-items:center;justify-content:center;margin-right:8px;flex-shrink:0;">
                                    <i class="fas fa-user" style="color:white;font-size:0.72rem;"></i>
                                </div>
                                <div>
                                    <div style="color:#2d3748;font-size:0.875rem;">{{ $satker->users->first()->name }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $satker->users->first()->email }}</div>
                                </div>
                            </div>
                            @else
                            <span class="badge" style="background:#fff3cd;color:#856404;border-radius:6px;font-size:0.75rem;padding:4px 10px;">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Belum ada PJ
                            </span>
                            @endif
                        </td>

                        {{-- ALAMAT --}}
                        <td class="align-middle text-muted" style="padding:14px 12px; font-size:0.85rem; max-width:200px;">
                            @if($satker->alamat)
                            <i class="fas fa-map-marker-alt mr-1" style="color:#c0392b;"></i>
                            {{ $satker->alamat }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="align-middle text-center" style="padding:14px 12px;">
                            <div class="dropdown no-arrow">
                                <button class="btn-action-dots dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-smooth animated--fade-in">
                                    <a class="dropdown-item small font-weight-bold" href="#" data-toggle="modal" data-target="#modalEditSatker-{{ $satker->id }}">
                                        <i class="fas fa-edit fa-sm fa-fw mr-2 text-warning"></i> Edit Satker
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item small font-weight-bold text-danger" href="#" data-toggle="modal" data-target="#modalHapusSatker-{{ $satker->id }}">
                                        <i class="fas fa-trash fa-sm fa-fw mr-2"></i> Hapus Satker
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEditSatker-{{ $satker->id }}" tabindex="-1">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
                                <div class="modal-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);">
                                    <h5 class="modal-title font-weight-bold text-white" style="font-size:0.95rem;">
                                        <i class="fas fa-edit mr-2" style="color:#f6c90e;"></i>Edit Satker
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <form action="{{ route('admin.satker.update', $satker->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body" style="background:#f8fff9; font-size:0.875rem;">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-muted">Nama Satker</label>
                                            <input type="text" name="nama_satker"
                                                class="form-control"
                                                style="border-radius:8px; font-size:0.875rem;"
                                                value="{{ $satker->nama_satker }}" required>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold text-muted">Alamat</label>
                                            <input type="text" name="alamat"
                                                class="form-control"
                                                style="border-radius:8px; font-size:0.875rem;"
                                                value="{{ $satker->alamat }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="background:#f8fff9;">
                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:6px;">
                                            <i class="fas fa-times mr-1"></i>Batal
                                        </button>
                                        <button type="submit" class="btn btn-sm font-weight-bold"
                                            style="background:#1a6b3c;color:white;border-radius:6px;padding:6px 16px;">
                                            <i class="fas fa-save mr-1"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL HAPUS --}}
                    <div class="modal fade" id="modalHapusSatker-{{ $satker->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
                                <div class="modal-header" style="background:linear-gradient(90deg,#c0392b,#e74c3c);">
                                    <h5 class="modal-title font-weight-bold text-white" style="font-size:0.95rem;">
                                        <i class="fas fa-trash mr-2"></i>Hapus Satker
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body text-center py-4" style="font-size:0.875rem;">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block" style="color:#f6c90e;"></i>
                                    <div class="font-weight-bold mb-1" style="color:#2d3748;">Yakin ingin menghapus?</div>
                                    <div class="text-muted" style="font-size:0.82rem;">
                                        <strong>{{ $satker->nama_satker }}</strong> akan dihapus secara permanen.
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center" style="gap:8px;">
                                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:6px;">
                                        <i class="fas fa-times mr-1"></i>Batal
                                    </button>
                                    <form action="{{ route('admin.satker.destroy', $satker->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm font-weight-bold btn-danger" style="border-radius:6px;">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-building fa-3x mb-3 d-block" style="color:#d1e7d8;"></i>
                            <div class="font-weight-bold text-muted mb-1">Belum ada data satker</div>
                            <small class="text-muted">Klik tombol <strong>Tambah Satker</strong> untuk menambahkan data baru</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahSatker" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);">
                <h5 class="modal-title font-weight-bold text-white" style="font-size:0.95rem;">
                    <i class="fas fa-plus-circle mr-2" style="color:#f6c90e;"></i>Tambah Satker
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.satker.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="background:#f8fff9; font-size:0.875rem;">

                    <div class="p-3 rounded mb-3" style="background:white;border:1px solid #e3e6f0;">
                        <div class="small text-muted">
                            <i class="fas fa-info-circle mr-1" style="color:#1a6b3c;"></i>
                            Satuan Kerja (Satker) adalah unit instansi yang memiliki kewenangan penggunaan anggaran.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Nama Satker <span class="text-danger">*</span></label>
                        <input type="text" name="nama_satker"
                            class="form-control"
                            style="border-radius:8px; font-size:0.875rem;"
                            placeholder="Contoh: KN Padang"
                            required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted">Alamat</label>
                        <input type="text" name="alamat"
                            class="form-control"
                            style="border-radius:8px; font-size:0.875rem;"
                            placeholder="Jl. ...">
                    </div>

                </div>
                <div class="modal-footer" style="background:#f8fff9;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:6px;">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-sm font-weight-bold"
                        style="background:#1a6b3c;color:white;border-radius:6px;padding:6px 16px;">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    LapauTable.init('tabelSatker', {
        pageSize: 10,
        sortDir: 'desc'
    });

});
</script>
@endpush