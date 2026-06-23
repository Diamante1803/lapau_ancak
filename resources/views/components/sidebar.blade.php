<div>
{{-- ===================== SIDEBAR ===================== --}}
<ul class="navbar-nav sidebar sidebar-dark accordion {{ request()->cookie('sidebar_toggled') ? 'toggled' : '' }}"
    id="accordionSidebar">

    {{-- ── Brand ── --}}
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-gavel"></i>
        </div>
        <div class="sidebar-brand-text">Lapau Ancak</div>
    </a>

    {{-- ── Dashboard ── --}}
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}" data-tooltip="Dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- ── DIVIDER: PEMULIHAN ASET ── --}}
    <div class="sidebar-section-label">Pemulihan Aset</div>

    {{-- ── Pengajuan ── --}}
    @php
        $pengajuanBaru  = 0;
        $revisiCount    = 0;
        if(auth()->user()->role === 'admin_pusat') {
            $pengajuanBaru = \App\Models\PengajuanLelang::where('status','submitted')->count();
        }
        if(auth()->user()->role === 'admin_satker') {
            $revisiCount = \App\Models\PengajuanLelang::where('satker_id', auth()->user()->satker_id)
                ->where('status','revision')->count();
        }
        $pengajuanActive = request()->routeIs('*.pengajuan.*');
    @endphp

    @if(auth()->user()->role === 'admin_pusat')
    {{-- Admin Pusat: Langsung link (karena cuma 1 menu) --}}
    <li class="nav-item {{ $pengajuanActive ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.pengajuan.index') }}" data-tooltip="Pengajuan">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Pengajuan</span>
            @if($pengajuanBaru > 0)
            <span class="sidebar-badge sidebar-badge-yellow ml-auto">{{ $pengajuanBaru }}</span>
            @endif
        </a>
    </li>
    @else
    {{-- Admin Satker: Tetap collapse (karena ada sub-menu Revisi) --}}
    <li class="nav-item {{ $pengajuanActive ? 'active' : '' }}">
        <a class="nav-link {{ $pengajuanActive ? '' : 'collapsed' }}"
            href="#collapsePengajuan"
            data-toggle="collapse"
            aria-expanded="{{ $pengajuanActive ? 'true' : 'false' }}"
            data-tooltip="Pengajuan">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Pengajuan</span>
            @if($pengajuanBaru > 0 || $revisiCount > 0)
            <span class="sidebar-badge sidebar-badge-yellow ml-auto">
                {{ $pengajuanBaru + $revisiCount }}
            </span>
            @endif
        </a>
        <div id="collapsePengajuan"
            class="collapse {{ $pengajuanActive ? 'show' : '' }}"
            data-parent="#accordionSidebar">
            <div class="collapse-inner">
                <a class="collapse-item {{ request()->routeIs('*.pengajuan.index') && !request('status') ? 'active' : '' }}"
                    href="{{ route('satker.pengajuan.index') }}">
                    <i class="fas fa-list fa-xs mr-2"></i>Semua Pengajuan
                    @if($pengajuanBaru > 0)
                    <span class="sidebar-badge sidebar-badge-yellow ml-auto">{{ $pengajuanBaru }}</span>
                    @endif
                </a>
                @if(auth()->user()->role === 'admin_satker')
                <a class="collapse-item {{ request('status') === 'revision' ? 'active' : '' }}"
                    href="{{ route('satker.pengajuan.index', ['status' => 'revision']) }}">
                    <i class="fas fa-redo fa-xs mr-2"></i>Perlu Revisi
                    @if($revisiCount > 0)
                    <span class="sidebar-badge sidebar-badge-red ml-auto">{{ $revisiCount }}</span>
                    @endif
                </a>
                @endif
            </div>
        </div>
    </li>
    @endif

    {{-- ── DIVIDER: LELANG ── --}}
    <div class="sidebar-section-label">Lelang</div>

    {{-- ── Kelola Lelang ── --}}
    @php
        $lelangAktifCount  = \App\Models\Lelang::where('status','active')->count();
        $lelangAktifSatker = 0;
        $lelangApprovedCount = 0;
        if(auth()->user()->role === 'admin_satker') {
            $lelangAktifSatker = \App\Models\Lelang::where('status','active')
                ->whereHas('barang.perkara.pengajuan', fn($q) =>
                    $q->where('satker_id', auth()->user()->satker_id)
                )->count();
        }
        if(auth()->user()->role === 'admin_pusat') {
            $lelangApprovedCount = \App\Models\PengajuanLelang::where('status','approved')
                ->whereHas('perkaras.barangs', fn($q) =>
                    $q->whereDoesntHave('lelang', fn($q2) =>
                        $q2->whereIn('status', ['scheduled','active'])
                    )
                )->count();
        }
        $badgeLelang = auth()->user()->role === 'admin_pusat' ? $lelangAktifCount : $lelangAktifSatker;
        $lelangActive = request()->routeIs('*.lelang.*');
    @endphp

    <li class="nav-item {{ $lelangActive ? 'active' : '' }}">
        <a class="nav-link {{ $lelangActive ? '' : 'collapsed' }}"
            href="#collapseLelang"
            data-toggle="collapse"
            aria-expanded="{{ $lelangActive ? 'true' : 'false' }}"
            data-tooltip="Kelola Lelang">
            <i class="fas fa-fw fa-gavel"></i>
            <span>Kelola Lelang</span>
            @if($badgeLelang > 0)
            <span class="sidebar-badge sidebar-badge-green ml-auto">{{ $badgeLelang }} Live</span>
            @endif
        </a>
        <div id="collapseLelang"
            class="collapse {{ $lelangActive ? 'show' : '' }}"
            data-parent="#accordionSidebar">
            <div class="collapse-inner">
                @if(auth()->user()->role === 'admin_pusat')
                <a class="collapse-item {{ request()->routeIs('admin.lelang.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.lelang.dashboard') }}">
                    <i class="fas fa-calendar fa-xs mr-2"></i>Jadwal Lelang
                    @if($lelangApprovedCount > 0)
                    <span class="sidebar-badge sidebar-badge-yellow ml-auto">{{ $lelangApprovedCount }}</span>
                    @endif
                </a>
                @endif
                <a class="collapse-item {{ request()->routeIs('*.lelang.aktif') ? 'active' : '' }}"
                    href="{{ auth()->user()->role === 'admin_pusat' ? route('admin.lelang.aktif') : route('satker.lelang.aktif') }}">
                    <i class="fas fa-fire fa-xs mr-2"></i>Lelang Aktif
                    @if($badgeLelang > 0)
                    <span class="sidebar-badge sidebar-badge-green ml-auto">{{ $badgeLelang }}</span>
                    @endif
                </a>
                <a class="collapse-item {{ request()->routeIs('*.lelang.selesai') ? 'active' : '' }}"
                    href="{{ auth()->user()->role === 'admin_pusat' ? route('admin.lelang.selesai') : route('satker.lelang.selesai') }}">
                    <i class="fas fa-check-circle fa-xs mr-2"></i>Lelang Selesai
                </a>
            </div>
        </div>
    </li>

    {{-- ── ADMIN PUSAT ONLY ── --}}
    @if(auth()->user()->role === 'admin_pusat')

    <div class="sidebar-section-label">Administrasi</div>

    <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.users.index') }}" data-tooltip="Manajemen User">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Manajemen User</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.satker.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.satker.index') }}" data-tooltip="Data Satker">
            <i class="fas fa-fw fa-building"></i>
            <span>Data Satker</span>
        </a>
    </li>

    <div class="sidebar-section-label">Monitoring</div>

    <li class="nav-item {{ request()->routeIs('admin.aktivitas.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.aktivitas.index') }}" data-tooltip="Aktivitas">
            <i class="fas fa-fw fa-history"></i>
            <span>Aktivitas</span>
        </a>
    </li>

    @endif

    {{-- ── DIVIDER: LAPORAN ── --}}
    <div class="sidebar-section-label">Laporan</div>

    @php $laporanActive = request()->routeIs('*.laporan.*'); @endphp

    <li class="nav-item {{ $laporanActive ? 'active' : '' }}">
        <a class="nav-link" 
            href="{{ auth()->user()->role === 'admin_pusat' ? route('admin.laporan.index') : route('satker.laporan.index') }}" 
            data-tooltip="Laporan">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Laporan</span>
        </a>
    </li>

    {{-- ── Spacer bawah ── --}}
    <div style="flex: 1;"></div>

