@extends('layouts.public')

@section('content')

@php
    $barang  = $lelang->barang;
    $satker  = $barang->perkara->pengajuan->satker;
    $fotos   = $barang->fotoBarang;
    $hargaTertinggi = $lelang->harga_tertinggi ?? $lelang->harga_awal;
    $minPenawaran   = $hargaTertinggi + 10000;
@endphp

<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- BREADCRUMB --}}
    <div class="text-sm text-gray-400 mb-6 flex items-center gap-2">
        <a href="/" class="hover:text-blue-600 transition">🏠 Beranda</a>
        <span>/</span>
        <span class="text-gray-600">{{ $barang->nama_barang }}</span>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

        {{-- KIRI: FOTO --}}
        <div>

            {{-- Main foto --}}
            <div class="relative bg-gray-100 rounded-2xl overflow-hidden mb-3"
                style="height: 420px;">

                @if($fotos->count() > 0)
                    @foreach($fotos as $fIdx => $foto)
                    <div class="detail-slide absolute inset-0 transition-opacity duration-300"
                        style="opacity: {{ $fIdx == 0 ? '1' : '0' }}; z-index: {{ $fIdx == 0 ? '1' : '0' }}">
                        <img src="{{ asset('storage/' . $foto->file_path) }}"
                            class="w-full h-full object-cover cursor-zoom-in"
                            onclick="zoomFoto('{{ asset('storage/' . $foto->file_path) }}')">
                    </div>
                    @endforeach

                    {{-- Tombol panah --}}
                    @if($fotos->count() > 1)
                    <button onclick="detailSlide(-1, {{ $fotos->count() }})"
                        class="absolute left-3 top-1/2 -translate-y-1/2 z-10 bg-black/40 hover:bg-black/70 text-white w-10 h-10 rounded-full flex items-center justify-center transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="detailSlide(1, {{ $fotos->count() }})"
                        class="absolute right-3 top-1/2 -translate-y-1/2 z-10 bg-black/40 hover:bg-black/70 text-white w-10 h-10 rounded-full flex items-center justify-center transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    {{-- Counter --}}
                    <div class="absolute bottom-3 right-3 z-10 bg-black/50 text-white text-xs px-3 py-1 rounded-full">
                        <span id="detailCounter">1</span>/{{ $fotos->count() }}
                    </div>
                    @endif

                    {{-- Badge Live --}}
                    <div class="absolute top-3 left-3 z-10" id="badge-live">
                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></span>
                            LIVE
                        </span>
                    </div>

                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <div class="text-center">
                            <i class="fas fa-image text-6xl mb-2 block"></i>
                            <span>Belum ada foto</span>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Thumbnail strip --}}
            @if($fotos->count() > 1)
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($fotos as $tIdx => $foto)
                <div class="detail-thumb flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden cursor-pointer border-2 transition"
                    style="border-color: {{ $tIdx == 0 ? '#1d4ed8' : '#e5e7eb' }}"
                    onclick="detailGoTo({{ $tIdx }}, {{ $fotos->count() }})">
                    <img src="{{ asset('storage/' . $foto->file_path) }}"
                        class="w-full h-full object-cover">
                </div>
                @endforeach
            </div>
            @endif

        </div>

        {{-- KANAN: DETAIL --}}
        <div class="flex flex-col">

            {{-- Satker badge --}}
            <div class="mb-3">
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                    🏢 {{ $satker->nama_satker }}
                </span>
            </div>

            {{-- Nama barang --}}
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                {{ $barang->nama_barang }}
            </h1>

            {{-- Deskripsi --}}
            <p class="text-gray-500 text-sm mb-5 leading-relaxed">
                {{ $barang->deskripsi ?? 'Tidak ada deskripsi tersedia.' }}
            </p>

            {{-- Harga box --}}
            <div class="bg-gray-50 rounded-2xl p-4 mb-4 border border-gray-100">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Harga Awal</div>
                        <div class="font-bold text-gray-700">
                            Rp {{ number_format($lelang->harga_awal, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400 mb-1">Penawaran Tertinggi</div>
                        <div class="font-bold text-green-600 text-lg" id="hargaTertinggiDetail">
                            @if($lelang->harga_tertinggi)
                                Rp {{ number_format($lelang->harga_tertinggi, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400 font-normal text-sm">Belum ada</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Minimal penawaran --}}
                <div id="info-min-penawaran" class="bg-yellow-50 border border-yellow-200 rounded-xl px-3 py-2 text-sm flex justify-between">
                    <span class="text-yellow-700">Minimal penawaran berikutnya</span>
                    <span class="font-bold text-yellow-800" id="minPenawaranDetail">
                        Rp {{ number_format($minPenawaran, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Countdown --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-5 text-center">
                <div class="text-xs text-blue-500 mb-2 font-medium">⏱ Waktu Lelang Tersisa</div>
                <div class="font-mono font-bold text-blue-800 countdown flex items-end justify-center gap-2"
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
                <div class="text-xs text-gray-400 mt-2">
                    Berakhir: {{ $lelang->tanggal_selesai->format('d M Y, H:i') }} WIB
                </div>
            </div>

            {{-- TOMBOL AJUKAN PENAWARAN --}}
            <button id="tombol-penawaran" onclick="bukaModal()"
                class="w-full bg-blue-700 hover:bg-blue-800 active:scale-95 text-white font-bold py-4 rounded-2xl transition-all flex items-center justify-center gap-2 text-lg shadow-lg shadow-blue-200">
                <i class="fas fa-gavel"></i>
                Ajukan Penawaran
            </button>

            <p class="text-xs text-gray-400 text-center mt-2">
                Dengan mengajukan penawaran, Anda menyetujui syarat & ketentuan lelang
            </p>

        </div>
    </div>

    {{-- LIST PENAWARAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">
                📋 Riwayat Penawaran
            </h3>
            <span class="text-sm text-gray-400">
                {{ $lelang->penawarans->count() }} penawaran
            </span>
        </div>

        <div id="list-penawaran">

            @if($lelang->penawarans->count() > 0)
            <div class="divide-y divide-gray-50">
            @foreach($lelang->penawarans->sortByDesc('nilai_penawaran') as $penawaran)
                <div class="px-6 py-4 flex items-center justify-between
                    {{ $loop->first ? 'bg-green-50' : '' }}">

                    <div class="flex items-center gap-3">

                        {{-- Rank --}}
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                            {{ $loop->first ? 'bg-yellow-400 text-white' : 'bg-gray-100 text-gray-500' }}">
                            {{ $loop->iteration }}
                        </div>

                        <div>
                            {{-- Nama disembunyikan sebagian untuk privasi --}}
                            @php
                                $nama = $penawaran->pembeli->nama;
                                $namaAman = substr($nama, 0, 2) . str_repeat('*', max(0, strlen($nama) - 4)) . substr($nama, -2);
                            @endphp
                            <div class="font-medium text-gray-700 text-sm">
                                {{ $namaAman }}
                                @if($loop->first)
                                    <span class="ml-1 text-xs bg-yellow-400 text-white px-2 py-0.5 rounded-full">
                                        🏆 Tertinggi
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $penawaran->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <div class="font-bold {{ $loop->first ? 'text-green-600 text-lg' : 'text-gray-600' }}">
                        Rp {{ number_format($penawaran->nilai_penawaran, 0, ',', '.') }}
                    </div>

                </div>
            @endforeach
            </div>

            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                <p>Belum ada penawaran. Jadilah yang pertama!</p>
            </div>
            @endif
        </div>

    </div>

</div>

{{-- ===== MODAL PENAWARAN ===== --}}
<div id="modalPenawaran" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
    style="background: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        <div class="bg-gradient-to-r from-blue-800 to-blue-700 px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-bold text-lg">
                <i class="fas fa-gavel mr-2 text-yellow-300"></i>Ajukan Penawaran
            </h3>
            <button onclick="tutupModal()" class="text-white/70 hover:text-white text-xl">✕</button>
        </div>

        <div class="p-6">

            {{-- Info harga --}}
            <div class="bg-blue-50 rounded-xl p-4 mb-5">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Harga Awal</span>
                    <span class="font-bold text-blue-700">
                        Rp {{ number_format($lelang->harga_awal, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-sm mt-1">
                    <span class="text-gray-500">Penawaran Tertinggi</span>
                    <span class="font-bold text-green-600" id="modalHargaTertinggi">
                        @if($lelang->harga_tertinggi)
                            Rp {{ number_format($lelang->harga_tertinggi, 0, ',', '.') }}
                        @else
                            Belum ada
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm mt-1 pt-2 border-t border-blue-100">
                    <span class="text-gray-500">Minimal Penawaran</span>
                    <span class="font-bold text-red-500">
                        Rp {{ number_format($minPenawaran, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Step 1: Input data --}}
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
                <button id="btnKirimLink" onclick="kirimMagicLink()"
                    class="w-full mt-4 bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-xl transition">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Link Verifikasi
                </button>
            </div>

            {{-- Step 2: Penawaran --}}
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

                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-500">Rp</span>
                        <input type="text" id="displayPenawaran"
                            class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="0"
                            autocomplete="off"
                            oninput="
                                let raw = this.value.replace(/\D/g, '');
                                this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
                                document.getElementById('inputPenawaran').value = raw || '';
                                validateKelipatan(document.getElementById('inputPenawaran'));
                            ">
                        <input type="number" id="inputPenawaran"
                            class="hidden"
                            min="{{ $minPenawaran }}"
                            step="1000">
                    </div>

                    <p class="text-xs text-gray-400 mt-1" id="textMinimalPenawaran">
                        Minimal Rp {{ number_format($minPenawaran, 0, ',', '.') }} • Kelipatan Rp 1.000
                    </p>
                    <p id="msg-kelipatan" class="text-xs text-red-500 mt-1" style="display:none;">
                        <i class="fas fa-exclamation-circle mr-1"></i>Nominal harus kelipatan Rp 1.000
                    </p>
                </div>

                <div id="errorStep2" class="text-red-500 text-xs mt-2 hidden"></div>

                <button id="btnSubmitPenawaran" onclick="submitPenawaran()"
                    class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition">
                    <i class="fas fa-gavel mr-2"></i>Kirim Penawaran
                </button>
            </div>

        </div>
    </div>
</div>

{{-- MODAL ZOOM FOTO --}}
<div id="modalZoom" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
    style="background: rgba(0,0,0,0.85);">
    <img id="zoomImg" src="" class="max-w-full max-h-full rounded-xl object-contain">
    <button onclick="tutupZoom()"
        class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">✕</button>
</div>

<script>
// 1. Variables global
const minPenawaran = {{ $minPenawaran }};
const lelangId     = {{ $lelang->id }};

let pollingInterval = null;
let detailIdx = 0;
let lastUpdate = null;

@if(session('verified_pembeli_id'))
window._verifiedNama    = '{{ session('verified_pembeli_nama') }}';
window._verifiedExpired = '{{ session('verified_expired') }}';
@else
window._verifiedNama    = null;
window._verifiedExpired = null;
@endif

// 2. Fungsi modal
async function bukaModal() {
    const verified  = window._verifiedNama;
    const expired   = window._verifiedExpired;
    const now       = new Date().getTime();
    const sessionOk = verified && expired && now < new Date(expired).getTime();

    if (sessionOk) {
        // Session masih valid — langsung ke step 2
        document.getElementById('namaVerifikasi').textContent = verified;
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
    } else {
        // ✅ Cek email tersimpan di localStorage
        const emailTersimpan = localStorage.getItem('lapau_email');

        if (emailTersimpan) {
            // Cek ke server apakah token masih valid
            try {
                const res  = await fetch(`/pembeli/cek-token?email=${encodeURIComponent(emailTersimpan)}`);
                const data = await res.json();

                if (data.verified) {
                    // Token masih valid — langsung step 2
                    window._verifiedNama    = data.nama;
                    window._verifiedExpired = data.expired;
                    document.getElementById('namaVerifikasi').textContent = data.nama;
                    document.getElementById('step1').classList.add('hidden');
                    document.getElementById('step2').classList.remove('hidden');

                    const m = document.getElementById('modalPenawaran');
                    m.classList.remove('hidden');
                    m.classList.add('flex');
                    return;
                }
            } catch (e) {
                console.log('Cek token gagal:', e);
            }
        }

        // Token tidak valid / tidak ada — tampil step 1
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('step2').classList.add('hidden');
    }

    const m = document.getElementById('modalPenawaran');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function tutupModal() {
    const m = document.getElementById('modalPenawaran');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

// 3. Magic link
async function kirimMagicLink() {
    const nama  = document.getElementById('inputNama').value.trim();
    const noHp  = document.getElementById('inputNoHp').value.trim();
    const email = document.getElementById('inputEmail').value.trim();
    const err   = document.getElementById('errorStep1');
    const btn   = document.getElementById('btnKirimLink');

    if (!nama || !noHp || !email) {
        err.textContent = 'Semua field wajib diisi.';
        err.classList.remove('hidden');
        return;
    }

    err.classList.add('hidden');
    btn.disabled    = true;
    btn.textContent = 'Mengirim...';
    localStorage.setItem('lapau_email', email);

    try {
        const res = await fetch('/lelang/{{ $lelang->id }}/magic-link', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ nama, no_hp: noHp, email })
        });

        const data = await res.json();

        if (data.success) {
            document.getElementById('step1').innerHTML = `
                <div class="text-center py-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-envelope text-blue-500 text-2xl"></i>
                    </div>
                    <p class="font-bold text-gray-800 mb-1">Email Terkirim!</p>
                    <p class="text-sm text-gray-500">${data.message}</p>
                    <p class="text-xs text-gray-400 mt-3">Klik link di email untuk melanjutkan penawaran.</p>
                </div>
            `;
        } else {
            err.textContent = data.message;
            err.classList.remove('hidden');
            btn.disabled    = false;
            btn.innerHTML   = '<i class="fas fa-paper-plane mr-2"></i>Kirim Link Verifikasi';
        }
    } catch (e) {
        err.textContent = 'Terjadi kesalahan. Coba lagi.';
        err.classList.remove('hidden');
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Link Verifikasi';
    }
}

// 4. Submit penawaran
async function submitPenawaran() {

    const input = document.getElementById('inputPenawaran');

    const nilai = parseInt(input.value);

    const err = document.getElementById('errorStep2');
    const btn = document.getElementById('btnSubmitPenawaran');

    // validasi kosong
    if (!nilai) {
        err.textContent = 'Nominal penawaran wajib diisi.';
        err.classList.remove('hidden');
        return;
    }

    // validasi minimal
    if (nilai < minPenawaran) {
        err.textContent = 'Penawaran minimal Rp ' + minPenawaran.toLocaleString('id-ID');
        err.classList.remove('hidden');
        return;
    }

    // validasi kelipatan
    if (nilai % 1000 !== 0) {
        document.getElementById('msg-kelipatan').style.display = 'block';
        input.focus();
        return;
    }

    err.classList.add('hidden');

    btn.disabled = true;
    btn.innerHTML = 'Mengirim...';

    try {

        const res = await fetch('/lelang/{{ $lelang->id }}/bid', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                nilai_penawaran: nilai
            })
        });

        const data = await res.json();

        if (data.success) {

            tutupModal();

            document.getElementById('hargaTertinggiDetail').textContent =
                data.harga_formatted;

            document.getElementById('minPenawaranDetail').textContent =
                'Rp ' + data.min_berikutnya.toLocaleString('id-ID');

            tampilToast('🎉 ' + data.message);

            setTimeout(() => {
                location.reload();
            }, 2000);

        } else if (data.reVerify) {

            tutupModal();

            tampilToast('⚠️ Sesi habis, silakan verifikasi ulang.');

        } else {

            err.textContent = data.message;
            err.classList.remove('hidden');

        }

    } catch (e) {

        console.error(e);

        err.textContent = 'Terjadi kesalahan. Coba lagi.';
        err.classList.remove('hidden');

    }

    btn.disabled = false;
    btn.innerHTML =
        '<i class="fas fa-gavel mr-2"></i>Kirim Penawaran';
}

// 5. Toast
function tampilToast(pesan) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 text-sm font-medium';
    toast.textContent = pesan;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// 6. Countdown
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        const end  = new Date(el.dataset.end).getTime();
        const now  = new Date().getTime();
        const diff = end - now;

        const hariEl   = el.querySelector('[id^="cd-hari-"]');
        const jamEl    = el.querySelector('[id^="cd-jam-"]');
        const menitEl  = el.querySelector('[id^="cd-menit-"]');
        const detikEl  = el.querySelector('[id^="cd-detik-"]');

        const tombol = document.getElementById('tombol-penawaran');

        if (diff <= 0) {
            // Update teks countdown
            el.textContent = 'Lelang Telah Berakhir';
            el.classList.remove('text-blue-800');
            el.classList.add('text-red-500');

            // Update container countdown jadi abu
            const box = el.closest('.bg-blue-50');
            if (box) {
                box.classList.remove('bg-blue-50', 'border-blue-100');
                box.classList.add('bg-gray-50', 'border-gray-100');
                const label = box.querySelector('.text-blue-500');
                if (label) {
                    label.classList.remove('text-blue-500');
                    label.classList.add('text-gray-400');
                    label.textContent = '⏱ Waktu Lelang';
                }
            }

            // Disable tombol penawaran
            if (tombol && !tombol.dataset.disabled) {
                tombol.dataset.disabled = 'true';
                tombol.onclick = null;
                tombol.disabled = true;
                tombol.className = 'w-full bg-gray-200 text-gray-400 font-bold py-4 rounded-2xl flex items-center justify-center gap-2 text-lg cursor-not-allowed';
                tombol.innerHTML = '<i class="fas fa-clock"></i> Lelang Telah Berakhir';
            }

            stopPolling();

            // Sembunyikan info minimal penawaran
            const infoMin = document.getElementById('info-min-penawaran');
            if (infoMin) infoMin.style.display = 'none';

            const badgeLive = document.getElementById('badge-live');
            if (badgeLive && !badgeLive.dataset.updated) {
                badgeLive.dataset.updated = 'true';
                badgeLive.innerHTML = `
                    <span class="bg-gray-400 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                        SELESAI
                    </span>
                `;
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
        if (diff < 3600000) {
            el.classList.remove('text-blue-800');
            el.classList.add('text-red-500');
        }
    });
}
setInterval(updateCountdowns, 1000);
updateCountdowns();

// 7. Slideshow
function detailSlide(arah, total) {
    detailIdx = (detailIdx + arah + total) % total;
    detailUpdateSlide(total);
}

function detailGoTo(index, total) {
    detailIdx = index;
    detailUpdateSlide(total);
}

function detailUpdateSlide(total) {
    document.querySelectorAll('.detail-slide').forEach((s, i) => {
        s.style.opacity = i === detailIdx ? '1' : '0';
        s.style.zIndex  = i === detailIdx ? '1' : '0';
    });
    document.querySelectorAll('.detail-thumb').forEach((t, i) => {
        t.style.borderColor = i === detailIdx ? '#1d4ed8' : '#e5e7eb';
    });
    const counter = document.getElementById('detailCounter');
    if (counter) counter.textContent = detailIdx + 1;
}

// 8. Zoom foto
function zoomFoto(url) {
    document.getElementById('zoomImg').src = url;
    const m = document.getElementById('modalZoom');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function tutupZoom() {
    const m = document.getElementById('modalZoom');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

// 9. Event listeners
document.getElementById('modalPenawaran').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});

document.getElementById('modalZoom').addEventListener('click', function(e) {
    if (e.target === this) tutupZoom();
});

document.addEventListener('DOMContentLoaded', function () {
    @if($lelang->status === 'active')
    startPolling();
    @endif

    @if($lelang->status === 'closed')

    const tombol = document.getElementById('tombol-penawaran');
    if (tombol) {
        tombol.onclick   = null;
        tombol.disabled  = true;
        tombol.className = 'w-full bg-gray-200 text-gray-400 font-bold py-4 rounded-2xl flex items-center justify-center gap-2 text-lg cursor-not-allowed';
        tombol.innerHTML = '<i class="fas fa-clock"></i> Lelang Telah Berakhir';
    }

    const infoMin = document.getElementById('info-min-penawaran');
    if (infoMin) infoMin.style.display = 'none';

    const badgeLive = document.getElementById('badge-live');
    if (badgeLive) {
        badgeLive.innerHTML = `
            <span class="bg-gray-400 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                SELESAI
            </span>
        `;
    }
    @endif
});

document.addEventListener('visibilitychange', () => {

    if (document.hidden) {
        stopPolling();
    } else {
        startPolling();
    }

});

function validateKelipatan(input) {
    const val = parseInt(input.value);
    const msg = document.getElementById('msg-kelipatan');

    if (!val) {
        msg.style.display = 'none';
        return;
    }

    if (val % 1000 !== 0) {
        msg.style.display = 'block';
        input.classList.add('border-red-400', 'focus:ring-red-400');
        input.classList.remove('border-gray-200', 'focus:ring-blue-400');
    } else {
        msg.style.display = 'none';
        input.classList.remove('border-red-400', 'focus:ring-red-400');
        input.classList.add('border-gray-200', 'focus:ring-blue-400');
    }
}
function startPolling() {
    if (pollingInterval) return; // cegah double interval

    pollingInterval = setInterval(fetchPenawaran, 5000);
}

function stopPolling() {
    clearInterval(pollingInterval);
    pollingInterval = null;
}
async function fetchPenawaran() {
    try {
        const res = await fetch(`/lelang/${lelangId}/polling`);
        const data = await res.json();

        const textMin = document.getElementById('textMinimalPenawaran');

        if (textMin) {

            textMin.textContent =
                'Minimal Rp ' +
                Number(data.min_penawaran).toLocaleString('id-ID') +
                ' • Kelipatan Rp 1.000';

        }

        if (!data.success) return;

        // Check if the data has been updated
        if (data.updated_at !== lastUpdate) {
            lastUpdate = data.updated_at;
        } else {
            return;
        }

        const list = document.getElementById('list-penawaran');
        if (list) list.innerHTML = data.html;

        const harga = document.getElementById('hargaTertinggiDetail');
        if (harga) {
            harga.textContent = 'Rp ' + Number(data.harga_tertinggi).toLocaleString('id-ID');
        }

        const min = document.getElementById('minPenawaranDetail');
        if (min) {
            min.textContent = 'Rp ' + Number(data.min_penawaran).toLocaleString('id-ID');
        }

    } catch (e) {
        console.log('Polling error:', e);
    }
}
</script>

@endsection