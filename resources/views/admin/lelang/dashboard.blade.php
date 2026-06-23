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

        {{-- SEARCH SATKER --}}
        <div class="mt-3 mt-sm-0" style="min-width: 300px;">
            <div style="position: relative;">
                <i class="fas fa-search" style="
                    position: absolute; left: 14px; top: 50%;
                    transform: translateY(-50%);
                    color: #1a6b3c; font-size: 0.85rem; z-index: 1;"></i>

                <input type="text" id="searchSatkerDashboard"
                    placeholder="Cari satker..."
                    oninput="filterSatkerDashboard(this.value)"
                    style="
                        width: 100%;
                        padding: 10px 40px 10px 38px;
                        border: 2px solid #e0eeea;
                        border-radius: 12px;
                        font-size: 0.875rem;
                        background: white;
                        color: #2d3748;
                        outline: none;
                        transition: all 0.2s;
                        box-shadow: 0 2px 8px rgba(26,107,60,0.08);"
                    onfocus="this.style.borderColor='#1a6b3c'; this.style.boxShadow='0 2px 12px rgba(26,107,60,0.15)'"
                    onblur="this.style.borderColor='#e0eeea'; this.style.boxShadow='0 2px 8px rgba(26,107,60,0.08)'">

                <button id="btnClearDashboard"
                    onclick="clearSearchDashboard()"
                    style="
                        position: absolute; right: 10px; top: 50%;
                        transform: translateY(-50%);
                        background: #f6c90e; color: #1a6b3c;
                        border: none; border-radius: 8px;
                        width: 26px; height: 26px;
                        font-size: 0.7rem; cursor: pointer;
                        display: none;
                        align-items: center; justify-content: center;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="searchInfo" class="mt-1" style="font-size: 0.75rem; min-height: 18px; padding-left: 4px;"></div>
        </div>
    </div>

    {{-- LOOP PENGAJUAN --}}
    @forelse($pengajuans as $pengajuan)

    <div class="card shadow mb-4 pengajuan-card" 
        style="border: none; border-radius: 12px; overflow: hidden;"
        data-satker="{{ strtolower(optional($pengajuan->satker)->nama_satker) }}">

        {{-- HEADER PENGAJUAN --}}
        <div class="card-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
            
            {{-- Kiri: info pengajuan + tombol toggle --}}
            <div class="d-flex align-items-center" style="gap: 12px; cursor: pointer; flex: 1;"
                onclick="togglePengajuan({{ $pengajuan->id }})">
                
                {{-- Chevron --}}
                <div id="chevron-pengajuan-{{ $pengajuan->id }}"
                    style="width:28px; height:28px; border-radius:50%; background:rgba(255,255,255,0.15);
                        display:flex; align-items:center; justify-content:center; flex-shrink:0;
                        transition: transform 0.3s;">
                    <i class="fas fa-chevron-down" style="color:white; font-size:0.75rem;"></i>
                </div>

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
            </div>

            <div class="d-flex align-items-center" style="gap: 8px;">

                {{-- Cek status lelang pengajuan ini --}}
                @php
                    $semuaBarang = $pengajuan->perkaras->flatMap->barangs;
                    $countAvailable = $semuaBarang->where('status', 'available')->count();
                    $countScheduled = $semuaBarang->filter(fn($b) => $b->lelang && $b->lelang->status === 'scheduled')->count();
                    $countActive    = $semuaBarang->filter(fn($b) => $b->lelang && $b->lelang->status === 'active')->count();
                    $countFinished  = $semuaBarang->filter(fn($b) => in_array($b->status, ['sold', 'unsold']))->count();
                    $totalBarang    = $semuaBarang->count();
                    $lelangScheduledSample = $semuaBarang->first(fn($b) => $b->lelang && $b->lelang->status === 'scheduled');
                @endphp

                @if($countActive > 0)
                    <span class="badge px-3 py-2" style="background: #28a745; color: white; border-radius: 20px; font-size: 0.75rem;">
                        <i class="fas fa-fire mr-1"></i>Live ({{ $countActive }})
                    </span>
                @endif

                @if($countScheduled > 0)
                    <span class="dashboard-scheduled-timer d-none"
                        data-start="{{ $lelangScheduledSample->lelang->tanggal_mulai->toIso8601String() }}"></span>

                    {{-- Info jadwal --}}
                    <div class="text-right mr-2">
                        <small style="color: rgba(255,255,255,0.8);">
                            <i class="fas fa-calendar-check mr-1"></i>
                            {{ \Carbon\Carbon::parse($lelangScheduledSample->lelang->tanggal_mulai)->format('d M Y H:i') }}
                        </small>
                    </div>

                    {{-- Badge terjadwal --}}
                    <span class="badge px-3 py-2"
                        style="background: #17a2b8; color: white; border-radius: 20px; font-size: 0.75rem;">
                        <i class="fas fa-calendar mr-1"></i>Terjadwal
                    </span>

                    {{-- Tombol Batal --}}
                    <form action="{{ route('admin.lelang.batal', $pengajuan->id) }}" method="POST"
                        id="formBatalLelang-{{ $pengajuan->id }}">
                        @csrf
                        <button type="button" class="btn btn-sm font-weight-bold"
                            style="background: #fde8e8; color: #e74a3b; border-radius: 8px; padding: 6px 14px;"
                            onclick="swalSubmitForm('formBatalLelang-{{ $pengajuan->id }}', {
                                title: 'Batalkan Jadwal Lelang?',
                                text: 'Semua jadwal lelang pada pengajuan ini akan dibatalkan.',
                                icon: 'warning',
                                confirmText: 'Ya, Batalkan',
                                cancelText: 'Tidak',
                                confirmColor: '#e74a3b'
                            })">
                            <i class="fas fa-times mr-1"></i>Batalkan
                        </button>
                    </form>

                @endif

                {{-- Tombol Jadwalkan hanya muncul jika ada yang available --}}
                @if($countAvailable > 0)
                    @if($countScheduled == 0 && $countActive == 0)
                        <span class="badge px-3 py-2 mr-2" style="background: #f6c90e; color: #1a6b3c; border-radius: 20px; font-size: 0.75rem;">
                            Siap Dijadwalkan
                        </span>
                    @endif
                    <button class="btn btn-sm font-weight-bold"
                        style="background: #f6c90e; color: #1a6b3c; border-radius: 8px; padding: 6px 14px;"
                        data-toggle="modal"
                        data-target="#modalJadwal-{{ $pengajuan->id }}">
                        <i class="fas fa-gavel mr-1"></i>Jadwalkan Lelang
                    </button>
                @elseif($countFinished == $totalBarang)
                    <span class="badge px-3 py-2" style="background: rgba(255,255,255,0.2); color: white; border-radius: 20px; font-size: 0.75rem;">
                        <i class="fas fa-check-double mr-1"></i>Semua Selesai
                    </span>
                @endif

            </div>
        </div>
        {{-- MODAL JADWALKAN LELANG — per pengajuan --}}
        @php
            $semuaBarang = $pengajuan->perkaras->flatMap->barangs;
            $totalBarang = $semuaBarang->count(); // Total barang di pengajuan ini
            $barangTersedia = $semuaBarang->where('status', 'available'); // Barang yang statusnya 'available'
            $barangTersediaCount = $barangTersedia->count();
        @endphp
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
                                        {{ $totalBarang }} barang (Total)
                                    </small>
                                </div>
                            </div>

                            @if($barangTersediaCount > 0)
                            <div class="mb-3 p-3 rounded" style="background: #e8f5ee; border: 1px solid #b2d8c0;">
                                <div class="font-weight-bold mb-2" style="color: #1a6b3c; font-size: 0.9rem;">
                                    <i class="fas fa-boxes mr-1"></i> Barang yang akan dijadwalkan ({{ $barangTersediaCount }}):
                                </div>
                                <ul class="list-unstyled mb-0" style="font-size: 0.85rem; color: #2d3748;">
                                    @foreach($barangTersedia as $barang)
                                        <li class="mb-1">
                                            <i class="fas fa-dot-circle mr-2" style="font-size: 0.7rem; color: #1a6b3c;"></i>
                                            {{ $barang->nama_barang }}
                                            <small class="text-muted ml-2">(Rp {{ number_format($barang->harga_awal, 0, ',', '.') }})</small>
                                        </li>
                                    @endforeach
                                </ul>
                                <small class="text-muted mt-2 d-block">
                                    Barang yang sudah terjual atau sedang aktif tidak akan diubah.
                                </small>
                            </div>
                            @else
                            <div class="alert alert-info py-2" style="border-radius: 8px; font-size: 0.82rem;">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tidak ada barang yang tersedia untuk dijadwalkan lelang pada pengajuan ini.
                            </div>
                            @endif
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                                    <i class="fas fa-clock mr-1 text-success"></i> Waktu Mulai
                                </label>
                                <div class="modern-datetime-wrapper">
                                    <i class="fas fa-calendar-alt modern-datetime-icon text-success"></i>
                                    <input type="text" id="display_tanggal_mulai_{{ $pengajuan->id }}" class="form-control datetimepicker modern-datetime-input" placeholder="Pilih tanggal & waktu" autocomplete="off" required>
                                    <input type="hidden" name="tanggal_mulai" id="input_tanggal_mulai_{{ $pengajuan->id }}">
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label class="small font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                                    <i class="fas fa-flag-checkered mr-1 text-danger"></i> Waktu Selesai
                                </label>
                                <div class="modern-datetime-wrapper">
                                    <i class="fas fa-calendar-check modern-datetime-icon text-danger"></i>
                                    <input type="text" id="display_tanggal_selesai_{{ $pengajuan->id }}" class="form-control datetimepicker modern-datetime-input" placeholder="Pilih tanggal & waktu" autocomplete="off" required>
                                    <input type="hidden" name="tanggal_selesai" id="input_tanggal_selesai_{{ $pengajuan->id }}">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer" style="background: #f8fff9;">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-dismiss="modal" style="border-radius: 6px;">
                                <i class="fas fa-times mr-1"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-sm font-weight-bold"
                                style="background: #1a6b3c; color: white; border-radius: 6px; padding: 6px 16px;">
                                <i class="fas fa-calendar-check mr-1"></i>Jadwalkan {{ $barangTersediaCount }} Barang
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div id="body-pengajuan-{{ $pengajuan->id }}"
            style="overflow: hidden; max-height: 0; transition: max-height 0.4s ease, opacity 0.3s ease; opacity: 0;">
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
function filterSatkerDashboard(keyword) {
    const q        = keyword.toLowerCase().trim();
    const cards    = document.querySelectorAll('.pengajuan-card');
    const clearBtn = document.getElementById('btnClearDashboard');
    const info     = document.getElementById('searchInfo');

    clearBtn.style.display = q ? 'flex' : 'none';

    let visible = 0;

    cards.forEach(card => {
        // Ambil dari data-satker
        const namaSatker = (card.dataset.satker || '').toLowerCase();
        const cocok      = !q || namaSatker.includes(q);

        card.style.display = cocok ? '' : 'none';

        // Highlight border card yang cocok
        if (cocok && q) {
            card.style.boxShadow = '0 0 0 2px #1a6b3c, 0 4px 12px rgba(26,107,60,0.15)';
            visible++;
        } else if (cocok) {
            card.style.boxShadow = '';
            visible++;
        } else {
            card.style.boxShadow = '';
        }
    });

    // Update info
    if (q) {
        if (visible > 0) {
            info.innerHTML = `<span style="color:#1a6b3c;"><i class="fas fa-check-circle mr-1"></i>${visible} pengajuan ditemukan</span>`;
        } else {
            info.innerHTML = `<span style="color:#e74a3b;"><i class="fas fa-times-circle mr-1"></i>Tidak ditemukan untuk "<strong>${keyword}</strong>"</span>`;
        }
    } else {
        info.innerHTML = '';
        cards.forEach(card => card.style.boxShadow = '');
    }

    saveDashboardState();
}

