<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapau Ancak</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚖️</text></svg>">
    
    {{-- ① Font Awesome + SB Admin CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">

    {{-- ③ Slot CSS tambahan dari blade child --}}
    @stack('styles')
</head>

<body id="page-top">

<div id="wrapper">

    @include('components.sidebar')

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            @include('components.topbar')

            <div class="container-fluid">
                @yield('content')
            </div>

        </div>

        @include('components.footer')
    </div>

</div>

{{-- ================= MODAL PREVIEW DOKUMEN ================= --}}
{{-- 1x di layout, dipakai semua halaman via previewDokumen()  --}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(90deg,#1a6b3c,#145c32);">
                <h5 class="modal-title text-white font-weight-bold" id="modalTitle">
                    <i class="fas fa-eye mr-2" style="color:#f6c90e;"></i>Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center" style="background:#f8fff9;">
                <iframe id="previewFrame" width="100%" height="500px"
                    style="display:none;border-radius:8px;border:none;"></iframe>
                <img id="previewImage" src=""
                    style="max-width:100%;display:none;border-radius:8px;" />
            </div>
        </div>
    </div>
</div>

{{-- ================= SCROLL BUTTON ================= --}}
<button id="scrollBtn" onclick="toggleScroll()"
    style="position:fixed;bottom:30px;right:30px;width:45px;height:45px;
           border-radius:50%;background:#4e73df;color:white;border:none;
           font-size:18px;cursor:pointer;display:none;align-items:center;
           justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.2);
           z-index:9999;transition:background 0.2s;">
    <i id="scrollIcon" class="fas fa-arrow-up"></i>
</button>

{{-- ① jQuery HARUS PALING PERTAMA --}}
<script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>

{{-- ② Bootstrap --}}
<script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

{{-- ③ SB Admin --}}
<script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>

{{-- ⑤ SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ================================================
     GLOBAL JS — fungsi yang dipakai di semua halaman
     ================================================ --}}
<script>

// ===== SWEETALERT HELPERS =====
function swalConfirm({ title, text, icon = 'warning', confirmText = 'Ya', cancelText = 'Batal', confirmColor = '#1a6b3c' }) {
    return Swal.fire({
        title,
        text,
        icon,
        showCancelButton:    true,
        confirmButtonColor:  confirmColor,
        cancelButtonColor:   '#6c757d',
        confirmButtonText:   confirmText,
        cancelButtonText:    cancelText,
        customClass: {
            popup:         'swal-custom-popup',
            confirmButton: 'swal-custom-confirm',
            cancelButton:  'swal-custom-cancel',
        }
    });
}

function swalSubmitForm(formId, options) {
    swalConfirm(options).then(result => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

function swalToast(icon, title) {
    Swal.fire({
        toast:              true,
        position:           'top-end',
        icon,
        title,
        showConfirmButton:  false,
        timer:              3000,
        timerProgressBar:   true,
    });
}

// ===== PREVIEW DOKUMEN (PDF & Gambar) =====
function previewDokumen(url, nama) {
    const img   = document.getElementById('previewImage');
    const frame = document.getElementById('previewFrame');
    const title = document.getElementById('modalTitle');
    if (!img || !frame || !title) return;

    title.innerHTML = '<i class="fas fa-eye mr-2" style="color:#f6c90e;"></i>' + (nama ?? 'Dokumen');

    if (url.match(/\.(jpeg|jpg|png|webp)$/i)) {
        img.src             = url;
        img.style.display   = 'block';
        frame.style.display = 'none';
    } else {
        frame.src           = url;
        frame.style.display = 'block';
        img.style.display   = 'none';
    }
    $('#previewModal').modal('show');
}

// ===== VALIDASI HARGA LIMIT =====
function validateHargaLimit(input) {
    const maxLimit = 35000000;
    const msg      = input.closest('.form-group')?.querySelector('[id^="harga-limit-msg"]');
    if (parseInt(input.value) > maxLimit) {
        input.value = maxLimit;
        if (msg) { msg.style.display = 'block'; setTimeout(() => msg.style.display = 'none', 4000); }
    } else if (!input.value || parseInt(input.value) <= 0) {
        input.value = '';
        if (msg) { msg.style.display = 'block'; setTimeout(() => msg.style.display = 'none', 4000); }
    } else {
        if (msg) msg.style.display = 'none';
    }
}

// ===== SCROLL BUTTON =====
const scrollBtn  = document.getElementById('scrollBtn');
const scrollIcon = document.getElementById('scrollIcon');

window.addEventListener('scroll', function () {
    scrollBtn.style.display = window.scrollY > 200 ? 'flex' : 'none';
    scrollIcon.className    = window.scrollY < document.body.offsetHeight / 2
        ? 'fas fa-arrow-down'
        : 'fas fa-arrow-up';
});

function toggleScroll() {
    const halfway = document.body.offsetHeight / 2;
    window.scrollTo({
        top:      window.scrollY < halfway ? document.body.offsetHeight : 0,
        behavior: 'smooth'
    });
}

</script>

{{-- ================================================
     CSS GLOBAL
     ================================================ --}}
<style>
/* SweetAlert */
.swal-custom-popup  { border-radius:14px !important; font-family:inherit !important; }
.swal-custom-confirm,
.swal-custom-cancel { border-radius:8px !important; font-weight:600 !important; padding:8px 20px !important; }

/* Foto barang — tombol hapus */
.photo-delete {
    opacity: 0; transition: 0.2s;
    background: rgba(231,74,59,0.85); color: white;
    border: none; border-radius: 50%;
    width: 20px; height: 20px; font-size: 14px; line-height: 1;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
}
.photo-box:hover .photo-delete { opacity: 1; }

/* Wizard progress bar sticky */
.wizard-sticky {
    position: sticky; top: 0; z-index: 100;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

/* DataTables — sesuaikan warna pagination dengan tema */
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #1a6b3c !important;
    border-color: #1a6b3c !important;
    color: white !important;
    border-radius: 6px !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e8f5ee !important;
    border-color: #b2d8c0 !important;
    color: #1a6b3c !important;
    border-radius: 6px !important;
}
.dataTables_wrapper .dataTables_info {
    font-size: 0.82rem;
    color: #6c757d;
}
</style>

<script>
/**
 * LapauTable — Custom DataTable Global
 *
 * Cara pakai di blade child:
 *
 * @push('scripts')
 * <script>
 * document.addEventListener('DOMContentLoaded', function () {
 *     LapauTable.init('idTabel', {
 *         searchable: true,      // tampilkan kolom search (default: true)
 *         sortable:   true,      // klik header untuk sort (default: true)
 *         pageSize:   10,        // baris per halaman (default: 10)
 *         emptyText:  'Tidak ada data', // teks saat kosong
 *         sortCol:    0,         // kolom default sort (index, default: 0)
 *         sortDir:    'asc',     // 'asc' atau 'desc' (default: 'asc')
 *     });
 * });
 * <\/script>
 * @endpush
 */

const LapauTable = (function () {

    const instances = {};

    // ── Utilitas ──────────────────────────────────────────
    function getText(cell) {
        return (cell.dataset.sort ?? cell.innerText ?? '').trim().toLowerCase();
    }

    function paginate(arr, page, size) {
        const start = (page - 1) * size;
        return arr.slice(start, start + size);
    }

    function formatAngka(n, total) {
        return n.toLocaleString('id-ID');
    }

    // ── Render pagination ──────────────────────────────────
    function renderPagination(inst) {
        const { wrapId, filtered, currentPage, pageSize } = inst;
        const totalPages = Math.ceil(filtered.length / pageSize);
        const wrap       = document.getElementById(wrapId);
        if (!wrap) return;

        const pag = wrap.querySelector('.lt-pagination');
        if (!pag) return;

        pag.innerHTML = '';

        if (totalPages <= 1) return;

        // Tombol Prev
        const prev = document.createElement('button');
        prev.className = 'lt-btn-page' + (currentPage === 1 ? ' lt-disabled' : '');
        prev.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prev.disabled  = currentPage === 1;
        prev.onclick   = () => { inst.currentPage--; render(inst); };
        pag.appendChild(prev);

        // Nomor halaman
        let startPage = Math.max(1, currentPage - 2);
        let endPage   = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

        if (startPage > 1) {
            appendPageBtn(pag, 1, inst);
            if (startPage > 2) appendEllipsis(pag);
        }

        for (let i = startPage; i <= endPage; i++) {
            appendPageBtn(pag, i, inst);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) appendEllipsis(pag);
            appendPageBtn(pag, totalPages, inst);
        }

        // Tombol Next
        const next = document.createElement('button');
        next.className = 'lt-btn-page' + (currentPage === totalPages ? ' lt-disabled' : '');
        next.innerHTML = '<i class="fas fa-chevron-right"></i>';
        next.disabled  = currentPage === totalPages;
        next.onclick   = () => { inst.currentPage++; render(inst); };
        pag.appendChild(next);
    }

    function appendPageBtn(pag, num, inst) {
        const btn = document.createElement('button');
        btn.className = 'lt-btn-page' + (num === inst.currentPage ? ' lt-active' : '');
        btn.textContent = num;
        btn.onclick = () => { inst.currentPage = num; render(inst); };
        pag.appendChild(btn);
    }

    function appendEllipsis(pag) {
        const span = document.createElement('span');
        span.className   = 'lt-ellipsis';
        span.textContent = '…';
        pag.appendChild(span);
    }

    // ── Render info ────────────────────────────────────────
    function renderInfo(inst) {
        const { wrapId, filtered, currentPage, pageSize, allRows } = inst;
        const wrap = document.getElementById(wrapId);
        if (!wrap) return;
        const info = wrap.querySelector('.lt-info');
        if (!info) return;

        const total = filtered.length;
        if (total === 0) {
            info.textContent = 'Tidak ada data';
            return;
        }
        const from = (currentPage - 1) * pageSize + 1;
        const to   = Math.min(currentPage * pageSize, total);
        info.textContent = `Menampilkan ${from}–${to} dari ${total} data`
            + (filtered.length < allRows.length ? ` (difilter dari ${allRows.length} total)` : '');
    }

    // ── Sort ───────────────────────────────────────────────
    function sortRows(inst) {

        const { sortCol, sortDir } = inst;

        if (sortCol === null) return;

        inst.filtered.sort((a, b) => {

            const cellA = a.cells[sortCol];
            const cellB = b.cells[sortCol];

            let ta = (cellA?.dataset.sort ?? cellA?.innerText ?? '').trim();
            let tb = (cellB?.dataset.sort ?? cellB?.innerText ?? '').trim();

            // ===============================
            // Cek apakah BENAR-BENAR angka
            // ===============================

            const isNumericA = /^-?\d+(\.\d+)?$/.test(ta);
            const isNumericB = /^-?\d+(\.\d+)?$/.test(tb);

            // Jika dua-duanya numeric
            if (isNumericA && isNumericB) {

                const na = Number(ta);
                const nb = Number(tb);

                return sortDir === 'asc'
                    ? na - nb
                    : nb - na;
            }

            // ===============================
            // String
            // ===============================

            ta = ta.toLowerCase();
            tb = tb.toLowerCase();

            return sortDir === 'asc'
                ? ta.localeCompare(tb, 'id')
                : tb.localeCompare(ta, 'id');
        });
    }

    // ── Update header sort icon ────────────────────────────
    function updateSortIcons(inst) {
        const wrap = document.getElementById(inst.wrapId);
        if (!wrap) return;
        wrap.querySelectorAll('th[data-col]').forEach(th => {
            const col = parseInt(th.dataset.col);
            th.querySelector('.lt-sort-icon').innerHTML =
                col !== inst.sortCol
                    ? '<i class="fas fa-sort text-muted" style="opacity:0.3;"></i>'
                    : inst.sortDir === 'asc'
                        ? '<i class="fas fa-sort-up" style="color:#1a6b3c;"></i>'
                        : '<i class="fas fa-sort-down" style="color:#1a6b3c;"></i>';
        });
    }

    // ── Render utama ───────────────────────────────────────
    function render(inst) {
        sortRows(inst);

        const page  = paginate(inst.filtered, inst.currentPage, inst.pageSize);
        const tbody = document.querySelector(`#${inst.tableId} tbody`);
        if (!tbody) return;

        // Sembunyikan semua baris dulu
        tbody.innerHTML = '';

        if (inst.filtered.length === 0) {

            const cols = document.querySelector(`#${inst.tableId} thead tr`)?.cells.length ?? 1;

            const emptyRow = document.createElement('tr');

            emptyRow.className = 'lt-empty-row';

            emptyRow.innerHTML = `
                <td colspan="${cols}" class="text-center py-4 text-muted">
                    <i class="fas fa-search fa-2x mb-2 d-block" style="color:#e0eeea;"></i>
                    ${inst.emptyText}
                </td>
            `;

            tbody.appendChild(emptyRow);

        } else {

            page.forEach(row => {
                tbody.appendChild(row);
            });
        }

        renderInfo(inst);
        renderPagination(inst);
        if (inst.sortable) updateSortIcons(inst);
    }

    // ── Filter / search ────────────────────────────────────
    function applyFilter(inst, query) {
        const q = query.trim().toLowerCase();
        inst.filtered    = q === ''
            ? [...inst.allRows]
            : inst.allRows.filter(row =>
                Array.from(row.cells).some(cell =>
                    getText(cell).includes(q)
                )
            );
        inst.currentPage = 1;
        render(inst);
    }

    // ── Build wrapper HTML ─────────────────────────────────
    function buildWrapper(table, opts) {
        const wrapId = 'lt-wrap-' + table.id;

        // Bungkus tabel dengan wrapper
        const wrapper = document.createElement('div');
        wrapper.id        = wrapId;
        wrapper.className = 'lt-wrapper';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);

        // Toolbar atas (search + info)
        if (opts.searchable) {

            const toolbar = document.createElement('div');
            toolbar.className = 'lt-toolbar';

            toolbar.innerHTML = `
                <div class="lt-search-wrap">
                    <div class="input-group input-group-sm" style="width:220px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background:#f8fff9;border-color:#b2d8c0;border-radius:8px 0 0 8px;">
                                <i class="fas fa-search" style="color:#1a6b3c;font-size:0.75rem;"></i>
                            </span>
                        </div>
                        <input type="text" class="lt-search form-control form-control-sm"
                            placeholder="Cari..."
                            style="border-color:#b2d8c0;border-radius:0 8px 8px 0;font-size:0.82rem;">
                    </div>
                </div>
            `;

            wrapper.insertBefore(toolbar, table);
        }

        // Pagination bawah
        const pagWrap = document.createElement('div');
        pagWrap.className = 'lt-pag-wrap';
        pagWrap.innerHTML = `
            <div class="lt-bottom-bar">
                <span class="lt-info text-muted small"></span>
                <div class="lt-pagination"></div>
            </div>
        `;
        wrapper.appendChild(pagWrap);

        return wrapId;
    }

    // ── Setup sort header ──────────────────────────────────
    function setupHeaders(inst) {
        const ths = document.querySelectorAll(`#${inst.tableId} thead th`);
        ths.forEach((th, i) => {
            // Skip kolom yang di-mark no-sort
            if (th.dataset.noSort !== undefined) return;

            th.dataset.col   = i;
            th.style.cursor  = 'pointer';
            th.style.userSelect = 'none';
            th.style.whiteSpace = 'nowrap';

            const icon = document.createElement('span');
            icon.className   = 'lt-sort-icon ml-1';
            icon.innerHTML   = '<i class="fas fa-sort text-muted" style="opacity:0.3;"></i>';
            th.appendChild(icon);

            th.addEventListener('click', () => {
                if (inst.sortCol === i) {
                    inst.sortDir = inst.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    inst.sortCol = i;
                    inst.sortDir = 'asc';
                }
                render(inst);
            });
        });
    }

    // ── Public: init ───────────────────────────────────────
    function init(tableId, opts = {}) {
        const table = document.getElementById(tableId);
        if (!table) {
            console.warn(`LapauTable: tabel #${tableId} tidak ditemukan.`);
            return;
        }

        const options = {
            searchable: opts.searchable  ?? true,
            sortable:   opts.sortable    ?? true,
            pageSize:   opts.pageSize    ?? 10,
            emptyText:  opts.emptyText   ?? 'Tidak ada data yang sesuai',
            sortCol:    opts.sortCol     ?? null,
            sortDir:    opts.sortDir     ?? 'asc',
        };

        const allRows = Array.from(table.querySelectorAll('tbody tr')).filter(r => !r.classList.contains('lt-empty-row'));
        const wrapId  = buildWrapper(table, options);

        const inst = {
            tableId,
            wrapId,
            allRows,
            filtered:    [...allRows],
            currentPage: 1,
            pageSize:    options.pageSize,
            emptyText:   options.emptyText,
            sortable:    options.sortable,
            sortCol:     options.sortCol,
            sortDir:     options.sortDir,
        };

        instances[tableId] = inst;

        // Setup sort
        if (options.sortable) setupHeaders(inst);

        // Setup search
        if (options.searchable) {
            const searchInput = document.querySelector(`#${wrapId} .lt-search`);
            if (searchInput) {
                let debounce;
                searchInput.addEventListener('input', function () {
                    clearTimeout(debounce);
                    debounce = setTimeout(() => applyFilter(inst, this.value), 200);
                });
            }
        }

        render(inst);
        return inst;
    }

    // ── Public: refresh (setelah HTML diupdate via AJAX) ──
    function refresh(tableId) {
        const inst = instances[tableId];
        if (!inst) return;
        const table   = document.getElementById(tableId);
        inst.allRows  = Array.from(table.querySelectorAll('tbody tr')).filter(r => !r.classList.contains('lt-empty-row'));
        inst.filtered = [...inst.allRows];
        inst.currentPage = 1;
        render(inst);
    }

    return { init, refresh };

})();
</script>

{{-- ── CSS LapauTable ─────────────────────────────────────── --}}
<style>
.lt-wrapper { width: 100%; }

.lt-toolbar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 10px 10px 8px;
    flex-wrap: wrap;
    gap: 8px;
}

.lt-pag-wrap {
    display: flex;
    justify-content: flex-end;
    padding: 10px 0 4px;
}

.lt-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}

.lt-btn-page {
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border: 1px solid #b2d8c0;
    border-radius: 6px;
    background: white;
    color: #1a6b3c;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.lt-btn-page:hover:not(.lt-disabled):not(.lt-active) {
    background: #e8f5ee;
    border-color: #1a6b3c;
}
.lt-btn-page.lt-active {
    background: #1a6b3c;
    border-color: #1a6b3c;
    color: white;
}
.lt-btn-page.lt-disabled {
    opacity: 0.35;
    cursor: not-allowed;
    pointer-events: none;
}
.lt-ellipsis {
    padding: 0 4px;
    color: #6c757d;
    font-size: 0.85rem;
    line-height: 32px;
}

.lt-info {
    font-size: 0.8rem;
    color: #858796;
    margin-left: 10px;
}

.lt-bottom-bar{
    width:100%;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    flex-wrap:wrap;
}

/* Sort icon spacing */
.lt-sort-icon { font-size: 0.72rem; }

/* Responsive */
@media (max-width: 576px) {
    .lt-toolbar { flex-direction: column; align-items: flex-start; }
    .lt-search-wrap .input-group { width: 100% !important; }
    .lt-bottom-bar{
        flex-direction:column;
        align-items:flex-start;
    }
}
</style>

{{-- ⑥ Stack scripts dari blade child — WAJIB PALING BAWAH --}}
@stack('scripts')

</body>
</html>