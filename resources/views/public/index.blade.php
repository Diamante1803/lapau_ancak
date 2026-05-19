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

        {{-- SEARCH BAR --}}
        <div class="max-w-xl mx-auto">
            <label class="block text-sm text-blue-200 mb-2 text-center">
                <i class="fas fa-search mr-1"></i> Cari Barang Lelang
            </label>
            <div class="relative flex items-center bg-white rounded-2xl shadow-2xl shadow-black/20 overflow-hidden">
                <i class="fas fa-search text-gray-400 ml-4 text-lg"></i>
                <input type="text" id="searchBarang"
                    oninput="searchByNama(this.value)"
                    placeholder="Cari nama barang..."
                    class="flex-1 px-4 py-4 text-gray-700 placeholder-gray-400 outline-none text-base">
                <button type="button" onclick="clearSearchBarang()"
                    id="btnClearBarang"
                    class="hidden mr-2 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
                <button type="button"
                    class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-4 font-semibold transition-all mr-2 rounded-xl text-sm">
                    Cari
                </button>
            </div>
        </div>

    </div>
</section>

{{-- ===== FILTER BAR ===== --}}
<section class="bg-white border-b sticky top-0 z-40 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">

            {{-- Filter Tabs --}}
            <div class="flex items-center gap-2 flex-wrap">
                <button onclick="setFilter('all')" id="filter-all"
                    class="filter-btn active flex items-center gap-1.5 px-4 py-2 rounded-xl font-medium text-sm transition-all duration-200 bg-blue-600 text-white shadow-lg shadow-blue-500/30">
                    <i class="fas fa-th-large text-xs"></i> Semua
                </button>
                <button onclick="setFilter('live')" id="filter-live"
                    class="filter-btn flex items-center gap-1.5 px-4 py-2 rounded-xl font-medium text-sm transition-all duration-200 bg-white text-gray-600 hover:bg-gray-50 border border-gray-200">
                    <i class="fas fa-fire text-xs text-red-500"></i> Sedang Live
                </button>
                <button onclick="setFilter('ending_soon')" id="filter-ending"
                    class="filter-btn flex items-center gap-1.5 px-4 py-2 rounded-xl font-medium text-sm transition-all duration-200 bg-white text-gray-600 hover:bg-gray-50 border border-gray-200">
                    <i class="fas fa-clock text-xs text-orange-500"></i> Segera Berakhir
                </button>
                <button onclick="setFilter('popular')" id="filter-popular"
                    class="filter-btn flex items-center gap-1.5 px-4 py-2 rounded-xl font-medium text-sm transition-all duration-200 bg-white text-gray-600 hover:bg-gray-50 border border-gray-200">
                    <i class="fas fa-chart-line text-xs text-green-500"></i> Populer
                </button>
            </div>

            {{-- Kanan: Filter Satker + View Mode --}}
            <div class="flex items-center gap-2">

                {{-- Filter Satker --}}
                <div class="relative" id="filterSatkerDropdown">
                    <button onclick="toggleFilterSatker()"
                        class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                        <i class="fas fa-sliders-h text-xs"></i>
                        <span id="filterSatkerLabel">Filter Satker</span>
                        <i class="fas fa-chevron-down text-xs" id="filterSatkerChevron"></i>
                    </button>
                    <div id="filterSatkerMenu"
                        class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                        <div class="px-3 py-2 bg-gray-50 border-b">
                            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-1.5">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                                <input type="text" id="filterSatkerSearch"
                                    oninput="filterSatkerList(this.value)"
                                    placeholder="Cari satker..."
                                    class="w-full text-sm outline-none"
                                    onclick="event.stopPropagation()">
                            </div>
                        </div>
                        <div style="max-height:220px;overflow-y:auto;">
                            <div onclick="selectFilterSatker('', 'Filter Satker')"
                                class="satker-filter-opt px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-sm text-gray-700 flex items-center gap-2 border-b border-gray-50"
                                data-name="semua">
                                <i class="fas fa-globe text-blue-400 text-xs"></i> Semua Satker
                            </div>
                            @foreach($satkers as $satker)
                            <div onclick="selectFilterSatker('{{ $satker->id }}', '{{ $satker->nama_satker }}')"
                                class="satker-filter-opt px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-sm text-gray-700 flex items-center gap-2 border-b border-gray-50"
                                data-name="{{ strtolower($satker->nama_satker) }}">
                                <i class="fas fa-building text-gray-400 text-xs"></i>
                                {{ $satker->nama_satker }}
                            </div>
                            @endforeach
                            <div id="filterSatkerEmpty" class="hidden px-4 py-4 text-center text-gray-400 text-xs">
                                Satker tidak ditemukan
                            </div>
                        </div>
                    </div>
                </div>

                {{-- View Mode Toggle --}}
                <div class="flex bg-white border border-gray-200 rounded-xl p-1">
                    <button onclick="setViewMode('grid')" id="btn-grid"
                        class="p-2 rounded-lg transition-colors bg-blue-100 text-blue-600"
                        title="Tampilan Grid">
                        <i class="fas fa-th-large text-xs"></i>
                    </button>
                    <button onclick="setViewMode('list')" id="btn-list"
                        class="p-2 rounded-lg transition-colors text-gray-400"
                        title="Tampilan List">
                        <i class="fas fa-list text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Result Count --}}
        <p class="text-xs text-gray-400 mt-2" id="resultCount">
            Menampilkan <span id="countVisible" class="font-semibold text-gray-600">0</span> lelang
        </p>
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
            <div id="carouselControlsAktif" class="flex gap-2">
                <button onclick="slideCarousel('aktif', -1)" class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" id="btnPrevAktif">
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
                <button onclick="slideCarousel('aktif', 1)" class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" id="btnNextAktif">
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="relative">
            <div id="carouselAktif" class="overflow-hidden">
                <div id="gridLelang" class="flex gap-6 transition-transform duration-500">
            @forelse($lelangsAktif as $lelang)
            @php
                $barang       = $lelang->barang;
                $satker       = $barang->perkara->pengajuan->satker;
                $fotos        = $barang->fotoBarang;
                $hargaId      = 'harga-' . $lelang->id;
                $jumlahBid    = $lelang->penawarans->count();
                $sisaDetik    = now()->diffInSeconds($lelang->tanggal_selesai, false);
                $isEndingSoon = $sisaDetik > 0 && $sisaDetik <= 86400; // < 24 jam
            @endphp

            <div class="lelang-card bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                data-satker="{{ $satker->id }}"
                data-lelang-id="{{ $lelang->id }}"
                data-end="{{ $lelang->tanggal_selesai->toIso8601String() }}"
                data-bids="{{ $jumlahBid }}"
                data-ending-soon="{{ $isEndingSoon ? '1' : '0' }}">

                {{-- ... isi card sama seperti sebelumnya ... --}}
                {{-- SLIDESHOW FOTO --}}
                <div class="relative h-48 bg-gray-100 overflow-hidden card-image">
                    @if($fotos->count() > 0)
                        @foreach($fotos as $fIdx => $foto)
                        <div class="pub-slide-{{ $lelang->id }} absolute inset-0 transition-opacity duration-300"
                            style="opacity: {{ $fIdx == 0 ? '1' : '0' }}; z-index: {{ $fIdx == 0 ? '1' : '0' }}">
                            <img src="{{ asset('storage/' . $foto->file_path) }}" class="w-full h-full object-cover">
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
                        @endif
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <i class="fas fa-image text-4xl"></i>
                        </div>
                    @endif

                    <div class="absolute top-3 left-3 z-10" id="badge-live-{{ $lelang->id }}">
                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></span>
                            LIVE
                        </span>
                    </div>
                    <div class="absolute top-3 right-3 z-10">
                        <span class="bg-blue-900/70 text-white text-xs px-2 py-1 rounded-lg">{{ $satker->nama_satker }}</span>
                    </div>
                    @if($isEndingSoon)
                    <div class="absolute bottom-3 left-3 z-10">
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse">
                            🔥 Segera Berakhir
                        </span>
                    </div>
                    @endif
                </div>

                <div class="p-4 card-body">
                    <h3 class="font-bold text-gray-800 text-base mb-1">{{ $barang->nama_barang }}</h3>
                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ $barang->deskripsi ?? 'Tidak ada deskripsi' }}</p>

                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <div class="text-xs text-gray-400">Harga Awal</div>
                            <div class="font-bold text-blue-700">Rp {{ number_format($lelang->harga_awal, 0, ',', '.') }}</div>
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

                    {{-- Jumlah bid --}}
                    <div class="text-xs text-gray-400 mb-3">
                        <i class="fas fa-gavel mr-1"></i>
                        <span class="font-semibold text-gray-600">{{ $jumlahBid }}</span> penawaran masuk
                    </div>

                    <div class="bg-blue-50 rounded-xl px-3 py-2 mb-4 text-center">
                        <div class="text-xs text-blue-500 mb-1">⏱ Waktu Tersisa</div>
                        <div class="flex justify-center items-end mt-1 gap-2 countdown"
                            data-end="{{ $lelang->tanggal_selesai->toIso8601String() }}">
                            @foreach([['id'=>'cd-hari','label'=>'Hari'],['id'=>'cd-jam','label'=>'Jam'],['id'=>'cd-menit','label'=>'Menit'],['id'=>'cd-detik','label'=>'Detik']] as $unit)
                            <div class="text-center">
                                <div id="{{ $unit['id'] }}-{{ $lelang->id }}" class="font-bold"
                                    style="font-size:1.6rem;color:#1d4ed8;line-height:1;min-width:36px;letter-spacing:1px;">00</div>
                                <div style="font-size:0.65rem;color:#6b7280;text-transform:uppercase;letter-spacing:1px;">{{ $unit['label'] }}</div>
                            </div>
                            @if(!$loop->last)
                            <div class="font-bold pb-4" style="color:#1d4ed8;font-size:1.2rem;">:</div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <div id="tombol-{{ $lelang->id }}">
                        <a href="{{ route('public.detail', $lelang->id) }}"
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-16 text-gray-400" id="emptyLelang">
                <i class="fas fa-gavel text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">Belum ada lelang yang berlangsung saat ini</p>
            </div>
            @endforelse
                </div>
            </div>
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
            <div id="carouselControlsMendatang" class="flex gap-2">
                <button onclick="slideCarousel('mendatang', -1)" class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" id="btnPrevMendatang">
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
                <button onclick="slideCarousel('mendatang', 1)" class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" id="btnNextMendatang">
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
            </div>
        </div>

        <div class="relative">
            <div id="carouselMendatang" class="overflow-hidden">
                <div class="flex gap-6 transition-transform duration-500" id="gridMendatang">

            @foreach($lelangsMendatang as $lelang)
            @php
                $barang = $lelang->barang;
                $satker = $barang->perkara->pengajuan->satker;
                $fotos  = $barang->fotoBarang;
            @endphp

            <div class="lelang-card bg-white rounded-2xl shadow-md overflow-hidden..."
                data-satker="{{ $satker->id }}"
                data-lelang-id="{{ $lelang->id }}"
                data-end="{{ $lelang->tanggal_selesai->toIso8601String() }}"
                data-bids="{{ $lelang->penawarans->count() }}"
                data-ending-soon="{{ now()->diffInSeconds($lelang->tanggal_selesai, false) <= 86400 ? '1' : '0' }}">

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

                    {{-- Countdown Akan Dibuka --}}
                    <div class="bg-yellow-50 rounded-xl px-3 py-2 mb-4 text-center border border-yellow-100">

                        <div class="text-xs text-yellow-600 mb-1">
                            🗓 Dibuka Dalam
                        </div>

                        <div class="countdown-start flex justify-center items-end mt-1 gap-2"
                            data-start="{{ $lelang->tanggal_mulai->toIso8601String() }}">

                            @foreach([
                                ['id'=>'start-hari','label'=>'Hari'],
                                ['id'=>'start-jam','label'=>'Jam'],
                                ['id'=>'start-menit','label'=>'Menit'],
                                ['id'=>'start-detik','label'=>'Detik']
                            ] as $unit)

                            <div class="text-center">
                                <div id="{{ $unit['id'] }}-{{ $lelang->id }}"
                                    class="font-bold"
                                    style="font-size: 1.3rem; color: #ca8a04; line-height: 1; min-width: 32px;">
                                    00
                                </div>

                                <div style="font-size: 0.65rem; color: #6b7280;">
                                    {{ $unit['label'] }}
                                </div>
                            </div>

                            @if(!$loop->last)
                            <div class="font-bold pb-4"
                                style="color:#ca8a04; font-size:1rem;">
                                :
                            </div>
                            @endif

                            @endforeach

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
    </div>