function clearSearchDashboard() {
    const input = document.getElementById('searchSatkerDashboard');
    input.value = '';
    input.focus();
    filterSatkerDashboard('');
    saveDashboardState();
}
const DASHBOARD_STATE_KEY = 'lapau.lelang.dashboard.state';
const pengajuanState = {};

function saveDashboardState() {
    const input = document.getElementById('searchSatkerDashboard');
    const openIds = Object.keys(pengajuanState).filter(id => pengajuanState[id]);

    sessionStorage.setItem(DASHBOARD_STATE_KEY, JSON.stringify({
        search: input?.value || '',
        openIds,
        scrollY: window.scrollY,
    }));
}

function restoreDashboardState() {
    let state = null;

    try {
        state = JSON.parse(sessionStorage.getItem(DASHBOARD_STATE_KEY) || 'null');
    } catch (e) {
        state = null;
    }

    if (!state) return false;

    if (state.search) {
        const input = document.getElementById('searchSatkerDashboard');
        if (input) {
            input.value = state.search;
            filterSatkerDashboard(state.search);
        }
    }

    if (Array.isArray(state.openIds) && state.openIds.length > 0) {
        state.openIds.forEach(id => {
            if (!pengajuanState[id]) togglePengajuan(id);
        });
    }

    if (Number.isFinite(state.scrollY)) {
        setTimeout(() => window.scrollTo(0, state.scrollY), 150);
    }

    return Array.isArray(state.openIds) && state.openIds.length > 0;
}

