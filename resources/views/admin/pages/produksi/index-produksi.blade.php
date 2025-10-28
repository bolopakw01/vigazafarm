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

        /* Tambah Produksi button icon color filter */
        header a.bolopa-tabel-btn-primary .bolopa-icon-svg {
            filter: invert(76%) sepia(100%) saturate(0%) hue-rotate(177deg) brightness(114%) contrast(101%);
        }

        /* Action icons (View, Edit, Delete) color filter */
        .bolopa-tabel-actions .bolopa-icon-svg {
            filter: invert(76%) sepia(100%) saturate(0%) hue-rotate(177deg) brightness(114%) contrast(101%);
        }

        /* Sort icons: stack much closer vertically and show as black when active */
        .bolopa-tabel-sort-wrap {
            display: inline-flex;
            flex-direction: column;
            gap: 0px;
            /* remove gap to bring icons adjacent */
            align-items: center;
            justify-content: center;
            line-height: 0;
            padding-left: 6px;
            /* slight separation from header text */
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
        .bolopa-tabel-sort-icon.bolopa-tabel-sort-up {
            transform: translateY(1px);
        }

        .bolopa-tabel-sort-icon.bolopa-tabel-sort-down {
            transform: translateY(-1px);
        }

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
                    <img src="{{ asset('bolopa/img/icon/streamline-sharp--archive-box-solid.svg') }}" alt="Egg"
                        class="bolopa-icon-svg">
                    Produksi
                </h1>
                <a href="{{ route('admin.produksi.create') }}" class="bolopa-tabel-btn bolopa-tabel-btn-primary">
                    <img src="{{ asset('bolopa/img/icon/line-md--plus-square-filled.svg') }}" alt="Add"
                        class="bolopa-icon-svg">
                    Tambah Produksi
                </a>
            </header>

            <!-- KAI Summary Cards -->
            {{-- <div class="bolopa-kai-card" style="margin-bottom: 20px;">
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
        </div> --}}

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
                        <img src="{{ asset('bolopa/img/icon/line-md--file-search-filled.svg') }}" alt="Search"
                            class="bolopa-icon-svg">
                        <input type="text" id="searchInput" placeholder="Cari data..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="bolopa-tabel-right-controls">
                    {{-- <a href="{{ route('admin.produksi.create') }}" class="bolopa-tabel-btn bolopa-tabel-btn-primary">
                    <img src="{{ asset('bolopa/img/icon/line-md--plus-square-filled.svg') }}" alt="Add" class="bolopa-icon-svg">
                    Tambah Data
                </a> --}}
                    <button class="bolopa-tabel-btn bolopa-tabel-btn-success" id="btnExport">
                        <img src="{{ asset('bolopa/img/icon/line-md--file-export-filled.svg') }}" alt="Export"
                            class="bolopa-icon-svg">
                        Export
                    </button>
                    <button class="bolopa-tabel-btn bolopa-tabel-btn-info" id="btnPrint" onclick="window.print()">
                        <img src="{{ asset('bolopa/img/icon/line-md--cloud-alt-print-twotone-loop.svg') }}" alt="Print"
                            class="bolopa-icon-svg">
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
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                                </span>
                            </th>
                            <th data-sort="batch">
                                Batch
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                                </span>
                            </th>
                            <th data-sort="kandang">
                                Kandang
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                                </span>
                            </th>
                            <th data-sort="tipe_produksi">
                                Tipe
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                                </span>
                            </th>
                            <th data-sort="tanggal">
                                Tanggal Masuk
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                                </span>
                            </th>
                            <th data-sort="expired">
                                Expired
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                                </span>
                            </th>
                            <th data-sort="harga">
                                Harga
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                                </span>
                            </th>
                            <th data-sort="status">
                                Status
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}"
                                        alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down">
                                </span>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produksi as $i => $row)
                            <tr>
                                <td>{{ $produksi->firstItem() + $i }}</td>
                                <td>{{ $row->batch_produksi_id ?? '-' }}</td>
                                <td>{{ $row->kandang->nama_kandang ?? '-' }}</td>
                                <td>
                                    @if ($row->tipe_produksi == 'telur')
                                        <span class="badge bg-info">Telur</span>
                                    @elseif($row->tipe_produksi == 'puyuh')
                                        <span class="badge bg-warning">Puyuh</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $row->tipe_produksi ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $row->tanggal_akhir ? \Carbon\Carbon::parse($row->tanggal_akhir)->format('d/m/Y') : '-' }}
                                </td>
                                <td>{{ number_format($row->harga_per_kg ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @if ($row->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($row->status == 'selesai')
                                        <span class="badge bg-primary">Selesai</span>
                                    @elseif($row->status == 'dibatalkan')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $row->status ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="bolopa-tabel-actions">
                                        <button class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-info btn-view"
                                            title="Lihat Detail"
                                            data-id="{{ $row->id }}"
                                            data-batch="{{ $row->batch_produksi_id ?? '-' }}"
                                            data-kandang="{{ $row->kandang->nama_kandang ?? '-' }}"
                                            data-tipe="{{ $row->tipe_produksi ?? '-' }}"
                                            data-tanggal="{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-' }}"
                                            data-expired="{{ $row->tanggal_akhir ? \Carbon\Carbon::parse($row->tanggal_akhir)->format('d/m/Y') : '-' }}"
                                            data-harga="{{ number_format($row->harga_per_kg ?? 0, 0, ',', '.') }}"
                                            data-status="{{ $row->status ?? '-' }}">
                                            <img src="{{ asset('bolopa/img/icon/el--eye-open.svg') }}" alt="View"
                                                class="bolopa-icon-svg">
                                        </button>
                                        <a href="{{ route('admin.produksi.show', $row->id) }}"
                                            class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-success"
                                            title="Pencatatan Produksi">
                                            <img src="{{ asset('bolopa/img/icon/icon-park-outline--view-grid-detail.svg') }}" alt="Record"
                                                class="bolopa-icon-svg">
                                        </a>
                                        <a href="{{ route('admin.produksi.edit', $row->id) }}"
                                            class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-warning"
                                            title="Edit">
                                            <img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}"
                                                alt="Edit" class="bolopa-icon-svg">
                                        </a>
                                        <button class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-danger"
                                            title="Hapus" onclick="confirmDelete(this, {{ $row->id }})">
                                            <img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" alt="Delete"
                                                class="bolopa-icon-svg">
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px;">
                                    <img src="{{ asset('bolopa/img/icon/iconoir--box-iso.svg') }}" alt="No Data"
                                        style="width: 60px; height: 60px; opacity: 0.3; margin-bottom: 10px;">
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
                    Menampilkan {{ $produksi->firstItem() ?? 0 }} - {{ $produksi->lastItem() ?? 0 }} dari
                    {{ $produksi->total() ?? 0 }} entri
                </div>
                <div class="bolopa-tabel-pagination-buttons">
                    @if ($produksi->onFirstPage())
                        <button disabled>
                            <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous"
                                class="bolopa-icon-svg">
                        </button>
                    @else
                        <a href="{{ $produksi->previousPageUrl() }}" style="text-decoration: none;">
                            <button>
                                <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous"
                                    class="bolopa-icon-svg">
                            </button>
                        </a>
                    @endif

                    @foreach (range(1, $produksi->lastPage()) as $page)
                        @if ($page == $produksi->currentPage())
                            <button class="bolopa-tabel-active">{{ $page }}</button>
                        @else
                            <a href="{{ $produksi->url($page) }}" style="text-decoration: none;">
                                <button>{{ $page }}</button>
                            </a>
                        @endif
                    @endforeach

                    @if ($produksi->hasMorePages())
                        <a href="{{ $produksi->nextPageUrl() }}" style="text-decoration: none;">
                            <button>
                                <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" alt="Next"
                                    class="bolopa-icon-svg">
                            </button>
                        </a>
                    @else
                        <button disabled>
                            <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" alt="Next"
                                class="bolopa-icon-svg">
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div class="bolopa-tabel-toast" id="toast" style="display: none;"></div>
    </div>
@endsection

@push('scripts')
    <script>
        // Delete confirmation function - defined globally
        function confirmDelete(button, id) {
            const batchName = button.closest('tr').cells[1].textContent.trim() || 'Data Produksi';
            const kandangName = button.closest('tr').cells[2].textContent.trim() || '';
            const displayName = kandangName ? `${batchName} (${kandangName})` : batchName;

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `
            <div style="text-align: center; margin-bottom: 15px; padding: 0 10px;">
                Apakah Anda yakin ingin menghapus data produksi ini?
            </div>
            <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 12px; border-radius: 6px; margin: 15px 10px; text-align: left;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-exclamation-triangle text-danger"></i>
                    <div>
                        <div style="font-weight: 600; color: #991b1b; font-size: 14px; margin-bottom: 2px;">Data yang akan dihapus:</div>
                        <div style="font-weight: 700; color: #7f1d1d; font-size: 16px; background: #fecaca; padding: 4px 8px; border-radius: 4px; display: inline-block;">${displayName}</div>
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
                                // Create and submit form
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = '{{ route('admin.produksi.show', ':id') }}'
                                    .replace(':id', id);

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
                    window.location.href = '{{ route('admin.produksi') }}?export=csv';
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
                    'batch': 1,
                    'kandang': 2,
                    'tipe_produksi': 3,
                    'tanggal': 4,
                    'expired': 5,
                    'harga': 6,
                    'status': 7
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
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    icon: 'success',
                    title: '{{ session('success') }}',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    icon: 'error',
                    title: '{{ session('error') }}',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            @endif
        });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.btn-view').forEach(btn => {
		btn.addEventListener('click', function() {
			const id = this.getAttribute('data-id');
			const batch = this.getAttribute('data-batch') || '-';
			const kandang = this.getAttribute('data-kandang') || '-';
			const tipe = this.getAttribute('data-tipe') || '-';
			const tanggal = this.getAttribute('data-tanggal') || '-';
			const expired = this.getAttribute('data-expired') || '-';
			const harga = this.getAttribute('data-harga') || '-';
			const status = this.getAttribute('data-status') || '-';

			const tipeBadge = (() => {
				if (tipe === 'telur') return `<span class="badge bg-info px-3 py-2"><i class="fa-solid fa-egg me-1"></i>Telur</span>`;
				if (tipe === 'puyuh') return `<span class="badge bg-warning px-3 py-2"><i class="fa-solid fa-feather me-1"></i>Puyuh</span>`;
				return `<span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-question me-1"></i>${escapeHtml(tipe)}</span>`;
			})();

			const statusBadge = (() => {
				if (status === 'aktif') return `<span class="badge bg-success px-3 py-2"><i class="fa-solid fa-play me-1"></i>Aktif</span>`;
				if (status === 'selesai') return `<span class="badge bg-primary px-3 py-2"><i class="fa-solid fa-check me-1"></i>Selesai</span>`;
				if (status === 'dibatalkan') return `<span class="badge bg-danger px-3 py-2"><i class="fa-solid fa-times me-1"></i>Dibatalkan</span>`;
				return `<span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-question me-1"></i>${escapeHtml(status)}</span>`;
			})();

			Swal.fire({
				title: `
					<div class="d-flex align-items-center justify-content-center gap-2 mb-2">
						<img src="{{ asset('bolopa/img/icon/fluent--production-20-filled.svg') }}" alt="Produksi" style="width:32px;height:32px;">
						<h5 class="fw-semibold mb-0 text-dark">Detail Batch Produksi</h5>
					</div>
				`,
				html: `
					<div class="card shadow-sm border-0 p-3 text-start" style="border-radius: 1rem; max-width: 700px;">
						<div class="card-body">

							<!-- Header -->
							<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
								<div class="d-flex align-items-center gap-3">
									<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold; color: white;">
										<i class="fa-solid fa-industry"></i>
									</div>
									<div>
										<h5 class="fw-semibold mb-0">Batch ${batch}</h5>
										<small class="text-muted">${kandang}</small>
									</div>
								</div>
								<div class="d-flex gap-2">
									${tipeBadge}
									${statusBadge}
								</div>
							</div>

							<!-- Grid Info -->
							<div class="row g-3 mb-3">
								<!-- Batch ID -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-hashtag text-primary fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Batch ID</small>
											<span class="fw-semibold">${batch}</span>
										</div>
									</div>
								</div>

								<!-- Kandang -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-home text-success fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Kandang</small>
											<span class="fw-semibold">${kandang}</span>
										</div>
									</div>
								</div>

								<!-- Tanggal Masuk -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-calendar-plus text-info fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Tanggal Masuk</small>
											<span class="fw-semibold">${tanggal}</span>
										</div>
									</div>
								</div>

								<!-- Tanggal Expired -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-calendar-times text-danger fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Tanggal Expired</small>
											<span class="fw-semibold">${expired}</span>
										</div>
									</div>
								</div>

								<!-- Harga per Kg -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-money-bill-wave text-warning fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Harga per Kg</small>
											<span class="fw-semibold">Rp ${harga}</span>
										</div>
									</div>
								</div>

								<!-- Status -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-info-circle text-secondary fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Status</small>
											<span class="fw-semibold">${status === 'aktif' ? 'Aktif' : status === 'selesai' ? 'Selesai' : status === 'dibatalkan' ? 'Dibatalkan' : status}</span>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				`,
				showConfirmButton: false,
				showCloseButton: true,
				width: 750,
				background: '#ffffff',
				customClass: {
					popup: 'p-0',
				},
			});
		});
	});

	function escapeHtml(unsafe) {
		return String(unsafe)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}
});
</script>
@endpush
