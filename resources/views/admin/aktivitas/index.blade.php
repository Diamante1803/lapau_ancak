@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 font-weight-bold text-gray-800">
            <i class="fas fa-history mr-2 text-primary"></i> Aktivitas Terbaru
        </h1>
    </div>

    {{-- STATS CARDS --}}
<div class="row mb-4">

    <div class="col-xl-3 col-md-6 mb-3">
        <x-statistic-card
            title="Hari Ini"
            value="{{ number_format($stats['today']) }}"
            unit="aktivitas"
            icon="fa-calendar-day"
            color="#1a6b3c"
        />
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <x-statistic-card
            title="Minggu Ini"
            value="{{ number_format($stats['week']) }}"
            unit="aktivitas"
            icon="fa-calendar-week"
            color="#17a2b8"
        />
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <x-statistic-card
            title="Bulan Ini"
            value="{{ number_format($stats['month']) }}"
            unit="aktivitas"
            icon="fa-calendar-alt"
            color="#f6c90e"
        />
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <x-statistic-card
            title="User Teraktif"
            value="{{ $stats['top_user']->name ?? '—' }}"
            icon="fa-medal"
            color="#c0392b"
            description="Hari ini"
        />
    </div>

</div>
<style>
    /* Overrides spesifik halaman aktivitas jika diperlukan */
</style>

