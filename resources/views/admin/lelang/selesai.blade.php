@extends('layouts.admin')

@section('content')

@php
    $isPusat  = auth()->user()->role === 'admin_pusat';
    $isSatker = auth()->user()->role === 'admin_satker';
@endphp

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0 font-weight-bold" style="color: #1a6b3c;">
                <i class="fas fa-check-circle mr-2" style="color: #f6c90e;"></i>
                Lelang Selesai
            </h1>
            <small class="text-muted">
                @if($isPusat) Seluruh lelang yang telah selesai
                @else Lelang selesai dari satker Anda
                @endif
            </small>
        </div>
        <span class="badge px-3 py-2 mt-2 mt-sm-0"
            style="background: #1a6b3c; color: white; border-radius: 20px; font-size: 0.8rem;">
            {{ $lelangs->count() }} lelang selesai
        </span>
    </div>

    {{-- TABEL --}}
    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">

        <div class="card-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-list mr-2" style="color: #f6c90e;"></i>Data Lelang Selesai
            </h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabelLelangSelesai" class="table table-hover mb-0">
                    <thead style="background: #f8fff9;">
                        <tr>
                            <th class="border-0 pl-4" style="color: #1a6b3c; font-size: 0.82rem;">No</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Barang</th>
                            @if($isPusat)
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Satker</th>
                            @endif
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Harga Awal</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Harga Final</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Pemenang</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Tanggal Selesai</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Status Barang</th>
                            <th class="border-0 text-center" style="color: #1a6b3c; font-size: 0.82rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lelangs as $i => $lelang)
                        @php 
                            $barang = $lelang->barang; 
                            $uniqueBiddersCount = $lelang->penawarans->unique('pembeli_id')->count();
                        @endphp
                        <tr style="border-left: 3px solid transparent; transition: 0.2s;"
                            onmouseover="this.style.borderLeft='3px solid #1a6b3c'"
                            onmouseout="this.style.borderLeft='3px solid transparent'">

                            <td class="pl-4 align-middle text-muted small">{{ $i + 1 }}</td>

                            {{-- Barang --}}
                            <td class="align-middle">
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    @if($barang->fotoBarang->count() > 0)
                                    <img src="{{ asset('storage/' . $barang->fotoBarang->first()->file_path) }}"
                                        style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 2px solid #e3e6f0;">
                                    @else
                                    <div style="width: 44px; height: 44px; background: #f0faf4; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold small">{{ $barang->nama_barang }}</div>
                                        <small class="text-muted">{{ Str::limit($barang->deskripsi, 30) ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Satker — hanya admin pusat --}}
                            @if($isPusat)
                            <td class="align-middle small">
                                <i class="fas fa-building mr-1 text-muted"></i>
                                {{ optional($barang->perkara->pengajuan->satker)->nama_satker ?? '-' }}
                            </td>
                            @endif

                            {{-- Harga Awal --}}
                            <td class="align-middle small">
                                Rp {{ number_format($lelang->harga_awal, 0, ',', '.') }}
                            </td>

                            {{-- Harga Final --}}
                            <td class="align-middle">
                                <span class="font-weight-bold" style="color: #1a6b3c;">
                                    Rp {{ number_format($lelang->harga_tertinggi ?? $lelang->harga_awal, 0, ',', '.') }}
                                </span>
                                @php
                                    $selisih = ($lelang->harga_tertinggi ?? $lelang->harga_awal) - $lelang->harga_awal;
                                @endphp
                                @if($selisih > 0)
                                <small class="d-block text-success">
                                    +Rp {{ number_format($selisih, 0, ',', '.') }}
                                </small>
                                @endif
                            </td>

                            {{-- Pemenang --}}
                            <td class="align-middle">
                                @if($lelang->pemenang)
                                    <div class="font-weight-bold small">
                                        {{ $lelang->pemenang->nama }}
                                    </div>
                                    <small class="text-muted d-block">
                                        {{ $lelang->pemenang->no_hp }}
                                    </small>
                                    @if(($lelang->pemenang_urutan ?? 1) > 1)
                                    <span class="badge badge-warning mt-1" style="font-size: 0.65rem; border-radius: 20px;">
                                        Pemenang ke-{{ $lelang->pemenang_urutan }}
                                    </span>
                                    @endif
                                    @if($lelang->catatan_pemenang)
                                    <small class="text-muted d-block" style="font-size: 0.7rem; font-style: italic;">
                                        {{ $lelang->catatan_pemenang }}
                                    </small>
                                    @endif
                                @else
                                    <span class="badge badge-secondary" style="border-radius: 20px;">
                                        Tidak ada penawar
                                    </span>
                                @endif
                            </td>

                            {{-- Tanggal Selesai --}}
                            <td class="align-middle text-muted small">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $lelang->tanggal_selesai->format('d M Y, H:i') }}
                            </td>

                            {{-- Status Barang --}}
                            <td class="align-middle">
                                @if($barang->status === 'sold')
                                    <span class="badge badge-success px-3 py-1" style="border-radius: 20px;">
                                        ✅ Terjual
                                    </span>

                                @elseif($barang->status === 'unsold')
                                    <div>
                                        <span class="badge badge-danger px-3 py-1 d-block mb-1" style="border-radius: 20px;">
                                            ❌ Tidak Terjual
                                        </span>

                                        {{-- Tombol ajukan ulang — hanya admin satker --}}
                                        @if($isSatker)
                                        <form action="{{ route('satker.lelang.ulang', $lelang->id) }}" method="POST"
                                            id="formLelangUlang-{{ $lelang->id }}">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-block font-weight-bold mt-1"
                                                style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;font-size:0.78rem;"
                                                onclick="swalSubmitForm('formLelangUlang-{{ $lelang->id }}', {
                                                    title: 'Ajukan Lelang Ulang?',
                                                    text: 'Barang &quot;{{ addslashes($barang->nama_barang) }}&quot; akan diajukan kembali untuk lelang ulang.',
                                                    icon: 'question',
                                                    confirmText: 'Ya, Ajukan Ulang',
                                                    confirmColor: '#1a6b3c'
                                                })">
                                                <i class="fas fa-redo mr-1"></i>Ajukan Lelang Ulang
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center" style="gap: 5px;">
                                    {{-- Tombol Riwayat (Pusat & Satker) --}}
                                    <button class="btn btn-sm"
                                        style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px;"
                                        data-toggle="modal"
                                        data-target="#modalRiwayat-{{ $lelang->id }}"
                                        title="Riwayat Penawaran">
                                        <i class="fas fa-history mr-1"></i>Riwayat
                                    </button>

                                    {{-- Tombol ganti pemenang: Hanya jika Admin Satker DAN penawar unik > 1 --}}
                                    @if($isSatker && $lelang->penawarans->unique('pembeli_id')->count() > 1)
                                <button class="btn btn-sm"
                                    style="background: #fff3cd; color: #856404; border-radius: 6px;"
                                    data-toggle="modal"
                                    data-target="#modalGantiPemenang-{{ $lelang->id }}"
                                    title="Ganti Pemenang">
                                    <i class="fas fa-exchange-alt mr-1"></i>Ganti Pemenang
                                </button>
                                @endif
                                </div>
                            </td>

                        </tr>                        
                        @empty
                        <tr>
                            <td colspan="{{ $isPusat ? 9 : 8 }}" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-3x mb-3 d-block" style="color: #d1e7d8;"></i>
                                Belum ada lelang yang selesai
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Render Modals di luar tabel untuk validasi HTML yang lebih baik --}}
    @foreach($lelangs as $lelang)
        @php 
            $barang = $lelang->barang; 
            $uniqueBiddersCount = $lelang->penawarans->unique('pembeli_id')->count();
        @endphp
        
        {{-- MODAL RIWAYAT PENAWARAN --}}
        <div class="modal fade" id="modalRiwayat-{{ $lelang->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                    <div class="modal-header" style="background: linear-gradient(90deg, #1a6b3c, #145c32);">
                        <h5 class="modal-title font-weight-bold text-white">
                            <i class="fas fa-history mr-2" style="color: #f6c90e;"></i>
                            Riwayat Penawaran — {{ $barang->nama_barang }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="tabelRiwayat-{{ $lelang->id }}" class="table table-sm table-hover">
                                <thead style="background: #f8fff9;">
                                    <tr class="small text-muted font-weight-bold">
                                        <th>WAKTU</th>
                                        <th>PENAWAR</th>
                                        <th class="text-right">NILAI PENAWARAN</th>
                                        <th class="text-center">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lelang->penawarans->sortByDesc('nilai_penawaran') as $idx => $penawaran)
                                    <tr>
                                        <td class="align-middle small">{{ $penawaran->created_at->format('d M Y, H:i') }}</td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold small">{{ $penawaran->pembeli->nama }}</div>
                                            <small class="text-muted">{{ $penawaran->pembeli->email }}</small>
                                        </td>
                                        <td class="align-middle text-right font-weight-bold" style="color: #1a6b3c;">
                                            Rp {{ number_format($penawaran->nilai_penawaran, 0, ',', '.') }}
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($penawaran->pembeli_id === $lelang->pemenang_id)
                                                <span class="badge badge-success" style="font-size: 0.65rem;">Pemenang</span>
                                            @else
                                                <span class="badge badge-light border" style="font-size: 0.65rem;">Bid</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL GANTI PEMENANG --}}
        @if($isSatker && $uniqueBiddersCount > 1)
        <div class="modal fade" id="modalGantiPemenang-{{ $lelang->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                    <div class="modal-header" style="background: linear-gradient(90deg, #856404, #a07800);">
                        <h5 class="modal-title font-weight-bold text-white">
                            <i class="fas fa-exchange-alt mr-2" style="color: #f6c90e;"></i>
                            Ganti Pemenang — {{ $barang->nama_barang }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form method="POST" action="{{ route('satker.lelang.ganti-pemenang', $lelang->id) }}">
                        @csrf
                        <div class="modal-body" style="background: #fffdf0;">
                            <label class="small font-weight-bold text-muted mb-2 d-block">Pilih Pemenang Baru:</label>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-hover mb-0">
                                    <thead style="background: #f5e6a3;">
                                        <tr class="small">
                                            <th>Pilih</th>
                                            <th>Nama</th>
                                            <th>Nilai Penawaran</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lelang->penawarans->sortByDesc('nilai_penawaran')->unique('pembeli_id') as $idx => $penawaran)
                                        <tr>
                                            <td>
                                                <input type="radio" name="pembeli_id" value="{{ $penawaran->pembeli_id }}"
                                                    data-rank="{{ $loop->iteration }}" class="radio-pilih-pemenang"
                                                    {{ $penawaran->pembeli_id === $lelang->pemenang_id ? 'disabled' : '' }} required>
                                            </td>
                                            <td class="small font-weight-bold">{{ $penawaran->pembeli->nama }}</td>
                                            <td class="small font-weight-bold text-success">Rp {{ number_format($penawaran->nilai_penawaran, 0, ',', '.') }}</td>
                                            <td class="small text-muted">{{ $penawaran->created_at->format('d/m/y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-muted label-alasan">Alasan Penggantian <span class="text-danger">*</span></label>
                                <textarea name="catatan_pemenang" rows="2" class="form-control textarea-alasan" style="font-size: 0.85rem;" placeholder="Wajib diisi..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer" style="background: #fffdf0;">
                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm font-weight-bold" style="background: #856404; color: white;">Proses Ganti</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    LapauTable.init('tabelLelangSelesai', {
        searchable: false,
        pageSize: 10,
        sortDir: 'desc'
    });

    // Inisialisasi tabel riwayat di dalam modal saat modal dibuka (untuk pagination 10 per halaman)
    $('.modal').on('shown.bs.modal', function() {
        const $table = $(this).find('table[id^="tabelRiwayat-"]');
        const tableId = $table.attr('id');
        
        // Cek apakah tabel sudah pernah diinisialisasi sebelumnya
        if (tableId && !$table.data('initialized')) {
            LapauTable.init(tableId, { pageSize: 10, searchable: false });
            $table.data('initialized', true); // Tandai sebagai sudah diinisialisasi
        }
    });

    // Logika dinamis untuk kolom alasan penggantian pemenang
    $(document).on('change', '.radio-pilih-pemenang', function() {
        const rank = $(this).data('rank');
        const $modal = $(this).closest('.modal');
        const $textarea = $modal.find('.textarea-alasan');
        const $label = $modal.find('.label-alasan');

        if (rank == 1) {
            $textarea.removeAttr('required').attr('placeholder', 'Opsional untuk pemenang utama...');
            $label.html('Alasan Penggantian <span class="font-weight-normal text-muted">(Opsional)</span>');
        } else {
            $textarea.attr('required', 'required').attr('placeholder', 'Wajib diisi...');
            $label.html('Alasan Penggantian <span class="text-danger">*</span>');
        }
    });
});
</script>
@endpush