</section>
@endif
<style>
    html { scroll-behavior: smooth; }

    /* ── CAROUSEL ── */
    #gridLelang, #gridMendatang {
        scroll-behavior: smooth;
    }

    #carouselAktif, #carouselMendatang {
        cursor: grab;
        user-select: none;
    }

    #carouselAktif.grabbing, #carouselMendatang.grabbing {
        cursor: grabbing;
    }

    #gridLelang .lelang-card,
    #gridMendatang .lelang-card {
        flex-shrink: 0;
        width: 320px;
    }

    /* ── LIST MODE ── */
    #gridLelang.list-mode,
    #gridMendatang.list-mode {
        flex-direction: column;
    }
    #gridLelang.list-mode .lelang-card,
    #gridMendatang.list-mode .lelang-card {
        width: 100%;
        display: flex;
        flex-direction: row;
    }
    #gridLelang.list-mode .card-image,
    #gridMendatang.list-mode .card-image {
        width: 200px;
        min-width: 200px;
        height: auto !important;
    }
    #gridLelang.list-mode .card-body,
    #gridMendatang.list-mode .card-body {
        flex: 1;
    }

    /* ── FILTER BTN ACTIVE ── */
    .filter-btn.active {
        background: #2563eb !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(37,99,235,0.3);
        border-color: transparent !important;
    }

    /* ── FILTER SATKER DROPDOWN ── */
    #filterSatkerMenu { animation: fadeDown 0.15s ease; }
    @keyframes fadeDown {
        from { opacity:0; transform:translateY(-6px); }
        to   { opacity:1; transform:translateY(0); }
    }

    @media (max-width: 640px) {
        #gridLelang .lelang-card,
        #gridMendatang .lelang-card {
            width: 280px;
        }

        #gridLelang.list-mode .card-image { width: 120px; min-width: 120px; }
    }
