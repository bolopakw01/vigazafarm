@extends('admin.layouts.app')

@section('title', 'Pembesaran Puyuh')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Pembesaran'],
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-penetasan.css') }}">
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-pembesaran.css') }}">
@endpush

@section('content')
<div class="bolopa-tabel-wrapper">
    <div class="bolopa-tabel-container">
        <header>
            <div>
                <h1>
                    <img src="{{ asset('bolopa/img/icon/game-icons--nest-birds.svg') }}" alt="Pembesaran" width="28" height="28" style="vertical-align: middle;">
                    Data Pembesaran
                </h1>
                {{-- <p class="text-muted mb-0" style="font-size: 0.95rem;">
                    Alur Day Old Quail (DOQ) hingga puyuh dewasa siap produksi.
                </p> --}}
            </div>
            <a href="{{ route('admin.pembesaran.create') }}" class="bolopa-tabel-btn bolopa-tabel-btn-primary">
                <img src="{{ asset('bolopa/img/icon/line-md--plus-square-filled.svg') }}" alt="Tambah" width="20" height="20" style="vertical-align: middle;">
                Tambah Pembesaran
            </a>
        </header>

        <div class="bolopa-tabel-controls">
            <div class="bolopa-tabel-left-controls">
                <div class="bolopa-tabel-entries-select">
                    <span>Tampilkan</span>
                    <select id="entriesSelect">
                        <option value="5" {{ request('per_page', 5) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                    <span>entri</span>
                </div>

                <div class="bolopa-tabel-search-box">
                    <img src="{{ asset('bolopa/img/icon/line-md--file-search-filled.svg') }}" alt="Search" width="16" height="16" style="vertical-align: middle;">
                    <input type="text" id="searchInput" placeholder="Cari data..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="bolopa-tabel-right-controls">
                <button class="bolopa-tabel-btn bolopa-tabel-btn-success" id="btnExport">
                    <img src="{{ asset('bolopa/img/icon/line-md--file-export-filled.svg') }}" alt="Export" width="20" height="20" style="vertical-align: middle;">
                    Export
                </button>
                <button class="bolopa-tabel-btn bolopa-tabel-btn-primary" id="btnPrint">
                    <img src="{{ asset('bolopa/img/icon/line-md--cloud-alt-print-twotone-loop.svg') }}" alt="Print" width="20" height="20" style="vertical-align: middle;">
                    Print
                </button>
            </div>
        </div>

        <div class="bolopa-tabel-table-responsive">
            <table id="dataTable">
                <thead>
                    <tr>
                        <th data-sort="no" style="width: 60px;" class="bolopa-tabel-text-center">
                            No
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="batch" class="bolopa-tabel-text-left">
                            Batch
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="kandang" class="bolopa-tabel-text-left">
                            Kandang
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="tanggal_masuk" class="bolopa-tabel-text-center">
                            Tanggal Masuk
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="jumlah_anak_ayam" class="bolopa-tabel-text-right">
                            Jumlah DOQ
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="jenis_kelamin" class="bolopa-tabel-text-center">
                            Jenis
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="status" class="bolopa-tabel-text-center">
                            Status
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="aksi" style="width: 120px;" class="bolopa-tabel-text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembesaran as $index => $item)
                    <tr>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">{{ $pembesaran->firstItem() + $index }}</td>
                        <td class="bolopa-tabel-text-left" style="text-align: left;">{{ $item->batch_label }}</td>
                        <td class="bolopa-tabel-text-left" style="text-align: left;">{{ $item->kandang->nama_kandang ?? '-' }}</td>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td class="bolopa-tabel-text-right" style="text-align: right;">{{ number_format($item->jumlah_anak_ayam) }} ekor</td>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">
                            @if($item->jenis_kelamin === 'betina')
                                <span class="bolopa-tabel-badge bolopa-tabel-badge-pink">♀ Betina</span>
                            @elseif($item->jenis_kelamin === 'jantan')
                                <span class="bolopa-tabel-badge bolopa-tabel-badge-blue">♂ Jantan</span>
                            @else
                                <span class="bolopa-tabel-badge bolopa-tabel-badge-gray">⚥ Campuran</span>
                            @endif
                        </td>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">
                            @php
                                $status = $item->status_batch ?? 'Aktif';
                                if (strtolower($status) === 'aktif') {
                                    $statusClass = 'bolopa-tabel-badge-green';
                                } else {
                                    $statusClass = 'bolopa-tabel-badge-gray';
                                }
                            @endphp
                            <span class="bolopa-tabel-badge {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">
                            <div class="bolopa-tabel-actions">
                                <a href="{{ route('admin.pembesaran.show', $item->id) }}" class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action" title="Detail & Recording">
                                    <img src="{{ asset('bolopa/img/icon/icon-park-outline--view-grid-detail.svg') }}" alt="Detail" width="14" height="14">
                                </a>
                                <a href="{{ route('admin.pembesaran.edit', $item->id) }}" class="bolopa-tabel-btn bolopa-tabel-btn-warning bolopa-tabel-btn-action" title="Edit">
                                    <img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" alt="Edit" width="14" height="14">
                                </a>
                                <form action="{{ route('admin.pembesaran.destroy', $item->id) }}" method="POST" style="display: inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="bolopa-tabel-btn bolopa-tabel-btn-danger bolopa-tabel-btn-action delete-btn" title="Hapus">
                                        <img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" alt="Delete" width="14" height="14">
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <img src="{{ asset('bolopa/img/icon/game-icons--nest-birds.svg') }}" alt="No Data" width="64" height="64" style="opacity: 0.3;">
                            <p style="margin-top: 16px; color: #6c757d;">Tidak ada data pembesaran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bolopa-tabel-pagination">
            <div class="bolopa-tabel-pagination-info">
                Menampilkan {{ $pembesaran->firstItem() ?? 0 }} sampai {{ $pembesaran->lastItem() ?? 0 }} dari {{ $pembesaran->total() }} entri
            </div>
            <div class="bolopa-tabel-pagination-buttons">
                <a href="{{ $pembesaran->previousPageUrl() }}" class="bolopa-tabel-pagination-btn {{ $pembesaran->onFirstPage() ? 'disabled' : '' }}">
                    <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous" width="18" height="18">
                </a>

                @foreach($pembesaran->getUrlRange(1, $pembesaran->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="bolopa-tabel-pagination-btn {{ $page == $pembesaran->currentPage() ? 'bolopa-tabel-active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach

                <a href="{{ $pembesaran->nextPageUrl() }}" class="bolopa-tabel-pagination-btn {{ !$pembesaran->hasMorePages() ? 'disabled' : '' }}">
                    <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" alt="Next" width="18" height="18">
                </a>
            </div>
        </div>
        <div class="bolopa-tabel-toast" id="toast" style="display: none;"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggerFlashToast = (icon, title, message, timer = 3500) => {
        if (!message) {
            return;
        }
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title,
            text: message,
            showConfirmButton: false,
            timer,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    };

    const searchInput = document.getElementById('searchInput');
    const entriesSelect = document.getElementById('entriesSelect');

    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('search', this.value);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            }, 500);
        });
    }

    if (entriesSelect) {
        entriesSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });
    }

    const showToast = (message, type = 'success') => {
        const toast = document.getElementById('toast');
        if (!toast) return;
        if (toast.dataset.timeoutId) {
            clearTimeout(parseInt(toast.dataset.timeoutId, 10));
        }
        toast.textContent = message;
        toast.style.background = type === 'success' ? '#28a745' : type === 'info' ? '#0d6efd' : '#dc3545';
        toast.style.display = 'block';
        toast.classList.add('bolopa-tabel-show');
        const timeoutId = window.setTimeout(() => {
            toast.classList.remove('bolopa-tabel-show');
            toast.style.display = 'none';
            toast.dataset.timeoutId = '';
        }, 3000);
        toast.dataset.timeoutId = timeoutId.toString();
    };

    const exportTools = (function() {
        const sanitize = (value = '') => value.replace(/\s+/g, ' ').trim();
        const collectHeaders = (table) => {
            const skipIndexes = new Set();
            const headers = [];
            table.querySelectorAll('thead th').forEach((th, index) => {
                const text = sanitize(th.innerText || th.textContent || '');
                if (!text || text.toLowerCase() === 'aksi') {
                    skipIndexes.add(index);
                    return;
                }
                headers.push(text);
            });
            return { headers, skipIndexes };
        };
        const collectRows = (table, skipIndexes) => {
            const rows = [];
            table.querySelectorAll('tbody tr').forEach((tr) => {
                const cells = tr.querySelectorAll('td');
                if (cells.length <= 1) {
                    return;
                }
                const row = [];
                cells.forEach((td, index) => {
                    if (skipIndexes.has(index)) {
                        return;
                    }
                    row.push(sanitize(td.innerText || td.textContent || ''));
                });
                if (row.some((item) => item !== '')) {
                    rows.push(row);
                }
            });
            return rows;
        };
        const toCSV = (rows) => rows.map((row) => row.map((value) => {
            const needsQuotes = /[",\n;]/.test(value);
            const escaped = value.replace(/"/g, '""');
            return needsQuotes ? `"${escaped}"` : escaped;
        }).join(','))
        .join('\r\n');
        const downloadCSV = (content, filename) => {
            const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        };
        const escapeHtml = (value = '') => value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        const buildTable = (headers, rows) => {
            let html = '<table class="print-table"><thead><tr>';
            headers.forEach((header) => {
                html += `<th>${escapeHtml(header)}</th>`;
            });
            html += '</tr></thead><tbody>';
            rows.forEach((row) => {
                html += '<tr>';
                row.forEach((cell) => {
                    html += `<td>${escapeHtml(cell)}</td>`;
                });
                html += '</tr>';
            });
            html += '</tbody></table>';
            return html;
        };
        const printTable = (title, headers, rows) => {
            const win = window.open('', '_blank');
            if (!win) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cetak diblokir',
                    text: 'Izinkan popup untuk mencetak tabel.'
                });
                return;
            }
            const doc = win.document;
            doc.write('<html><head><title>' + title + '</title>');
            doc.write('<style>body{font-family:Arial,sans-serif;padding:24px;color:#111;}h1{margin-bottom:18px;font-size:20px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #444;padding:8px;font-size:12px;text-align:left;}th{background:#f1f5f9;}</style>');
            doc.write('</head><body>');
            doc.write('<h1>' + escapeHtml(title) + '</h1>');
            if (!rows.length) {
                doc.write('<p>Tidak ada data yang dapat dicetak.</p>');
            } else {
                doc.write(buildTable(headers, rows));
            }
            doc.write('<p style="margin-top:16px;font-size:12px;color:#555;">Dicetak pada ' + new Date().toLocaleString('id-ID') + '</p>');
            doc.write('</body></html>');
            doc.close();
            win.focus();
            win.print();
        };
        const timestamp = () => {
            const now = new Date();
            const pad = (num) => num.toString().padStart(2, '0');
            return `${now.getFullYear()}${pad(now.getMonth() + 1)}${pad(now.getDate())}_${pad(now.getHours())}${pad(now.getMinutes())}${pad(now.getSeconds())}`;
        };
        return { collectHeaders, collectRows, toCSV, downloadCSV, printTable, timestamp };
    })();

    (function setupExportAndPrint() {
        const table = document.getElementById('dataTable');
        if (!table) {
            return;
        }
        const exportBtn = document.getElementById('btnExport');
        const printBtn = document.getElementById('btnPrint');
        const { headers, skipIndexes } = exportTools.collectHeaders(table);

        exportBtn?.addEventListener('click', () => {
            const rows = exportTools.collectRows(table, skipIndexes);
            if (!rows.length) {
                showToast('Tidak ada data untuk diekspor', 'info');
                return;
            }
            const csv = exportTools.toCSV([headers, ...rows]);
            exportTools.downloadCSV(csv, `pembesaran-${exportTools.timestamp()}.csv`);
            showToast('File CSV berhasil disiapkan', 'success');
        });

        printBtn?.addEventListener('click', () => {
            const rows = exportTools.collectRows(table, skipIndexes);
            exportTools.printTable('Data Pembesaran', headers, rows);
        });
    })();

    // Table sorting functionality (client-side)
    const table = document.getElementById('dataTable');
    const tbody = table ? table.querySelector('tbody') : null;

    if (table && tbody) {
        const headers = table.querySelectorAll('th[data-sort]');
        let sortState = { column: null, direction: 'asc' };

        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                const column = this.getAttribute('data-sort');
                // Toggle direction
                if (sortState.column === column) {
                    sortState.direction = sortState.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    sortState.column = column;
                    sortState.direction = 'asc';
                }
                sortTable(column, sortState.direction);
                updateSortIcons(column, sortState.direction);
            });
        });

        function getColumnIndex(sortKey) {
            const mapping = {
                'no': 0,
                'batch': 1,
                'kandang': 2,
                'tanggal_masuk': 3,
                'jumlah_anak_ayam': 4,
                'jenis_kelamin': 5,
                'status': 6,
                'aksi': 7
            };
            return mapping[sortKey] ?? -1;
        }

        function getCellValue(row, index) {
            const cell = row.cells[index];
            if (!cell) return '';
            let value = cell.textContent.trim();
            // Normalize numbers: remove dots, replace comma with dot
            const numeric = value.replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.-]/g, '');
            if (numeric !== '' && !isNaN(numeric)) return parseFloat(numeric);
            return value.toLowerCase();
        }

        function sortTable(column, direction) {
            const columnIndex = getColumnIndex(column);
            if (columnIndex === -1) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                const aVal = getCellValue(a, columnIndex);
                const bVal = getCellValue(b, columnIndex);

                if (typeof aVal === 'number' && typeof bVal === 'number') {
                    return direction === 'asc' ? aVal - bVal : bVal - aVal;
                }
                return direction === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            });

            rows.forEach(r => tbody.appendChild(r));
        }

        function updateSortIcons(activeColumn, direction) {
            const headers = table.querySelectorAll('th[data-sort]');
            headers.forEach(header => {
                const col = header.getAttribute('data-sort');
                const up = header.querySelector('.bolopa-tabel-sort-up');
                const down = header.querySelector('.bolopa-tabel-sort-down');
                up?.classList.remove('bolopa-tabel-active');
                down?.classList.remove('bolopa-tabel-active');
                if (col === activeColumn) {
                    if (direction === 'asc') up?.classList.add('bolopa-tabel-active'); else down?.classList.add('bolopa-tabel-active');
                }
            });
        }
    }

    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-form');
            
            // Get batch name for display
            const batchName = (() => {
                const row = this.closest('tr');
                if (!row) return 'Data Pembesaran';
                const batchCell = row.cells[1]; // Batch column
                return batchCell ? batchCell.textContent.trim() || 'Data Pembesaran' : 'Data Pembesaran';
            })();

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `
                    <div style="text-align: center; margin-bottom: 15px; padding: 0 10px;">
                        Apakah Anda yakin ingin menghapus data pembesaran ini?
                    </div>
                    <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 12px; border-radius: 6px; margin: 15px 10px; text-align: left;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-exclamation-triangle text-danger"></i>
                            <div>
                                <div style="font-weight: 600; color: #991b1b; font-size: 14px; margin-bottom: 2px;">Batch yang akan dihapus:</div>
                                <div style="font-weight: 700; color: #7f1d1d; font-size: 16px; background: #fecaca; padding: 4px 8px; border-radius: 4px; display: inline-block;">${batchName}</div>
                            </div>
                        </div>
                    </div>
                    <div style="background: #fef3c7; border-left: 4px solid #d97706; padding: 12px; border-radius: 6px; margin: 15px 10px; text-align: left;">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <i class="fa-solid fa-info-circle text-warning" style="margin-top: 2px;"></i>
                            <div>
                                <div style="font-weight: 600; color: #92400e; font-size: 14px; margin-bottom: 4px;">Peringatan</div>
                                <div style="color: #78350f; font-size: 13px; line-height: 1.4;">
                                    Data yang sudah dihapus <strong>tidak dapat dikembalikan</strong> dan akan hilang secara permanen dari sistem.
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-danger px-4 py-2',
                    cancelButton: 'btn btn-secondary px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Data...',
                        html: `
                            <div style="text-align: center;">
                                <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p style="margin-top: 15px; color: #6c757d;">Mohon tunggu sebentar, data sedang dihapus...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            setTimeout(() => {
                                try {
                                    form.submit();
                                } catch (error) {
                                    console.error('Error submitting form:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Terjadi kesalahan saat menghapus data'
                                    });
                                }
                            }, 500);
                        }
                    });
                }
            });
        });
    });

    @if(session('success'))
        triggerFlashToast('success', 'Berhasil!', @json(session('success')));
    @endif

    @if(session('error'))
        triggerFlashToast('error', 'Gagal!', @json(session('error')));
    @endif
});
</script>

@endsection