</ul>

{{-- ── Tooltip container (untuk mini sidebar) ── --}}
<div id="sidebar-tooltip" class="sidebar-tooltip-box"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar  = document.getElementById('accordionSidebar');
    const body     = document.querySelector('body');
    const toggleBtn = document.getElementById('sidebarToggleTop');
    const tooltip  = document.getElementById('sidebar-tooltip');

    // Restore state
    if (localStorage.getItem('sidebar-state') === 'collapsed') {
        sidebar.classList.add('toggled');
        body.classList.add('sidebar-toggled');
    }

    // Perbaikan: Jika sidebar dalam kondisi toggled (mini) saat halaman dimuat, 
    // tutup semua sub-menu yang terbuka otomatis agar tidak melayang di atas halaman.
    if (sidebar.classList.contains('toggled')) {
        document.querySelectorAll('.collapse.show').forEach(coll => {
            coll.classList.remove('show');
        });
    }

    // Toggle
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            // Modern approach: Tutup semua submenu saat transisi agar tidak melayang
            document.querySelectorAll('.collapse.show').forEach(coll => {
                $(coll).collapse('hide');
            });

            setTimeout(() => {
                const isToggled = sidebar.classList.contains('toggled');
                localStorage.setItem('sidebar-state', isToggled ? 'collapsed' : 'expanded');

                // Jika kembali ke mode lebar, buka kembali menu yang memang aktif (berdasarkan route)
                if (!isToggled) {
                    $('.nav-item.active .collapse').collapse('show');
                }
            }, 50);
        });
    }

    // Sinkronisasi posisi sub-menu saat sidebar mengecil (collapsed)
    $(sidebar).on('show.bs.collapse', '.collapse', function (e) {
        if (sidebar.classList.contains('toggled')) {
            // Tutup sub-menu lain yang sedang terbuka (perilaku accordion saat mini)
            $(sidebar).find('.collapse.show').not(this).collapse('hide');

            // Cari elemen pemicu (link menu) berdasarkan ID collapse
            const trigger = $(`[data-target="#${this.id}"], [href="#${this.id}"]`);
            if (trigger.length) {
                const rect = trigger[0].getBoundingClientRect();
                // Atur posisi top agar sejajar dengan tombol menu yang diklik
                this.style.top = rect.top + 'px';
            }
        }
    });

    // Tutup sub-menu segera saat item diklik (agar tidak menggantung saat pindah halaman)
    $(sidebar).on('click', '.collapse-item', function() {
        if (sidebar.classList.contains('toggled')) {
            $(this).closest('.collapse').collapse('hide');
        }
    });

    // Tooltip saat mini sidebar
    document.querySelectorAll('.nav-link[data-tooltip]').forEach(link => {
        link.addEventListener('mouseenter', function (e) {
            if (!sidebar.classList.contains('toggled')) return;
            const rect = link.getBoundingClientRect();
            tooltip.textContent = link.dataset.tooltip;
            tooltip.style.top   = (rect.top + rect.height / 2) + 'px';
            tooltip.style.opacity = '1';
            tooltip.style.pointerEvents = 'none';
        });
        link.addEventListener('mouseleave', function () {
            tooltip.style.opacity = '0';
        });
    });
});
</script>

