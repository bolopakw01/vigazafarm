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
                <button class="bolopa-tabel-btn bolopa-tabel-btn-primary" id="btnPrint" onclick="window.print()">
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
                        <td class="bolopa-tabel-text-center">{{ $penetasan->firstItem() + $index }}</td>
                        <td class="bolopa-tabel-text-left">{{ $item->batch ?? '-' }}</td>
                        <td class="bolopa-tabel-text-left">{{ $item->kandang->nama_kandang ?? '-' }}</td>
                        <td class="bolopa-tabel-text-center">{{ \Carbon\Carbon::parse($item->tanggal_simpan_telur)->format('d/m/Y') }}</td>
                        <td class="bolopa-tabel-text-right">{{ number_format($item->jumlah_telur) }} butir</td>
                        <td class="bolopa-tabel-text-center">{{ $item->tanggal_menetas ? \Carbon\Carbon::parse($item->tanggal_menetas)->format('d/m/Y') : '-' }}</td>
                        <td class="bolopa-tabel-text-center">
                            @php
                                $statusClass = 'bolopa-tabel-badge-secondary';
                                $statusText = 'Proses';
                                switch($item->status ?? 'proses') {
                                    case 'aktif':
                                        $statusClass = 'bolopa-tabel-badge-info';
                                        $statusText = 'Aktif';
                                        break;
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
                        <td class="bolopa-tabel-text-center">
                            <div class="bolopa-tabel-actions">
                                <button type="button"
                                    class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action"
                                    title="Lihat Detail"
                                    onclick="showDetailModal(@js([
                                        'id' => $item->id,
                                        'kandang' => $item->kandang->nama_kandang ?? '-',
                                        'formatted_tanggal_simpan_telur' => optional($item->tanggal_simpan_telur)->format('d/m/Y'),
                                        'formatted_tanggal_menetas' => optional($item->tanggal_menetas)->format('d/m/Y'),
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
                {{-- Previous Button --}}
                <a href="{{ $penetasan->previousPageUrl() }}" class="bolopa-tabel-pagination-btn {{ $penetasan->onFirstPage() ? 'disabled' : '' }}">
                    <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous" width="18" height="18">
                </a>

                {{-- Page Numbers --}}
                @foreach($penetasan->getUrlRange(1, $penetasan->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="bolopa-tabel-pagination-btn {{ $page == $penetasan->currentPage() ? 'bolopa-tabel-active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Next Button --}}
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
    document.getElementById('btnExport')?.addEventListener('click', function() {
        showToast('Fitur export sedang dalam pengembangan', 'info');
    });

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

    // Show success/error messages with SweetAlert2
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
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

    // SweetAlert2 Detail Modal Function
    function showDetailModal(data) {
        const numberFormatter = new Intl.NumberFormat('id-ID');
        const percentFormatter = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

        const escapeHtml = (value = '') => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const toNumber = (value) => {
            if (value === null || value === undefined || value === '') return null;
            const numeric = Number(value);
            return Number.isFinite(numeric) ? numeric : null;
        };

        const formatNumber = (value, unit = '') => {
            const numeric = toNumber(value);
            if (numeric === null) return '-';
            const formatted = numberFormatter.format(numeric);
            return unit ? `${formatted} ${unit}` : formatted;
        };

        const formatDecimal = (value, unit) => {
            const numeric = toNumber(value);
            if (numeric === null) return '-';
            const rounded = Math.abs(numeric - Math.round(numeric)) < 0.05
                ? Math.round(numeric).toString()
                : numeric.toFixed(1);
            return `${rounded}${unit}`;
        };

        const formatPercent = (value) => {
            const numeric = toNumber(value);
            if (numeric === null) return '-';
            return `${percentFormatter.format(numeric)}%`;
        };

        const withinRange = (value, min, max) => {
            const numeric = toNumber(value);
            if (numeric === null) return false;
            return numeric >= min && numeric <= max;
        };

        const totalEggs = toNumber(data.jumlah_telur);
        const hatched = toNumber(data.jumlah_menetas);
        const infertile = toNumber(data.telur_tidak_fertil);
        const doc = toNumber(data.jumlah_doc);
        const percent = toNumber(data.persentase_tetas);
        const suhu = toNumber(data.suhu_penetasan);
        const kelembaban = toNumber(data.kelembaban_penetasan);

        const failed = totalEggs !== null
            ? Math.max(totalEggs - (hatched ?? 0) - (infertile ?? 0), 0)
            : null;

        const statusInfo = (() => {
            if (percent === null && suhu === null && kelembaban === null) {
                return {
                    label: 'Belum Ada Data',
                    variant: 'secondary',
                    description: 'Tambahkan data suhu, kelembapan, atau persentase tetas untuk memantau performa batch ini.'
                };
            }

            if (percent !== null) {
                const isOptimal = percent >= 85 && withinRange(suhu, 36.5, 38.0) && withinRange(kelembaban, 55, 65);
                if (isOptimal) {
                    return {
                        label: 'Optimal',
                        variant: 'success',
                        description: 'Kinerja penetasan berada dalam rentang optimal. Pertahankan prosedur dan pencatatan saat ini.'
                    };
                }

                if (percent >= 60) {
                    return {
                        label: 'Perlu Pemantauan',
                        variant: 'warning',
                        description: 'Hasil penetasan cukup baik namun masih dapat ditingkatkan. Periksa kembali stabilitas suhu, kelembapan, dan rotasi telur.'
                    };
                }
            }

            return {
                label: 'Perlu Tindakan',
                variant: 'danger',
                description: 'Hasil penetasan rendah. Evaluasi kualitas telur, protokol pembalikan, dan kalibrasi mesin penetas.'
            };
        })();

        const variantMapping = {
            success: { badge: 'bg-success-subtle text-success-emphasis', progress: 'bg-success' },
            warning: { badge: 'bg-warning-subtle text-warning-emphasis', progress: 'bg-warning' },
            danger: { badge: 'bg-danger-subtle text-danger-emphasis', progress: 'bg-danger' },
            secondary: { badge: 'bg-secondary-subtle text-secondary-emphasis', progress: 'bg-secondary' }
        };
        const variantClass = variantMapping[statusInfo.variant] || variantMapping.secondary;
        const statusBadge = `<span class="badge ${variantClass.badge} px-3 py-2 rounded-pill">${statusInfo.label}</span>`;

        const statusIconMap = {
            success: 'fa-solid fa-circle-check',
            warning: 'fa-solid fa-triangle-exclamation',
            danger: 'fa-solid fa-circle-exclamation',
            secondary: 'fa-regular fa-circle'
        };

        const statusAccentClassMap = {
            success: 'icon-circle-success',
            warning: 'icon-circle-warning',
            danger: 'icon-circle-danger',
            secondary: 'icon-circle-neutral'
        };

        const statusIconClass = statusIconMap[statusInfo.variant] || 'fa-solid fa-circle-info';
        const statusAccentClass = statusAccentClassMap[statusInfo.variant] || 'icon-circle-neutral';

        const percentDisplay = formatPercent(percent);
        const percentProgress = percent !== null ? Math.max(Math.min(percent, 100), 0) : null;

        const temperatureStatus = (() => {
            if (suhu === null) {
                return { label: 'Belum Ada', badge: 'bg-secondary-subtle text-secondary-emphasis' };
            }
            return withinRange(suhu, 36.5, 38.0)
                ? { label: 'Stabil', badge: 'bg-success-subtle text-success-emphasis' }
                : { label: 'Periksa', badge: 'bg-warning-subtle text-warning-emphasis' };
        })();

        const humidityStatus = (() => {
            if (kelembaban === null) {
                return { label: 'Belum Ada', badge: 'bg-secondary-subtle text-secondary-emphasis' };
            }
            return withinRange(kelembaban, 55, 65)
                ? { label: 'Stabil', badge: 'bg-success-subtle text-success-emphasis' }
                : { label: 'Periksa', badge: 'bg-warning-subtle text-warning-emphasis' };
        })();

        const operationalMessage = (() => {
            switch (statusInfo.variant) {
                case 'success':
                    return 'Pertahankan jadwal pengecekan harian agar status tetap optimal.';
                case 'warning':
                    return 'Kalibrasi sensor suhu & kelembapan dan lakukan pemantauan 24 jam ke depan.';
                case 'danger':
                    return 'Segera lakukan inspeksi mesin penetas serta validasi mutu telur dan SOP.';
                default:
                    return 'Tambahkan data lingkungan untuk mendapatkan rekomendasi operasional.';
            }
        })();

        const environmentHeadline = (() => {
            const parts = [];
            parts.push(temperatureStatus.label !== 'Belum Ada' ? `Suhu ${temperatureStatus.label}` : 'Suhu belum ada');
            parts.push(humidityStatus.label !== 'Belum Ada' ? `RH ${humidityStatus.label}` : 'RH belum ada');
            return parts.join(' • ');
        })();

        const totalEggsDisplay = formatNumber(totalEggs, 'butir');
        const hatchedDisplay = formatNumber(hatched, 'ekor');
        const infertileDisplay = formatNumber(infertile, 'butir');
        const docDisplay = doc !== null ? formatNumber(doc, 'ekor') : (hatched !== null ? hatchedDisplay : '-');
        const failedDisplay = formatNumber(failed, 'butir');

        const kandangName = escapeHtml(data.kandang ?? '-');
        const noteHtml = data.catatan ? escapeHtml(data.catatan).replace(/\n/g, '<br>') : '<em class="text-muted">Tidak ada catatan tambahan.</em>';
        const createdAt = data.formatted_dibuat_pada ? escapeHtml(data.formatted_dibuat_pada) : '-';
        const updatedAt = data.formatted_diperbarui_pada ? escapeHtml(data.formatted_diperbarui_pada) : '-';
        const dateStored = data.formatted_tanggal_simpan_telur ? escapeHtml(data.formatted_tanggal_simpan_telur) : '-';
        const dateHatched = data.formatted_tanggal_menetas ? escapeHtml(data.formatted_tanggal_menetas) : '-';

        const environmentSummary = [
            suhu !== null ? `Suhu ${formatDecimal(suhu, '°C')} (${temperatureStatus.label})` : 'Suhu belum tersedia',
            kelembaban !== null ? `RH ${formatDecimal(kelembaban, '%')} (${humidityStatus.label})` : 'Kelembapan belum tersedia'
        ].map(line => escapeHtml(line)).join('<br>');

        const metrics = [
            { label: 'Jumlah Telur', value: totalEggsDisplay, icon: 'fa-solid fa-egg', accent: 'icon-circle-primary' },
            { label: 'Menetas', value: hatchedDisplay, icon: 'fa-solid fa-feather-pointed', accent: 'icon-circle-success' },
            { label: 'DOC', value: docDisplay, icon: 'fa-solid fa-dove', accent: 'icon-circle-info' },
            { label: 'Tidak Fertil', value: infertileDisplay, icon: 'fa-solid fa-ban', accent: 'icon-circle-danger' },
            { label: 'Perkiraan Gagal', value: failedDisplay, icon: 'fa-solid fa-chart-line-down', accent: 'icon-circle-warning' },
            { label: 'Persentase', value: percentDisplay, icon: 'fa-solid fa-gauge-high', accent: statusAccentClass }
        ];

        const metricsHtml = metrics.map(metric => `
            <div class="col">
                <div class="metric-card border h-100 p-3">
                    <span class="icon-circle metric-icon ${metric.accent}">
                        <i class="${metric.icon}"></i>
                    </span>
                    <p class="metric-label text-uppercase text-muted small fw-semibold mb-1">${metric.label}</p>
                    <p class="metric-value text-body fw-semibold fs-6 mb-0">${metric.value}</p>
                </div>
            </div>
        `).join('');

        const highlightCards = [
            {
                label: 'Status Batch',
                title: escapeHtml(statusInfo.label),
                description: escapeHtml(statusInfo.description),
                icon: statusIconClass,
                accent: statusAccentClass
            },
            {
                label: 'Operasional',
                title: 'Langkah Disarankan',
                description: escapeHtml(operationalMessage),
                icon: 'fa-solid fa-screwdriver-wrench',
                accent: 'icon-circle-info'
            },
            {
                label: 'Kondisi Inkubator',
                title: escapeHtml(environmentHeadline),
                description: environmentSummary,
                icon: 'fa-solid fa-temperature-half',
                accent: 'icon-circle-primary'
            }
        ];

        const summaryHighlightsHtml = highlightCards.map(card => `
            <div class="col">
                <div class="summary-highlight h-100">
                    <span class="icon-circle ${card.accent}">
                        <i class="${card.icon}"></i>
                    </span>
                    <div class="summary-highlight-content">
                        <p class="summary-highlight-label mb-1">${card.label}</p>
                        <p class="summary-highlight-title mb-1">${card.title}</p>
                        ${card.description ? `<p class="summary-highlight-description mb-0">${card.description}</p>` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        const timelineItems = [
            { label: 'Tanggal Simpan', value: dateStored, icon: 'fa-regular fa-calendar-days', accent: 'icon-circle-primary' },
            { label: 'Tanggal Menetas', value: dateHatched, icon: 'fa-solid fa-egg', accent: 'icon-circle-warning' },
            { label: 'Dibuat Pada', value: createdAt, icon: 'fa-regular fa-clock', accent: 'icon-circle-neutral' },
            { label: 'Diperbarui', value: updatedAt, icon: 'fa-solid fa-rotate', accent: 'icon-circle-info' }
        ];

        const timelineGridHtml = timelineItems.map(item => `
            <div class="col-6">
                <div class="timeline-card h-100">
                    <span class="icon-circle ${item.accent}">
                        <i class="${item.icon}"></i>
                    </span>
                    <div>
                        <p class="timeline-label mb-1">${item.label}</p>
                        <p class="timeline-value mb-0">${item.value}</p>
                    </div>
                </div>
            </div>
        `).join('');

        const htmlContent = `
            <div class="bolopa-detail-modal container-fluid px-0">
                <div class="row g-3 align-items-stretch">
                    <div class="col-lg-6 d-flex flex-column gap-3">
                        <div class="card summary-card border-0 shadow-sm flex-grow-1">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                    <div>
                                        <p class="text-uppercase text-muted small fw-semibold mb-1">Batch Penetasan</p>
                                        <h5 class="fw-semibold text-body mb-1">Penetasan #${escapeHtml(data.id ?? '-')}</h5>
                                        <p class="text-muted mb-0">Kandang: <span class="fw-semibold text-body">${kandangName}</span></p>
                                    </div>
                                    <div class="text-lg-end d-flex flex-column align-items-lg-end gap-2">
                                        <div>${statusBadge}</div>
                                        <div class="fw-semibold text-primary fs-3">${percentDisplay}</div>
                                        <p class="small text-muted mb-0">Persentase Tetas</p>
                                    </div>
                                </div>
                                ${percentProgress !== null ? `<div class="progress mt-3"><div class="progress-bar ${variantClass.progress}" role="progressbar" style="width: ${percentProgress}%;"></div></div>` : ''}
                                <div class="row row-cols-1 row-cols-sm-3 g-3 summary-highlights mt-3">
                                    ${summaryHighlightsHtml}
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase fw-semibold small mb-3">Kondisi Lingkungan</h6>
                                <div class="row g-3 align-items-stretch">
                                    <div class="col-sm-6">
                                        <div class="environment-card border rounded-4 p-3 h-100">
                                            <div class="d-flex align-items-start gap-3">
                                                <span class="icon-circle icon-circle-heat">
                                                    <i class="fa-solid fa-temperature-three-quarters"></i>
                                                </span>
                                                <div>
                                                    <p class="environment-label mb-1">Suhu Penetasan</p>
                                                    <div class="d-flex align-items-baseline gap-2 flex-wrap">
                                                        <span class="environment-value">${formatDecimal(suhu, '°C')}</span>
                                                        <span class="badge ${temperatureStatus.badge}">${temperatureStatus.label}</span>
                                                    </div>
                                                    <p class="small text-muted mb-0 mt-2">Rekomendasi 37.0°C – 38.0°C</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="environment-card border rounded-4 p-3 h-100">
                                            <div class="d-flex align-items-start gap-3">
                                                <span class="icon-circle icon-circle-humidity">
                                                    <i class="fa-solid fa-droplet"></i>
                                                </span>
                                                <div>
                                                    <p class="environment-label mb-1">Kelembapan</p>
                                                    <div class="d-flex align-items-baseline gap-2 flex-wrap">
                                                        <span class="environment-value">${formatDecimal(kelembaban, '%')}</span>
                                                        <span class="badge ${humidityStatus.badge}">${humidityStatus.label}</span>
                                                    </div>
                                                    <p class="small text-muted mb-0 mt-2">Rekomendasi 55% – 65%</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="border rounded-4 p-3 bg-body-tertiary operational-card">
                                            <div class="d-flex align-items-start gap-3">
                                                <span class="icon-circle icon-circle-operational">
                                                    <i class="fa-solid fa-headset"></i>
                                                </span>
                                                <div>
                                                    <p class="text-muted small text-uppercase fw-semibold mb-1">Rekomendasi Operasional</p>
                                                    <p class="small mb-0 text-body-secondary">${escapeHtml(operationalMessage)}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex flex-column gap-3">
                        <div class="card border-0 shadow-sm flex-grow-1">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase fw-semibold small mb-3">Ringkasan Produksi</h6>
                                <div class="row row-cols-2 row-cols-md-3 g-3 metrics-grid">
                                    ${metricsHtml}
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase fw-semibold small mb-3">Timeline</h6>
                                <div class="row row-cols-2 g-3 timeline-grid">
                                    ${timelineGridHtml}
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase fw-semibold small mb-3">Catatan</h6>
                                <div class="note-box d-flex align-items-start gap-3">
                                    <span class="icon-circle icon-circle-note">
                                        <i class="fa-regular fa-note-sticky"></i>
                                    </span>
                                    <div class="text-body note-text">${noteHtml}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        Swal.fire({
            title: '<strong class="text-primary">📋 Detail Data Penetasan</strong>',
            html: htmlContent,
            width: 'min(1000px, 94vw)',
            padding: '1.25rem',
            showCloseButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#4361ee',
            customClass: {
                container: 'bolopa-swal-container',
                popup: 'bolopa-swal-popup',
                title: 'bolopa-swal-title',
                htmlContainer: 'bolopa-swal-html'
            }
        });
    }

    // Handle delete confirmation with SweetAlert2
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('.delete-form');
                const batchName = form.dataset.batch;
                
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `
                        <div style="text-align: left;">
                            <p style="margin-bottom: 10px;">Apakah Anda yakin ingin menghapus data penetasan ini?</p>
                            <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 12px; border-radius: 4px; margin-top: 15px;">
                                <p style="margin: 0; color: #991b1b; font-weight: 600;">
                                    <i class="fa-solid fa-exclamation-triangle"></i> Batch: ${batchName}
                                </p>
                            </div>
                            <p style="margin-top: 15px; color: #dc2626; font-size: 14px;">
                                <i class="fa-solid fa-info-circle"></i> <strong>Perhatian:</strong> Data yang sudah dihapus tidak dapat dikembalikan!
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa-solid fa-trash"></i> Ya, Hapus!',
                    cancelButtonText: '<i class="fa-solid fa-times"></i> Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'btn btn-danger px-4',
                        cancelButton: 'btn btn-secondary px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menghapus Data...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Submit form
                        form.submit();
                    }
                });
            });
        });
    });
</script>

@endsection
