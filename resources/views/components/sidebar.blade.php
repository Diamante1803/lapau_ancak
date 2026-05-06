<div>
    <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar"
        style="background: linear-gradient(180deg, #1a6b3c 0%, #145c32 50%, #0f4526 100%);">

        <!-- Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center py-4" href="#">
            <div class="sidebar-brand-icon mr-2">
                <i class="fas fa-gavel" style="font-size: 1.5rem; color: #f6c90e;"></i>
            </div>
            <div class="sidebar-brand-text font-weight-bold" style="font-size: 1.1rem; letter-spacing: 1px;">
                Lapau Ancak
            </div>
        </a>

        <hr class="sidebar-divider my-0" style="border-color: rgba(255,255,255,0.15);">

        <!-- Dashboard -->
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.1);">
        <div class="sidebar-heading" style="color: rgba(255,255,255,0.5); font-size: 0.7rem; letter-spacing: 2px;">
            ⚖️ PEMULIHAN ASET
        </div>

        {{-- ======== PENGAJUAN ======== --}}
        <li class="nav-item {{ request()->routeIs('*.pengajuan.*') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengajuan"
                aria-expanded="{{ request()->routeIs('*.pengajuan.*') ? 'true' : 'false' }}"
                aria-controls="collapsePengajuan">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Pengajuan Penjualan</span>

                {{-- Badge notifikasi pengajuan baru (admin pusat) --}}
                @if(auth()->user()->role === 'admin_pusat')
                    @php
                        $pengajuanBaru = \App\Models\PengajuanLelang::where('status','submitted')->count();
                    @endphp
                    @if($pengajuanBaru > 0)
                    <span class="ml-auto badge"
                        style="background: #f6c90e; color: #1a6b3c; border-radius: 20px; font-size: 0.65rem; padding: 2px 7px;">
                        {{ $pengajuanBaru }}
                    </span>
                    @endif
                @endif

            </a>
            <div id="collapsePengajuan" 
                class="collapse {{ request()->routeIs('*.pengajuan.*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.15);">

                    {{-- Semua Pengajuan --}}
                    <a class="collapse-item {{ request()->routeIs('*.pengajuan.index') ? 'active' : '' }}"
                        style="color: rgba(255,255,255,0.8);"
                        href="{{ auth()->user()->role === 'admin_pusat' ? route('admin.pengajuan.index') : route('satker.pengajuan.index') }}">
                        <i class="fas fa-list fa-sm mr-2"></i>Semua Pengajuan
                    </a>

                    {{-- Pengajuan Revisi — hanya admin satker --}}
                    @if(auth()->user()->role === 'admin_satker')                    
                    <a class="collapse-item {{ request()->routeIs('admin.pengajuan.revisi') ? 'active' : '' }}"
                        style="color: rgba(255,255,255,0.8);"
                        href="{{ route('satker.pengajuan.index', ['status' => 'revision']) }}">
                        <i class="fas fa-redo fa-sm mr-2"></i>Pengajuan Revisi
                        @php $revisiCount = \App\Models\PengajuanLelang::where('satker_id', auth()->user()->satker_id)->where('status','revision')->count(); @endphp
                        @if($revisiCount > 0)
                        <span class="badge ml-1"
                            style="background: #e74a3b; color: white; border-radius: 20px; font-size: 0.65rem; padding: 2px 7px;">
                            {{ $revisiCount }}
                        </span>
                        @endif
                    </a>
                    @endif

                </div>
            </div>
        </li>

        {{-- ======== KELOLA LELANG ======== --}}
        <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.1);">
        <div class="sidebar-heading" style="color: rgba(255,255,255,0.5); font-size: 0.7rem; letter-spacing: 2px;">
            🔨 LELANG
        </div>

        @php
            $lelangAktifCount = \App\Models\Lelang::where('status','active')->count();

            // Untuk admin satker, hitung lelang aktif satker sendiri
            $lelangAktifSatker = 0;
            if(auth()->user()->role === 'admin_satker') {
                $lelangAktifSatker = \App\Models\Lelang::where('status','active')
                    ->whereHas('barang.perkara.pengajuan', function($q) {
                        $q->where('satker_id', auth()->user()->satker_id);
                    })->count();
            }

            // Hanya admin pusat
            $lelangApprovedCount = 0;
            if(auth()->user()->role === 'admin_pusat') {
                $lelangApprovedCount = \App\Models\PengajuanLelang::where('status','approved')
                    ->whereHas('perkaras.barangs', function($q) {
                        $q->whereDoesntHave('lelang', function($q2) {
                            $q2->whereIn('status', ['scheduled','active']);
                        });
                    })->count();
            }
        @endphp

        <li class="nav-item {{ request()->routeIs('*.lelang.*') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLelang"
                aria-expanded="{{ request()->routeIs('*.lelang.*') ? 'true' : 'false' }}"
                aria-controls="collapseLelang">
                <i class="fas fa-fw fa-gavel"></i>
                <span>Kelola Lelang</span>

                {{-- Badge live --}}
                @if(auth()->user()->role === 'admin_pusat' && $lelangAktifCount > 0)
                <span class="ml-auto badge"
                    style="background: #28a745; color: white; border-radius: 20px; font-size: 0.65rem; padding: 2px 7px;">
                    {{ $lelangAktifCount }} live
                </span>
                @elseif(auth()->user()->role === 'admin_satker' && $lelangAktifSatker > 0)
                <span class="ml-auto badge"
                    style="background: #28a745; color: white; border-radius: 20px; font-size: 0.65rem; padding: 2px 7px;">
                    {{ $lelangAktifSatker }} live
                </span>
                @endif

            </a>

            <div id="collapseLelang"
                class="collapse {{ request()->routeIs('*.lelang.*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.15);">

                    {{-- Jadwal Lelang — hanya admin pusat --}}
                    @if(auth()->user()->role === 'admin_pusat')
                    <a class="collapse-item {{ request()->routeIs('admin.lelang.dashboard') ? 'active' : '' }}"
                        style="color: rgba(255,255,255,0.8);"
                        href="{{ route('admin.lelang.dashboard') }}">
                        <i class="fas fa-calendar fa-sm mr-2"></i>Jadwal Lelang
                        @if($lelangApprovedCount > 0)
                        <span class="badge ml-1"
                            style="background: #f6c90e; color: #1a6b3c; border-radius: 20px; font-size: 0.65rem; padding: 2px 7px;">
                            {{ $lelangApprovedCount }}
                        </span>
                        @endif
                    </a>
                    @endif

                    {{-- Lelang Aktif — semua role, route berbeda --}}
                    <a class="collapse-item {{ request()->routeIs('*.lelang.aktif') ? 'active' : '' }}"
                        style="color: rgba(255,255,255,0.8);"
                        href="{{ auth()->user()->role === 'admin_pusat' ? route('admin.lelang.aktif') : route('satker.lelang.aktif') }}">
                        <i class="fas fa-fire fa-sm mr-2"></i>Lelang Aktif
                        @if(auth()->user()->role === 'admin_pusat' && $lelangAktifCount > 0)
                        <span class="badge ml-1"
                            style="background: #28a745; color: white; border-radius: 20px; font-size: 0.65rem; padding: 2px 7px;">
                            {{ $lelangAktifCount }}
                        </span>
                        @elseif(auth()->user()->role === 'admin_satker' && $lelangAktifSatker > 0)
                        <span class="badge ml-1"
                            style="background: #28a745; color: white; border-radius: 20px; font-size: 0.65rem; padding: 2px 7px;">
                            {{ $lelangAktifSatker }}
                        </span>
                        @endif
                    </a>

                    {{-- Lelang Selesai — semua role, route berbeda --}}
                    <a class="collapse-item {{ request()->routeIs('*.lelang.selesai') ? 'active' : '' }}"
                        style="color: rgba(255,255,255,0.8);"
                        href="{{ auth()->user()->role === 'admin_pusat' ? route('admin.lelang.selesai') : route('satker.lelang.selesai') }}">
                        <i class="fas fa-check-circle fa-sm mr-2"></i>Lelang Selesai
                    </a>

                </div>
            </div>
        </li>

        @if(auth()->user()->role === 'admin_pusat')
        <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.1);">
        <div class="sidebar-heading" style="color: rgba(255,255,255,0.5); font-size: 0.7rem; letter-spacing: 2px;">
            👥 ADMINISTRASI
        </div>

        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Manajemen User</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.satker.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.satker.index') }}">
                <i class="fas fa-fw fa-building"></i>
                <span>Data Satker</span>
            </a>
        </li>

        @endif

        {{-- ======== LAPORAN ======== --}}
        <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.1);">
        <div class="sidebar-heading" style="color: rgba(255,255,255,0.5); font-size: 0.7rem; letter-spacing: 2px;">
            📊 LAPORAN
        </div>

        <li class="nav-item {{ request()->routeIs('*.laporan.*') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan"
                aria-expanded="{{ request()->routeIs('*.laporan.*') ? 'true' : 'false' }}"
                aria-controls="collapseLaporan">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
            <div id="collapseLaporan"
                class="collapse {{ request()->routeIs('*.laporan.*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded" style="background: rgba(0,0,0,0.15);">

                    <a class="collapse-item" style="color: rgba(255,255,255,0.8);" 
                    href="{{ auth()->user()->role === 'admin_pusat' ? route('admin.laporan.index') : route('satker.laporan.index') }}">
                        <i class="fas fa-file-pdf fa-sm mr-2"></i>Laporan Lelang
                    </a>

                </div>
            </div>
        </li>

        <hr class="sidebar-divider d-none d-md-block" style="border-color: rgba(255,255,255,0.1);">

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"
                style="background: rgba(255,255,255,0.1);"></button>
        </div>

    </ul>

    <style>
        #accordionSidebar .nav-item.active .nav-link {
            background: rgba(255,255,255,0.15);
            border-left: 3px solid #f6c90e;
            border-radius: 4px;
        }
        #accordionSidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
            transition: 0.2s;
        }
        #accordionSidebar .collapse-item:hover {
            background: rgba(255,255,255,0.1) !important;
            color: #fff !important;
            border-radius: 4px;
            transition: 0.2s;
        }
        #accordionSidebar .collapse-item.active {
            color: #f6c90e !important;
            font-weight: bold;
        }
    </style>
</div>