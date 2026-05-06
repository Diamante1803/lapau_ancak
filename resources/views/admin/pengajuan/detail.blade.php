@extends('layouts.admin')

@section('admin_content')
<h4>Detail Pengajuan</h4>

<p>Judul: {{ $pengajuan->judul_pengajuan }}</p>
<p>Status: {{ $pengajuan->status }}</p>

@if($pengajuan->status == 'draft' || $pengajuan->status == 'revision')
<form method="POST" action="/pengajuan/{{ $pengajuan->id }}/submit">
    @csrf
    <button class="btn btn-primary">Submit</button>
</form>
@endif

@endsection