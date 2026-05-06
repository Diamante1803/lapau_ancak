@extends('layouts.app')

@section('content')
<h4>{{ $lelang->barang->nama_barang }}</h4>

<p>Harga saat ini: {{ $lelang->harga_tertinggi ?? $lelang->harga_awal }}</p>

@if($lelang->status == 'active')
<form method="POST" action="/bid/{{ $lelang->id }}">
    @csrf
    <input type="text" name="nama" placeholder="Nama" class="form-control mb-2">
    <input type="email" name="email" placeholder="Email" class="form-control mb-2">
    <input type="text" name="no_hp" placeholder="No HP" class="form-control mb-2">
    <input type="number" name="nilai" placeholder="Nilai Bid" class="form-control mb-2">

    <button class="btn btn-success">Tawar</button>
</form>
@endif
@endsection