</style>

<script>
// ===== CAROUSEL =====
const carouselState = {
    aktif: { current: 0, perView: 3, total: 0 },
    mendatang: { current: 0, perView: 3, total: 0 }
};

const CARD_WIDTH = 320;
const GAP = 24;
const SWIPE_THRESHOLD = 50;

let touchState = {
    startX: 0,
    startY: 0,
    isDragging: false,
    activeCarousel: null
};

function updateCarouselState() {
    const getVisibleCount = (gridId) => {
        const grid = document.getElementById(gridId);
        if (!grid) return 0;
        return grid.querySelectorAll('.lelang-card:not([style*="display: none"])').length;
    };

    carouselState.aktif.total = getVisibleCount('gridLelang');
    carouselState.mendatang.total = getVisibleCount('gridMendatang');

    const width = window.innerWidth;
    if (width < 768) {
        carouselState.aktif.perView = 1;
        carouselState.mendatang.perView = 1;
    } else if (width < 1024) {
        carouselState.aktif.perView = 2;
        carouselState.mendatang.perView = 2;
    } else {
        carouselState.aktif.perView = 3;
        carouselState.mendatang.perView = 3;
    }

    carouselState.aktif.current = 0;
    carouselState.mendatang.current = 0;
    updateCarouselButtons();
}

