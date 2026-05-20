@extends('layouts.public')

@section('content')

{{-- HERO --}}
<section class="py-5 bg-white border-bottom">
    <div class="container text-center">

        <h1 class="text-4xl font-bold text-blue-700 mb-3">
            LAPAU ANCAK
        </h1>

        <p class="text-gray-600 text-lg mb-4">
            Platform Lelang Barang Rampasan Negara
        </p>

        <div class="d-flex justify-content-center gap-3">
            <a href="/lelang" class="btn btn-primary px-4">
                Lihat Lelang
            </a>
        </div>
    </div>
</section>

{{-- DASHBOARD STYLE CARD --}}
<section class="py-5">
    <div class="container">

        <div class="row g-4">

            <div class="col-md-4">
                <div class="bg-white rounded shadow p-4 text-center">
                    <h5 class="text-gray-500">Total Lelang</h5>
                    <h2 class="fw-bold text-primary">120</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="bg-white rounded shadow p-4 text-center">
                    <h5 class="text-gray-500">Lelang Aktif</h5>
                    <h2 class="fw-bold text-success">35</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="bg-white rounded shadow p-4 text-center">
                    <h5 class="text-gray-500">Barang Terjual</h5>
                    <h2 class="fw-bold text-danger">80</h2>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- LIST SAMPLE (STYLE MIRIP DASHBOARD) --}}
<section class="pb-5">
    <div class="container">

        <h4 class="mb-4">Lelang Terbaru</h4>

        <div class="row g-4">

            {{-- Dummy card dulu --}}
            @for ($i = 1; $i <= 6; $i++)
            <div class="col-md-4">
                <div class="bg-white rounded shadow p-3">

                    <div class="h-40 bg-gray-200 rounded mb-3"></div>

                    <h6 class="fw-bold">Barang Rampasan {{ $i }}</h6>

                    <p class="text-gray-500 mb-2">
                        Harga mulai Rp 1.000.000
                    </p>

                    <a href="#" class="btn btn-primary btn-sm w-100">
                        Lihat Detail
                    </a>

                </div>
            </div>
            @endfor

        </div>

    </div>
</section>

@endsection