function togglePengajuan(id) {
    const body    = document.getElementById('body-pengajuan-' + id);
    const chevron = document.getElementById('chevron-pengajuan-' + id);
    if (!body) return;

    const isOpen = pengajuanState[id] ?? false;

    if (isOpen) {
        // Tutup
        body.style.maxHeight = '0';
        body.style.opacity   = '0';
        chevron.style.transform = 'rotate(0deg)';
        pengajuanState[id] = false;
    } else {
        // Buka — set maxHeight ke scrollHeight agar smooth
        body.style.maxHeight = body.scrollHeight + 'px';
        body.style.opacity   = '1';
        chevron.style.transform = 'rotate(180deg)';
        pengajuanState[id] = true;
    }

    saveDashboardState();
}

function scheduleDashboardReload() {
    if (window.__dashboardStatusReloading) return;
    window.__dashboardStatusReloading = true;
    saveDashboardState();

    if (typeof swalToast === 'function') {
        swalToast('info', 'Status lelang berubah. Dashboard diperbarui...');
    }

    setTimeout(() => window.location.reload(), 900);
}

function initDashboardReverb() {
    if (typeof window.Echo === 'undefined') {
        setTimeout(initDashboardReverb, 400);
        return;
    }

    window.Echo.channel('lelang-updates')
        .listen('.lelang.status.updated', () => {
            scheduleDashboardReload();
        });
}