function slideCarousel(type, direction) {
    const state = carouselState[type];
    const maxScroll = Math.max(0, state.total - state.perView);

    state.current += direction;
    state.current = Math.max(0, Math.min(state.current, maxScroll));

    const grid = type === 'aktif' ? document.getElementById('gridLelang') : document.getElementById('gridMendatang');

    // Hitung offset berdasarkan card visible
    let offset = 0;
    let visibleCount = 0;
    const cards = grid.querySelectorAll('.lelang-card');

    for (let i = 0; i < cards.length && visibleCount < state.current; i++) {
        if (cards[i].style.display !== 'none') {
            offset += CARD_WIDTH + GAP;
            visibleCount++;
        }
    }

    grid.style.transform = `translateX(-${offset}px)`;
    updateCarouselButtons();
}

function updateCarouselButtons() {
    const updateBtn = (type, prevBtnId, nextBtnId) => {
        const state = carouselState[type];
        const maxScroll = Math.max(0, state.total - state.perView);

        const prevBtn = document.getElementById(prevBtnId);
        const nextBtn = document.getElementById(nextBtnId);

        if (prevBtn) prevBtn.disabled = state.current === 0;
        if (nextBtn) nextBtn.disabled = state.current >= maxScroll;
    };

    updateBtn('aktif', 'btnPrevAktif', 'btnNextAktif');
    updateBtn('mendatang', 'btnPrevMendatang', 'btnNextMendatang');
}

