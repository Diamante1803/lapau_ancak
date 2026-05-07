@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0 font-weight-bold" style="color: #1a6b3c;">
                <i class="fas fa-gavel mr-2" style="color: #f6c90e;"></i>
                Dashboard Lelang
            </h1>
            <small class="text-muted">Pengajuan yang telah disetujui — siap dijadwalkan lelang</small>
        </div>
    </div>

    {{-- ALERT --}}
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm"
        style="border-left: 4px solid #e74a3b; border-radius: 8px;">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    {{-- LOOP PENGAJUAN --}}
    @forelse($pengajuans as $pengajuan)

    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">

        {{-- HEADER PENGAJUAN --}}
        <div class="card-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
            <div>
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-folder-open mr-2" style="color: #f6c90e;"></i>
                    {{ $pengajuan->judul_pengajuan }}
                </h6>
                <small style="color: rgba(255,255,255,0.7);">
                    <i class="fas fa-building mr-1"></i>{{ optional($pengajuan->satker)->nama_satker ?? '-' }}
                    &nbsp;·&nbsp;
                    <i class="fas fa-calendar mr-1"></i>Disetujui {{ $pengajuan->updated_at->format('d M Y') }}
                    &nbsp;·&nbsp;
                    <i class="fas fa-box mr-1"></i>
                    {{ $pengajuan->perkaras->flatMap->barangs->count() }} barang
                </small>
            </div>

            <div class="d-flex align-items-center" style="gap: 8px;">

                {{-- Cek status lelang pengajuan ini --}}
                @php
                    $semuaBarang = $pengajuan->perkaras->flatMap->barangs;
                    $lelangAktif = $semuaBarang->filter(fn($b) => $b->lelang && in_array($b->lelang->status, ['scheduled','active']))->first();
                    $lelangPertama = $semuaBarang->first()?->lelang;
                @endphp

                @if($lelangAktif && $lelangAktif->lelang->status == 'scheduled')

                    {{-- Info jadwal --}}
                    <div class="text-right mr-2">
                        <small style="color: rgba(255,255,255,0.8);">
                            <i class="fas fa-calendar-check mr-1"></i>
                            {{ \Carbon\Carbon::parse($lelangAktif->lelang->tanggal_mulai)->format('d M Y H:i') }}
                            →
                            {{ \Carbon\Carbon::parse($lelangAktif->lelang->tanggal_selesai)->format('d M Y H:i') }}
                        </small>
                    </div>

                    {{-- Badge terjadwal --}}
                    <span class="badge px-3 py-2"
                        style="background: #17a2b8; color: white; border-radius: 20px; font-size: 0.75rem;">
                        <i class="fas fa-calendar mr-1"></i>Terjadwal
                    </span>

                    {{-- Tombol Batal --}}
                    <form action="{{ route('admin.lelang.batal', $pengajuan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm font-weight-bold"
                            style="background: #fde8e8; color: #e74a3b; border-radius: 8px; padding: 6px 14px;"
                            onclick="return confirm('Batalkan semua jadwal lelang pada pengajuan ini?')">
                            <i class="fas fa-times mr-1"></i>Batalkan
                        </button>
                    </form>

                @elseif($lelangAktif && $lelangAktif->lelang->status == 'active')
                    <span class="badge px-3 py-2"
                        style="background: #28a745; color: white; border-radius: 20px; font-size: 0.75rem;">
                        <i class="fas fa-fire mr-1"></i>Sedang Berlangsung
                    </span>

                @elseif($semuaBarang->every(fn($b) => $b->lelang && $b->lelang->status == 'closed'))
                    <span class="badge px-3 py-2"
                        style="background: rgba(255,255,255,0.2); color: white; border-radius: 20px; font-size: 0.75rem;">
                        <i class="fas fa-check mr-1"></i>Selesai
                    </span>

                @else
                    {{-- Belum dijadwalkan --}}
                    <span class="badge px-3 py-2"
                        style="background: #f6c90e; color: #1a6b3c; border-radius: 20px; font-size: 0.75rem;">
                        ✅ Approved
                    </span>

                    <button class="btn btn-sm font-weight-bold"
                        style="background: #f6c90e; color: #1a6b3c; border-radius: 8px; padding: 6px 14px;"
                        data-toggle="modal"
                        data-target="#modalJadwal-{{ $pengajuan->id }}">
                        <i class="fas fa-gavel mr-1"></i>Jadwalkan Lelang
                    </button>
                @endif

            </div>
        </div>
        {{-- MODAL JADWALKAN LELANG — per pengajuan --}}
        <div class="modal fade" id="modalJadwal-{{ $pengajuan->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">

                    <div class="modal-header"
                        style="background: linear-gradient(90deg, #1a6b3c, #145c32);">
                        <h5 class="modal-title font-weight-bold text-white">
                            <i class="fas fa-gavel mr-2" style="color: #f6c90e;"></i>
                            Jadwalkan Lelang
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form action="{{ route('admin.lelang.jadwalkan', $pengajuan->id) }}" method="POST">
                        @csrf

                        <div class="modal-body" style="background: #f8fff9;">

                            {{-- Ringkasan pengajuan --}}
                            <div class="mb-3 p-3 rounded" style="background: white; border: 1px solid #e3e6f0;">
                                <div class="font-weight-bold" style="color: #2d3748;">
                                    {{ $pengajuan->judul_pengajuan }}
                                </div>
                                <small class="text-muted">
                                    {{ optional($pengajuan->satker)->nama_satker }}
                                </small>
                                <div class="mt-2 d-flex" style="gap: 12px;">
                                    <small style="color: #1a6b3c;">
                                        <i class="fas fa-balance-scale mr-1"></i>
                                        {{ $pengajuan->perkaras->count() }} perkara
                                    </small>
                                    <small style="color: #1a6b3c;">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $pengajuan->perkaras->flatMap->barangs->count() }} barang
                                    </small>
                                </div>
                            </div>

                            <div class="alert alert-warning py-2" style="border-radius: 8px; font-size: 0.82rem;">
                                <i class="fas fa-info-circle mr-1"></i>
                                Semua <strong>{{ $pengajuan->perkaras->flatMap->barangs->count() }} barang</strong>
                                dalam pengajuan ini akan dijadwalkan dengan waktu yang sama.
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Tanggal & Waktu Mulai</label>
                                <input type="datetime-local" name="tanggal_mulai"
                                    class="form-control"
                                    style="border-radius: 8px;"
                                    min="{{ now()->format('Y-m-d\TH:i') }}"
                                    required>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-muted">Tanggal & Waktu Selesai</label>
                                <input type="datetime-local" name="tanggal_selesai"
                                    class="form-control"
                                    style="border-radius: 8px;"
                                    min="{{ now()->format('Y-m-d\TH:i') }}"
                                    required>
                            </div>

                        </div>

                        <div class="modal-footer" style="background: #f8fff9;">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-dismiss="modal" style="border-radius: 6px;">
                                <i class="fas fa-times mr-1"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-sm font-weight-bold"
                                style="background: #1a6b3c; color: white; border-radius: 6px; padding: 6px 16px;">
                                <i class="fas fa-calendar-check mr-1"></i>Jadwalkan Semua Barang
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="card-body">

            {{-- LOOP PERKARA --}}
            @foreach($pengajuan->perkaras as $perkara)

            <div class="mb-4">
                <h6 class="font-weight-bold mb-3" style="color: #c0392b; border-bottom: 2px solid #f5c6cb; padding-bottom: 6px;">
                    <i class="fas fa-balance-scale mr-2"></i>
                    Perkara {{ $perkara->nomor_perkara }} — {{ $perkara->nama_tersangka }}
                </h6>

                {{-- GRID CARD BARANG --}}
                <div class="row">
                    @forelse($perkara->barangs as $barang)

                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm"
                            style="border-radius: 10px; border: 1px solid #e3e6f0; overflow: hidden; transition: 0.2s;"
                            onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.12)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">

                            {{-- FOTO BARANG --}}
                            <div style="position: relative; width: 100%; height: 180px; background: #f8f9fa; 
                            overflow: hidden;">

                                @if($barang->fotoBarang->count() > 0)

                                    {{-- SLIDES --}}
                                    @foreach($barang->fotoBarang as $fIndex => $foto)
                                    <div class="slide-{{ $barang->id }}"
                                        style="display: {{ $fIndex == 0 ? 'block' : 'none' }}; width: 100%; height: 180px;">
                                        <img src="{{ asset('storage/' . $foto->file_path) }}"
                                            style="width: 100%; height: 180px; object-fit: cover; cursor: pointer;"
                                            onclick="previewDokumen('{{ asset('storage/' . $foto->file_path) }}', '{{ $barang->nama_barang }}')">
                                    </div>
                                    @endforeach

                                    {{-- TOMBOL PANAH — hanya jika foto > 1 --}}
                                    @if($barang->fotoBarang->count() > 1)

                                    {{-- Panah Kiri --}}
                                    <button onclick="slideBarang({{ $barang->id }}, -1, {{ $barang->fotoBarang->count() }})"
                                        style="
                                            position: absolute; left: 6px; top: 50%; transform: translateY(-50%);
                                            background: rgba(0,0,0,0.45); color: white; border: none;
                                            width: 28px; height: 28px; border-radius: 50%;
                                            font-size: 12px; cursor: pointer; z-index: 10;
                                            display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    {{-- Panah Kanan --}}
                                    <button onclick="slideBarang({{ $barang->id }}, 1, {{ $barang->fotoBarang->count() }})"
                                        style="
                                            position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
                                            background: rgba(0,0,0,0.45); color: white; border: none;
                                            width: 28px; height: 28px; border-radius: 50%;
                                            font-size: 12px; cursor: pointer; z-index: 10;
                                            display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>

                                    {{-- Indikator Dots --}}
                                    <div style="position: absolute; bottom: 6px; width: 100%; text-align: center; z-index: 10;">
                                        @foreach($barang->fotoBarang as $dIndex => $dot)
                                        <span class="dot-{{ $barang->id }}"
                                            style="
                                                display: inline-block; width: 7px; height: 7px; border-radius: 50%;
                                                background: {{ $dIndex == 0 ? 'white' : 'rgba(255,255,255,0.45)' }};
                                                margin: 0 2px; cursor: pointer; transition: 0.2s;"
                                            onclick="goToSlide({{ $barang->id }}, {{ $dIndex }}, {{ $barang->fotoBarang->count() }})">
                                        </span>
                                        @endforeach
                                    </div>

                                    @endif

                                @else
                                    <div style="width: 100%; height: 180px; display: flex; align-items: center; justify-content: center;">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-2x mb-1 d-block" style="color: #d1d3e2;"></i>
                                            <small>Belum ada foto</small>
                                        </div>
                                    </div>
                                @endif

                            </div>

                            {{-- INFO BARANG --}}
                            <div class="card-body p-3">

                                <h6 class="font-weight-bold mb-1" style="font-size: 0.9rem; color: #2d3748;">
                                    {{ $barang->nama_barang }}
                                </h6>

                                <small class="text-muted d-block mb-2">
                                    {{ Str::limit($barang->deskripsi, 50) ?? '-' }}
                                </small>

                                <div class="font-weight-bold mb-3" style="color: #1a6b3c; font-size: 0.95rem;">
                                    Rp {{ number_format($barang->harga_awal, 0, ',', '.') }}
                                </div>

                                {{-- STATUS LELANG --}}
                                @php $lelang = $barang->lelang; @endphp

                                @if($lelang && $lelang->status == 'scheduled')
                                    <span class="badge badge-info d-block py-1" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fas fa-calendar mr-1"></i>Terjadwal
                                        <small class="d-block mt-1" style="font-weight: normal;">
                                            {{ \Carbon\Carbon::parse($lelang->tanggal_mulai)->format('d M Y, H:i') }} WIB
                                        </small>
                                    </span>

                                @elseif($lelang && $lelang->status == 'active')
                                    <span class="badge badge-success d-block py-1" style="border-radius: 6px;">
                                        <i class="fas fa-fire mr-1"></i>Sedang Berlangsung
                                    </span>

                                @elseif($lelang && $lelang->status == 'closed')
                                    <span class="badge badge-secondary d-block py-1" style="border-radius: 6px;">
                                        <i class="fas fa-check mr-1"></i>Selesai
                                    </span>

                                @elseif($lelang && $lelang->status == 'cancelled')
                                    <span class="badge badge-danger d-block py-1" style="border-radius: 6px;">
                                        <i class="fas fa-times mr-1"></i>Dibatalkan
                                    </span>

                                @else
                                    <span class="badge d-block py-1"
                                        style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px;">
                                        <i class="fas fa-clock mr-1"></i>Menunggu Jadwal
                                    </span>
                                @endif

                            </div>

                            {{-- JUMLAH FOTO (badge) --}}
                            @if($barang->fotoBarang->count() > 1)
                            <div style="position: absolute; top: 8px; right: 8px;">
                                <span class="badge"
                                    style="background: rgba(0,0,0,0.55); color: white; border-radius: 20px; font-size: 0.7rem; padding: 3px 8px;">
                                    <i class="fas fa-images mr-1"></i>{{ $barang->fotoBarang->count() }} foto
                                </span>
                            </div>
                            @endif

                        </div>
                    </div>
                    
                    @empty
                    <div class="col-12">
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-box-open fa-2x mb-2 d-block" style="color: #d1e7d8;"></i>
                            Belum ada barang pada perkara ini
                        </div>
                    </div>
                    @endforelse
                </div>

            </div>

            @endforeach

        </div>
    </div>

    @empty
    <div class="card shadow" style="border-radius: 12px; border: none;">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3 d-block" style="color: #d1e7d8;"></i>
            Belum ada pengajuan yang disetujui
        </div>
    </div>
    @endforelse

