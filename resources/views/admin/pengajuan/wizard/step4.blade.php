@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0">

    {{-- ================= WIZARD PROGRESS BAR (STICKY) ================= --}}
    <div class="wizard-sticky bg-white border-bottom mb-4">
        <div class="container-fluid px-4 py-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('satker.pengajuan.step3', $pengajuan) }}" class="btn btn-sm mr-3"
                        style="background:rgba(26,107,60,0.1);color:#1a6b3c;border:1px solid #1a6b3c;border-radius:8px;">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <div>
                        <h6 class="mb-0 font-weight-bold" style="color:#1a6b3c;">
                            <i class="fas fa-paper-plane mr-2" style="color:#f6c90e;"></i>
                            {{ $pengajuan->judul_pengajuan }}
                        </h6>
                        <small class="text-muted">Pengajuan Lelang — Langkah 4 dari 4</small>
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
                    $currentStep = 4;
                @endphp
                @foreach($stepItems as $num => $item)
                @php
                    $isDone   = $steps[$num] ?? false;
                    $isActive = $num === $currentStep;
                    $isLocked = false; // step 4 semua sudah bisa diklik
                @endphp
                <div class="d-flex align-items-center flex-fill">
                    @if($isActive)
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

        {{-- ================= RINGKASAN INFO ================= --}}
        @php
            $isDraft     = $pengajuan->status === 'draft';
            $isRevision  = $pengajuan->status === 'revision';
            $isSubmitted = $pengajuan->status === 'submitted';
            $isApproved  = $pengajuan->status === 'approved';
        @endphp
        <div class="card shadow mb-4" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="card-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);padding:14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-info-circle mr-2" style="color:#f6c90e;"></i>Ringkasan Pengajuan
                </h6>
            </div>
            <div class="card-body" style="background:#f8fff9;">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><span class="text-muted small">Judul Pengajuan</span></p>
                        <p class="font-weight-bold" style="color:#1a6b3c;">{{ $pengajuan->judul_pengajuan }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><span class="text-muted small">Jumlah Perkara</span></p>
                        <p class="font-weight-bold" style="color:#1a6b3c;">
                            {{ $pengajuan->perkaras->count() }} perkara
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><span class="text-muted small">Total Barang</span></p>
                        <p class="font-weight-bold" style="color:#1a6b3c;">
                            {{ $pengajuan->perkaras->sum(fn($p) => $p->barangs->count()) }} barang
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= CHECKLIST KELENGKAPAN ================= --}}
        <div class="card shadow mb-4" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="card-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);padding:14px 20px;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-tasks mr-2" style="color:#f6c90e;"></i>Checklist Kelengkapan
                </h6>
            </div>
            <div class="card-body">
                @php
                    $sk    = $pengajuan->dokumenPengajuan->where('jenis','sk_panitia')->first();
                    $izin  = $pengajuan->dokumenPengajuan->where('jenis','izin_penjualan')->first();
                    $harga = $pengajuan->dokumenPengajuan->where('jenis','surat_penetapan_harga')->first();
                    $checks = [
                        ['label' => 'Judul pengajuan',              'ok' => !empty($pengajuan->judul_pengajuan)],
                        ['label' => 'SK Panitia',                   'ok' => (bool) $sk],
                        ['label' => 'Izin Penjualan',               'ok' => (bool) $izin],
                        ['label' => 'Surat Penetapan Harga Limit',  'ok' => (bool) $harga],
                        ['label' => 'Minimal 1 perkara',            'ok' => $pengajuan->perkaras->count() > 0],
                        ['label' => 'Semua perkara punya dokumen',  'ok' => $pengajuan->perkaras->every(fn($p) => $p->dokumenPerkara->count() > 0)],
                        ['label' => 'Minimal 1 barang',             'ok' => $pengajuan->perkaras->sum(fn($p) => $p->barangs->count()) > 0],
                    ];
                    $allOk = collect($checks)->every(fn($c) => $c['ok']);
                @endphp

                <div class="row">
                    @foreach($checks as $check)
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center p-2"
                            style="border-radius:8px;background:{{ $check['ok'] ? '#f0faf4' : '#fff5f5' }};
                                   border:1px solid {{ $check['ok'] ? '#b2d8c0' : '#f5c6cb' }};">
                            <i class="fas {{ $check['ok'] ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"
                                style="color:{{ $check['ok'] ? '#1a6b3c' : '#e74a3b' }};font-size:1rem;"></i>
                            <span class="small {{ $check['ok'] ? '' : 'text-danger' }}">{{ $check['label'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ================= RINGKASAN PERKARA & BARANG ================= --}}
        @foreach($pengajuan->perkaras as $i => $perkara)
        <div class="card shadow mb-3" style="border:none;border-radius:12px;overflow:hidden;">

            {{-- Accordion Header --}}
            <div class="card-header d-flex align-items-center justify-content-between"
                style="background:linear-gradient(90deg,#f6c90e,#e0b800);padding:12px 20px;cursor:pointer;"
                onclick="toggleReview({{ $perkara->id }})">
                <div class="d-flex align-items-center" style="gap:10px;">
                    <div style="width:28px;height:28px;border-radius:50%;background:#1a6b3c;color:white;
                        display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:0.75rem;flex-shrink:0;">
                        {{ $i + 1 }}
                    </div>
                    <div>
                        <div class="font-weight-bold small" style="color:#1a6b3c;">{{ $perkara->nomor_perkara }}</div>
                        <div style="font-size:0.75rem;color:#556b2f;">{{ $perkara->nama_tersangka }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <span class="badge" style="background:#1a6b3c;color:white;border-radius:20px;font-size:0.7rem;">
                        {{ $perkara->barangs->count() }} barang
                    </span>
                    <span class="badge" style="background:rgba(26,107,60,0.15);color:#1a6b3c;border-radius:20px;font-size:0.7rem;">
                        {{ $perkara->dokumenPerkara->count() }} dok
                    </span>
                    <i class="fas fa-chevron-down" id="chevron-review-{{ $perkara->id }}"
                        style="color:#1a6b3c;font-size:0.8rem;transition:transform 0.3s;"></i>
                </div>
            </div>

            {{-- Accordion Body --}}
            <div id="review-body-{{ $perkara->id }}" class="review-body">
                <div class="card-body" style="background:#fffdf0;">

                    {{-- Dokumen Perkara --}}
                    <h6 class="small font-weight-bold mb-2" style="color:#856404;">
                        <i class="fas fa-paperclip mr-1"></i> Dokumen Perkara
                    </h6>
                    <div class="mb-3">
                        @forelse($perkara->dokumenPerkara as $doc)
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-file-pdf text-danger mr-2 small"></i>
                            <span class="small mr-2">{{ $doc->nama_dokumen }}</span>
                            <button type="button" class="btn btn-sm"
                                style="background:#e8f5ee;color:#1a6b3c;border-radius:6px;font-size:0.72rem;padding:2px 8px;"
                                onclick="previewDokumen('{{ asset('storage/'.$doc->file_path) }}','{{ $doc->nama_dokumen }}')">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </button>
                        </div>
                        @empty
                        <span class="text-danger small"><i class="fas fa-exclamation-circle mr-1"></i>Belum ada dokumen</span>
                        @endforelse
                    </div>

                    {{-- Barang --}}
                    <h6 class="small font-weight-bold mb-2" style="color:#856404;">
                        <i class="fas fa-boxes mr-1"></i> Barang
                    </h6>
                    @forelse($perkara->barangs as $barang)
                    <div class="p-2 mb-2" style="background:white;border-radius:8px;border:1px solid #e0eeea;">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="font-weight-bold small">{{ $barang->nama_barang }}</div>
                                <div class="small text-muted">{{ $barang->deskripsi ?? '-' }}</div>
                                @if($barang->catatan_internal)
                                <span class="badge mt-1"
                                    style="background:#fff3cd;color:#856404;border-radius:6px;font-size:0.68rem;padding:2px 6px;">
                                    <i class="fas fa-lock mr-1" style="font-size:0.6rem;"></i>{{ $barang->catatan_internal }}
                                </span>
                                @endif
                            </div>
                            <div class="text-right ml-3">
                                <div class="small text-muted">Harga Limit</div>
                                <div class="font-weight-bold small" style="color:#1a6b3c;">
                                    Rp {{ number_format($barang->harga_awal, 0, ',', '.') }}
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ $barang->fotoBarang->count() }} foto
                                </div>
                            </div>
                        </div>
                        {{-- Mini foto --}}
                        @if($barang->fotoBarang->count() > 0)
                        <div class="d-flex flex-wrap mt-2" style="gap:4px;">
                            @foreach($barang->fotoBarang->take(4) as $foto)
                            <img src="{{ asset('storage/'.$foto->file_path) }}"
                                style="width:40px;height:40px;object-fit:cover;border-radius:6px;cursor:pointer;border:1px solid #e0eeea;"
                                onclick="previewDokumen('{{ asset('storage/'.$foto->file_path) }}','Foto {{ $barang->nama_barang }}')">
                            @endforeach
                            @if($barang->fotoBarang->count() > 4)
                            <div style="width:40px;height:40px;border-radius:6px;background:#e9ecef;
                                display:flex;align-items:center;justify-content:center;font-size:0.7rem;color:#6c757d;">
                                +{{ $barang->fotoBarang->count() - 4 }}
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-2 text-muted small">
                        <i class="fas fa-box-open mr-1 text-warning"></i>Belum ada barang di perkara ini
                    </div>
                    @endforelse

                </div>
            </div>
        </div>
        @endforeach

        {{-- ================= TOMBOL SUBMIT ================= --}}
        <div class="card shadow mb-4" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="card-body text-center py-4" style="background:#f8fff9;">

                @php
                    // Kumpulkan semua barang dari semua perkara
                    $semuaBarang = $pengajuan->perkaras->flatMap->barangs;

                    // Kumpulkan semua lelang dari semua barang (relasi lewat barang)
                    $semuaLelang = $semuaBarang
                        ->map(fn($b) => $b->lelang)   // relasi singular di model Barang
                        ->filter();                    // buang yang null (belum punya lelang)

                    // Status lelang
                    $lelangStatuses = $semuaLelang->pluck('status')->unique()->values();
                    $hasActive    = $lelangStatuses->contains('active');
                    $hasScheduled = $lelangStatuses->contains('scheduled');
                    $hasClosed    = $lelangStatuses->contains('closed');

                    // Hitung barang per kondisi
                    $totalBarang     = $semuaBarang->count();
                    $barangBelumLelang = $semuaBarang->filter(fn($b) => !$b->lelang)->count();

                    $barangPerStatus = [
                        'scheduled' => $semuaLelang->where('status', 'scheduled')->count(),
                        'active'    => $semuaLelang->where('status', 'active')->count(),
                        'closed'    => $semuaLelang->where('status', 'closed')->count(),
                    ];

                    // Pemenang (sold) — lelang closed dan ada pemenang_id
                    $barangTerjual  = $semuaLelang->where('status', 'closed')->whereNotNull('pemenang_id')->count();
                    $barangTidakLaku = $semuaLelang->where('status', 'closed')->whereNull('pemenang_id')->count();
                @endphp

                {{-- ── Ringkasan status barang (tampil jika ada lelang) ── --}}
                @if($semuaLelang->isNotEmpty())
                <div class="row justify-content-center mb-4" style="gap:0;">
                    <div class="col-auto px-2">
                        <div class="px-3 py-2 rounded-lg text-center" style="background:#e8f4fd;border:1px solid #bee5eb;min-width:80px;">
                            <div class="font-weight-bold" style="color:#17a2b8;font-size:1.1rem;">{{ $barangPerStatus['scheduled'] }}</div>
                            <div style="font-size:0.68rem;color:#6c757d;">Terjadwal</div>
                        </div>
                    </div>
                    <div class="col-auto px-2">
                        <div class="px-3 py-2 rounded-lg text-center" style="background:#e8f5ee;border:1px solid #b2d8c0;min-width:80px;">
                            <div class="font-weight-bold" style="color:#1a6b3c;font-size:1.1rem;">{{ $barangPerStatus['active'] }}</div>
                            <div style="font-size:0.68rem;color:#6c757d;">Sedang Lelang</div>
                        </div>
                    </div>
                    <div class="col-auto px-2">
                        <div class="px-3 py-2 rounded-lg text-center" style="background:#fff3cd;border:1px solid #ffc107;min-width:80px;">
                            <div class="font-weight-bold" style="color:#856404;font-size:1.1rem;">{{ $barangTerjual }}</div>
                            <div style="font-size:0.68rem;color:#6c757d;">Terjual</div>
                        </div>
                    </div>
                    <div class="col-auto px-2">
                        <div class="px-3 py-2 rounded-lg text-center" style="background:#fde8e8;border:1px solid #f5c6cb;min-width:80px;">
                            <div class="font-weight-bold" style="color:#c0392b;font-size:1.1rem;">{{ $barangTidakLaku }}</div>
                            <div style="font-size:0.68rem;color:#6c757d;">Tidak Laku</div>
                        </div>
                    </div>
                    @if($barangBelumLelang > 0)
                    <div class="col-auto px-2">
                        <div class="px-3 py-2 rounded-lg text-center" style="background:#f8f9fa;border:1px solid #dee2e6;min-width:80px;">
                            <div class="font-weight-bold" style="color:#6c757d;font-size:1.1rem;">{{ $barangBelumLelang }}</div>
                            <div style="font-size:0.68rem;color:#6c757d;">Belum Dilelang</div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- ── Tombol kondisi ── --}}
                @if($hasActive)
                <p class="mb-3 small">
                    <i class="fas fa-gavel text-success mr-1"></i>
                    {{ $barangPerStatus['active'] }} barang sedang dalam proses lelang.
                </p>
                <button disabled class="btn font-weight-bold"
                    style="background:#1a6b3c;color:white;border-radius:10px;padding:10px 32px;font-size:1rem;cursor:not-allowed;opacity:0.7;">
                    <i class="fas fa-broadcast-tower mr-2"></i> Lelang Sedang Berlangsung
                </button>

                @elseif($hasScheduled)
                <p class="mb-3 small">
                    <i class="fas fa-calendar-check text-info mr-1"></i>
                    {{ $barangPerStatus['scheduled'] }} barang sudah dijadwalkan untuk dilelang.
                </p>
                <button disabled class="btn font-weight-bold"
                    style="background:#17a2b8;color:white;border-radius:10px;padding:10px 32px;font-size:1rem;cursor:not-allowed;opacity:0.7;">
                    <i class="fas fa-clock mr-2"></i> Lelang Terjadwal
                </button>

                @elseif($hasClosed)
                <p class="mb-3 small">
                    <i class="fas fa-flag-checkered text-secondary mr-1"></i>
                    Proses lelang telah selesai —
                    <strong>{{ $barangTerjual }} terjual</strong>,
                    {{ $barangTidakLaku }} tidak laku.
                </p>
                <button disabled class="btn font-weight-bold"
                    style="background:#6c757d;color:white;border-radius:10px;padding:10px 32px;font-size:1rem;cursor:not-allowed;opacity:0.7;">
                    <i class="fas fa-check-double mr-2"></i> Lelang Selesai
                </button>

                @elseif($isApproved)
                <p class="mb-3 small">
                    <i class="fas fa-check-circle text-success mr-1"></i>
                    Pengajuan telah disetujui. Menunggu Admin Pusat membuat jadwal lelang.
                </p>
                <button disabled class="btn font-weight-bold"
                    style="background:#1a6b3c;color:white;border-radius:10px;padding:10px 32px;font-size:1rem;cursor:not-allowed;opacity:0.7;">
                    <i class="fas fa-check-circle mr-2"></i> Disetujui
                </button>

                @elseif($isSubmitted)
                <p class="mb-3 small">
                    <i class="fas fa-paper-plane text-info mr-1"></i>
                    Pengajuan sudah dikirim dan sedang menunggu review Admin Pusat.
                </p>
                <button disabled class="btn font-weight-bold"
                    style="background:#17a2b8;color:white;border-radius:10px;padding:10px 32px;font-size:1rem;cursor:not-allowed;opacity:0.7;">
                    <i class="fas fa-clock mr-2"></i> Menunggu Review...
                </button>

                @elseif($allOk)
                <p class="text-muted mb-3 small">
                    <i class="fas fa-check-circle text-success mr-1"></i>
                    Semua data sudah lengkap. Pengajuan siap dikirim ke Admin Pusat.
                </p>
                <form id="form-submit-pengajuan"
                    method="POST" action="{{ route('satker.pengajuan.submit', $pengajuan) }}">
                    @csrf
                    <button type="button" class="btn font-weight-bold"
                        style="background:linear-gradient(135deg,#1a6b3c,#145c32);color:white;
                            border-radius:10px;padding:10px 32px;font-size:1rem;"
                        onclick="swalSubmitForm('form-submit-pengajuan', {
                            title: 'Kirim Pengajuan?',
                            text: 'Pengajuan akan dikirim ke Admin Pusat untuk direview.',
                            icon: 'question',
                            confirmText: 'Ya, Kirim',
                            confirmColor: '#1a6b3c'
                        })">
                        <i class="fas fa-paper-plane mr-2"></i> Submit ke Admin Pusat
                    </button>
                </form>

                @else
                <p class="text-muted mb-3 small">
                    <i class="fas fa-exclamation-circle text-warning mr-1"></i>
                    Lengkapi semua checklist di atas sebelum mengirim pengajuan.
                </p>
                <button disabled class="btn font-weight-bold"
                    style="background:#ccc;color:white;border-radius:10px;padding:10px 32px;font-size:1rem;cursor:not-allowed;">
                    <i class="fas fa-paper-plane mr-2"></i> Submit ke Admin Pusat
                </button>

                @endif
            </div>
        </div>

        {{-- Navigasi Bawah --}}
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('satker.pengajuan.step3', $pengajuan) }}" class="btn btn-sm btn-secondary"
                style="border-radius:8px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Barang
            </a>
            <a href="{{ route('satker.pengajuan.index') }}" class="btn btn-sm"
                style="background:rgba(26,107,60,0.1);color:#1a6b3c;border:1px solid #1a6b3c;border-radius:8px;">
                <i class="fas fa-list mr-1"></i> Ke Daftar Pengajuan
            </a>
        </div>

    </div>
</div>

<style>
    .review-body {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.35s ease;
}
.review-body.open {
    max-height: 2000px;
}
</style>

<script>
function toggleReview(id) {
    const body    = document.getElementById('review-body-' + id);
    const chevron = document.getElementById('chevron-review-' + id);
    const isOpen  = body.classList.contains('open');

    body.classList.toggle('open');
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Auto buka perkara pertama
document.addEventListener('DOMContentLoaded', function () {
    const first = document.querySelector('[id^="review-body-"]');
    if (first) {
        const id = first.id.replace('review-body-', '');
        toggleReview(id);
    }
});
</script>

@endsection