window.addEventListener('resize', () => {
    updateCarouselState();
});

// ===== TOUCH SWIPE =====
function initTouchSwipe() {
    const carouselAktif = document.getElementById('carouselAktif');
    const carouselMendatang = document.getElementById('carouselMendatang');

    if (carouselAktif) {
        carouselAktif.addEventListener('touchstart', handleTouchStart, false);
        carouselAktif.addEventListener('touchmove', handleTouchMove, false);
        carouselAktif.addEventListener('touchend', handleTouchEnd, false);
    }

    if (carouselMendatang) {
        carouselMendatang.addEventListener('touchstart', handleTouchStart, false);
        carouselMendatang.addEventListener('touchmove', handleTouchMove, false);
        carouselMendatang.addEventListener('touchend', handleTouchEnd, false);
    }
}

function handleTouchStart(e) {
    const carousel = e.currentTarget;
    const carouselType = carousel.id === 'carouselAktif' ? 'aktif' : 'mendatang';

    touchState.startX = e.touches[0].clientX;
    touchState.startY = e.touches[0].clientY;
    touchState.isDragging = true;
    touchState.activeCarousel = carouselType;

    carousel.classList.add('grabbing');
}

function handleTouchMove(e) {
    if (!touchState.isDragging) return;

    // Optional: bisa tambahkan visual feedback di sini
    // e.preventDefault();
}

function handleTouchEnd(e) {
    const carousel = e.currentTarget;
    carousel.classList.remove('grabbing');

    if (!touchState.isDragging) return;

    const endX = e.changedTouches[0].clientX;
    const endY = e.changedTouches[0].clientY;
    const diffX = touchState.startX - endX;
    const diffY = Math.abs(touchState.startY - endY);

    touchState.isDragging = false;

    // Ensure swipe is horizontal (not vertical scroll)
    if (Math.abs(diffX) > SWIPE_THRESHOLD && diffY < 50) {
        if (diffX > 0) {
            // Swipe left → next
            slideCarousel(touchState.activeCarousel, 1);
        } else {
            // Swipe right → prev
            slideCarousel(touchState.activeCarousel, -1);
        }
    }

    touchState.activeCarousel = null;
}

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

// ===== COUNTDOWN LELANG AKTIF =====
const GRACE_PERIOD_DAYS = 2;

function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        const end  = new Date(el.dataset.end).getTime();
        const now  = new Date().getTime();
        const diff = end - now;

        const hariEl  = el.querySelector('[id^="cd-hari-"]');
        const jamEl   = el.querySelector('[id^="cd-jam-"]');
        const menitEl = el.querySelector('[id^="cd-menit-"]');
        const detikEl = el.querySelector('[id^="cd-detik-"]');

        if (!hariEl) return;

        const lelangId = hariEl.id.replace('cd-hari-', '');
        const tombolEl = document.getElementById('tombol-' + lelangId);
        const cardEl   = el.closest('[data-lelang-id]');

        if (diff <= 0) {
            [hariEl, jamEl, menitEl, detikEl].forEach(e => {
                if (e) { e.textContent = '00'; e.style.color = '#ef4444'; }
            });

            const expiredDays = Math.abs(diff) / 86400000;
            if (expiredDays > GRACE_PERIOD_DAYS) {
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

            const badgeLive = cardEl?.querySelector('.bg-green-500');
            if (badgeLive && badgeLive.textContent.includes('LIVE')) {
                badgeLive.classList.replace('bg-green-500', 'bg-gray-400');
                badgeLive.innerHTML = `<span class="w-1.5 h-1.5 bg-white rounded-full"></span> SELESAI`;
            }

            const badgeEndingSoon = cardEl?.querySelector('.bg-red-500.animate-pulse');
            if (badgeEndingSoon) {
                badgeEndingSoon.parentElement.remove();
            }

            const countdownBox = el.closest('.bg-blue-50');
            if (countdownBox && !countdownBox.dataset.changed) {
                countdownBox.dataset.changed = '1';
                countdownBox.classList.replace('bg-blue-50', 'bg-gray-50');
                const label = countdownBox.querySelector('.text-blue-500');
                if (label) {
                    label.classList.replace('text-blue-500', 'text-gray-400');
                    label.textContent = '⏱ Lelang Berakhir';
                }
            }

            return;
        }

        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        hariEl.textContent  = String(d).padStart(2, '0');
        jamEl.textContent   = String(h).padStart(2, '0');
        menitEl.textContent = String(m).padStart(2, '0');
        detikEl.textContent = String(s).padStart(2, '0');

        const urgent = diff < 3600000;
        [hariEl, jamEl, menitEl, detikEl].forEach(e => {
            if (e) e.style.color = urgent ? '#ef4444' : '#1d4ed8';
        });
    });
}

