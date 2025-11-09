@extends('admin.layouts.app')

@section('title', 'Data Penetasan')

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-penetasan.css') }}">
@endpush

@section('content')
<div class="bolopa-tabel-wrapper">
    <div class="bolopa-tabel-container">
        <header>
            <h1>
                <img src="{{ asset('bolopa/img/icon/game-icons--nest-eggs.svg') }}" alt="Penetasan" width="28" height="28" style="vertical-align: middle;">
                Data Penetasan
            </h1>
            <a href="{{ route('admin.penetasan.create') }}" class="bolopa-tabel-btn bolopa-tabel-btn-primary">
                <img src="{{ asset('bolopa/img/icon/line-md--plus-square-filled.svg') }}" alt="Tambah" width="20" height="20" style="vertical-align: middle;">
                Tambah Penetasan
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
                        <th data-sort="tanggal_simpan" class="bolopa-tabel-text-center">
                            Tanggal Simpan
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="jumlah_telur" class="bolopa-tabel-text-right">
                            Jumlah Telur
                            <span class="bolopa-tabel-sort-wrap">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
                                <img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
                            </span>
                        </th>
                        <th data-sort="tanggal_menetas" class="bolopa-tabel-text-center">
                            Tanggal Menetas
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
                        <th data-sort="aksi" style="width: 180px;" class="bolopa-tabel-text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penetasan as $index => $item)
                    <tr>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">{{ $penetasan->firstItem() + $index }}</td>
                        <td class="bolopa-tabel-text-left" style="text-align: left;">{{ $item->batch ?? '-' }}</td>
                        <td class="bolopa-tabel-text-left" style="text-align: left;">{{ $item->kandang->nama_kandang ?? '-' }}</td>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">{{ \Carbon\Carbon::parse($item->tanggal_simpan_telur)->format('d/m/Y') }}</td>
                        <td class="bolopa-tabel-text-right" style="text-align: right;">{{ number_format($item->jumlah_telur) }} butir</td>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">{{ $item->tanggal_menetas ? \Carbon\Carbon::parse($item->tanggal_menetas)->format('d/m/Y') : '-' }}</td>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">
                            @php
                                $statusClass = 'bolopa-tabel-badge-secondary';
                                $statusText = 'Proses';
                                switch($item->status ?? 'proses') {
                                    case 'selesai':
                                        $statusClass = 'bolopa-tabel-badge-success';
                                        $statusText = 'Selesai';
                                        break;
                                    case 'gagal':
                                        $statusClass = 'bolopa-tabel-badge-danger';
                                        $statusText = 'Gagal';
                                        break;
                                }
                            @endphp
                            <span class="bolopa-tabel-badge {{ $statusClass }}" data-status="{{ $item->status ?? 'proses' }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="bolopa-tabel-text-center" style="text-align: center;">
                            <div class="bolopa-tabel-actions">
                                <button type="button"
                                    class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action"
                                    title="Lihat Detail"
                                    onclick="showDetailModal(@js([
                                        'id' => $item->id,
                                        'batch' => $item->batch ?? '-',
                                        'kandang' => $item->kandang->nama_kandang ?? '-',
                                        'formatted_tanggal_simpan_telur' => optional($item->tanggal_simpan_telur)->format('d/m/Y'),
                                        'formatted_tanggal_simpan_telur_waktu' => optional($item->tanggal_simpan_telur)->format('d/m/Y H:i'),
                                        'formatted_tanggal_menetas' => optional($item->tanggal_menetas)->format('d/m/Y'),
                                        'formatted_tanggal_menetas_waktu' => optional($item->tanggal_menetas)->format('d/m/Y H:i'),
                                        'jumlah_telur' => $item->jumlah_telur,
                                        'jumlah_menetas' => $item->jumlah_menetas,
                                        'jumlah_doc' => $item->jumlah_doc,
                                        'telur_tidak_fertil' => $item->telur_tidak_fertil,
                                        'persentase_tetas' => $item->persentase_tetas,
                                        'suhu_penetasan' => $item->suhu_penetasan,
                                        'kelembaban_penetasan' => $item->kelembaban_penetasan,
                                        'catatan' => $item->catatan,
                                        'status' => $item->status ?? 'proses',
                                        'formatted_dibuat_pada' => optional($item->dibuat_pada)->format('d/m/Y H:i'),
                                        'formatted_diperbarui_pada' => optional($item->diperbarui_pada)->format('d/m/Y H:i')
                                    ]))">
                                    <img src="{{ asset('bolopa/img/icon/line-md--watch.svg') }}" alt="View" width="14" height="14">
                                </button>
                                
                                {{-- @if($item->status === 'selesai' && $item->jumlah_doc > 0)
                                <a href="{{ route('admin.pembesaran.createFromPenetasan', $item->id) }}" class="bolopa-tabel-btn bolopa-tabel-btn-success bolopa-tabel-btn-action" title="Pindahkan ke Pembesaran">
                                    <img src="{{ asset('bolopa/img/icon/game-icons--nest-birds.svg') }}" alt="To Growing" width="14" height="14">
                                </a>
                                @endif --}}
                                
                                <a href="{{ route('admin.penetasan.edit', $item->id) }}" class="bolopa-tabel-btn bolopa-tabel-btn-warning bolopa-tabel-btn-action" title="Edit">
                                    <img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" alt="Edit" width="14" height="14">
                                </a>
                                <form action="{{ route('admin.penetasan.destroy', $item->id) }}" method="POST" style="display: inline;" class="delete-form" data-batch="{{ $item->batch ?? 'Data' }}">
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
                            <img src="{{ asset('bolopa/img/icon/game-icons--nest-eggs.svg') }}" alt="No Data" width="64" height="64" style="opacity: 0.3;">
                            <p style="margin-top: 16px; color: #6c757d;">Tidak ada data penetasan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bolopa-tabel-pagination">
            <div class="bolopa-tabel-pagination-info">
                Menampilkan {{ $penetasan->firstItem() ?? 0 }} sampai {{ $penetasan->lastItem() ?? 0 }} dari {{ $penetasan->total() }} entri
            </div>
            <div class="bolopa-tabel-pagination-buttons">
                <a href="{{ $penetasan->previousPageUrl() }}" class="bolopa-tabel-pagination-btn {{ $penetasan->onFirstPage() ? 'disabled' : '' }}">
                    <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous" width="18" height="18">
                </a>

                @foreach($penetasan->getUrlRange(1, $penetasan->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="bolopa-tabel-pagination-btn {{ $page == $penetasan->currentPage() ? 'bolopa-tabel-active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach

                <a href="{{ $penetasan->nextPageUrl() }}" class="bolopa-tabel-pagination-btn {{ !$penetasan->hasMorePages() ? 'disabled' : '' }}">
                    <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" alt="Next" width="18" height="18">
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="bolopa-tabel-toast" id="toast"></div>

<script>
    // Search functionality with debounce
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const entriesSelect = document.getElementById('entriesSelect');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('search', this.value);
                url.searchParams.set('page', '1'); // Reset to first page
                window.location.href = url.toString();
            }, 500);
        });
    }

    if (entriesSelect) {
        entriesSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1'); // Reset to first page
            window.location.href = url.toString();
        });
    }

    // Export functionality
    const tableUtils = (function() {
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
            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                showToast('Popup diblokir. Izinkan pop-up untuk mencetak.', 'error');
                return;
            }
            const doc = printWindow.document;
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
            printWindow.focus();
            printWindow.print();
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
        const { headers, skipIndexes } = tableUtils.collectHeaders(table);

        exportBtn?.addEventListener('click', () => {
            const rows = tableUtils.collectRows(table, skipIndexes);
            if (!rows.length) {
                showToast('Tidak ada data untuk diekspor', 'info');
                return;
            }
            const csvContent = tableUtils.toCSV([headers, ...rows]);
            tableUtils.downloadCSV(csvContent, `penetasan-${tableUtils.timestamp()}.csv`);
            showToast('File CSV berhasil disiapkan', 'success');
        });

        printBtn?.addEventListener('click', () => {
            const rows = tableUtils.collectRows(table, skipIndexes);
            tableUtils.printTable('Data Penetasan', headers, rows);
        });
    })();

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.style.background = type === 'success' ? '#28a745' : (type === 'info' ? '#17a2b8' : '#dc3545');
        toast.classList.add('bolopa-tabel-show');

        setTimeout(() => {
            toast.classList.remove('bolopa-tabel-show');
        }, 3000);
    }

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
            didOpen: toast => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    };

    @if(session('success'))
    triggerFlashToast('success', 'Berhasil!', @json(session('success')));
    @endif

    @if(session('error'))
    triggerFlashToast('error', 'Gagal!', @json(session('error')), 4500);
    @endif

    // Table sorting (client-side)
    const thElements = document.querySelectorAll('th[data-sort]');
    let currentSort = {
        column: null,
        direction: 'asc'
    };

    thElements.forEach(th => {
        th.addEventListener('click', () => {
            const column = th.getAttribute('data-sort');
            if (column === 'aksi') return; // Don't sort actions column

            sortTable(column);
        });
    });

    function sortTable(column) {
        const table = document.getElementById('dataTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = Array.from(tbody.getElementsByTagName('tr'));

        // Update sort direction
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }

        // Update UI - Remove all active classes
        thElements.forEach(th => {
            th.classList.remove('bolopa-tabel-active');
            const icons = th.querySelectorAll('.bolopa-tabel-sort-icon');
            icons.forEach(icon => icon.classList.remove('bolopa-tabel-active'));
        });

        // Add active class to current column and icon
        const currentTh = document.querySelector(`th[data-sort="${column}"]`);
        if (currentTh) {
            currentTh.classList.add('bolopa-tabel-active');
            const currentUp = currentTh.querySelector('.bolopa-tabel-sort-up');
            const currentDown = currentTh.querySelector('.bolopa-tabel-sort-down');
            
            if (currentSort.direction === 'asc' && currentUp) {
                currentUp.classList.add('bolopa-tabel-active');
            } else if (currentSort.direction === 'desc' && currentDown) {
                currentDown.classList.add('bolopa-tabel-active');
            }
        }

        // Sort rows
        const columnIndex = getColumnIndex(column);
        rows.sort((a, b) => {
            let aValue = a.getElementsByTagName('td')[columnIndex]?.textContent.trim() || '';
            let bValue = b.getElementsByTagName('td')[columnIndex]?.textContent.trim() || '';

            // Remove formatting for numbers
            aValue = aValue.replace(/[^\d.-]/g, '');
            bValue = bValue.replace(/[^\d.-]/g, '');

            const aNum = parseFloat(aValue);
            const bNum = parseFloat(bValue);

            let comparison = 0;
            if (!isNaN(aNum) && !isNaN(bNum)) {
                comparison = aNum - bNum;
            } else {
                comparison = aValue.localeCompare(bValue);
            }

            return currentSort.direction === 'asc' ? comparison : -comparison;
        });

        // Remove and re-add rows
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        rows.forEach(row => tbody.appendChild(row));
    }

    function getColumnIndex(column) {
        const headers = Array.from(document.querySelectorAll('th'));
        return headers.findIndex(header => header.getAttribute('data-sort') === column);
    }

    // SweetAlert2 Detail Modal Function - POPUPLoopa Style
    function showDetailModal(data) {
        const escapeHtml = (value = '') => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const toNumber = (value) => {
            if (value === null || value === undefined || value === '') return null;
            const numeric = Number(value);
            return Number.isFinite(numeric) ? numeric : null;
        };
        const formatNumber = (value) => {
            const numeric = toNumber(value);
            return numeric === null ? '-' : new Intl.NumberFormat('id-ID').format(numeric);
        };

        // Parse data
        const totalTelur = toNumber(data.jumlah_telur);
        const menetas = toNumber(data.jumlah_menetas);
        const doc = toNumber(data.jumlah_doc);
        const tidakFertil = toNumber(data.telur_tidak_fertil);
        const persentase = toNumber(data.persentase_tetas);
        const suhu = toNumber(data.suhu_penetasan);
        const kelembaban = toNumber(data.kelembaban_penetasan);
        const gagal = totalTelur !== null ? Math.max(totalTelur - (menetas ?? 0) - (tidakFertil ?? 0), 0) : null;

        // Split date and time helper
        const splitDateTime = (dateTimeStr) => {
            if (!dateTimeStr || dateTimeStr === '-') return { date: '-', time: '' };
            const parts = dateTimeStr.split(' ');
            return { 
                date: parts[0] || '-', 
                time: parts[1] || '' 
            };
        };

        const simpanDateTime = splitDateTime(data.formatted_tanggal_simpan_telur_waktu);
        const menetasDateTime = splitDateTime(data.formatted_tanggal_menetas_waktu);

        // Format dates
        const pad = (n) => String(n).padStart(2, '0');
        const formatDate = (dateStr) => {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`;
        };
        const formatDateTime = (dateStr) => {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
        };

        const htmlContent = `
            <style>
                /* Inline popup styles - vertical layout: label di atas, desc di bawah */
                .bolopa-popup-content .stats-grid .stat-item{grid-template-columns:44px 1fr min-content}
                .bolopa-popup-content .stats-grid .stat-item .stat-body{display:flex;flex-direction:column;align-items:flex-start;gap:2px}
                .bolopa-popup-content .stats-grid .stat-item .stat-body .label{white-space:nowrap;font-weight:600;font-size:0.95rem;color:#000}
                .bolopa-popup-content .stats-grid .stat-item .stat-body .desc{white-space:normal;word-wrap:break-word;width:100%;color:var(--muted);font-size:0.72rem;line-height:1.3}
                @media (max-width:520px){
                    .bolopa-popup-content .stats-grid .stat-item .stat-body .label{font-size:.88rem}
                    .bolopa-popup-content .stats-grid .stat-item .stat-body .desc{font-size:.68rem}
                }
            </style>
            <div class="bolopa-popup-content" style="display:flex;flex-direction:column;gap:12px">
        <div class="swal-body">
            <div class="left">
                <div class="panel">
                    <div class="card-summary">
                        <div class="card-header">
                            <div>
                                <h5>Ringkasan Hasil</h5>
                                <div class="subtle-date">${escapeHtml(data.formatted_tanggal_menetas || '-')} • ${escapeHtml(data.kandang || 'Kandang')}</div>
                            </div>
                            <div class="text-end"><small class="text-muted">Batch: <span class="id-val">${escapeHtml(data.batch || '-')}</span></small></div>
                        </div>
                        <div class="stats-grid">
                            <div class="stat-item"><div class="stat-icon icon total"><i class="fa-solid fa-egg"></i></div><div class="stat-body"><div class="label">Total Telur</div><div class="desc">Jumlah keseluruhan</div></div><div class="value" id="sw-total">${formatNumber(totalTelur)}</div></div>
                            <div class="stat-item"><div class="stat-icon icon menetas"><i class="fa-solid fa-circle-check"></i></div><div class="stat-body"><div class="label">Menetas</div><div class="desc">Telur yang menetas</div></div><div class="value" id="sw-menetas">${formatNumber(menetas)}</div></div>
                            <div class="stat-item"><div class="stat-icon icon doc"><i class="fa-solid fa-dove"></i></div><div class="stat-body"><div class="label">DOC</div><div class="desc">Day Old Chicks</div></div><div class="value" id="sw-doc">${formatNumber(doc)}</div></div>
                            <div class="stat-item"><div class="stat-icon icon fertil"><i class="fa-solid fa-seedling"></i></div><div class="stat-body"><div class="label">Tidak Fertil</div><div class="desc">Tidak berkembang</div></div><div class="value" id="sw-fertil">${formatNumber(tidakFertil)}</div></div>
                            <div class="stat-item"><div class="stat-icon icon gagal"><i class="fa-solid fa-triangle-exclamation"></i></div><div class="stat-body"><div class="label">Gagal</div><div class="desc">Gagal menetas</div></div><div class="value" id="sw-gagal">${formatNumber(gagal)}</div></div>
                        </div>
                        <div class="percent-row">
                            <div class="percent-info"><div class="label">% Tetas</div><div class="desc">Persentase keberhasilan menetas dari total</div></div>
                            <div style="min-width:170px;text-align:right"><div class="percent-value" id="sw-percent">${persentase !== null ? persentase.toFixed(1) : '0.0'}%</div><div class="progress mt-2" aria-hidden="true"><div class="progress-bar" id="sw-progressBar" role="progressbar" style="width:${persentase || 0}%" aria-valuemin="0" aria-valuemax="100"></div></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right">
                <div class="metrics-row">
                    <div class="card-simple" id="cardTemp" role="region" aria-label="Suhu"><div><p class="label">Suhu</p><div style="display:flex;align-items:baseline;gap:.4rem"><div class="value"><span id="metric-temp">${suhu !== null ? suhu.toFixed(1) : '—'}</span><span class="unit">°C</span></div></div><div class="target">Target: 37–38°C</div></div><div style="text-align:right"><div class="icon temp"><i class="fa-solid fa-temperature-half"></i></div></div></div>
                    <div class="card-simple" id="cardHum" role="region" aria-label="Kelembapan"><div><p class="label">Kelembapan</p><div style="display:flex;align-items:baseline;gap:.4rem"><div class="value"><span id="metric-hum">${kelembaban !== null ? kelembaban.toFixed(1) : '—'}</span><span class="unit">%</span></div></div><div class="target">Target: 55–65%</div></div><div style="text-align:right"><div class="icon hum"><i class="fa-solid fa-droplet"></i></div></div></div>
                </div>

                <div class="panel timeline-panel">
                    <div class="card-clean">
                        <div class="mini-label">Timeline</div>
                        <div class="entry" aria-label="Tanggal Menyimpan"><div style="display:flex;align-items:center;gap:.5rem"><i class="fa-regular fa-calendar-days entry-icon"></i><div class="label">Tanggal Menyimpan</div></div><div class="result-box"><div class="value">${escapeHtml(simpanDateTime.date)}</div><div class="time">${escapeHtml(simpanDateTime.time)}</div></div></div>
                        <div class="entry" aria-label="Tanggal Menetas"><div style="display:flex;align-items:center;gap:.5rem"><i class="fa-solid fa-calendar-check entry-icon"></i><div class="label">Tanggal Menetas</div></div><div class="result-box"><div class="value">${escapeHtml(menetasDateTime.date)}</div><div class="time">${escapeHtml(menetasDateTime.time)}</div></div></div>
                    </div>
                </div>

                <div class="note-row"><div class="note-card"><div class="stat-icon icon note"><i class="fa-regular fa-sticky-note"></i></div><div class="note-inner"><div class="note-title">Catatan</div><div class="note-desc">${escapeHtml(data.catatan) || 'Tidak ada catatan'}</div></div></div></div>
            </div>
        </div>

        <div class="footer-row" style="margin:0;border-top-left-radius:0;border-top-right-radius:0;border-bottom-left-radius:12px;border-bottom-right-radius:12px;padding:.9rem 1.2rem;">
            <div class="muted" style="display:flex;flex-direction:column;align-items:flex-start;gap:2px;"><div style="font-size:.92rem">Terakhir diperbarui: <strong>${escapeHtml(data.formatted_diperbarui_pada || '-')}</strong></div></div>
            <div><button id="sw-copy" class="btn btn-sm btn-ghost me-2" title="Salin ringkasan" aria-label="Salin"><i class="fa-regular fa-copy me-1"></i> Salin</button><button id="sw-close" class="btn btn-sm btn-primary" aria-label="Tutup">Tutup</button></div>
        </div>
    </div>
        `;

        Swal.fire({
            html: htmlContent,
            showConfirmButton: false,
            customClass: { popup: 'bolopa-popup-swal2-popup' },
            width: '960px',
            padding: '0',
            didOpen: () => {
                const root = Swal.getHtmlContainer();

                // Calculate and animate percentage
                const total = parseFloat(root.querySelector('#sw-total')?.textContent.replace(/\./g, '') || '0');
                const menotasVal = parseFloat(root.querySelector('#sw-menetas')?.textContent.replace(/\./g, '') || '0');
                const percentEl = root.querySelector('#sw-percent');
                const bar = root.querySelector('#sw-progressBar');
                let pct = 0;
                if (total > 0) pct = Math.max(0, Math.min(100, (menotasVal / total) * 100));
                if (percentEl) percentEl.textContent = (Math.round(pct * 10) / 10).toFixed(1) + '%';
                setTimeout(() => { if (bar) bar.style.width = pct + '%'; }, 80);

                // Animated Temperature & Humidity (random fluctuation)
                const tempEl = root.querySelector('#metric-temp');
                const humEl = root.querySelector('#metric-hum');
                const cardTemp = root.querySelector('#cardTemp');
                const cardHum = root.querySelector('#cardHum');
                
                // Store original values
                const originalTemp = suhu !== null ? suhu : 37.5;
                const originalHum = kelembaban !== null ? kelembaban : 60;
                
                // Random fluctuation range
                const tempRange = 0.3;  // ±0.3°C
                const humRange = 1.5;   // ±1.5%
                
                // Animation interval
                let intervalId = null;
                if (tempEl && humEl) {
                    intervalId = setInterval(() => {
                        // Generate random values within range
                        const tempFluctuation = (Math.random() - 0.5) * 2 * tempRange;
                        const humFluctuation = (Math.random() - 0.5) * 2 * humRange;
                        
                        const newTemp = originalTemp + tempFluctuation;
                        const newHum = originalHum + humFluctuation;
                        
                        // Fade out effect on numbers only
                        tempEl.style.opacity = '0.5';
                        humEl.style.opacity = '0.5';
                        
                        // Update display after fade out
                        setTimeout(() => {
                            tempEl.textContent = newTemp.toFixed(1);
                            humEl.textContent = newHum.toFixed(1);
                            
                            // Fade in effect
                            tempEl.style.opacity = '1';
                            humEl.style.opacity = '1';
                        }, 100);
                    }, 2000); // Update every 2 seconds
                }

                // Close button handler
                const closeBtn = root.querySelector('#sw-close');
                if (closeBtn) closeBtn.addEventListener('click', () => {
                    if (intervalId) clearInterval(intervalId); // Clear interval on close
                    Swal.close();
                });

                // Copy handler
                const copyBtn = root.querySelector('#sw-copy');
                if (copyBtn) copyBtn.addEventListener('click', async () => {
                    const summary = [
                        `ID: ${root.querySelector('.id-val')?.textContent || ''}`,
                        `Total Telur: ${root.querySelector('#sw-total')?.textContent || ''}`,
                        `Menetas: ${root.querySelector('#sw-menetas')?.textContent || ''}`,
                        `DOC: ${root.querySelector('#sw-doc')?.textContent || ''}`,
                        `Tidak Fertil: ${root.querySelector('#sw-fertil')?.textContent || ''}`,
                        `Gagal: ${root.querySelector('#sw-gagal')?.textContent || ''}`,
                        `Suhu: ${originalTemp.toFixed(1)}°C`,
                        `Kelembapan: ${originalHum.toFixed(1)}%`
                    ].join('\n');
                    try {
                        await navigator.clipboard.writeText(summary);
                        copyBtn.innerHTML = '<i class="fa-regular fa-copy me-1"></i> Tersalin';
                        setTimeout(() => copyBtn.innerHTML = '<i class="fa-regular fa-copy me-1"></i> Salin', 1500);
                    } catch (e) {
                        alert('Gagal menyalin');
                    }
                });
            }
        });
    }

    // Handle delete confirmation with SweetAlert2
    document.addEventListener('DOMContentLoaded', function() {
        // Use event delegation for better performance and to handle dynamically added elements
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-btn')) {
                e.preventDefault();

                const button = e.target.closest('.delete-btn');
                const form = button.closest('.delete-form');

                if (!form) {
                    console.error('Form delete tidak ditemukan');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memproses permintaan hapus'
                    });
                    return;
                }

                // Get batch name for display
                const batchName = (() => {
                    const row = button.closest('tr');
                    if (!row) return 'Data Penetasan';
                    const batchCell = row.cells[1]; // Batch column
                    return batchCell ? batchCell.textContent.trim() || 'Data Penetasan' : 'Data Penetasan';
                })();

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `
                        <div style="text-align: center; margin-bottom: 15px; padding: 0 10px;">
                            Apakah Anda yakin ingin menghapus data penetasan ini?
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
            }
        });
    });
</script>

@endsection
