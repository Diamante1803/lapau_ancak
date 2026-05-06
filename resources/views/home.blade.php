@extends('layouts.public')

@section('content')

{{-- ===== HERO ===== --}}
<section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">

        <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-1 text-sm mb-4">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-ping inline-block"></span>
            <span>{{ $stats['aktif'] }} Lelang sedang berlangsung</span>
        </div>

        <h1 class="text-4xl md:text-5xl font-bold mb-3 tracking-tight">
            ⚖️ LAPAU ANCAK
        </h1>
        <p class="text-blue-200 text-lg mb-8">
            Platform Resmi Lelang Barang Rampasan Negara
        </p>

        {{-- PILIH SATKER --}}
        <div class="max-w-xl mx-auto">
            <label class="block text-sm text-blue-200 mb-2">Pilih Satker Penjual</label>
            <div class="flex gap-2">
                <select id="filterSatker" onchange="filterBySatker(this.value)"
                    class="flex-1 rounded-xl px-4 py-3 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Semua Satker --</option>
                    @foreach($satkers as $satker)
                        <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                    @endforeach
                </select>
                <button onclick="filterBySatker(document.getElementById('filterSatker').value)"
                    class="bg-yellow-400 hover:bg-yellow-300 text-blue-900 font-bold px-5 py-3 rounded-xl transition">
                    Cari
                </button>
            </div>
        </div>

    </div>
</section>

{{-- ===== STAT CARDS ===== --}}
<section class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="grid grid-cols-3 gap-4">

            <div class="text-center p-4 rounded-xl bg-blue-50 border border-blue-100">
                <div class="text-3xl font-bold text-blue-700">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500 mt-1">Total Lelang</div>
            </div>

            <div class="text-center p-4 rounded-xl bg-green-50 border border-green-100">
                <div class="text-3xl font-bold text-green-600">{{ $stats['aktif'] }}</div>
                <div class="text-sm text-gray-500 mt-1 flex items-center justify-content gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block animate-ping"></span>
                    Lelang Aktif
                </div>
            </div>

            <div class="text-center p-4 rounded-xl bg-red-50 border border-red-100">
                <div class="text-3xl font-bold text-red-500">{{ $stats['terjual'] }}</div>
                <div class="text-sm text-gray-500 mt-1">Barang Terjual</div>
            </div>

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
                 data-satker="{{ $satker->id }}">

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
                        <div class="font-mono font-bold text-blue-800 text-lg countdown"
                             data-end="{{ $lelang->tanggal_selesai->toIso8601String() }}">
                            --:--:--
                        </div>
                    </div>

                    {{-- TOMBOL PENAWARAN --}}
                    <button onclick="bukaFormPenawaran({{ $lelang->id }}, {{ $lelang->harga_awal }}, {{ $lelang->harga_tertinggi ?? $lelang->harga_awal }})"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-gavel"></i>
                        Ajukan Penawaran
                    </button>

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

{{-- ===== MODAL PENAWARAN ===== --}}
<div id="modalPenawaran" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
    style="background: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-800 to-blue-700 px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-bold text-lg">
                <i class="fas fa-gavel mr-2 text-yellow-300"></i>Ajukan Penawaran
            </h3>
            <button onclick="tutupModal()"
                class="text-white/70 hover:text-white text-xl transition">✕</button>
        </div>

        <div class="p-6">

            {{-- Info harga --}}
            <div class="bg-blue-50 rounded-xl p-4 mb-5">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Harga Awal</span>
                    <span class="font-bold text-blue-700" id="modalHargaAwal">-</span>
                </div>
                <div class="flex justify-between text-sm mt-1">
                    <span class="text-gray-500">Penawaran Tertinggi</span>
                    <span class="font-bold text-green-600" id="modalHargaTertinggi">-</span>
                </div>
                <div class="flex justify-between text-sm mt-1 pt-2 border-t border-blue-100">
                    <span class="text-gray-500">Minimal Penawaran</span>
                    <span class="font-bold text-red-500" id="modalMinimal">-</span>
                </div>
            </div>

            {{-- Step 1: Input data pembeli --}}
            <div id="step1">
                <p class="text-sm text-gray-500 mb-4">
                    Masukkan data Anda untuk verifikasi sebelum melakukan penawaran.
                </p>

                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600 block mb-1">Nama Lengkap</label>
                        <input type="text" id="inputNama"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Nama lengkap Anda">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600 block mb-1">Nomor HP</label>
                        <input type="text" id="inputNoHp"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600 block mb-1">Email</label>
                        <input type="email" id="inputEmail"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="email@contoh.com">
                    </div>
                </div>

                <div id="errorStep1" class="text-red-500 text-xs mt-2 hidden"></div>

                <button onclick="kirimMagicLink()"
                    class="w-full mt-4 bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-xl transition">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Link Verifikasi
                </button>
            </div>

            {{-- Step 2: Input penawaran (setelah verifikasi) --}}
            <div id="step2" class="hidden">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-check text-green-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600">Identitas terverifikasi</p>
                    <p class="font-bold text-gray-800" id="namaVerifikasi"></p>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-600 block mb-1">Nilai Penawaran (Rp)</label>
                    <input type="number" id="inputPenawaran"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan nominal">
                    <p class="text-xs text-gray-400 mt-1">
                        Minimal naik <strong>Rp 10.000</strong> dari penawaran tertinggi
                    </p>
                </div>

                <div id="errorStep2" class="text-red-500 text-xs mt-2 hidden"></div>

                <button onclick="submitPenawaran()"
                    class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition">
                    <i class="fas fa-gavel mr-2"></i>Kirim Penawaran
                </button>
            </div>

        </div>
    </div>