setInterval(updateCountdowns, 1000);
updateCountdowns();

// ===== COUNTDOWN LELANG MENDATANG =====
function updateStartCountdowns() {
    document.querySelectorAll('.countdown-start').forEach(el => {
        const start = new Date(el.dataset.start).getTime();
        const now   = new Date().getTime();
        const diff  = start - now;

        const hariEl  = el.querySelector('[id^="start-hari-"]');
        const jamEl   = el.querySelector('[id^="start-jam-"]');
        const menitEl = el.querySelector('[id^="start-menit-"]');
        const detikEl = el.querySelector('[id^="start-detik-"]');

        if (!hariEl) return;

        if (diff <= 0) {
            [hariEl, jamEl, menitEl, detikEl].forEach(e => {
                if (e) e.textContent = '00';
            });

            const flagKey = 'reloaded_start_' + el.dataset.start;
            if (!localStorage.getItem(flagKey)) {
                localStorage.setItem(flagKey, '1');
                setTimeout(() => window.location.reload(), 1500);
            }

            return;
        }

        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        hariEl.textContent  = String(d).padStart(2, '0');
        jamEl.textContent   = String(h).padStart(2, '0');
        menitEl.textContent = String(m).padStart(2, '0');
        detikEl.textContent = String(s).padStart(2, '0');
    });
}

setInterval(updateStartCountdowns, 1000);
updateStartCountdowns();

// ===== SEARCH NAMA BARANG =====
let activeSearch = '';

function searchByNama(keyword) {
    activeSearch = keyword.toLowerCase().trim();
    document.getElementById('btnClearBarang').classList.toggle('hidden', activeSearch === '');
    applyFilters();
}

function clearSearchBarang() {
    document.getElementById('searchBarang').value = '';
    activeSearch = '';
    document.getElementById('btnClearBarang').classList.add('hidden');
    applyFilters();
}

// ===== FILTER TAB & VIEW MODE =====
let activeFilter = 'all';
let activeSatker = '';
let activeView   = 'grid';

function setFilter(filter) {
    activeFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-lg');
        btn.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200');
    });
    const activeBtn = document.getElementById('filter-' + filter);
    if (activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200');
    }
    applyFilters();
}

function setViewMode(mode) {
    activeView = mode;
    const gridEl  = document.getElementById('gridLelang');
    const btnGrid = document.getElementById('btn-grid');
    const btnList = document.getElementById('btn-list');

    if (mode === 'list') {
        gridEl.classList.add('list-mode');
        btnList.classList.add('bg-blue-100', 'text-blue-600');
        btnList.classList.remove('text-gray-400');
        btnGrid.classList.remove('bg-blue-100', 'text-blue-600');
        btnGrid.classList.add('text-gray-400');
    } else {
        gridEl.classList.remove('list-mode');
        btnGrid.classList.add('bg-blue-100', 'text-blue-600');
        btnGrid.classList.remove('text-gray-400');
        btnList.classList.remove('bg-blue-100', 'text-blue-600');
        btnList.classList.add('text-gray-400');
    }
}

