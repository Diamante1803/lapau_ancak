@if($penawarans->count() > 0)

<div class="divide-y divide-gray-50">

    @foreach($penawarans as $penawaran)

        <div class="px-6 py-4 flex items-center justify-between
            {{ $loop->first ? 'bg-green-50' : '' }} penawaran-item">

            <div class="flex items-center gap-3">

                {{-- Rank --}}
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                    {{ $loop->first ? 'bg-yellow-400 text-white' : 'bg-gray-100 text-gray-500' }}">
                    {{ $loop->iteration }}
                </div>

                <div>

                    @php
                        $nama = $penawaran->pembeli->nama ?? 'User';
                        $namaAman =
                            substr($nama, 0, 2) .
                            str_repeat('*', max(0, strlen($nama) - 4)) .
                            substr($nama, -2);
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

            <div class="font-bold
                {{ $loop->first ? 'text-green-600 text-lg' : 'text-gray-600' }}">

                Rp {{ number_format($penawaran->nilai_penawaran, 0, ',', '.') }}

            </div>

        </div>

    @endforeach

</div>

@else

<div class="text-center py-12 text-gray-400">
    <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
    <p>Belum ada penawaran</p>
</div>

@endif