<style>
/* ── Base sidebar ── */
#accordionSidebar {
    background: #0f3d24 !important;
    width: 240px;
    min-height: 100vh;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: width 0.3s cubic-bezier(0.4,0,0.2,1);
    border-right: 1px solid rgba(255,255,255,0.06);
    overflow: hidden;
    position: relative;
}

/* ── Brand ── */
.sidebar-brand {
    padding: 20px 16px 16px !important;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    margin-bottom: 6px;
    flex-shrink: 0;
}
.sidebar-brand-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #f6c90e;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.3s;
}
.sidebar-brand-icon i { color: #0f3d24; font-size: 0.95rem; }
.sidebar-brand-text {
    font-size: 0.95rem;
    font-weight: 700;
    color: white;
    letter-spacing: 0.5px;
    margin-left: 10px;
    white-space: nowrap;
    transition: opacity 0.2s, width 0.3s;
}

/* ── Section labels ── */
.sidebar-section-label {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.3);
    padding: 16px 20px 4px;
    white-space: nowrap;
    overflow: hidden;
    transition: opacity 0.2s;
}

/* ── Nav items ── */
#accordionSidebar .nav-item { position: relative; }

#accordionSidebar .nav-link {
    display: flex;
    align-items: center;
    padding: 9px 14px !important;
    margin: 2px 10px !important;
    border-radius: 10px !important;
    color: rgba(255,255,255,0.65) !important;
    font-size: 0.82rem;
    font-weight: 500;
    transition: all 0.2s ease;
    white-space: nowrap;
    text-decoration: none;
}
#accordionSidebar .nav-link i.fa-fw {
    font-size: 1rem;
    width: 20px;
    flex-shrink: 0;
    text-align: center;
    margin-right: 10px;
    transition: margin 0.3s;
}

/* ── Indikator penanda menu yang memiliki sub-menu ── */
#accordionSidebar:not(.toggled) .nav-link[data-toggle="collapse"] {
    position: relative;
    padding-right: 2.5rem !important;
}

#accordionSidebar .nav-link[data-toggle="collapse"]::after {
    content: "\f107"; /* FontAwesome Chevron Down */
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.7rem;
    color: rgba(255,255,255,0.3);
    transition: transform 0.3s ease, color 0.3s ease;
}

/* State Panah saat Terbuka (Mode Lebar) */
#accordionSidebar:not(.toggled) .nav-link:not(.collapsed)[data-toggle="collapse"]::after {
    transform: translateY(-50%) rotate(180deg);
    color: #f6c90e;
}

