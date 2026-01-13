@extends('admin.layouts.app')

@section('title', 'Data Produksi')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Produksi'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('bolopa/css/admin-penetasan.css') }}">
    <link rel="stylesheet" href="{{ asset('bolopa/css/admin-produksi.css') }}">
@endpush

@section('content')
    <div class="bolopa-tabel-wrapper">
        <div class="bolopa-tabel-container">
            <!-- Header Card -->
            <header>
                <h1>
                    <img src="{{ asset('bolopa/img/icon/streamline-sharp--archive-box-solid.svg') }}" alt="Egg"
                        class="bolopa-icon-svg">
                    Data Produksi
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
                    <button class="bolopa-tabel-btn bolopa-tabel-btn-info" id="btnPrint">
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
                            <th data-sort="no" class="bolopa-tabel-text-center" style="width: 60px;">
                                No
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up" width="10" height="9">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down" width="10" height="9">
                                </span>
                            </th>
                            <th data-sort="batch" class="bolopa-tabel-text-left">
                                Batch
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up" width="10" height="9">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down" width="10" height="9">
                                </span>
                            </th>
                            <th data-sort="kandang" class="bolopa-tabel-text-left">
                                Kandang
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up" width="10" height="9">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down" width="10" height="9">
                                </span>
                            </th>
                            <th data-sort="tipe_produksi" class="bolopa-tabel-text-center">
                                Tipe
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up" width="10" height="9">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down" width="10" height="9">
                                </span>
                            </th>
                            <th data-sort="tanggal" class="bolopa-tabel-text-center">
                                Tanggal Masuk
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up" width="10" height="9">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down" width="10" height="9">
                                </span>
                            </th>
                            <th data-sort="expired" class="bolopa-tabel-text-center">
                                Expired
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up" width="10" height="9">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down" width="10" height="9">
                                </span>
                            </th>
                            <th data-sort="harga" class="bolopa-tabel-text-right" style="display: none;">
                                Harga
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up" width="10" height="9">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down" width="10" height="9">
                                </span>
                            </th>
                            <th data-sort="status" class="bolopa-tabel-text-center">
                                Status
                                <span class="bolopa-tabel-sort-wrap">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-up" width="10" height="9">
                                    <img src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down"
                                        class="bolopa-tabel-sort-icon bolopa-tabel-sort-icon-svg bolopa-tabel-sort-down" width="10" height="9">
                                </span>
                            </th>
                            <th class="bolopa-tabel-text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produksi as $i => $row)
                            <tr>
                                @php
                                    $pembesaran = $row->pembesaran;
                                    $startDateValue = $row->tanggal
                                        ?? $row->tanggal_mulai
                                        ?? optional($pembesaran)->tanggal_siap
                                        ?? optional($pembesaran)->tanggal_masuk;
                                    $startDateFormatted = $startDateValue
                                        ? \Carbon\Carbon::parse($startDateValue)->format('d/m/Y')
                                        : '-';

                                    $endDateValue = $row->tanggal_akhir;
                                    $endDateFormatted = $endDateValue
                                        ? \Carbon\Carbon::parse($endDateValue)->format('d/m/Y')
                                        : '-';

                                    $umurMulai = $row->umur_mulai_produksi
                                        ?? optional($pembesaran)->umur_hari
                                        ?? null;

                                    $jumlahJantanData = $row->jumlah_jantan;
                                    if (is_null($jumlahJantanData)) {
                                        if ($pembesaran) {
                                            $jumlahSiap = $pembesaran->jumlah_siap ?? 0;
                                            $jenisKelaminPembesaran = strtolower($pembesaran->jenis_kelamin ?? '');
                                            if ($jenisKelaminPembesaran === 'jantan') {
                                                $jumlahJantanData = $jumlahSiap;
                                            } elseif ($jenisKelaminPembesaran === 'campuran') {
                                                $jumlahJantanData = (int) ceil($jumlahSiap / 2);
                                            } else {
                                                $jumlahJantanData = 0;
                                            }
                                        }
                                    }

                                    $jumlahBetinaData = $row->jumlah_betina;
                                    if (is_null($jumlahBetinaData)) {
                                        if ($pembesaran) {
                                            $jumlahSiap = $pembesaran->jumlah_siap ?? 0;
                                            $jenisKelaminPembesaran = strtolower($pembesaran->jenis_kelamin ?? '');
                                            if ($jenisKelaminPembesaran === 'betina') {
                                                $jumlahBetinaData = $jumlahSiap;
                                            } elseif ($jenisKelaminPembesaran === 'campuran') {
                                                $jumlahBetinaData = (int) floor($jumlahSiap / 2);
                                            } else {
                                                $jumlahBetinaData = 0;
                                            }
                                        }
                                    }

                                    $jantanCount = (int) ($jumlahJantanData ?? 0);
                                    $betinaCount = (int) ($jumlahBetinaData ?? 0);

                                    if ($jantanCount > 0 && $betinaCount > 0) {
                                        $jenisPopulasi = 'campuran';
                                    } elseif ($jantanCount > 0) {
                                        $jenisPopulasi = 'jantan';
                                    } elseif ($betinaCount > 0) {
                                        $jenisPopulasi = 'betina';
                                    } else {
                                        $jenisPopulasi = strtolower(optional($pembesaran)->jenis_kelamin ?? '-');
                                        if ($jenisPopulasi === '') {
                                            $jenisPopulasi = '-';
                                        }
                                    }
                                @endphp
                                <td class="bolopa-tabel-text-center">{{ $produksi->firstItem() + $i }}</td>
                                <td class="bolopa-tabel-text-left">{{ $row->batch_label ?? '-' }}</td>
                                <td class="bolopa-tabel-text-left">{{ $row->kandang->nama_kandang ?? '-' }}</td>
                                <td class="bolopa-tabel-text-center">
                                    @if ($row->tipe_produksi === 'telur')
                                        <span class="bolopa-tabel-badge bolopa-tabel-badge-info">Telur</span>
                                    @elseif ($row->tipe_produksi === 'puyuh')
                                        <span class="bolopa-tabel-badge bolopa-tabel-badge-warning">Puyuh</span>
                                    @else
                                        <span class="bolopa-tabel-badge bolopa-tabel-badge-secondary">{{ $row->tipe_produksi ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td class="bolopa-tabel-text-center">{{ $startDateFormatted }}</td>
                                <td class="bolopa-tabel-text-center">{{ $endDateFormatted }}</td>
                                <td class="bolopa-tabel-text-right" style="display: none;">
                                    @if (!is_null($row->harga_per_kg))
                                        Rp {{ number_format($row->harga_per_kg, 0, ',', '.') }} / Kg
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="bolopa-tabel-text-center">
                                    @php
                                        $statusLabel = $row->status;
                                        if (in_array($statusLabel, ['selesai', 'dibatalkan'])) {
                                            $statusLabel = 'tidak_aktif';
                                        }
                                    @endphp
                                    @if ($statusLabel === 'aktif')
                                        <span class="bolopa-tabel-badge bolopa-tabel-badge-green">Aktif</span>
                                    @elseif ($statusLabel === 'tidak_aktif')
                                        <span class="bolopa-tabel-badge bolopa-tabel-badge-gray">Tidak Aktif</span>
                                    @else
                                        <span class="bolopa-tabel-badge bolopa-tabel-badge-secondary">{{ $row->status ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td class="bolopa-tabel-text-center">
                                    <div class="bolopa-tabel-actions">
                                        <button class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-info btn-view"
                                            title="Lihat Detail"
                                            data-id="{{ $row->id }}"
                                            data-batch="{{ $row->batch_label ?? '-' }}"
                                            data-kandang="{{ $row->kandang->nama_kandang ?? '-' }}"
                                            data-tipe="{{ $row->tipe_produksi ?? '-' }}"
                                            data-jenis-input="{{ $row->jenis_input ?? 'manual' }}"
                                            data-tanggal="{{ $startDateFormatted }}"
                                            data-start-date="{{ $startDateValue ? \Carbon\Carbon::parse($startDateValue)->toDateString() : '' }}"
                                            data-expired="{{ $endDateFormatted }}"
                                            data-harga="{{ number_format($row->harga_per_pcs ?? 0, 0, ',', '.') }}"
                                            data-status="{{ $row->status ?? '-' }}"
                                            data-jumlah-indukan="{{ $row->jumlah_indukan ?? '-' }}"
                                            data-umur="{{ $umurMulai ?? '-' }}"
                                            data-berat-rata="{{ $row->berat_rata_rata ?? ($row->pembesaran ? $row->pembesaran->berat_rata_rata ?? '2.0' : '2.0') }}"
                                            data-catatan="{{ $row->catatan ?? 'Tidak ada catatan' }}"
                                            data-rasio-jantan="{{ $jumlahJantanData }}"
                                            data-rasio-betina="{{ $jumlahBetinaData }}"
                                            data-jenis-populasi="{{ $jenisPopulasi }}"
                                            data-jumlah-telur="{{ $row->jumlah_telur ?? '-' }}"
                                            data-persentase-fertil="{{ $row->persentase_fertil ?? '-' }}"
                                            data-berat-rata-telur="{{ $row->berat_rata_telur ?? '-' }}">
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
                                <td colspan="8" style="text-align: center; padding: 40px;">
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
                    Menampilkan {{ $produksi->firstItem() ?? 0 }} Sampai {{ $produksi->lastItem() ?? 0 }} dari
                    {{ $produksi->total() ?? 0 }} entri
                </div>
                <div class="bolopa-tabel-pagination-buttons">
                    <a href="{{ $produksi->previousPageUrl() }}" class="bolopa-tabel-pagination-btn {{ $produksi->onFirstPage() ? 'disabled' : '' }}">
                        <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous" width="18" height="18">
                    </a>

                    @foreach ($produksi->getUrlRange(1, $produksi->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="bolopa-tabel-pagination-btn {{ $page == $produksi->currentPage() ? 'bolopa-tabel-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    <a href="{{ $produksi->nextPageUrl() }}" class="bolopa-tabel-pagination-btn {{ !$produksi->hasMorePages() ? 'disabled' : '' }}">
                        <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" alt="Next" width="18" height="18">
                    </a>
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
            const printBtn = document.getElementById('btnPrint');
            const table = document.getElementById('dataTable');
            const tbody = table ? table.querySelector('tbody') : null;

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

            if (entriesSelect) {
                entriesSelect.addEventListener('change', function() {
                    const url = new URL(window.location.href);
                    url.searchParams.set('per_page', this.value);
                    url.searchParams.set('page', '1');
                    window.location.href = url.toString();
                });
            }

            const exportTools = (() => {
                const sanitize = (value = '') => value.replace(/\s+/g, ' ').trim();

                const collectHeaders = (tableEl) => {
                    const skipIndexes = new Set();
                    const headers = [];

                    tableEl.querySelectorAll('thead th').forEach((th, index) => {
                        const text = sanitize(th.innerText || th.textContent || '');
                        if (!text || text.toLowerCase() === 'aksi' || text.toLowerCase() === 'harga') {
                            skipIndexes.add(index);
                            return;
                        }
                        headers.push(text);
                    });

                    return { headers, skipIndexes };
                };

                const collectRows = (tableEl, skipIndexes) => {
                    const rows = [];

                    tableEl.querySelectorAll('tbody tr').forEach((tr) => {
                        const cells = tr.querySelectorAll('td');
                        if (!cells.length) {
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
                }).join(',')).join('\r\n');

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

            if (table) {
                const { headers, skipIndexes } = exportTools.collectHeaders(table);

                btnExport?.addEventListener('click', () => {
                    const rows = exportTools.collectRows(table, skipIndexes);
                    if (!rows.length) {
                        showToast('Tidak ada data untuk diekspor', 'info');
                        return;
                    }
                    const csv = exportTools.toCSV([headers, ...rows]);
                    exportTools.downloadCSV(csv, `produksi-${exportTools.timestamp()}.csv`);
                    showToast('File CSV berhasil disiapkan', 'success');
                });

                printBtn?.addEventListener('click', () => {
                    const rows = exportTools.collectRows(table, skipIndexes);
                    exportTools.printTable('Data Produksi', headers, rows);
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

                const rows = Array.from(tbody.querySelectorAll('tr'));

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
                    'status': 6
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

            @if (session('success'))
            triggerFlashToast('success', 'Berhasil!', @json(session('success')));
            @endif

            @if (session('error'))
            triggerFlashToast('error', 'Gagal!', @json(session('error')), 4500);
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
            const jenisInput = this.getAttribute('data-jenis-input') || 'manual';

            const sanitizeAttr = (value) => {
                if (value === null || value === undefined) return '-';
                const trimmed = String(value).trim();
                if (!trimmed || trimmed.toLowerCase() === 'null' || trimmed.toLowerCase() === 'undefined') {
                    return '-';
                }
                return trimmed;
            };
            const getDataAttr = (name, fallback = '-') => {
                const rawValue = this.dataset[name] ?? this.getAttribute(`data-${name}`) ?? fallback;
                return sanitizeAttr(rawValue);
            };

            const tanggalRaw = getDataAttr('tanggal');
            const startDateIso = sanitizeAttr(this.getAttribute('data-start-date'));
            const expiredRaw = getDataAttr('expired');
            const harga = getDataAttr('harga');
            const status = getDataAttr('status');
            const jumlahIndukan = getDataAttr('jumlahIndukan');
            const umurRaw = getDataAttr('umur');
            let catatan = getDataAttr('catatan', 'Tidak ada catatan');
            if (catatan === '-') {
                catatan = 'Tidak ada catatan';
            }
            const rasioJantan = getDataAttr('rasioJantan');
            const rasioBetina = getDataAttr('rasioBetina');
            const jumlahTelur = getDataAttr('jumlahTelur');
            const persentaseFertil = getDataAttr('persentaseFertil');
            const beratRataTelur = getDataAttr('beratRataTelur');
            const jenisPopulasiRaw = getDataAttr('jenisPopulasi');
            const beratRataRaw = getDataAttr('beratRata');

            const parseDateDMY = (value) => {
                if (!value || value === '-') return null;
                const parts = value.split('/');
                if (parts.length !== 3) return null;
                const [day, month, year] = parts.map((part) => parseInt(part, 10));
                if (Number.isNaN(day) || Number.isNaN(month) || Number.isNaN(year)) return null;
                const dateObj = new Date(year, month - 1, day);
                return Number.isNaN(dateObj.getTime()) ? null : dateObj;
            };

            const formatTanggal = (value) => {
                const dateObj = parseDateDMY(value);
                if (!dateObj) return '-';
                return dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            };

            const parseAngka = (value) => {
                if (!value || value === '-') return Number.NaN;
                const normalized = String(value).replace(/\./g, '').replace(',', '.');
                return parseFloat(normalized);
            };

            const toKilogram = (gramsValue) => {
                if (Number.isNaN(gramsValue)) return '-';
                const kgValue = gramsValue / 1000;
                return kgValue.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            const beratRataKg = toKilogram(parseAngka(beratRataRaw));

            const tanggal = tanggalRaw && tanggalRaw !== '-' ? formatTanggal(tanggalRaw) : '-';
            const expired = expiredRaw && expiredRaw !== '-' ? formatTanggal(expiredRaw) : '-';

            const jenisPopulasi = jenisPopulasiRaw !== '-' ? jenisPopulasiRaw.toLowerCase() : '-';
            const jenisDisplay = (() => {
                if (jenisPopulasi === 'jantan') return 'Jantan';
                if (jenisPopulasi === 'betina') return 'Betina';
                if (jenisPopulasi === 'campuran') return 'Campuran';
                return '-';
            })();

            const normalizeCountDisplay = (value) => {
                if (value === null || value === undefined) return '0';
                const asString = String(value).trim();
                if (!asString || asString === '-' || asString.toLowerCase() === 'null' || asString.toLowerCase() === 'undefined') {
                    return '0';
                }
                return asString;
            };

            const rasioMarkup = (() => {
                if (jenisPopulasi === '-') {
                    return '-';
                }
                const displayJantan = normalizeCountDisplay(rasioJantan);
                const displayBetina = normalizeCountDisplay(rasioBetina);

                if (jenisPopulasi === 'jantan') {
                    return '<i class="fas fa-mars text-primary"></i> ' + displayJantan;
                }
                if (jenisPopulasi === 'betina') {
                    return '<i class="fas fa-venus text-danger"></i> ' + displayBetina;
                }
                return '<i class="fas fa-mars text-primary"></i> ' + displayJantan + ' / <i class="fas fa-venus text-danger"></i> ' + displayBetina;
            })();

        const umurParsed = parseInt(umurRaw, 10);
        const computeDaysSinceStart = () => {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            let startDateObj = null;

            if (startDateIso && startDateIso !== '-') {
                const isoDate = new Date(`${startDateIso}T00:00:00`);
                if (!Number.isNaN(isoDate.getTime())) {
                    startDateObj = isoDate;
                }
            }

            if (!startDateObj) {
                startDateObj = parseDateDMY(tanggalRaw);
            }

            if (!startDateObj || Number.isNaN(startDateObj.getTime())) {
                return null;
            }

            const diffMs = today.getTime() - startDateObj.getTime();
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            return Number.isNaN(diffDays) || diffDays < 0 ? null : diffDays;
        };

        const daysSinceStart = computeDaysSinceStart();
        let umurDisplay = '-';

        if (tipe === 'telur') {
            if (daysSinceStart !== null) {
                umurDisplay = `${daysSinceStart} hari`;
            }
        } else {
            const initialAge = Number.isNaN(umurParsed) ? 0 : Math.max(umurParsed, 0);
            if (daysSinceStart !== null) {
                umurDisplay = `${initialAge + daysSinceStart} hari`;
            } else if (!Number.isNaN(umurParsed) && umurParsed > 0) {
                umurDisplay = `${umurParsed} hari`;
            }
        }

        if (umurDisplay === '-' && !Number.isNaN(umurParsed) && umurParsed > 0) {
            umurDisplay = `${umurParsed} hari`;
        }

        const jenisInputText = (() => {
            switch (jenisInput) {
                case 'manual': return 'Manual';
                case 'dari_pembesaran': return 'Dari Pembesaran';
                case 'dari_produksi':
                case 'dari_penetasan':
                    return 'Dari Produksi';
                default: return 'Manual';
            }
        })();

        const legacyStatus = ['selesai', 'dibatalkan'];
        const statusBadge = (() => {
            if (status === 'aktif') {
                return `<span class="badge bg-success px-3 py-2"><i class="fa-solid fa-play me-1"></i>Aktif</span>`;
            }
            if (status === 'tidak_aktif' || legacyStatus.includes(status)) {
                return `<span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-pause me-1"></i>Tidak Aktif</span>`;
            }
            return `<span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-question me-1"></i>${escapeHtml(status)}</span>`;
        })();

			const tipeBadge = (() => {
				if (tipe === 'telur') return `<span class="badge bg-info px-3 py-2"><i class="fa-solid fa-egg me-1"></i>Telur</span>`;
				if (tipe === 'puyuh') return `<span class="badge bg-warning px-3 py-2"><i class="fa-solid fa-feather me-1"></i>Puyuh</span>`;
				return `<span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-question me-1"></i>${escapeHtml(tipe)}</span>`;
			})();

            const isTelur = tipe === 'telur';

            const createDataSection = (title, iconClass, items) => {
                const validItems = items.filter(item => item && typeof item === 'object');
                if (!validItems.length) {
                    return '';
                }
                const itemsMarkup = validItems.map(({ iconClass: itemIconClass = 'fas fa-circle', label = '', value = '-', valueClass = '', isHtml = false }) => {
                    const safeLabel = escapeHtml(label);
                    const displayValue = value === undefined || value === null || value === '' ? '-' : value;
                    const resolvedValue = isHtml ? displayValue : escapeHtml(String(displayValue));
                    return `
                        <div class="data-item">
                            <span class="data-label"><i class="${itemIconClass}"></i>${safeLabel}</span>
                            <span class="data-value ${valueClass}">${resolvedValue}</span>
                        </div>
                    `;
                }).join('');
                return `
                    <div class="data-section">
                        <h6><i class="${iconClass}"></i>${escapeHtml(title)}</h6>
                        <div class="data-grid">
                            ${itemsMarkup}
                        </div>
                    </div>
                `;
            };

            const jumlahIndukanDisplay = jumlahIndukan !== '-' ? `${jumlahIndukan} ekor` : '-';
            const beratPopulasiDisplay = beratRataKg !== '-' ? `${beratRataKg} kg` : '-';
            const jumlahTelurDisplay = jumlahTelur !== '-' ? `${jumlahTelur} butir` : '-';
            const fertilDisplay = persentaseFertil !== '-' ? `${persentaseFertil}%` : '-';
            const beratTelurDisplay = beratRataTelur !== '-' ? `${beratRataTelur} gram` : '-';
            const catatanPlain = catatan !== '-' ? catatan : 'Tidak ada catatan';
            const catatanHtml = escapeHtml(catatanPlain).replace(/\n/g, '<br>');

            const informasiDasarSection = createDataSection('Informasi Dasar', 'fas fa-info-circle', [
                { iconClass: 'fas fa-home', label: 'Kandang', value: kandang },
                { iconClass: 'fas fa-cogs', label: 'Jenis Input', value: jenisInputText },
                { iconClass: 'fas fa-target', label: 'Fokus', value: tipeBadge, isHtml: true },
                { iconClass: 'fas fa-hashtag', label: 'Batch', value: batch, valueClass: 'batch-id' },
            ]);

            const detailSection = isTelur
                ? createDataSection('Detail Telur', 'fas fa-egg', [
                    { iconClass: 'fas fa-egg', label: 'Jumlah Telur', value: jumlahTelurDisplay },
                    { iconClass: 'fas fa-percentage', label: 'Fertil', value: fertilDisplay },
                    { iconClass: 'fas fa-weight', label: 'Berat Telur', value: beratTelurDisplay },
                ])
                : createDataSection('Populasi', 'fas fa-users', [
                    { iconClass: 'fas fa-feather', label: 'Jumlah', value: jumlahIndukanDisplay },
                    { iconClass: 'fas fa-venus-mars', label: 'Jenis', value: jenisDisplay },
                    { iconClass: 'fas fa-weight', label: 'Berat', value: beratPopulasiDisplay },
                    { iconClass: 'fas fa-balance-scale', label: 'Rasio', value: rasioMarkup, isHtml: true },
                ]);

            const periodeSection = createDataSection('Periode & Status', 'fas fa-calendar-alt', [
                { iconClass: 'fas fa-play', label: 'Mulai', value: tanggal },
                { iconClass: 'fas fa-stop', label: 'Akhir', value: expired },
                { iconClass: 'fas fa-clock', label: 'Umur', value: umurDisplay },
                { iconClass: 'fas fa-check-circle', label: 'Status', value: statusBadge, isHtml: true },
            ]);

            const priceLabelText = isTelur ? 'Harga Telur' : 'Harga';
            const priceLabelHtml = escapeHtml(priceLabelText);
            const priceSuffix = harga !== '-' ? (isTelur ? ' / Butir' : ' / Ekor') : '';
            const hargaDisplay = harga !== '-' ? `Rp ${harga}${priceSuffix}` : '-';

            const ekonomiSection = `
                <div class="data-section">
                    <div class="data-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <h6 style="margin-bottom: 10px; font-size: 14px; color: #495057; border-bottom: 2px solid #e9ecef; padding-bottom: 6px;">
                                <i class="fas fa-sticky-note"></i> Catatan
                            </h6>
                            <div class="notes-section" style="margin: 0; text-align: left;">
                                ${catatanHtml}
                            </div>
                        </div>
                        <div>
                            <h6 style="margin-bottom: 10px; font-size: 14px; color: #495057; border-bottom: 2px solid #e9ecef; padding-bottom: 6px;">
                                <i class="fas fa-chart-line"></i> Ekonomi
                            </h6>
                            <div style="text-align: center; padding: 15px; background: rgba(40, 167, 69, 0.1); border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <span class="data-label" style="margin: 0;"><i class="fas fa-tags"></i>${priceLabelHtml}</span>
                                </div>
                                <div class="price-highlight" style="font-size: 16px; font-weight: bold; text-align: right;">
                                    ${hargaDisplay}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const detailContent = [
                informasiDasarSection,
                detailSection,
                periodeSection,
                ekonomiSection,
            ].filter(Boolean).join('');

            Swal.fire({
				title: '<i class="fas fa-clipboard-list text-primary me-2"></i>Data Produksi',
				width: 650,
				showCloseButton: true,
				showConfirmButton: false,
				html: `
					<div class="popup-content">
						${detailContent}
					</div>
				`,
				confirmButtonText: '<i class="fas fa-times me-2"></i>Tutup',
				confirmButtonColor: '#6c757d',
				customClass: {
					popup: 'border-0 shadow-lg',
					confirmButton: 'btn btn-secondary px-4'
				}
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
