@extends('admin.layouts.app')

@section('title', 'Produksi - Daftar Produksi')

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-produksi.css') }}">
<style>
    /* SVG Icon Styling */
    .bolopa-icon-svg {
        width: 20px;
        height: 20px;
        display: inline-block;
        vertical-align: middle;
    }
    
    h1 .bolopa-icon-svg {
        width: 28px;
        height: 28px;
    }
    
    .bolopa-tabel-sort-icon-svg {
        width: 12px;
        height: 12px;
        display: block;
    }
    /* Right control icons (Tambah Data, Export, Print) color filter */
    .bolopa-tabel-right-controls .bolopa-icon-svg {
        filter: invert(100%) sepia(3%) saturate(14%) hue-rotate(112deg) brightness(107%) contrast(105%);
    }

    /* Sort icons: stack much closer vertically and show as black when active */
    .bolopa-tabel-sort-wrap {
        display: inline-flex;
        flex-direction: column;
        gap: 0px; /* remove gap to bring icons adjacent */
        align-items: center;
        justify-content: center;
        line-height: 0;
        padding-left: 6px; /* slight separation from header text */
    }

    .bolopa-tabel-sort-icon {
        display: block;
        line-height: 0;
        opacity: 0.7;
        margin: 0;
        padding: 0;
        transform: translateY(0);
        transition: filter 0.12s ease, opacity 0.12s ease, transform 0.12s ease;
    }

    /* Slightly overlap icons so they're visually closer */
    .bolopa-tabel-sort-icon.bolopa-tabel-sort-up { transform: translateY(1px); }
    .bolopa-tabel-sort-icon.bolopa-tabel-sort-down { transform: translateY(-1px); }

    /* When a sort icon is active, make it visually black and more prominent */
    .bolopa-tabel-sort-icon.bolopa-tabel-active {
        /* Make icon appear black regardless of original color */
        filter: brightness(0) saturate(100%);
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
<div class="bolopa-tabel-wrapper">
    <div class="bolopa-tabel-container">
        <!-- Header Card -->
        <header>
            <h1>
                <img src="{{ asset('bolopa/img/icon/streamline-sharp--archive-box-solid.svg') }}" alt="Egg" class="bolopa-icon-svg">
                Produksi
            </h1>
        </header>

        <!-- KAI Summary Cards -->
        <div class="bolopa-kai-card" style="margin-bottom: 20px;">
            <div class="bolopa-kai-item">
                <div class="bolopa-kai-label">TOTAL TELUR</div>
                <div class="bolopa-kai-value"><strong>{{ number_format($totalTelur ?? 0) }}</strong> pcs</div>
                <div class="bolopa-kai-sub">Total produksi</div>
            </div>
            <div class="bolopa-kai-item">
                <div class="bolopa-kai-label">RATA-RATA/HARI</div>
                <div class="bolopa-kai-value"><strong>{{ number_format($rataTelurPerHari ?? 0, 2) }}</strong> pcs</div>
                <div class="bolopa-kai-sub">Per hari aktif</div>
            </div>
            <div class="bolopa-kai-item">
                <div class="bolopa-kai-label">PENDAPATAN</div>
                <div class="bolopa-kai-value"><strong>Rp {{ number_format($pendapatan ?? 0, 0, ',', '.') }}</strong></div>
                <div class="bolopa-kai-sub">Estimasi total</div>
            </div>
            <div class="bolopa-kai-item">
                <div class="bolopa-kai-label">LOSS/REJECT</div>
                <div class="bolopa-kai-value"><strong>{{ number_format($lostRate ?? 0, 2) }}%</strong></div>
                <div class="bolopa-kai-sub">Persentase reject</div>
            </div>
            <div class="bolopa-kai-item">
                <div class="bolopa-kai-label">BATCH AKTIF</div>
                <div class="bolopa-kai-value"><strong>{{ $batchAktif ?? 0 }}</strong></div>
                <div class="bolopa-kai-sub">Sedang produksi</div>
            </div>
            <div class="bolopa-kai-item">
                <div class="bolopa-kai-label">KANDANG AKTIF</div>
                <div class="bolopa-kai-value"><strong>{{ $kandangAktif ?? 0 }}</strong></div>
                <div class="bolopa-kai-sub">Unit produktif</div>
            </div>
            <div class="bolopa-kai-item">
                <div class="bolopa-kai-label">USIA RATA-RATA</div>
                <div class="bolopa-kai-value"><strong>{{ $usiaRataRata ?? 0 }}</strong> hari</div>
                <div class="bolopa-kai-sub">Umur produksi</div>
            </div>
            <div class="bolopa-kai-item">
                <div class="bolopa-kai-label">INDUKAN TOTAL</div>
                <div class="bolopa-kai-value"><strong>{{ number_format($totalIndukan ?? 0) }}</strong> ekor</div>
                <div class="bolopa-kai-sub">Jumlah indukan</div>
            </div>
        </div>

        <!-- Controls -->
        <div class="bolopa-tabel-controls">
            <div class="bolopa-tabel-left-controls">
                <div class="bolopa-tabel-entries-select">
                    <span>Tampilkan</span>
                    <select id="entriesSelect">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span>entri</span>
                </div>
                <div class="bolopa-tabel-search-box">
                    <img src="{{ asset('bolopa/img/icon/line-md--file-search-filled.svg') }}" alt="Search" class="bolopa-icon-svg">
                    <input type="text" id="searchInput" placeholder="Cari data..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="bolopa-tabel-right-controls">
                <a href="{{ route('admin.produksi.create') }}" class="bolopa-tabel-btn bolopa-tabel-btn-primary">
                    <img src="{{ asset('bolopa/img/icon/line-md--plus-square-filled.svg') }}" alt="Add" class="bolopa-icon-svg">
                    Tambah Data
                </a>
                <button class="bolopa-tabel-btn bolopa-tabel-btn-success" id="btnExport">
                    <img src="{{ asset('bolopa/img/icon/line-md--file-export-filled.svg') }}" alt="Export" class="bolopa-icon-svg">
                    Export
                </button>
                <button class="bolopa-tabel-btn bolopa-tabel-btn-info" id="btnPrint" onclick="window.print()">
                    <img src="{{ asset('bolopa/img/icon/line-md--cloud-alt-print-twotone-loop.svg') }}" alt="Print" class="bolopa-icon-svg">
                    Print
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bolopa-tabel-table-responsive">
            <table id="dataTable">
                <thead>
                    <tr>
                        <th data-sort="no">
                            No
                            <span class="bolopa-tabel-sort-wrap">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                            </span>
                        </th>
                        <th data-sort="tanggal">
                            Tanggal
                            <span class="bolopa-tabel-sort-wrap">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                            </span>
                        </th>
                        <th data-sort="kandang">
                            Kandang
                            <span class="bolopa-tabel-sort-wrap">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                            </span>
                        </th>
                        <th data-sort="batch">
                            Batch
                            <span class="bolopa-tabel-sort-wrap">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                            </span>
                        </th>
                        <th data-sort="telur">
                            Jumlah Telur
                            <span class="bolopa-tabel-sort-wrap">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                            </span>
                        </th>
                        <th data-sort="berat">
                            Berat Rata-rata
                            <span class="bolopa-tabel-sort-wrap">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                            </span>
                        </th>
                        <th data-sort="harga">
                            Harga/pcs
                            <span class="bolopa-tabel-sort-wrap">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                            </span>
                        </th>
                        <th data-sort="total">
                            Total (Rp)
                            <span class="bolopa-tabel-sort-wrap">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                            </span>
                        </th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produksi as $i => $row)
                    <tr>
                        <td>{{ $produksi->firstItem() + $i }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $row->kandang->nama_kandang ?? '-' }}</td>
                        <td>{{ $row->batch_produksi_id ?? '-' }}</td>
                        <td>{{ number_format($row->jumlah_telur ?? 0) }}</td>
                        <td>{{ $row->berat_rata_rata ? number_format($row->berat_rata_rata, 2) . ' g' : '-' }}</td>
                        <td>{{ number_format($row->harga_per_pcs ?? 0, 0, ',', '.') }}</td>
                        <td>{{ number_format(($row->jumlah_telur ?? 0) * ($row->harga_per_pcs ?? 0), 0, ',', '.') }}</td>
                        <td>
                            <div class="bolopa-tabel-actions">
                                <a href="{{ route('admin.produksi.show', $row->id) }}" class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-info" title="Lihat Detail">
                                    <img src="{{ asset('bolopa/img/icon/el--eye-open.svg') }}" alt="View" class="bolopa-icon-svg">
                                </a>
                                <a href="{{ route('admin.produksi.edit', $row->id) }}" class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-warning" title="Edit">
                                    <img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" alt="Edit" class="bolopa-icon-svg">
                                </a>
                                <button class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-danger" title="Hapus" onclick="confirmDelete({{ $row->id }})">
                                    <img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" alt="Delete" class="bolopa-icon-svg">
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px;">
                            <img src="{{ asset('bolopa/img/icon/iconoir--box-iso.svg') }}" alt="No Data" style="width: 60px; height: 60px; opacity: 0.3; margin-bottom: 10px;">
                            <p style="color: var(--bolopa-tabel-gray);">Tidak ada data produksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bolopa-tabel-pagination">
            <div class="bolopa-tabel-pagination-info">
                Menampilkan {{ $produksi->firstItem() ?? 0 }} - {{ $produksi->lastItem() ?? 0 }} dari {{ $produksi->total() ?? 0 }} entri
            </div>
            <div class="bolopa-tabel-pagination-buttons">
                @if($produksi->onFirstPage())
                    <button disabled>
                        <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous" class="bolopa-icon-svg">
                    </button>
                @else
                    <a href="{{ $produksi->previousPageUrl() }}" style="text-decoration: none;">
                        <button>
                            <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous" class="bolopa-icon-svg">
                        </button>
                    </a>
                @endif

                @foreach(range(1, $produksi->lastPage()) as $page)
                    @if($page == $produksi->currentPage())
                        <button class="bolopa-tabel-active">{{ $page }}</button>
                    @else
                        <a href="{{ $produksi->url($page) }}" style="text-decoration: none;">
                            <button>{{ $page }}</button>
                        </a>
                    @endif
                @endforeach

                @if($produksi->hasMorePages())
                    <a href="{{ $produksi->nextPageUrl() }}" style="text-decoration: none;">
                        <button>
                            <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" alt="Next" class="bolopa-icon-svg">
                        </button>
                    </a>
                @else
                    <button disabled>
                        <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" alt="Next" class="bolopa-icon-svg">
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="bolopa-tabel-toast" id="toast"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const entriesSelect = document.getElementById('entriesSelect');
    const btnExport = document.getElementById('btnExport');
    const table = document.getElementById('dataTable');
    const tbody = table ? table.querySelector('tbody') : null;
    
    // Sort state
    let sortState = {
        column: null,
        direction: 'asc'
    };

    // Search functionality
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

    // Entries per page
    if (entriesSelect) {
        entriesSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });
    }

    // Export CSV
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            window.location.href = '{{ route("admin.produksi") }}?export=csv';
        });
    }

    // Sort functionality
    if (table) {
        const headers = table.querySelectorAll('th[data-sort]');
        
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                const column = this.getAttribute('data-sort');
                sortTable(column);
            });
        });
    }

    function sortTable(column) {
        if (!tbody) return;
        
        const rows = Array.from(tbody.querySelectorAll('tr:not(:last-child)'));
        
        // Toggle sort direction
        if (sortState.column === column) {
            sortState.direction = sortState.direction === 'asc' ? 'desc' : 'asc';
        } else {
            sortState.column = column;
            sortState.direction = 'asc';
        }
        
        // Get column index
        const columnIndex = getColumnIndex(column);
        if (columnIndex === -1) return;
        
        // Sort rows
        rows.sort((a, b) => {
            const aValue = getCellValue(a, columnIndex);
            const bValue = getCellValue(b, columnIndex);
            
            let comparison = 0;
            
            // Handle different data types
            if (!isNaN(aValue) && !isNaN(bValue)) {
                // Numeric comparison
                comparison = parseFloat(aValue) - parseFloat(bValue);
            } else if (isDate(aValue) && isDate(bValue)) {
                // Date comparison
                comparison = parseDate(aValue) - parseDate(bValue);
            } else {
                // String comparison
                comparison = aValue.toString().localeCompare(bValue.toString());
            }
            
            return sortState.direction === 'asc' ? comparison : -comparison;
        });
        
        // Re-append rows
        rows.forEach(row => tbody.appendChild(row));
        
        // Update sort icons
        updateSortIcons(column, sortState.direction);
    }
    
    function getColumnIndex(sortKey) {
        const mapping = {
            'no': 0,
            'tanggal': 1,
            'kandang': 2,
            'batch': 3,
            'telur': 4,
            'berat': 5,
            'harga': 6,
            'total': 7
        };
        return mapping[sortKey] ?? -1;
    }
    
    function getCellValue(row, index) {
        const cell = row.cells[index];
        if (!cell) return '';
        
        let value = cell.textContent.trim();
        
        // Remove formatting for numbers
        value = value.replace(/\./g, '').replace(/,/g, '.').replace(/[^\d.-]/g, '');
        
        return value || cell.textContent.trim();
    }
    
    function isDate(value) {
        // Check if value is in DD/MM/YYYY format
        return /^\d{2}\/\d{2}\/\d{4}$/.test(value);
    }
    
    function parseDate(dateString) {
        // Convert DD/MM/YYYY to Date object
        const parts = dateString.split('/');
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }
    
    function updateSortIcons(activeColumn, direction) {
        const headers = table.querySelectorAll('th[data-sort]');
        
        headers.forEach(header => {
            const column = header.getAttribute('data-sort');
            const upIcon = header.querySelector('.bolopa-tabel-sort-up');
            const downIcon = header.querySelector('.bolopa-tabel-sort-down');
            
            if (column === activeColumn) {
                // Active column
                if (direction === 'asc') {
                    upIcon?.classList.add('bolopa-tabel-active');
                    downIcon?.classList.remove('bolopa-tabel-active');
                } else {
                    upIcon?.classList.remove('bolopa-tabel-active');
                    downIcon?.classList.add('bolopa-tabel-active');
                }
            } else {
                // Inactive columns
                upIcon?.classList.remove('bolopa-tabel-active');
                downIcon?.classList.remove('bolopa-tabel-active');
            }
        });
    }

    // Toast notification
    @if(session('success'))
        showToast('{{ session("success") }}', 'success');
    @endif

    @if(session('error'))
        showToast('{{ session("error") }}', 'error');
    @endif
});

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.background = type === 'success' ? '#28a745' : '#dc3545';
    toast.classList.add('bolopa-tabel-show');
    
    setTimeout(() => {
        toast.classList.remove('bolopa-tabel-show');
    }, 3000);
}

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data produksi ini?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/produksi/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

    @push('scripts')
    <!-- SweetAlert2 for nicer confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data yang dihapus tidak bisa dikembalikan. Anda yakin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/produksi/${id}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    </script>
    @endpush
