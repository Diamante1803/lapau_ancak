@extends('layouts.public')

@section('content')

{{-- ===== HERO ===== --}}
<section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">

        <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-1 text-sm mb-4">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-ping inline-block"></span>
            {{-- ✅ Tambah id --}}
            <span><span id="heroStatAktif">{{ $stats['aktif'] }}</span> Lelang sedang berlangsung</span>
        </div>

        <h1 class="text-4xl md:text-5xl font-bold mb-3 tracking-tight">
            ⚖️ LAPAU ANCAK
        </h1>
        <p class="text-blue-200 text-lg mb-8">
            Platform Resmi Lelang Barang Rampasan Negara
        </p>

        {{-- DROPDOWN DENGAN SEARCH --}}
        <div class="max-w-xl mx-auto">
            <label class="block text-sm text-blue-200 mb-2 text-center">
                <i class="fas fa-building mr-1"></i> Pilih Satker Penjual
            </label>
            <div class="relative" id="customDropdown">

                {{-- Trigger --}}
                <button type="button" onclick="toggleDropdown()"
                    class="w-full flex items-center justify-between rounded-xl px-4 py-3 text-sm
                        bg-white/95 backdrop-blur shadow-lg
                        focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
                    id="dropdownTrigger">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-building text-gray-400 text-sm"></i>
                        <span id="dropdownLabel" class="text-gray-700">— Semua Satker —</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" id="dropdownChevron"></i>
                </button>

                {{-- Dropdown Menu --}}
                <div id="dropdownMenu"
                    class="hidden absolute z-50 w-full mt-2 rounded-xl shadow-xl overflow-hidden"
                    style="background:white; border:1px solid #e5e7eb;">

                    {{-- Search Input --}}
                    <div class="px-3 py-2" style="border-bottom:1px solid #f3f4f6; background:#f9fafb;">
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg"
                            style="background:white; border:1px solid #e5e7eb;">
                            <i class="fas fa-search text-gray-400 text-xs"></i>
                            <input type="text" id="searchSatker"
                                oninput="filterSatker(this.value)"
                                placeholder="Cari satker..."
                                class="w-full text-sm outline-none text-gray-700"
                                style="border:none; background:transparent;"
                                onclick="event.stopPropagation()">
                            <button type="button" onclick="clearSearch()"
                                id="btnClearSearch"
                                class="text-gray-400 hover:text-gray-600 hidden">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- List Options --}}
                    <div id="dropdownList" style="max-height: 240px; overflow-y: auto;">

                        {{-- Semua Satker --}}
                        <div onclick="selectSatker('', '— Semua Satker —')"
                            class="satker-option flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-green-50 transition"
                            data-name="semua satker"
                            style="border-bottom:1px solid #f3f4f6;">
                            <div style="width:32px;height:32px;border-radius:8px;background:#e8f5ee;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-globe" style="color:#1a6b3c;font-size:0.8rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.875rem;font-weight:600;color:#2d3748;">Semua Satker</div>
                                <div style="font-size:0.72rem;color:#6c757d;">Tampilkan semua lelang</div>
                            </div>
                        </div>

                        {{-- List Satker --}}
                        @foreach($satkers as $satker)
                        <div onclick="selectSatker('{{ $satker->id }}', '{{ $satker->nama_satker }}')"
                            class="satker-option flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-green-50 transition"
                            data-name="{{ strtolower($satker->nama_satker) }} {{ strtolower($satker->alamat ?? '') }}"
                            style="border-bottom:1px solid #f3f4f6;">
                            <div style="width:32px;height:32px;border-radius:8px;background:#e8f5ee;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-building" style="color:#1a6b3c;font-size:0.8rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.875rem;font-weight:600;color:#2d3748;">
                                    {{ $satker->nama_satker }}
                                </div>
                                @if($satker->alamat)
                                <div style="font-size:0.72rem;color:#6c757d;">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $satker->alamat }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        {{-- Empty state --}}
                        <div id="emptySearch" class="hidden px-4 py-6 text-center text-gray-400 text-sm">
                            <i class="fas fa-search mb-2 d-block text-2xl opacity-30"></i>
                            Satker tidak ditemukan
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ===== STAT CARDS ===== --}}
<section class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="grid grid-cols-3 gap-4">

            <div class="text-center p-4 rounded-xl bg-blue-50 border border-blue-100">
                <div class="text-3xl font-bold text-blue-700" id="statTotal">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500 mt-1">Total Lelang</div>
            </div>

            <div class="text-center p-4 rounded-xl bg-green-50 border border-green-100">
                <div class="text-3xl font-bold text-green-600" id="statAktif">{{ $stats['aktif'] }}</div>
                <div class="text-sm text-gray-500 mt-1 flex items-center justify-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block animate-ping"></span>
                    Lelang Aktif
                </div>
            </div>

            <a href="#lelang-mendatang"
                class="text-center p-4 rounded-xl bg-yellow-50 border border-yellow-100 block hover:bg-yellow-100 transition">
                <div class="text-3xl font-bold text-yellow-500" id="statMendatang">{{ $stats['mendatang'] }}</div>
                <div class="text-sm text-gray-500 mt-1">Akan Datang</div>
            </a>
        </div>
    </div>
