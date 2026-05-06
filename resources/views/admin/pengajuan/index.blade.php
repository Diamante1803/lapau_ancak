@extends('layouts.admin')

@section('content')

@php
    $user     = auth()->user();
    $isPusat  = $user->role === 'admin_pusat';
    $isSatker = $user->role === 'admin_satker';
    $routePrefix = $isPusat ? 'admin' : 'satker';
@endphp

<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0 font-weight-bold" style="color: #1a6b3c;">
                <i class="fas fa-file-alt mr-2" style="color: #f6c90e;"></i>
                @if($isPusat) Semua Pengajuan @else Daftar Pengajuan Saya @endif
            </h1>
            <small class="text-muted">
                @if($isPusat) Kelola seluruh pengajuan dari Admin Satker
                @else Pengajuan lelang dari satker Anda
                @endif
            </small>
        </div>

        {{-- Tombol Tambah — hanya admin_satker --}}
        @if($isSatker)
        <button class="btn btn-sm font-weight-bold shadow-sm mt-2 mt-sm-0"
            data-toggle="modal" data-target="#modalPengajuan"
            style="background: linear-gradient(135deg, #1a6b3c, #145c32); color: white; border-radius: 8px; padding: 8px 16px;">
            <i class="fas fa-plus mr-1"></i> Tambah Pengajuan
        </button>
        @endif
    </div>

    {{-- ================= ALERT ================= --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm"
        style="border-left: 4px solid #e74a3b; border-radius: 8px;">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif
    @if(session('success'))
    <div id="autoAlert" class="alert alert-success alert-dismissible fade show shadow-sm"
        style="border-left: 4px solid #1a6b3c; border-radius: 8px;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <script>
        setTimeout(function () {
            let a = document.getElementById('autoAlert');
            if (a) { a.style.transition = 'opacity 0.5s'; a.style.opacity = '0'; setTimeout(() => a.remove(), 500); }
        }, 4000);
    </script>
    @endif

    {{-- ================= TABEL ================= --}}
    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">

        <div class="card-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-list mr-2" style="color: #f6c90e;"></i>
                Data Pengajuan
            </h6>
            <span class="badge"
                style="background: rgba(255,255,255,0.15); color: white; border-radius: 20px; padding: 4px 12px;">
                {{ $pengajuans->count() }} pengajuan
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">

                    <thead style="background: #f8fff9;">
                        <tr>
                            <th class="border-0 pl-4" style="color: #1a6b3c; font-size: 0.82rem;">No</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Judul Pengajuan</th>

                            {{-- Kolom Satker — hanya admin_pusat --}}
                            @if($isPusat)
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Satker</th>
                            @endif

                            <th class="border-0 text-center" style="color: #1a6b3c; font-size: 0.82rem;">Dok. Pengajuan</th>
                            <th class="border-0 text-center" style="color: #1a6b3c; font-size: 0.82rem;">Dok. Perkara</th>
                            <th class="border-0 text-center" style="color: #1a6b3c; font-size: 0.82rem;">Foto Barang</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Tanggal</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Status</th>
                            <th class="border-0 text-center" style="color: #1a6b3c; font-size: 0.82rem;" width="160">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengajuans as $p)
                        <tr style="border-left: 3px solid transparent; transition: 0.2s;"
                            onmouseover="this.style.borderLeft='3px solid #1a6b3c'"
                            onmouseout="this.style.borderLeft='3px solid transparent'">

                            <td class="pl-4 align-middle text-muted small">{{ $loop->iteration }}</td>

                            <td class="align-middle font-weight-bold" style="color: #1a6b3c;">
                                {{ $p->judul_pengajuan }}
                            </td>

                            {{-- Kolom Satker — hanya admin_pusat --}}
                            @if($isPusat)
                            <td class="align-middle small">
                                <i class="fas fa-building mr-1 text-muted"></i>
                                {{ optional($p->satker)->nama_satker ?? '-' }}
                            </td>
                            @endif

                            {{-- Dokumen Satker --}}
                            <td class="align-middle text-center">
                                @php $jumlah = $p->dokumenPengajuan->count(); @endphp
                                @if($jumlah >= 3)
                                    <span class="badge badge-pill badge-success" style="font-size: 0.75rem;">✓ Lengkap ({{ $jumlah }})</span>
                                @elseif($jumlah > 0)
                                    <span class="badge badge-pill badge-warning" style="font-size: 0.75rem;">⚠ Sebagian ({{ $jumlah }})</span>
                                @else
                                    <span class="badge badge-pill badge-danger" style="font-size: 0.75rem;">✗ Kosong</span>
                                @endif
                            </td>

                            {{-- Dokumen Perkara --}}
                            <td class="align-middle text-center">
                                @php
                                    $jumlahPerkaraDokumen = $p->perkaras->sum(fn($pk) => $pk->dokumenPerkara->count());
                                @endphp
                                @if($jumlahPerkaraDokumen > 0)
                                    <span class="badge badge-pill badge-success" style="font-size: 0.75rem;">✓ Ada ({{ $jumlahPerkaraDokumen }})</span>
                                @else
                                    <span class="badge badge-pill" style="background: #e0e0e0; color: #999; font-size: 0.75rem;">Kosong</span>
                                @endif
                            </td>

                            {{-- Foto Barang --}}
                            <td class="align-middle text-center">
                                @if($p->perkaras->flatMap->barangs->flatMap->fotoBarang->count() > 0)
                                    <span class="badge badge-pill badge-success" style="font-size: 0.75rem;">✓ Ada</span>
                                @else
                                    <span class="badge badge-pill" style="background: #e0e0e0; color: #999; font-size: 0.75rem;">Kosong</span>
                                @endif
                            </td>

                            {{-- Tanggal --}}
                            <td class="align-middle text-muted small">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $p->tanggal_pengajuan
                                    ? \Carbon\Carbon::parse($p->tanggal_pengajuan)->format('d M Y')
                                    : '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="align-middle">
                                @if($p->status == 'draft')
                                    <span class="badge badge-pill badge-warning px-3" style="border-radius: 20px;">📝 Draft</span>
                                @elseif($p->status == 'submitted')
                                    <span class="badge badge-pill badge-info px-3" style="border-radius: 20px;">📤 Dikirim</span>
                                @elseif($p->status == 'approved')
                                    <span class="badge badge-pill badge-success px-3" style="border-radius: 20px;">✅ Disetujui</span>
                                @elseif($p->status == 'rejected')
                                    <span class="badge badge-pill badge-danger px-3" style="border-radius: 20px;">❌ Ditolak</span>
                                @elseif($p->status == 'revision')
                                    <span class="badge badge-pill badge-secondary px-3" style="border-radius: 20px;">🔄 Revisi</span>
                                @else
                                    <span class="badge badge-pill badge-secondary px-3">{{ $p->status }}</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center" style="gap: 6px;">

                                    {{-- DETAIL — semua role --}}
                                    <a href="{{ route($routePrefix . '.pengajuan.show', $p->id) }}"
                                        class="btn btn-sm"
                                        style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px; width: 34px;"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- APPROVE — hanya admin_pusat, status submitted --}}
                                    @if($isPusat && $p->status == 'submitted')
                                    <form action="{{ route('admin.pengajuan.approve', $p->id) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm"
                                            style="background: #d4edda; color: #155724; border-radius: 6px; width: 34px;"
                                            onclick="return confirm('Setujui pengajuan ini?')"
                                            title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- SUBMIT — hanya admin_satker, status draft --}}
                                    @if($isSatker && $p->status == 'draft')
                                    <form action="{{ route($routePrefix . '.pengajuan.submit', $p->id) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm"
                                            style="background: #cce5ff; color: #004085; border-radius: 6px; width: 34px;"
                                            onclick="return confirm('Kirim pengajuan ini ke Admin Pusat?')"
                                            title="Submit ke Pusat">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- DELETE — hanya admin_satker, status draft --}}
                                    @if($isSatker && $p->status == 'draft')
                                    <form action="{{ route($routePrefix . '.pengajuan.destroy', $p->id) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm"
                                            style="background: #fde8e8; color: #e74a3b; border-radius: 6px; width: 34px;"
                                            onclick="return confirm('Hapus pengajuan ini?')"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isPusat ? 9 : 8 }}" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block" style="color: #d1e7d8;"></i>
                                Belum ada data pengajuan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>

{{-- ================= MODAL TAMBAH PENGAJUAN — hanya admin_satker ================= --}}
@if($isSatker)
<div class="modal fade" id="modalPengajuan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">

            <form method="POST" action="{{ route('satker.pengajuan.store') }}">
                @csrf

                <div class="modal-header"
                    style="background: linear-gradient(90deg, #1a6b3c, #145c32);">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="fas fa-plus-circle mr-2" style="color: #f6c90e;"></i>Tambah Pengajuan Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="background: #f8fff9;">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted">Judul Pengajuan</label>
                        <input type="text" name="judul_pengajuan"
                            class="form-control"
                            placeholder="Masukkan judul pengajuan..."
                            style="border-radius: 8px;" required>
                        <small class="text-muted">Contoh: Pengajuan Lelang Aset Sitaan Perkara No. 001/2026</small>
                    </div>
                </div>

                <div class="modal-footer" style="background: #f8fff9;">
                    <button class="btn btn-sm btn-secondary" data-dismiss="modal"
                        style="border-radius: 6px;">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-sm font-weight-bold"
                        style="background: #1a6b3c; color: white; border-radius: 6px; padding: 6px 16px;">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endif

@endsection