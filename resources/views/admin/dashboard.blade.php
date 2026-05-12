@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- ================= PAGE HEADING ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 font-weight-bold" style="color: #1a6b3c;">
                <i class="fas fa-tachometer-alt mr-2" style="color: #f6c90e;"></i>
                Dashboard
            </h1>
            <small class="text-muted">
                Selamat datang, <strong>{{ auth()->user()->name }}</strong> —
                {{ auth()->user()->role == 'admin_pusat' ? '⚙️ Admin Pusat' : '🏢 ' . (optional(auth()->user()->satker)->nama_satker ?? 'Admin Satker') }}
            </small>
        </div>

        <!-- <a href="#" class="btn btn-sm shadow-sm mt-2 mt-sm-0"
            style="background:#1a6b3c;color:white;border-radius:8px;">
            <i class="fas fa-download fa-sm mr-1"></i> Generate Report
        </a> -->
    </div>

    {{-- ================= STAT CARDS ================= --}}
    @php
        $isPusat  = auth()->user()->role === 'admin_pusat';
        $isSatker = auth()->user()->role === 'admin_satker';
    @endphp

    <div class="row">

        {{-- Total Pengajuan --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2"
                style="border-left: 4px solid #1a6b3c; border-radius: 10px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1"
                                style="color: #1a6b3c;">
                                Total Pengajuan
                                @if($isSatker)
                                <span class="d-block font-weight-normal text-muted" style="text-transform:none; font-size:0.7rem;">
                                    Satker Anda
                                </span>
                                @else
                                <span class="d-block font-weight-normal text-muted" style="text-transform:none; font-size:0.7rem;">
                                    Semua Satker
                                </span>
                                @endif
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_pengajuan'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div style="width:48px;height:48px;border-radius:50%;background:rgba(26,107,60,0.1);display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-file-alt fa-lg" style="color:#1a6b3c;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lelang Aktif --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2"
                style="border-left: 4px solid #28a745; border-radius: 10px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1"
                                style="color: #28a745;">
                                Lelang Aktif
                                <span class="d-block font-weight-normal text-muted" style="text-transform:none; font-size:0.7rem;">
                                    Sedang berlangsung
                                </span>
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800 d-flex align-items-center" style="gap:6px;">
                                {{ $stats['lelang_aktif'] }}
                                @if($stats['lelang_aktif'] > 0)
                                <span style="width:8px;height:8px;border-radius:50%;background:#28a745;display:inline-block;animation:ping 1s infinite;"></span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <div style="width:48px;height:48px;border-radius:50%;background:rgba(40,167,69,0.1);display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-gavel fa-lg" style="color:#28a745;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barang Terjual --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2"
                style="border-left: 4px solid #36b9cc; border-radius: 10px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1"
                                style="color: #36b9cc;">
                                Barang Terjual
                                <span class="d-block font-weight-normal text-muted" style="text-transform:none; font-size:0.7rem;">
                                    Lelang selesai + ada pemenang
                                </span>
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['barang_terjual'] }}
                                <span class="text-muted small font-weight-normal">barang</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div style="width:48px;height:48px;border-radius:50%;background:rgba(54,185,204,0.1);display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-box-open fa-lg" style="color:#36b9cc;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Nilai Penjualan --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2"
                style="border-left: 4px solid #e74a3b; border-radius: 10px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1"
                                style="color: #e74a3b;">
                                Total Nilai Penjualan
                                <span class="d-block font-weight-normal text-muted" style="text-transform:none; font-size:0.7rem;">
                                    Sudah upload bukti pembayaran
                                </span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div style="width:48px;height:48px;border-radius:50%;background:rgba(231,74,59,0.1);display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-money-bill-wave fa-lg" style="color:#e74a3b;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
    @keyframes ping {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.3); }
    }
    </style>

    {{-- ================= TABEL PENGAJUAN TERBARU ================= --}}
    <div class="card shadow mb-4" style="border-radius: 12px; border: none;">

        <div class="card-header py-3 d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #1a6b3c, #145c32); border-radius: 12px 12px 0 0;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-list mr-2" style="color: #f6c90e;"></i>Pengajuan Terbaru
            </h6>
            <a href="{{ auth()->user()->role == 'admin_pusat' ? route('admin.pengajuan.index') : route('satker.pengajuan.index') }}"
                class="btn btn-sm"
                style="background: #f6c90e; color: #1a6b3c; font-weight: bold; border-radius: 6px;">
                <i class="fas fa-list fa-sm mr-1"></i> Lihat Semua
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabelDashboard" class="table table-hover mb-0" width="100%" cellspacing="0">

                    <thead style="background: #f8fff9;">
                        <tr>
                            @if(auth()->user()->role == 'admin_pusat')
                                <th class="border-0 pl-4" style="color: #1a6b3c;">No</th>
                                <th class="border-0" style="color: #1a6b3c;">Satker</th>
                                <th class="border-0" style="color: #1a6b3c;">Judul Pengajuan</th>
                                <th class="border-0" style="color: #1a6b3c;">Tanggal</th>
                                <th class="border-0" style="color: #1a6b3c;">Status Pengajuan</th>
                                <th class="border-0" style="color: #1a6b3c;">Status Lelang</th>
                                <th class="border-0" style="color: #1a6b3c;">Aksi</th>
                            @else
                                <th class="border-0 pl-4" style="color: #1a6b3c;">Judul Pengajuan</th>
                                <th class="border-0" style="color: #1a6b3c;">Tanggal</th>
                                <th class="border-0" style="color: #1a6b3c;">Status Pengajuan</th>
                                <th class="border-0" style="color: #1a6b3c;">Status Lelang</th>
                                <th class="border-0" style="color: #1a6b3c;">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengajuans as $key => $item)

                        @php
                            // Hitung status lelang dari semua barang dalam pengajuan
                            $semuaBarang = $item->perkaras->flatMap->barangs;
                            $totalBarang = $semuaBarang->count();

                            $jumlahSold      = $semuaBarang->filter(fn($b) => $b->status === 'sold')->count();
                            $jumlahAuction   = $semuaBarang->filter(fn($b) => $b->status === 'in_auction')->count();
                            $jumlahScheduled = $semuaBarang->filter(fn($b) => 
                                $b->lelang && $b->lelang->status === 'scheduled')->count();
                            $jumlahAvailable = $semuaBarang->filter(fn($b) => $b->status === 'available')->count();
                            $jumlahUnsold    = $semuaBarang->filter(fn($b) => $b->status === 'unsold')->count();

                            // Tentukan status dominan
                            if ($totalBarang === 0) {
                                $statusLelang = 'no_barang';
                            } elseif ($jumlahSold === $totalBarang) {
                                $statusLelang = 'all_sold';
                            } elseif ($jumlahAuction > 0) {
                                $statusLelang = 'active';
                            } elseif ($jumlahScheduled > 0) {
                                $statusLelang = 'scheduled';
                            } elseif ($jumlahSold > 0 && $jumlahSold < $totalBarang) {
                                $statusLelang = 'partial_sold';
                            } elseif ($jumlahUnsold > 0) {
                                $statusLelang = 'unsold';
                            } else {
                                $statusLelang = 'available';
                            }
                        @endphp

                        <tr style="border-left: 3px solid transparent; transition: 0.2s;"
                            onmouseover="this.style.borderLeft='3px solid #1a6b3c'"
                            onmouseout="this.style.borderLeft='3px solid transparent'">

                            @if(auth()->user()->role == 'admin_pusat')
                                <td class="pl-4 align-middle">{{ $key + 1 }}</td>
                                <td class="align-middle small">
                                    <i class="fas fa-building mr-1 text-muted"></i>
                                    {{ $item->satker->nama_satker ?? '-' }}
                                </td>
                                <td class="align-middle font-weight-bold">{{ $item->judul_pengajuan }}</td>
                                <td class="align-middle text-muted small">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $item->created_at->format('d M Y') }}
                                </td>
                            @else
                                <td class="pl-4 align-middle font-weight-bold">{{ $item->judul_pengajuan }}</td>
                                <td class="align-middle text-muted small">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $item->created_at->format('d M Y') }}
                                </td>
                            @endif

                            {{-- Status Pengajuan --}}
                            <td class="align-middle">
                                @if($item->status == 'draft')
                                    <span class="badge badge-warning px-2 py-1" style="border-radius:20px;">📝 Draft</span>
                                @elseif($item->status == 'submitted')
                                    <span class="badge badge-info px-2 py-1" style="border-radius:20px;">📤 Dikirim</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge badge-success px-2 py-1" style="border-radius:20px;">✅ Disetujui</span>
                                @elseif($item->status == 'rejected')
                                    <span class="badge badge-danger px-2 py-1" style="border-radius:20px;">❌ Ditolak</span>
                                @elseif($item->status == 'revision')
                                    <span class="badge badge-secondary px-2 py-1" style="border-radius:20px;">🔄 Revisi</span>
                                @endif
                            </td>

                            {{-- Status Lelang --}}
                            <td class="align-middle">
                                @if($statusLelang === 'no_barang')
                                    <span class="badge px-2 py-1" style="background:#e9ecef;color:#6c757d;border-radius:20px;">
                                        — Belum ada barang
                                    </span>

                                @elseif($statusLelang === 'available')
                                    <span class="badge px-2 py-1" style="background:#e8f5ee;color:#1a6b3c;border-radius:20px;">
                                        🟢 Tersedia ({{ $totalBarang }})
                                    </span>

                                @elseif($statusLelang === 'scheduled')
                                    <span class="badge badge-info px-2 py-1" style="border-radius:20px;">
                                        📅 Terjadwal ({{ $jumlahScheduled }}/{{ $totalBarang }})
                                    </span>

                                @elseif($statusLelang === 'active')
                                    <span class="badge px-2 py-1" style="background:#28a745;color:white;border-radius:20px;">
                                        🔴 Live ({{ $jumlahAuction }}/{{ $totalBarang }})
                                    </span>

                                @elseif($statusLelang === 'partial_sold')
                                    <span class="badge px-2 py-1" style="background:#17a2b8;color:white;border-radius:20px;">
                                        📦 Sebagian Terjual ({{ $jumlahSold }}/{{ $totalBarang }})
                                    </span>

                                @elseif($statusLelang === 'all_sold')
                                    <span class="badge px-2 py-1" style="background:#6f42c1;color:white;border-radius:20px;">
                                        ✅ Semua Terjual ({{ $totalBarang }})
                                    </span>

                                @elseif($statusLelang === 'unsold')
                                    <span class="badge badge-secondary px-2 py-1" style="border-radius:20px;">
                                        ❌ Tidak Terjual
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="align-middle">
                                <a href="{{ auth()->user()->role === 'admin_pusat' 
                                    ? route('admin.pengajuan.show', $item->id) 
                                    : route('satker.pengajuan.step4', $item->id) }}"
                                    class="btn btn-sm"
                                    style="background: #1a6b3c; color: white; border-radius: 6px;">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
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

@endsection
@push('scripts')
    
<script>
    document.addEventListener('DOMContentLoaded', function () {
    LapauTable.init('tabelDashboard', {
        searchable: false,
        pageSize:  10,
        sortDir:   'desc',
    });
});
</script>
@endpush