function initDashboardScheduleTimers() {
    document.querySelectorAll('.dashboard-scheduled-timer[data-start]').forEach(timer => {
        const start = new Date(timer.dataset.start).getTime();
        const diff = start - Date.now();

        if (!Number.isFinite(start)) return;

        if (diff <= 0) {
            scheduleDashboardReload();
            return;
        }

        setTimeout(() => {
            scheduleDashboardReload();
        }, Math.min(diff + 1000, 2147483647));
    });
}

// Auto buka pengajuan pertama saat halaman load
document.addEventListener('DOMContentLoaded', function () {
    const restoredOpenState = restoreDashboardState();
    const first = document.querySelector('[id^="body-pengajuan-"]');

    if (first && !restoredOpenState) {
        const id = first.id.replace('body-pengajuan-', '');
        togglePengajuan(id);
    }

    initDashboardReverb();
    initDashboardScheduleTimers();

    // Otomatis isi waktu mulai dengan waktu sekarang saat modal jadwal dibuka
    $('.modal').on('show.bs.modal', function() {
        const modal = $(this);
        const mulaiInput = modal.find('input[id^="display_tanggal_mulai_"]');
        if (mulaiInput.length && !mulaiInput.val()) {
            const fp = mulaiInput[0]._flatpickr;
            if (fp) {
                fp.setDate(new Date(), true); // 'true' untuk mentrigger event onChange
            }
        }
    });
});
</script>

<style>
.modern-datetime-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.modern-datetime-icon {
    position: absolute;
    left: 12px;
    z-index: 4;
    font-size: 0.9rem;
}
.modern-datetime-input {
    padding-left: 38px !important;
    border-radius: 12px !important;
    border: 1.5px solid #e0eeea !important;
    height: 45px !important;
    font-size: 0.9rem !important;
    transition: all 0.3s !important;
    cursor: pointer;
}
.modern-datetime-input:focus {
    border-color: #1a6b3c !important;
    box-shadow: 0 0 0 4px rgba(26, 107, 60, 0.1) !important;
    background-color: #f8fff9 !important;
}
.modern-datetime-input::-webkit-calendar-picker-indicator {
    background: transparent;
    bottom: 0;
    color: transparent;
    cursor: pointer;
    height: auto;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: auto;
}
    .pagination { margin-bottom: 0; }
    .page-item.active .page-link { 
        background-color: #1a6b3c; 
        border-color: #1a6b3c; 
    }
    .page-link { 
        color: #1a6b3c;
    }
</style>
@endsection
