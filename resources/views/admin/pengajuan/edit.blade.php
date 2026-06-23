@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 text-gray-800">Detail Pengajuan</h1>

        <form method="POST" action="{{ route('satker.pengajuan.submit', $pengajuan) }}">
            @csrf
            <button class="btn btn-success shadow-sm">
                <i class="fas fa-paper-plane"></i> Submit ke Pusat
            </button>
        </form>
    </div>

    {{-- ================= INFO PENGAJUAN ================= --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            Informasi Pengajuan
        </div>
        <div class="card-body">
            <p><strong>Judul:</strong> {{ $pengajuan->judul_pengajuan }}</p>
            <p><strong>Status:</strong>
                <span class="badge badge-info">{{ $pengajuan->status }}</span>
            </p>
        </div>
    </div>

    {{-- ================= DOKUMEN PENGAJUAN ================= --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Dokumen Pengajuan</h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('satker.pengajuan.uploadDokumen', $pengajuan) }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Jenis Dokumen</label>
                    <select name="jenis" class="form-control">
                        <option value="sk_panitia">SK Panitia</option>
                        <option value="izin_penjualan">Izin Penjualan</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="file" name="file[]" multiple class="form-control">
                </div>

                <button class="btn btn-primary btn-sm">Upload</button>
            </form>

            <div class="card-body">
            <ul class="list-group">
                @php
                    $sk = $pengajuan->dokumen->where('jenis','sk_panitia')->first();
                    $izin = $pengajuan->dokumen->where('jenis','izin_penjualan')->first();
                    $total = 2;
                    $done = 0;
                    if($sk) $done++;
                    if($izin) $done++;
                    $percent = ($done / $total) * 100;
                @endphp

                <div class="card shadow mb-4">
                    <div class="card-body">

                        <h6 class="font-weight-bold">Kelengkapan Dokumen</h6>

                        <div class="progress">
                            <div class="progress-bar bg-success"
                                role="progressbar"
                                style="width: {{ $percent }}%">
                                {{ $percent }}%
                            </div>
                        </div>

                    </div>
                </div>

                {{-- SK PANITIA --}}
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file-alt mr-2 text-primary"></i>
                        <strong>SK Panitia</strong><br>

                        @if($sk)
                            <button type="button" class="btn btn-link p-0" 
                            onclick="previewFile('{{ asset('storage/'.$sk->file_path) }}')">
                                Lihat Dokumen
                            </button>
                        @else
                            <span class="text-muted">Belum upload</span>
                        @endif
                    </div>

                    <div>
                        @if($sk)
                            <span class="badge badge-success mr-2">Sudah Upload</span>

                            <form action="{{ route('satker.dokumen.destroy', $sk->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus dokumen ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @else
                            <span class="badge badge-secondary">Belum</span>
                        @endif
                    </div>
                </li>

                {{-- IZIN PENJUALAN --}}
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file-alt mr-2 text-info"></i>
                        <strong>Izin Penjualan</strong><br>

                        @if($izin)
                            <button type="button" class="btn btn-link p-0" 
                            onclick="previewFile('{{ asset('storage/'.$izin->file_path) }}')">
                                Lihat Dokumen
                            </button>
                        @else
                            <span class="text-muted">Belum upload</span>
                        @endif
                    </div>

                    <div>
                        @if($izin)
                            <span class="badge badge-success mr-2">Sudah Upload</span>

                            <form action="{{ route('satker.dokumen.destroy', $izin->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus dokumen ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @else
                            <span class="badge badge-secondary">Belum</span>
                        @endif
                    </div>
                </li>
            </ul>
            </div>
        </div>
    </div>  


    {{-- ================= PERKARA ================= --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Perkara</h6>
        </div>

        <div class="card-body">
        
            <form method="POST" action="{{ route('satker.pengajuan.perkara.store', $pengajuan) }}">
                @csrf

                <input type="text" name="nomor_perkara" class="form-control mb-2" placeholder="Nomor Perkara">
                <input type="text" name="nama_tersangka" class="form-control mb-2" placeholder="Nama Tersangka">
                <input type="date" name="tanggal_putusan" class="form-control mb-2">

                <button class="btn btn-success btn-sm">Tambah Perkara</button>
            </form>

            <hr>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor</th>
                        <th>Tersangka</th>
                        <th>Tanggal Putusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan->perkara as $i => $p)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $p->nomor_perkara }}</td>
                        <td>{{ $p->nama_tersangka }}</td>
                        <td>{{ $p->tanggal_putusan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Data perkara belum ada
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    {{-- ================= LOOP PERKARA ================= --}}
    @foreach($pengajuan->perkara as $perkara)

    <div class="card shadow mb-4">
        <div class="card-header bg-warning text-white">
            Barang - Perkara {{ $perkara->nama_tersangka }}
        </div>

        <div class="card-body">

            {{-- TAMBAH BARANG --}}
            <form method="POST" action="{{ route('satker.perkara.barang.store', $perkara) }}">
                @csrf

                <input type="text" name="nama_barang" class="form-control mb-2" placeholder="Nama Barang">
                <input type="number" name="harga_awal" class="form-control mb-2" placeholder="Harga Awal">
                <textarea name="deskripsi" class="form-control mb-2" placeholder="Deskripsi"></textarea>

                <button class="btn btn-primary btn-sm">Tambah Barang</button>
            </form>

            <hr>

            {{-- LIST BARANG --}}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perkara->barangs as $barang)
                        <tr>
                            <td>{{ $barang->nama_barang }}</td>
                            <td>{{ $barang->harga_awal }}</td>
                            <td>

                                {{-- FORM UPLOAD FOTO --}}
                                <form method="POST" action="{{ route('satker.barang.uploadFoto', $barang) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="foto[]" multiple>
                                    <button class="btn btn-sm btn-success mt-1">Upload</button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada barang</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    @endforeach

</div>

{{-- MODAL PREVIEW DOKUMEN --}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Preview Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center">

                {{-- PDF --}}
                <iframe id="previewFrame"
                        width="100%"
                        height="500px"
                        style="display:none;"></iframe>

                {{-- IMAGE --}}
                <img id="previewImage"
                     src=""
                     style="max-width:100%; display:none;" />

            </div>

        </div>
    </div>
</div>

<script>
    function previewFile(url) {
        let frame = document.getElementById('previewFrame');
        let img = document.getElementById('previewImage');

        if (url.match(/\.(jpeg|jpg|png)$/)) {
            frame.style.display = 'none';
            img.style.display = 'block';
            img.src = url;
        } else {
            img.style.display = 'none';
            frame.style.display = 'block';
            frame.src = url;
        }

        $('#previewModal').modal('show');
    }
</script>

@endsection