</div>

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
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        const end  = new Date(el.dataset.end).getTime();
        const now  = new Date().getTime();
        const diff = end - now;

        if (diff <= 0) {
            el.textContent = 'Selesai';
            el.classList.add('text-red-500');
            return;
        }

        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        el.textContent = 
            String(h).padStart(2,'0') + ':' +
            String(m).padStart(2,'0') + ':' +
            String(s).padStart(2,'0');
    });
}
setInterval(updateCountdowns, 1000);
updateCountdowns();

// ===== FILTER SATKER =====
function filterBySatker(satkerId) {
    document.querySelectorAll('#gridLelang > div[data-satker]').forEach(card => {
        if (!satkerId || card.dataset.satker === satkerId) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// ===== MODAL PENAWARAN =====
let activeLelangId   = null;
let activeHargaMin   = 0;

function bukaFormPenawaran(lelangId, hargaAwal, hargaTertinggi) {
    activeLelangId = lelangId;
    activeHargaMin = hargaTertinggi + 10000;

    document.getElementById('modalHargaAwal').textContent      = 'Rp ' + hargaAwal.toLocaleString('id-ID');
    document.getElementById('modalHargaTertinggi').textContent  = hargaTertinggi > hargaAwal
        ? 'Rp ' + hargaTertinggi.toLocaleString('id-ID')
        : 'Belum ada';
    document.getElementById('modalMinimal').textContent         = 'Rp ' + activeHargaMin.toLocaleString('id-ID');
    document.getElementById('inputPenawaran').min               = activeHargaMin;

    // Cek apakah sudah pernah verifikasi hari ini
    const verified = localStorage.getItem('verified_pembeli');
    if (verified) {
        const data = JSON.parse(verified);
        document.getElementById('namaVerifikasi').textContent = data.nama;
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
    } else {
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('step2').classList.add('hidden');
    }

    const modal = document.getElementById('modalPenawaran');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function tutupModal() {
    const modal = document.getElementById('modalPenawaran');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Klik luar modal untuk tutup
document.getElementById('modalPenawaran').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});

async function kirimMagicLink() {
    const nama  = document.getElementById('inputNama').value.trim();
    const noHp  = document.getElementById('inputNoHp').value.trim();
    const email = document.getElementById('inputEmail').value.trim();
    const err   = document.getElementById('errorStep1');

    if (!nama || !noHp || !email) {
        err.textContent = 'Semua field wajib diisi.';
        err.classList.remove('hidden');
        return;
    }

    err.classList.add('hidden');

    // TODO: Kirim ke route magic link
    // Sementara simulasi verifikasi langsung
    localStorage.setItem('verified_pembeli', JSON.stringify({ nama, noHp, email }));
    document.getElementById('namaVerifikasi').textContent = nama;
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
}

async function submitPenawaran() {
    const nilai = parseInt(document.getElementById('inputPenawaran').value);
    const err   = document.getElementById('errorStep2');

    if (!nilai || nilai < activeHargaMin) {
        err.textContent = 'Penawaran minimal Rp ' + activeHargaMin.toLocaleString('id-ID');
        err.classList.remove('hidden');
        return;
    }

    err.classList.add('hidden');

    // TODO: Kirim ke route penawaran
    alert('Penawaran Rp ' + nilai.toLocaleString('id-ID') + ' berhasil dikirim!');
    tutupModal();
}
</script>

@endsection