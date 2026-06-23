@if($penawarans->count() > 0)
<div class="table-responsive">
    <table id="tabelPenawaran" class="table table-hover mb-0" style="font-size:0.875rem;width:100%;">
        <thead style="background:#f8f9fa;">
            <tr>
                <th class="border-0 pl-4" style="width:50px;color:#6c757d;font-weight:600;font-size:0.78rem;" data-no-sort>NO</th>
                <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">PENAWAR</th>
                <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">NILAI PENAWARAN</th>
                <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">WAKTU</th>
                <th class="border-0" style="color:#6c757d;font-weight:600;font-size:0.78rem;">STATUS</th>
                @if(auth()->user()->role === 'admin_pusat' && $lelang->status === 'active')
                <th class="border-0 text-center" style="color:#6c757d;font-weight:600;font-size:0.78rem;">AKSI</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($penawarans as $rank => $penawaran)
            @php $isTop = $rank === 0; @endphp
            <tr style="{{ $isTop ? 'background:#f0fff4;' : '' }}">
                {{-- RANK --}}
                <td class="pl-4 align-middle">
                    @if($rank === 0)
                        <span style="background:#f6c90e;color:#5a4000;border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;">
                            <i class="fas fa-trophy"></i>
                        </span>
                    @elseif($rank === 1)
                        <span style="background:#e0e0e0;color:#555;border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;">2</span>
                    @elseif($rank === 2)
                        <span style="background:#f4a460;color:#fff;border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;">3</span>
                    @else
                        <span class="text-muted" style="font-size:0.82rem;padding-left:6px;">{{ $rank + 1 }}</span>
                    @endif
                </td>

                {{-- PENAWAR --}}
                <td class="align-middle" data-sort="{{ strtolower($penawaran->pembeli->nama ?? 'anonim') }}">
                    <div class="d-flex align-items-center">
                        <div style="width:34px;height:34px;border-radius:50%;background:{{ $isTop ? '#1a6b3c' : '#dee2e6' }};display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fas fa-user" style="color:{{ $isTop ? 'white' : '#6c757d' }};font-size:0.8rem;"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold" style="color:#2d3748;font-size:0.875rem;">
                                {{ $penawaran->pembeli->nama ?? 'Anonim' }}
                            </div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                {{ $penawaran->pembeli->email ?? '-' }}
                            </div>
                        </div>
                    </div>
                </td>

                {{-- NILAI --}}
                <td class="align-middle" data-sort="{{ (float) $penawaran->nilai_penawaran }}">
                    <div class="font-weight-bold" style="color:{{ $isTop ? '#1a6b3c' : '#2d3748' }};font-size:{{ $isTop ? '0.95rem' : '0.875rem' }};">
                        Rp {{ number_format($penawaran->nilai_penawaran, 0, ',', '.') }}
                    </div>
                    @if($isTop)
                    <small style="color:#1a6b3c;font-size:0.72rem;">
                        <i class="fas fa-arrow-up mr-1"></i>Tertinggi
                    </small>
                    @endif
                </td>

                {{-- WAKTU --}}
                <td class="align-middle text-muted" style="font-size:0.8rem;" data-sort="{{ $penawaran->created_at->timestamp }}">
                    <i class="fas fa-clock mr-1"></i>
                    {{ \Carbon\Carbon::parse($penawaran->created_at)->format('d M Y') }}<br>
                    <span style="font-size:0.75rem;">{{ \Carbon\Carbon::parse($penawaran->created_at)->format('H:i') }} WIB</span>
                </td>

                {{-- STATUS --}}
                <td class="align-middle">
                    @if($isTop && $lelang->status == 'closed')
                        <span class="badge badge-success" style="border-radius:6px;font-size:0.72rem;padding:4px 8px;">
                            <i class="fas fa-check mr-1"></i>Pemenang
                        </span>
                    @elseif($isTop)
                        <span class="badge badge-warning text-dark" style="border-radius:6px;font-size:0.72rem;padding:4px 8px;">
                            <i class="fas fa-star mr-1"></i>Tertinggi
                        </span>
                    @else
                        <span class="badge badge-light border" style="border-radius:6px;font-size:0.72rem;padding:4px 8px;color:#6c757d;">
                            Kalah
                        </span>
                    @endif
                </td>

                {{-- AKSI --}}
                @if(auth()->user()->role === 'admin_pusat' && $lelang->status === 'active')
                <td class="align-middle text-center">
                    @if($isTop)
                    @php
                        $nilaiFormatted = number_format($penawaran->nilai_penawaran, 0, ',', '.');
                        $namaPenawar = addslashes($penawaran->pembeli->nama ?? '-');
                    @endphp
                    <form id="form-hapus-bid-{{ $penawaran->id }}"
                        action="{{ route('admin.lelang.hapusPenawaranTertinggi', $lelang->id) }}"
                        method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm"
                            style="background:#fff3cd;color:#856404;border-radius:6px;padding:4px 10px;font-size:0.75rem;"
                            onclick="swalSubmitForm('form-hapus-bid-{{ $penawaran->id }}', {
                                title: 'Hapus Penawaran Tertinggi?',
                                text: 'Penawaran Rp {{ $nilaiFormatted }} oleh {{ $namaPenawar }} akan dihapus.',
                                icon: 'warning',
                                confirmText: 'Ya, Hapus',
                                confirmColor: '#856404'
                            })">
                            <i class="fas fa-user-minus mr-1"></i>Hapus
                        </button>
                    </form>
                    @else
                    <span class="text-muted" style="font-size:0.75rem;">—</span>
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="text-center py-5 text-muted">
    <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#d1e7d8;"></i>
    <div class="font-weight-bold mb-1">Belum ada penawaran</div>
    <small>Penawaran akan muncul setelah lelang aktif</small>
</div>
@endif