function applyFilters() {
    const cards = document.querySelectorAll('#gridLelang .lelang-card');
    let visible = 0;

    cards.forEach(card => {
        const satkerId   = card.dataset.satker;
        const bids       = parseInt(card.dataset.bids) || 0;
        const endingSoon = card.dataset.endingSoon === '1';
        const end        = new Date(card.dataset.end).getTime();
        const isExpired  = end - Date.now() <= 0;
        const namaBarang = card.querySelector('h3')?.textContent?.toLowerCase() ?? '';
        const deskripsi  = card.querySelector('p')?.textContent?.toLowerCase() ?? '';

        let show = true;

        if (activeSearch && !namaBarang.includes(activeSearch) && !deskripsi.includes(activeSearch)) show = false;
        if (show && activeSatker && satkerId !== activeSatker) show = false;
        if (show && activeFilter === 'live'        && isExpired)   show = false;
        if (show && activeFilter === 'ending_soon' && !endingSoon) show = false;
        if (show && activeFilter === 'popular'     && bids < 3)    show = false;

        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('countVisible').textContent = visible;
    const empty = document.getElementById('emptyLelang');
    if (empty) empty.style.display = visible === 0 ? '' : 'none';

    // Reset carousel setelah filter
    carouselState.aktif.current = 0;
    const grid = document.getElementById('gridLelang');
    if (grid) grid.style.transform = 'translateX(0)';
    updateCarouselState();
}

// ===== DROPDOWN HERO (SATKER) =====
function toggleDropdown() {
    const menu    = document.getElementById('dropdownMenu');
    const chevron = document.getElementById('dropdownChevron');
    const search  = document.getElementById('searchSatker');
    const isHidden = menu.classList.contains('hidden');
    menu.classList.toggle('hidden');
    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    if (isHidden) setTimeout(() => search.focus(), 100);
    else clearSearch();
}

function filterSatker(keyword) {
    const q        = keyword.toLowerCase().trim();
    const options  = document.querySelectorAll('.satker-option');
    const empty    = document.getElementById('emptySearch');
    const clearBtn = document.getElementById('btnClearSearch');
    clearBtn.classList.toggle('hidden', q === '');
    let visibleCount = 0;
    options.forEach(opt => {
        const match = opt.dataset.name.includes(q) || q === '';
        opt.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });
    empty.classList.toggle('hidden', visibleCount > 0);
}

function clearSearch() {
    const input = document.getElementById('searchSatker');
    if (input) { input.value = ''; filterSatker(''); }
}

function selectSatker(id, nama) {
    document.getElementById('dropdownLabel').textContent = nama;
    document.getElementById('dropdownMenu').classList.add('hidden');
    document.getElementById('dropdownChevron').style.transform = 'rotate(0deg)';
    clearSearch();
    activeSatker = id;
    applyFilters();
    filterBySatker(id);

    const filterLabel = document.getElementById('filterSatkerLabel');
    if (filterLabel) filterLabel.textContent = id ? nama : 'Filter Satker';
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('customDropdown');
    if (dropdown && !dropdown.contains(e.target)) {
        document.getElementById('dropdownMenu').classList.add('hidden');
        document.getElementById('dropdownChevron').style.transform = 'rotate(0deg)';
    }
});

// ===== FILTER SATKER DROPDOWN (FILTER BAR) =====
function toggleFilterSatker() {
    const menu     = document.getElementById('filterSatkerMenu');
    const chevron  = document.getElementById('filterSatkerChevron');
    const isHidden = menu.classList.contains('hidden');
    menu.classList.toggle('hidden');
    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    if (isHidden) setTimeout(() => document.getElementById('filterSatkerSearch').focus(), 100);
}

function filterSatkerList(keyword) {
    const q     = keyword.toLowerCase();
    const opts  = document.querySelectorAll('.satker-filter-opt');
    const empty = document.getElementById('filterSatkerEmpty');
    let count   = 0;
    opts.forEach(opt => {
        const match = opt.dataset.name.includes(q) || q === '';
        opt.style.display = match ? '' : 'none';
        if (match) count++;
    });
    empty.classList.toggle('hidden', count > 0);
}

function selectFilterSatker(id, nama) {
    activeSatker = id;
    document.getElementById('filterSatkerLabel').textContent = id ? nama : 'Filter Satker';
    document.getElementById('filterSatkerMenu').classList.add('hidden');
    document.getElementById('filterSatkerChevron').style.transform = 'rotate(0deg)';
    document.getElementById('filterSatkerSearch').value = '';
    filterSatkerList('');
    applyFilters();

    const heroLabel = document.getElementById('dropdownLabel');
    if (heroLabel) heroLabel.textContent = id ? nama : '— Semua Satker —';
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('filterSatkerDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('filterSatkerMenu').classList.add('hidden');
        document.getElementById('filterSatkerChevron').style.transform = 'rotate(0deg)';
    }
});

// ===== INIT =====
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        updateCarouselState();
        initTouchSwipe();
    }, 100);
    applyFilters();
});
</script>

@endsection