</div>

{{-- Modal Preview --}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Preview</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" style="max-width:100%; display:none;" />
                <iframe id="previewFrame" width="100%" height="500px" style="display:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
function previewDokumen(url, nama) {
    let img   = document.getElementById('previewImage');
    let frame = document.getElementById('previewFrame');
    document.getElementById('modalTitle').innerText = nama ?? 'Preview';
    if (url.match(/\.(jpeg|jpg|png)$/i)) {
        img.src = url; img.style.display = 'block'; frame.style.display = 'none';
    } else {
        frame.src = url; frame.style.display = 'block'; img.style.display = 'none';
    }
    $('#previewModal').modal('show');
}
</script>

<script>
// Simpan index slide aktif per barang
const slideIndex = {};

function slideBarang(barangId, arah, total) {
    if (slideIndex[barangId] === undefined) slideIndex[barangId] = 0;
    slideIndex[barangId] = (slideIndex[barangId] + arah + total) % total;
    updateSlide(barangId, total);
}

function goToSlide(barangId, index, total) {
    slideIndex[barangId] = index;
    updateSlide(barangId, total);
}

function updateSlide(barangId, total) {
    const current = slideIndex[barangId];

    // Update tampilan slide
    const slides = document.querySelectorAll('.slide-' + barangId);
    slides.forEach((s, i) => s.style.display = i === current ? 'block' : 'none');

    // Update dots
    const dots = document.querySelectorAll('.dot-' + barangId);
    dots.forEach((d, i) => {
        d.style.background = i === current ? 'white' : 'rgba(255,255,255,0.45)';
    });

    // Update counter
    const counter = document.getElementById('fotoCounter-' + barangId);
    if (counter) counter.innerText = current + 1;
}
</script>
@endsection