/* State Berbeda untuk Mode Mini (Toggled) */
#accordionSidebar.toggled .nav-link[data-toggle="collapse"]::after {
    display: block !important; /* Paksa muncul karena default SB Admin menyembunyikannya saat toggled */
    content: "\f111"; /* FontAwesome Circle (Titik) */
    right: 12px;
    top: 10px;
    color: #f6c90e;
    font-size: 0.45rem;
    text-shadow: 0 0 5px rgba(246, 201, 14, 0.4);
    transform: none !important; /* Matikan rotasi chevron agar tidak miring di mode mini */
}
#accordionSidebar .nav-link:hover {
    background: rgba(255,255,255,0.08) !important;
    color: white !important;
}
#accordionSidebar .nav-item.active .nav-link {
    background: rgba(246,201,14,0.15) !important;
    color: #f6c90e !important;
}
#accordionSidebar .nav-item.active .nav-link i { color: #f6c90e; }

/* ── Active indicator bar ── */
#accordionSidebar .nav-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 6px;
    bottom: 6px;
    width: 3px;
    background: #f6c90e;
    border-radius: 0 3px 3px 0;
}

/* ── Collapse inner ── */
.collapse-inner {
    padding: 4px 6px;
    margin: 0 10px 6px;
    background: rgba(0,0,0,0.18);
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.05);
}
.collapse-item {
    display: flex;
    align-items: center;
    padding: 7px 10px !important;
    border-radius: 8px !important;
    font-size: 0.78rem !important;
    color: rgba(255,255,255,0.6) !important;
    text-decoration: none;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.collapse-item:hover {
    background: rgba(255,255,255,0.1) !important;
    color: white !important;
}
.collapse-item.active {
    background: rgba(246,201,14,0.12) !important;
    color: #f6c90e !important;
    font-weight: 600;
}

/* ── Badges ── */
.sidebar-badge {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 6px;
    line-height: 1.4;
    flex-shrink: 0;
}
.sidebar-badge-yellow { background: #f6c90e; color: #1a3d10; }
.sidebar-badge-green  { background: #22c55e; color: #052e0f; }
.sidebar-badge-red    { background: #ef4444; color: #fff; }

/* ── Tooltip mini sidebar ── */
.sidebar-tooltip-box {
    position: fixed;
    left: 88px;
    background: #1a6b3c;
    color: white;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 8px;
    opacity: 0;
    transition: opacity 0.15s;
    z-index: 9999;
    pointer-events: none;
    transform: translateY(-50%);
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.sidebar-tooltip-box::before {
    content: '';
    position: absolute;
    left: -5px;
    top: 50%;
    transform: translateY(-50%);
    border: 5px solid transparent;
    border-right-color: #1a6b3c;
    border-left: 0;
}

/* ════════════════════════════
   TOGGLED (Mini / Collapsed)
════════════════════════════ */
#accordionSidebar.toggled { width: 72px !important; }

#accordionSidebar.toggled .sidebar-brand {
    justify-content: center !important;
    padding: 18px 0 14px !important;
}
#accordionSidebar.toggled .sidebar-brand-text { opacity: 0; width: 0; margin: 0; }
#accordionSidebar.toggled .sidebar-brand-icon { margin: 0; }

#accordionSidebar.toggled .sidebar-section-label { opacity: 0; height: 8px; padding: 0; }

#accordionSidebar.toggled .nav-link {
    justify-content: center !important;
    padding: 12px 0 !important;
    margin: 2px 0 !important;
    width: 100% !important;
    border-radius: 0 !important;
}
#accordionSidebar.toggled .nav-link i.fa-fw { 
    margin-right: 0 !important; 
    font-size: 1.2rem;
}
#accordionSidebar.toggled .nav-link span,
#accordionSidebar.toggled .nav-link .sidebar-badge { display: none; }

#accordionSidebar.toggled .nav-item.active::before { display: none; }
#accordionSidebar.toggled .nav-item.active .nav-link {
    background: rgba(246,201,14,0.18) !important;
}

/* Floating dropdown saat mini */
#accordionSidebar.toggled .collapse {
    position: fixed;
    left: 76px;
    z-index: 1060;
    min-width: 180px;
    margin-top: 0 !important;
    animation: sidebarFadeIn 0.2s ease-out;
}
#accordionSidebar.toggled .collapse-inner {
    background: #1a4d2e !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3) !important;
    margin: 0 !important;
    border-radius: 10px !important;
}
#accordionSidebar.toggled .collapse-item { color: rgba(255,255,255,0.75) !important; }

/* Animasi Halus Sub-menu Melayang */
@keyframes sidebarFadeIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>

</div>