<div class="card filter-card-modern mb-4">
    <div class="card-body p-2 px-3">
        <form method="GET" action="{{ route('admin.aktivitas.index') }}">
            <div class="form-row align-items-center">

                {{-- Keyword --}}
                <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="interactive-field py-2" style="font-size: 0.85rem;" placeholder="Keyword...">
                </div>

                {{-- Dari Tanggal --}}
                <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                    <div class="position-relative">
                        <input type="text" name="dari" value="{{ request('dari') }}" 
                               class="interactive-field datepicker py-2" style="font-size: 0.85rem;" placeholder="Dari Tanggal">
                        <i class="material-icons position-absolute" style="right:10px; top:9px; font-size:18px; color:var(--c-theme-primary); pointer-events: none;">calendar_today</i>
                    </div>
                </div>

                {{-- Hingga Tanggal --}}
                <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                    <div class="position-relative">
                        <input type="text" name="sampai" value="{{ request('sampai') }}" 
                               class="interactive-field datepicker py-2" style="font-size: 0.85rem;" placeholder="Sampai Tanggal">
                        <i class="material-icons position-absolute" style="right:10px; top:9px; font-size:18px; color:var(--c-theme-primary); pointer-events: none;">event_available</i>
                    </div>
                </div>

                {{-- User Dropdown --}}
                <div class="col-lg-2 col-md-6 mb-2 mb-lg-0">
                    <div class="custom-dropdown-container">
                        <input type="hidden" name="user_id" id="hidden_user_id" value="{{ request('user_id') }}">
                        <button type="button" class="interactive-field text-left d-flex justify-content-between align-items-center py-2" 
                                style="font-size: 0.85rem;" id="btnUserDropdown" onclick="toggleCustomDropdown('user_filter')">
                            <span id="labelUserDropdown" class="text-truncate mr-1">
                                {{ request('user_id') ? ($users->firstWhere('id', request('user_id'))->name ?? 'User') : 'Pilih User' }}
                            </span>
                            <i class="material-icons dropdown-toggle-icon" style="font-size:16px; color:var(--c-theme-primary);" id="icon-user_filter">expand_more</i>
                        </button>
                        <div class="custom-dropdown-menu shadow-lg d-none" id="menu-user_filter">
                            <div class="p-2 border-bottom sticky-top bg-white">
                                <input type="text" class="form-control form-control-sm" placeholder="Cari user..." oninput="filterDropdownList('user_filter', this.value)" onclick="event.stopPropagation()">
                            </div>
                            <div class="list-wrapper">
                                <div class="dropdown-item-custom py-2 px-3 cursor-pointer no-filter" onclick="selectDropdownOption('user_filter', '', 'Pilih User', 'hidden_user_id', 'labelUserDropdown')">
                                    <span class="small font-weight-bold">Semua</span>
                                </div>
                                @foreach($users as $u)
                                <div class="dropdown-item-custom py-2 px-3 cursor-pointer user-option" data-satker="{{ $u->satker_id }}" data-search="{{ strtolower($u->name) }}" onclick="selectDropdownOption('user_filter', '{{ $u->id }}', '{{ $u->name }}', 'hidden_user_id', 'labelUserDropdown')">
                                    <div class="small font-weight-bold">{{ $u->name }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Satker Dropdown --}}
                <div class="col-lg-2 col-md-6 mb-2 mb-lg-0">
                    <div class="custom-dropdown-container">
                        <input type="hidden" name="satker_id" id="hidden_satker_id" value="{{ request('satker_id') }}">
                        <button type="button" class="interactive-field text-left d-flex justify-content-between align-items-center py-2" 
                                style="font-size: 0.85rem;" id="btnSatkerDropdown" onclick="toggleCustomDropdown('satker_filter')">
                            <span id="labelSatkerDropdown" class="text-truncate mr-1">
                                {{ request('satker_id') ? ($satkers->firstWhere('id', request('satker_id'))->nama_satker ?? 'Satker') : 'Pilih Satker' }}
                            </span>
                            <i class="material-icons dropdown-toggle-icon" style="font-size:16px; color:var(--c-theme-primary);" id="icon-satker_filter">expand_more</i>
                        </button>
                        <div class="custom-dropdown-menu shadow-lg d-none" id="menu-satker_filter">
                            <div class="p-2 border-bottom sticky-top bg-white">
                                <input type="text" class="form-control form-control-sm" placeholder="Cari satker..." oninput="filterDropdownList('satker_filter', this.value)" onclick="event.stopPropagation()">
                            </div>
                            <div class="list-wrapper">
                                <div class="dropdown-item-custom py-2 px-3 cursor-pointer no-filter" onclick="selectDropdownOption('satker_filter', '', 'Pilih Satker', 'hidden_satker_id', 'labelSatkerDropdown')">
                                    <span class="small font-weight-bold">Semua</span>
                                </div>
                            @foreach($satkers as $s)
                                <div class="dropdown-item-custom py-2 px-3 cursor-pointer" data-search="{{ strtolower($s->nama_satker) }}" onclick="selectDropdownOption('satker_filter', '{{ $s->id }}', '{{ $s->nama_satker }}', 'hidden_satker_id', 'labelSatkerDropdown'); updateUserFilter('{{ $s->id }}')">
                                    <div class="small font-weight-bold">{{ $s->nama_satker }}</div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="col-lg-2 col-md-12 d-flex" style="gap:5px;">
                    <button type="submit" class="btn-apply flex-fill py-2" style="font-size: 0.85rem;">
                        Filter
                    </button>
                    <a href="{{ route('admin.aktivitas.index') }}" class="interactive-field bg-light d-flex align-items-center justify-content-center px-2 py-2" style="width: auto;">
                        <i class="material-icons" style="font-size: 18px; color: var(--c-text-secondary)">refresh</i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

    {{-- TIMELINE --}}
    <div class="card shadow mb-4" style="border-radius: 16px; border: none;">
        <div class="card-body p-0">
            <div class="timeline-scroll-container p-0" style="max-height: 700px; overflow-y: auto; scroll-behavior: smooth;">
                <div class="timeline">
                @forelse($groupedLogs as $date => $dayLogs)
                    <div class="timeline-group mb-0">
                        <h6 class="timeline-date-title font-weight-bold text-primary sticky-top bg-white px-4 py-3 mb-0 border-bottom">
                            <i class="fas fa-calendar-day mr-2"></i> {{ $date }}
                        </h6>
                        <div class="px-4 pt-3 pb-2">
                        @foreach($dayLogs as $log)
                        <div class="timeline-item d-flex mb-0">
                            <div class="timeline-icon-wrapper mr-3 text-center" style="width: 50px;">
                                <div class="bg-{{ App\Http\Controllers\AuditLogController::getBadgeColor($log->event) }} text-white rounded-circle shadow-sm mx-auto" style="width: 35px; height: 35px; line-height: 35px;">
                                    <i class="fas {{ App\Http\Controllers\AuditLogController::getModuleIcon($log->auditable_type) }} fa-sm"></i>
                                </div>
                            </div>
                            <div class="timeline-content flex-grow-1 card border-left-{{ App\Http\Controllers\AuditLogController::getBadgeColor($log->event) }} shadow-sm mb-4">
                                <div class="card-body py-1.5 px-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="font-weight-bold text-dark mb-1">
                                            {{ $log->description }}
                                            <span class="badge badge-{{ App\Http\Controllers\AuditLogController::getBadgeColor($log->event) }} text-uppercase ml-2" style="font-size: 0.65rem;">
                                                {{ $log->event }}
                                            </span>
                                        </h6>
                                        <small class="text-muted"><i class="far fa-clock mr-1"></i>{{ Carbon\Carbon::parse($log->created_at)->format('H:i') }}</small>
                                    </div>
                                    <div class="small text-gray-600">
                                        <i class="fas fa-user-circle mr-1"></i> <strong>{{ $log->user_name ?? 'Sistem Otomatis' }}</strong> 
                                        <span class="mx-1">|</span>
                                        <span class="badge badge-light border text-muted px-2" style="font-size: 0.7rem;">{{ strtoupper($log->user_role ?? 'System') }}</span>
                                        @if($log->satker_name)
                                            <span class="mx-1">|</span>
                                            <i class="fas fa-building mr-1"></i> {{ $log->satker_name }}
                                        @endif
                                    </div>
                                    @if($log->auditable_type)
                                    <div class="mt-2 py-1 px-2 bg-light rounded border text-xs text-muted d-inline-block">
                                        <i class="fas fa-layer-group mr-1"></i> Modul: {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <img src="{{ asset('img/undraw_no_data.svg') }}" style="width: 150px;" class="mb-3 opacity-50">
                        <p class="text-muted">Tidak ada aktivitas ditemukan dalam kriteria pencarian ini.</p>
                    </div>
                @endforelse
                </div>
            </div>

            <div class="mt-0 p-3 border-top bg-white rounded-bottom d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $logs->firstItem() ?? 0 }} sampai {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} hasil
                </div>
                <div class="pagination-sm">
                    {{ $logs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline { position: relative; }
    .timeline-date-title {
        position: sticky;
        top: 0;
        z-index: 10;
        margin-bottom: 0;
        background: #ffffff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .timeline-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    .timeline-icon-wrapper {
        position: relative;
        width: 50px;
        min-width: 50px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .timeline-icon-wrapper::after {
        content: '';
        position: absolute;
        top: 35px;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: calc(100% + 15px);
        background: #e3e6f0;
        z-index: 0;
    }
    .timeline-group:last-child .timeline-item:last-child .timeline-icon-wrapper::after { display: none; }

    .timeline-content {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        min-width: 0;
        overflow: hidden;
        border-left-width: 4px !important;
        border-radius: 0.75rem;
        background: #ffffff;
    }
    .timeline-content .card-body {
        padding: 1rem 1rem 0.85rem;
    }
    .timeline-content:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(15, 23, 42, 0.08);
    }
    .timeline-content h6 {
        font-size: 0.95rem;
        margin-bottom: 0.45rem;
    }
    .timeline-content .small.text-gray-600 {
        color: #6b7280;
    }
    .timeline-content .badge {
        font-size: 0.65rem;
    }
    .timeline-line { display: none; }
    .bg-purple { background-color: #6f42c1; }
    .border-left-purple { border-left: .25rem solid #6f42c1 !important; }
    .text-xs { font-size: 0.75rem; }

    /* Scrollbar Styling agar lebih modern */
    .timeline-scroll-container::-webkit-scrollbar { width: 8px; }
    .timeline-scroll-container::-webkit-scrollbar-track { background: #f8f9fc; }
    .timeline-scroll-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    .timeline-scroll-container::-webkit-scrollbar-thumb:hover { background: #1a6b3c; }

    /* Pagination and action clarity */
    .pagination { margin-bottom: 0; }
    .page-item.active .page-link { 
        background-color: #1a6b3c; 
        border-color: #1a6b3c; 
    }
    .page-link { 
        color: #1a6b3c;
    }

    .btn-apply { background: #1a6b3c; color: #fff; border: none; border-radius: 8px; }
    .btn-apply:hover { background: #145c32; color: #fff; }
    .interactive-field { min-height: 42px; }
    .custom-dropdown-container .custom-dropdown-menu { max-height: 280px; overflow-y: auto; }
    .custom-dropdown-menu .list-wrapper { max-height: 220px; overflow-y: auto; }
</style>

<script>
function updateUserFilter(satkerId) {
    const userOptions = document.querySelectorAll('.user-option');
    // Reset Pilihan User jika Satker berubah
    document.getElementById('hidden_user_id').value = '';
    document.getElementById('labelUserDropdown').textContent = 'Pilih User';

    userOptions.forEach(opt => {
        if (!satkerId || opt.dataset.satker === satkerId) {
            opt.classList.remove('d-none');
        } else {
            opt.classList.add('d-none');
        }
    });
}
</script>
@endsection