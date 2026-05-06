@extends('layouts.app')

@section('content')
<h4>Daftar Lelang</h4>

@foreach($lelangs as $l)
<div class="card mb-2">
    <div class="card-body">
        <h5>{{ $l->barang->nama_barang }}</h5>
        <p>Harga: {{ $l->harga_tertinggi ?? $l->harga_awal }}</p>
        <a href="/lelang/{{ $l->id }}" class="btn btn-primary">Detail</a>
    </div>
</div>
@endforeach
@endsection