</section>

{{-- ===== CARD LELANG AKTIF ===== --}}
<section class="py-10 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">🔨 Lelang Berlangsung</h2>
                <p class="text-sm text-gray-500">Klik barang untuk ajukan penawaran</p>
            </div>
        </div>

        {{-- GRID CARD --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="gridLelang">

            @forelse($lelangsAktif as $lelang)
            @php
                $barang  = $lelang->barang;
                $satker  = $barang->perkara->pengajuan->satker;
                $fotos   = $barang->fotoBarang;
                $hargaId = 'harga-' . $lelang->id;
            @endphp

            <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                data-satker="{{ $satker->id }}"
                data-lelang-id="{{ $lelang->id }}"
                data-end="{{ $lelang->tanggal_selesai->toIso8601String() }}">

                {{-- SLIDESHOW FOTO --}}
                <div class="relative h-48 bg-gray-100 overflow-hidden">

                    @if($fotos->count() > 0)
                        @foreach($fotos as $fIdx => $foto)
                        <div class="pub-slide-{{ $lelang->id }} absolute inset-0 transition-opacity duration-300"
                            style="opacity: {{ $fIdx == 0 ? '1' : '0' }}; z-index: {{ $fIdx == 0 ? '1' : '0' }}">
                            <img src="{{ asset('storage/' . $foto->file_path) }}"
                                class="w-full h-full object-cover">
                        </div>
                        @endforeach

                        @if($fotos->count() > 1)
                        <button onclick="pubSlide({{ $lelang->id }}, -1, {{ $fotos->count() }})"
                            class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-black/40 hover:bg-black/60 text-white w-7 h-7 rounded-full flex items-center justify-center transition">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                        <button onclick="pubSlide({{ $lelang->id }}, 1, {{ $fotos->count() }})"
                            class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-black/40 hover:bg-black/60 text-white w-7 h-7 rounded-full flex items-center justify-center transition">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>

                        {{-- Dots --}}
                        <div class="absolute bottom-2 w-full flex justify-center gap-1 z-10">
                            @foreach($fotos as $dIdx => $dot)
                            <span class="pub-dot-{{ $lelang->id }} w-1.5 h-1.5 rounded-full cursor-pointer transition"
                                style="background: {{ $dIdx == 0 ? 'white' : 'rgba(255,255,255,0.4)' }}"
                                onclick="pubGoTo({{ $lelang->id }}, {{ $dIdx }}, {{ $fotos->count() }})">
                            </span>
                            @endforeach
                        </div>
                        @endif

                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <i class="fas fa-image text-4xl mb-2 block"></i>
                                <span class="text-sm">Belum ada foto</span>
                            </div>
                        </div>
                    @endif

                    {{-- Badge Live --}}
                    <div class="absolute top-3 left-3 z-10">
                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></span>
                            LIVE
                        </span>
                    </div>

                    {{-- Badge Satker --}}
                    <div class="absolute top-3 right-3 z-10">
                        <span class="bg-blue-900/70 text-white text-xs px-2 py-1 rounded-lg">
                            {{ $satker->nama_satker }}
                        </span>
                    </div>

                </div>

                {{-- INFO BARANG --}}
                <div class="p-4">

                    <h3 class="font-bold text-gray-800 text-base mb-1">
                        {{ $barang->nama_barang }}
                    </h3>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">
                        {{ $barang->deskripsi ?? 'Tidak ada deskripsi' }}
                    </p>

                    {{-- Harga --}}
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <div class="text-xs text-gray-400">Harga Awal</div>
                            <div class="font-bold text-blue-700">
                                Rp {{ number_format($lelang->harga_awal, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-400">Penawaran Tertinggi</div>
                            <div class="font-bold text-green-600" id="{{ $hargaId }}">
                                @if($lelang->harga_tertinggi)
                                    Rp {{ number_format($lelang->harga_tertinggi, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400 font-normal text-sm">Belum ada</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Countdown --}}
                    <div class="bg-blue-50 rounded-xl px-3 py-2 mb-4 text-center">
                        <div class="text-xs text-blue-500 mb-1">⏱ Waktu Tersisa</div>

                        <div class="flex justify-center items-end mt-1 gap-2 countdown"
                            data-end="{{ $lelang->tanggal_selesai->toIso8601String() }}">

                            @foreach([['id'=>'cd-hari','label'=>'Hari'],['id'=>'cd-jam','label'=>'Jam'],['id'=>'cd-menit','label'=>'Menit'],['id'=>'cd-detik','label'=>'Detik']] as $unit)
                            <div class="text-center">
                                <div id="{{ $unit['id'] }}-{{ $lelang->id }}"
                                    class="font-bold"
                                    style="font-size: 1.6rem; color: #1d4ed8; line-height: 1; min-width: 36px; letter-spacing: 1px;">
                                    00
                                </div>
                                <div style="font-size: 0.65rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">
                                    {{ $unit['label'] }}
                                </div>
                            </div>
                            @if(!$loop->last)
                            <div class="font-bold pb-4" style="color: #1d4ed8; font-size: 1.2rem;">:</div>
                            @endif
                            @endforeach

                        </div>
                    </div>

                    {{-- TOMBOL LIHAT DETAIL --}}
                    <div id="tombol-{{ $lelang->id }}">
                        <a href="{{ route('public.detail', $lelang->id) }}"
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fas fa-eye"></i>
                            Lihat Detail
                        </a>
                    </div>

                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-16 text-gray-400">
                <i class="fas fa-gavel text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">Belum ada lelang yang berlangsung saat ini</p>
            </div>
            @endforelse

        </div>
    </div>
</section>

{{-- ===== SECTION LELANG AKAN DATANG ===== --}}
@if($lelangsMendatang->count() > 0)
<section class="py-10 bg-white" id="lelang-mendatang" style="scroll-margin-top: 80px;">
    <div class="max-w-6xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">🗓 Lelang Akan Datang</h2>
                <p class="text-sm text-gray-500">Segera dibuka untuk penawaran</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="gridMendatang">

            @foreach($lelangsMendatang as $lelang)
            @php
                $barang = $lelang->barang;
                $satker = $barang->perkara->pengajuan->satker;
                $fotos  = $barang->fotoBarang;
            @endphp

            <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                 data-satker="{{ $satker->id }}"
                 data-mulai="{{ $lelang->tanggal_mulai->toIso8601String() }}">

                {{-- SLIDESHOW FOTO --}}
                <div class="relative h-48 bg-gray-100 overflow-hidden">

                    @if($fotos->count() > 0)
                        @foreach($fotos as $fIdx => $foto)
                        <div class="pub-slide-sch-{{ $lelang->id }} absolute inset-0 transition-opacity duration-300"
                            style="opacity: {{ $fIdx == 0 ? '1' : '0' }}; z-index: {{ $fIdx == 0 ? '1' : '0' }}">
                            <img src="{{ asset('storage/' . $foto->file_path) }}"
                                class="w-full h-full object-cover grayscale-[30%]">
                        </div>
                        @endforeach

                        @if($fotos->count() > 1)
                        <button onclick="pubSlide('sch-{{ $lelang->id }}', -1, {{ $fotos->count() }})"
                            class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-black/40 hover:bg-black/60 text-white w-7 h-7 rounded-full flex items-center justify-center transition">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                        <button onclick="pubSlide('sch-{{ $lelang->id }}', 1, {{ $fotos->count() }})"
                            class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-black/40 hover:bg-black/60 text-white w-7 h-7 rounded-full flex items-center justify-center transition">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                        @endif

                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <i class="fas fa-image text-4xl mb-2 block"></i>
                                <span class="text-sm">Belum ada foto</span>
                            </div>
                        </div>
                    @endif

                    {{-- Badge Scheduled --}}
                    <div class="absolute top-3 left-3 z-10">
                        <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">
                            🗓 SEGERA
                        </span>
                    </div>

                    {{-- Badge Satker --}}
                    <div class="absolute top-3 right-3 z-10">
                        <span class="bg-blue-900/70 text-white text-xs px-2 py-1 rounded-lg">
                            {{ $satker->nama_satker }}
                        </span>
                    </div>

                </div>

                {{-- INFO BARANG --}}
                <div class="p-4">

                    <h3 class="font-bold text-gray-800 text-base mb-1">
                        {{ $barang->nama_barang }}
                    </h3>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">
                        {{ $barang->deskripsi ?? 'Tidak ada deskripsi' }}
                    </p>

                    {{-- Harga Awal --}}
                    <div class="mb-3">
                        <div class="text-xs text-gray-400">Harga Awal</div>
                        <div class="font-bold text-blue-700">
                            Rp {{ number_format($lelang->harga_awal, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Dibuka Tanggal --}}
                    <div class="bg-yellow-50 rounded-xl px-3 py-2 mb-4 text-center border border-yellow-100">
                        <div class="text-xs text-yellow-600 mb-1">📅 Dibuka Pada</div>
                        <div class="font-bold text-yellow-700">
                            {{ $lelang->tanggal_mulai->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                    </div>

                    {{-- Tombol nonaktif --}}
                    <button disabled
                        class="w-full bg-gray-200 text-gray-400 font-bold py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Belum Dibuka
                    </button>

                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>
@endif

<style>
    html { scroll-behavior: smooth; }
</style>

{{-- Script --}}
<script>
// ===== SLIDESHOW =====
const pubSlideIdx = {};

function pubSlide(id, arah, total) {
    if (pubSlideIdx[id] === undefined) pubSlideIdx[id] = 0;
    pubSlideIdx[id] = (pubSlideIdx[id] + arah + total) % total;
    pubUpdateSlide(id, total);
}

function pubGoTo(id, index, total) {
    pubSlideIdx[id] = index;
    pubUpdateSlide(id, total);
}

function pubUpdateSlide(id, total) {
    const current = pubSlideIdx[id];
    document.querySelectorAll('.pub-slide-' + id).forEach((s, i) => {
        s.style.opacity = i === current ? '1' : '0';
        s.style.zIndex  = i === current ? '1' : '0';
    });
    document.querySelectorAll('.pub-dot-' + id).forEach((d, i) => {
        d.style.background = i === current ? 'white' : 'rgba(255,255,255,0.4)';
    });
}

// ===== COUNTDOWN =====
const GRACE_PERIOD_DAYS = 2; // card masih tampil N hari setelah berakhir

function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        const end  = new Date(el.dataset.end).getTime();
        const now  = new Date().getTime();
        const diff = end - now;

        const hariEl   = el.querySelector('[id^="cd-hari-"]');
        const jamEl    = el.querySelector('[id^="cd-jam-"]');
        const menitEl  = el.querySelector('[id^="cd-menit-"]');
        const detikEl  = el.querySelector('[id^="cd-detik-"]');

        if (!hariEl) return;

        // Ambil lelang id dari id element (format: cd-hari-{id})
        const lelangId = hariEl.id.replace('cd-hari-', '');
        const tombolEl = document.getElementById('tombol-' + lelangId);
        const cardEl   = el.closest('[data-lelang-id]');

        if (diff <= 0) {
            // Set semua ke 00 merah
            [hariEl, jamEl, menitEl, detikEl].forEach(e => {
                if (e) { e.textContent = '00'; e.style.color = '#ef4444'; }
            });

            // Hitung sudah berapa lama berakhir
            const expiredMs   = Math.abs(diff);
            const expiredDays = expiredMs / 86400000;

            if (expiredDays > GRACE_PERIOD_DAYS) {
                // Sudah lebih dari grace period → sembunyikan card
                if (cardEl) cardEl.style.display = 'none';
                return;
            }

            if (tombolEl && !tombolEl.dataset.updated) {
                tombolEl.dataset.updated = 'true';
                const link = tombolEl.querySelector('a');
                if (link) {
                    link.className = link.className
                        .replace('bg-blue-700', 'bg-gray-500')
                        .replace('hover:bg-blue-800', 'hover:bg-gray-600');
                    link.innerHTML = `<i class="fas fa-clock"></i> Lihat Detail`;
                }
            }

            // Ubah badge LIVE → SELESAI
            const badgeLive = cardEl?.querySelector('.bg-green-500');
            if (badgeLive && badgeLive.textContent.includes('LIVE')) {
                badgeLive.className = badgeLive.className
                    .replace('bg-green-500', 'bg-gray-400');
                badgeLive.innerHTML = `
                    <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                    SELESAI
                `;
            }

            // Ubah background countdown jadi abu
            const countdownBox = el.closest('.bg-blue-50');
            if (countdownBox) {
                countdownBox.classList.remove('bg-blue-50');
                countdownBox.classList.add('bg-gray-50');
                const label = countdownBox.querySelector('.text-blue-500');
                if (label) {
                    label.classList.remove('text-blue-500');
                    label.classList.add('text-gray-400');
                    label.textContent = '⏱ Lelang Berakhir';
                }
            }
            
            return;
        }

        // Countdown normal
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        hariEl.textContent  = String(d).padStart(2, '0');
        jamEl.textContent   = String(h).padStart(2, '0');
        menitEl.textContent = String(m).padStart(2, '0');
        detikEl.textContent = String(s).padStart(2, '0');

        // Warna merah jika sisa < 1 jam
        const urgent = diff < 3600000;
        [hariEl, jamEl, menitEl, detikEl].forEach(e => {
            if (e) e.style.color = urgent ? '#ef4444' : '#1d4ed8';
        });
    });
}

setInterval(updateCountdowns, 1000);
updateCountdowns();

// ===== FILTER SATKER =====
function filterBySatker(satkerId) {
    let countAktif = 0;
    let countMendatang = 0;
    let countTotal = 0;

    // Filter grid total
    document.querySelectorAll('#gridLelang > div[data-satker]').forEach(card => {
        if (!satkerId || card.dataset.satker === satkerId) {
            card.style.display = '';
            countTotal++;
        } else {
            card.style.display = 'none';
        }
    });

    // Filter grid aktif
    document.querySelectorAll('#gridLelang > div[data-satker]').forEach(card => {
        if (!satkerId || card.dataset.satker === satkerId) {
            card.style.display = '';
            countAktif++;
        } else {
            card.style.display = 'none';
        }
    });

    // Filter grid mendatang
    document.querySelectorAll('#gridMendatang > div[data-satker]').forEach(card => {
        if (!satkerId || card.dataset.satker === satkerId) {
            card.style.display = '';
            countMendatang++;
        } else {
            card.style.display = 'none';
        }
    });

    // ✅ Update stat cards
    document.getElementById('statAktif').textContent = countAktif;
    document.getElementById('statMendatang').textContent = countMendatang;
    document.getElementById('heroStatAktif').textContent = countAktif;
    document.getElementById('statTotal').textContent = countTotal;

}

function checkLelangMendatang() {
    const now = new Date();
    const cards = document.querySelectorAll('#gridMendatang > div[data-mulai]');
    
    console.log('Checker jalan, jumlah card:', cards.length);
    
    cards.forEach(card => {
        const tanggalMulai = new Date(card.dataset.mulai);
        console.log('Waktu sekarang:', now);
        console.log('Waktu mulai:', tanggalMulai);
        console.log('Sudah lewat?', now >= tanggalMulai);
        
        if (now >= tanggalMulai) {
            console.log('Trigger reload!');
            setTimeout(() => window.location.reload(), 2000);
        }
    });
}
checkLelangMendatang();

function toggleDropdown() {
    const menu    = document.getElementById('dropdownMenu');
    const chevron = document.getElementById('dropdownChevron');
    const search  = document.getElementById('searchSatker');

    const isHidden = menu.classList.contains('hidden');
    menu.classList.toggle('hidden');
    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';

    // Focus search saat buka
    if (isHidden) {
        setTimeout(() => search.focus(), 100);
    } else {
        clearSearch();
    }
}

function filterSatker(keyword) {
    const q       = keyword.toLowerCase().trim();
    const options = document.querySelectorAll('.satker-option');
    const empty   = document.getElementById('emptySearch');
    const clearBtn = document.getElementById('btnClearSearch');

    clearBtn.classList.toggle('hidden', q === '');

    let visibleCount = 0;

    options.forEach(opt => {
        const name = opt.dataset.name || '';
        if (name.includes(q) || q === '') {
            opt.style.display = '';
            visibleCount++;
        } else {
            opt.style.display = 'none';
        }
    });

    empty.classList.toggle('hidden', visibleCount > 0);
}

function clearSearch() {
    const input = document.getElementById('searchSatker');
    input.value = '';
    filterSatker('');
    input.focus();
}

function selectSatker(id, nama) {
    document.getElementById('dropdownLabel').textContent = nama;
    document.getElementById('dropdownMenu').classList.add('hidden');
    document.getElementById('dropdownChevron').style.transform = 'rotate(0deg)';
    clearSearch();
    filterBySatker(id);
}

// Tutup dropdown jika klik di luar
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('customDropdown');
    if (!dropdown.contains(e.target)) {
        document.getElementById('dropdownMenu').classList.add('hidden');
        document.getElementById('dropdownChevron').style.transform = 'rotate(0deg)';
        clearSearch();
    }
});
</script>

@endsection