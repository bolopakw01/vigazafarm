@extends('admin.layouts.app')

@section('title', 'Data Kandang')

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-kandang.css') }}">
<!-- Bootstrap for SweetAlert form styling -->
<link href="{{ asset('bolopa/css/bootstrap.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="bolopa-tabel-wrapper">
	<div class="bolopa-tabel-container">
		<header class="d-flex justify-content-between align-items-center">
			<h1>
				<img src="{{ asset('bolopa/img/icon/streamline-sharp--archive-box-solid.svg') }}" alt="Kandang" style="width:28px;height:28px;vertical-align:middle;margin-right:10px">
				Kandang
			</h1>
			<a href="{{ route('admin.kandang.create') }}" class="bolopa-tabel-btn bolopa-tabel-btn-primary">
				<img src="{{ asset('bolopa/img/icon/line-md--plus-square-filled.svg') }}" alt="Add" class="bolopa-icon-svg">
				Tambah Data
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
			<table id="dataTable" class="bolopa-tabel-table">
				<thead>
					<tr>
						<th data-sort="no" style="width: 8%; min-width: 60px;" class="bolopa-tabel-text-center">
							No
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="nama" style="width: 28%; min-width: 150px;" class="bolopa-tabel-text-left">
							Nama Kandang
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="tipe" style="width: 18%; min-width: 120px;" class="bolopa-tabel-text-left">
							Tipe Kandang
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="kapasitas" style="width: 15%; min-width: 100px;" class="bolopa-tabel-text-right">
							Kapasitas
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="terpakai" style="width: 15%; min-width: 100px;" class="bolopa-tabel-text-right">
							Terpakai
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="status" style="width: 15%; min-width: 100px;" class="bolopa-tabel-text-center">
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
					@forelse($kandang as $i => $row)
					<tr>
						<td class="bolopa-tabel-text-center" style="text-align: center;">{{ $kandang->firstItem() + $i }}</td>
						<td class="bolopa-tabel-text-left" style="text-align: left;">{{ $row->nama_kandang ?? '-' }}</td>
						<td class="bolopa-tabel-text-left" style="text-align: left;">{{ $row->tipe_kandang ?? $row->tipe ?? '-' }}</td>
						<td class="bolopa-tabel-text-right" style="text-align: right;">{{ number_format($row->kapasitas_maksimal ?? $row->kapasitas ?? 0) }}</td>
						<td class="bolopa-tabel-text-right" style="text-align: right;">{{ number_format($row->kapasitas_terpakai ?? 0) }}</td>
						<td class="bolopa-tabel-text-center" style="text-align: center;">
							@php $statusVal = $row->status ?? ($row->aktif ? 'aktif' : 'kosong'); @endphp
							@if(strtolower(trim($statusVal)) === 'aktif')
								<span class="bolopa-tabel-badge bolopa-tabel-badge-success">Aktif</span>
							@elseif(strtolower(trim($statusVal)) === 'maintenance')
								<span class="bolopa-tabel-badge bolopa-tabel-badge-warning">Maintenance</span>
							@else
								<span class="bolopa-tabel-badge bolopa-tabel-badge-danger">Tidak Aktif</span>
							@endif
						</td>
						<td class="bolopa-tabel-text-center" style="text-align: center;">
							<div class="bolopa-tabel-actions">
								<button type="button" data-id="{{ $row->id }}" data-nama="{{ $row->nama_kandang }}" data-tipe="{{ $row->tipe_kandang ?? $row->tipe ?? '' }}" data-kapasitas="{{ $row->kapasitas_maksimal ?? $row->kapasitas ?? 0 }}" data-status="{{ $statusVal }}" data-keterangan="{{ $row->keterangan ?? '' }}" data-kapasitas-terpakai="{{ $row->kapasitas_terpakai }}" class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-info btn-view" title="Lihat">
									<img src="{{ asset('bolopa/img/icon/el--eye-open.svg') }}" alt="View">
								</button>
								<button type="button" class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-warning btn-edit" title="Edit"
									data-id="{{ $row->id }}"
									data-nama="{{ $row->nama_kandang }}"
									data-tipe="{{ $row->tipe_kandang ?? $row->tipe ?? '' }}"
									data-kapasitas="{{ $row->kapasitas ?? $row->kapasitas_maksimal ?? 0 }}"
									data-status="{{ $row->status ?? ($row->aktif ? 'aktif' : 'kosong') }}"
									data-keterangan="{{ $row->keterangan ?? '' }}">
									<img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" alt="Edit">
								</button>
								<button class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-danger" title="Hapus" onclick="confirmDelete(this, {{ $row->id }})">
									<img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" alt="Delete">
								</button>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="7" style="text-align: center; padding: 40px;">
							<img src="{{ asset('bolopa/img/icon/streamline-sharp--archive-box-solid.svg') }}" alt="No Data" width="64" height="64" style="opacity: 0.3;">
							<p style="margin-top: 16px; color: #6c757d;">Tidak ada data kandang</p>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="bolopa-tabel-pagination">
			<div class="bolopa-tabel-pagination-info">
				Menampilkan {{ $kandang->firstItem() ?? 0 }} sampai {{ $kandang->lastItem() ?? 0 }} dari {{ $kandang->total() }} entri
			</div>
			<div class="bolopa-tabel-pagination-buttons">
				<a href="{{ $kandang->previousPageUrl() }}" class="bolopa-tabel-pagination-btn {{ $kandang->onFirstPage() ? 'disabled' : '' }}">
					<img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous" width="18" height="18">
				</a>

				@foreach($kandang->getUrlRange(1, $kandang->lastPage()) as $page => $url)
					<a href="{{ $url }}" class="bolopa-tabel-pagination-btn {{ $page == $kandang->currentPage() ? 'bolopa-tabel-active' : '' }}">
						{{ $page }}
					</a>
				@endforeach

				<a href="{{ $kandang->nextPageUrl() }}" class="bolopa-tabel-pagination-btn {{ !$kandang->hasMorePages() ? 'disabled' : '' }}">
					<img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" alt="Next" width="18" height="18">
				</a>
			</div>
		</div>
	</div>

	<div class="bolopa-tabel-toast" id="toast"></div>
</div>
@endsection

@push('scripts')
<script>
    // Search functionality with debounce
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const entriesSelect = document.getElementById('entriesSelect');

    function confirmDelete(button, id) {
        const kandangName = button.closest('tr').cells[1].textContent.trim() || 'Data Kandang';
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus "${kandangName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
			cancelButtonText: 'Batal',
			reverseButtons: true,
			focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/kandang') }}/${id}`;
                const token = document.querySelector('meta[name="csrf-token"]');
                const inputToken = document.createElement('input');
                inputToken.type = 'hidden';
                inputToken.name = '_token';
                inputToken.value = token ? token.getAttribute('content') : '';
                const inputMethod = document.createElement('input');
                inputMethod.type = 'hidden';
                inputMethod.name = '_method';
                inputMethod.value = 'DELETE';
                form.appendChild(inputToken);
                form.appendChild(inputMethod);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

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

	const tableUtils = (() => {
		const sanitize = (value = '') => value.replace(/\s+/g, ' ').trim();

		const collectHeaders = (table) => {
			const skipIndexes = new Set();
			const headers = [];

			table.querySelectorAll('thead th').forEach((th, index) => {
				const text = sanitize(th.innerText || th.textContent || '');
				if (!text || ['aksi', 'harga'].includes(text.toLowerCase())) {
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

		const toCSV = (rows) => rows
			.map((row) => row.map((value) => {
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
			const csv = tableUtils.toCSV([headers, ...rows]);
			tableUtils.downloadCSV(csv, `kandang-${tableUtils.timestamp()}.csv`);
			showToast('File CSV berhasil disiapkan', 'success');
		});

		printBtn?.addEventListener('click', () => {
			const rows = tableUtils.collectRows(table, skipIndexes);
			tableUtils.printTable('Data Kandang', headers, rows);
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

	// Client-side sorting setup
	const table = document.getElementById('dataTable');
	if (table) {
		const headers = Array.from(table.querySelectorAll('th[data-sort]'));
		let currentSort = { column: null, direction: 'asc' };

		headers.forEach(th => {
			// make header position relative so sort-wrap absolute positions correctly
			th.style.position = 'relative';
			const column = th.getAttribute('data-sort');
			th.style.cursor = 'pointer';
			th.addEventListener('click', function() {
				// toggle direction
				if (currentSort.column === column) {
					currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
				} else {
					currentSort.column = column;
					currentSort.direction = 'asc';
				}
				sortTable(column, currentSort.direction);
				updateSortIcons(column, currentSort.direction);
			});
		});

		function getColumnIndex(column) {
			const headers = Array.from(table.querySelectorAll('th'));
			const idx = headers.findIndex(h => h.getAttribute('data-sort') === column);
			return idx === -1 ? 0 : idx;
		}

		function sortTable(column, direction) {
			const tbody = table.querySelector('tbody');
			if (!tbody) return;
			const rows = Array.from(tbody.querySelectorAll('tr'));
			const idx = getColumnIndex(column);
			// attempt to detect numeric or date values
			rows.sort((a,b) => {
				const aText = a.children[idx] ? a.children[idx].textContent.trim() : '';
				const bText = b.children[idx] ? b.children[idx].textContent.trim() : '';

				const aNum = parseFloat(aText.replace(/[^0-9.,-]/g, '').replace(/,/g, ''));
				const bNum = parseFloat(bText.replace(/[^0-9.,-]/g, '').replace(/,/g, ''));

				let comparison = 0;
				if (!isNaN(aNum) && !isNaN(bNum)) {
					comparison = aNum - bNum;
				} else {
					comparison = aText.localeCompare(bText, 'id', { numeric: true });
				}

				return direction === 'asc' ? comparison : -comparison;
			});

			// append in new order
			rows.forEach(r => tbody.appendChild(r));
		}

		function updateSortIcons(activeColumn, direction) {
			const headers = Array.from(table.querySelectorAll('th[data-sort]'));
			headers.forEach(th => {
				const up = th.querySelector('.bolopa-tabel-sort-up');
				const down = th.querySelector('.bolopa-tabel-sort-down');
				if (up) up.classList.remove('bolopa-tabel-dominant');
				if (down) down.classList.remove('bolopa-tabel-dominant');
			});

			const activeTh = table.querySelector(`th[data-sort="${activeColumn}"]`);
			if (!activeTh) return;
			const upActive = activeTh.querySelector('.bolopa-tabel-sort-up');
			const downActive = activeTh.querySelector('.bolopa-tabel-sort-down');
			if (direction === 'asc') {
				if (upActive) upActive.classList.add('bolopa-tabel-dominant');
			} else {
				if (downActive) downActive.classList.add('bolopa-tabel-dominant');
			}
		}
	}


</script>
<!-- SweetAlert2 -->
<script src="{{ asset('bolopa/plugin/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
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
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.btn-view').forEach(btn => {
		btn.addEventListener('click', function() {
			const id = this.getAttribute('data-id');
			const nama = this.getAttribute('data-nama') || '-';
			const tipe = this.getAttribute('data-tipe') || '-';
			const kapasitas = this.getAttribute('data-kapasitas') || '-';
			const status = this.getAttribute('data-status') || '';
			const keterangan = this.getAttribute('data-keterangan') || '-';
			const kapasitasTerpakai = this.getAttribute('data-kapasitas-terpakai') || '0';

			const statusBadge = (() => {
				if (!status) return '';
				const s = status.toLowerCase();
				if (s === 'aktif') return `<span class="badge bg-success px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i>Aktif</span>`;
				if (s === 'maintenance') return `<span class="badge bg-warning text-dark px-3 py-2"><i class="fa-solid fa-wrench me-1"></i>Maintenance</span>`;
				if (s === 'kosong' || s === 'tidak aktif') return `<span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-circle-pause me-1"></i>Tidak Aktif</span>`;
				return `<span class="badge bg-info px-3 py-2"><i class="fa-solid fa-question me-1"></i>${escapeHtml(status)}</span>`;
			})();

			Swal.fire({
				title: `
					<div class="d-flex align-items-center justify-content-center gap-2 mb-2">
						<i class="fa-solid fa-warehouse text-primary fs-4"></i>
						<h5 class="fw-semibold mb-0 text-dark">Detail Kandang</h5>
					</div>
				`,
				html: `
					<div class="card shadow-sm border-0 p-3 text-start" style="border-radius: 1rem; max-width: 650px;">
						<div class="card-body">

							<!-- Header -->
							<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
								<div class="d-flex align-items-center gap-3">
									<div class="bg-primary bg-opacity-10 p-3 rounded-circle">
										<i class="fa-solid fa-warehouse text-primary fa-lg"></i>
									</div>
									<div>
										<h5 class="fw-semibold mb-0">${nama || '-'}</h5>
										<small class="text-muted">ID: #${id}</small>
									</div>
								</div>
								${statusBadge}
							</div>

							<!-- Grid Info -->
							<div class="row g-3 mb-3">
								<!-- Tipe -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-layer-group text-primary fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Tipe</small>
											<span class="fw-semibold">${tipe || '-'}</span>
										</div>
									</div>
								</div>

								<!-- Kapasitas Maks -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-cubes-stacked text-info fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Kapasitas Maks.</small>
											<span class="fw-semibold">${kapasitas || '0'}</span>
										</div>
									</div>
								</div>

								<!-- Terpakai -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-boxes-packing text-danger fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Terpakai</small>
											<span class="fw-semibold text-danger">${kapasitasTerpakai || '0'}</span>
										</div>
									</div>
								</div>

								<!-- Tersedia -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-box-open text-success fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Tersedia</small>
											<span class="fw-semibold text-success">${(parseInt(kapasitas) || 0) - (parseInt(kapasitasTerpakai) || 0)}</span>
										</div>
									</div>
								</div>
							</div>

							<!-- Keterangan -->
							<div class="bg-light rounded p-3 mt-2">
								<i class="fa-solid fa-note-sticky text-secondary me-2"></i>
								<small class="text-muted d-block">Keterangan</small>
								<p class="mb-0">${keterangan ? escapeHtml(keterangan) : '<em class="text-muted">Tidak ada keterangan</em>'}</p>
							</div>

						</div>
					</div>
				`,
				showConfirmButton: false,
				showCloseButton: true,
				width: 700,
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
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.querySelectorAll('.btn-edit').forEach(btn => {
			btn.addEventListener('click', function() {
				const id = this.dataset.id;
				const nama = this.dataset.nama || '';
				const tipe = this.dataset.tipe || '';
				const kapasitas = this.dataset.kapasitas || '';
				const status = this.dataset.status || 'aktif';
				const keterangan = this.dataset.keterangan || '';

				Swal.fire({
					title: '<h5 class="fw-semibold text-dark mb-3">Edit Data Kandang</h5>',
					html: `
						<div class="card shadow-sm border-0" style="border-radius: 1rem;">
							<div class="card-body text-start p-3">
								<div class="mb-2">
									<label class="form-label small text-muted mb-1">Nama Kandang</label>
									<input type="text" id="swal-nama" class="form-control form-control-sm" value="${escapeHtml(nama)}">
								</div>
								<div class="mb-2">
									<label class="form-label small text-muted mb-1">Tipe</label>
									<select id="swal-tipe" class="form-select form-select-sm">
										<option value="penetasan">Penetasan</option>
										<option value="pembesaran">Pembesaran</option>
										<option value="produksi">Produksi</option>
										<option value="karantina">Karantina</option>
									</select>
								</div>
								<div class="mb-2">
									<label class="form-label small text-muted mb-1">Kapasitas (ekor)</label>
									<input type="number" id="swal-kapasitas" class="form-control form-control-sm" value="${escapeHtml(kapasitas)}">
								</div>
								<div class="mb-2">
									<label class="form-label small text-muted mb-1">Status</label>
									<select id="swal-status" class="form-select form-select-sm">
										<option value="aktif">Aktif</option>
										<option value="maintenance">Maintenance</option>
										<option value="kosong">Tidak Aktif</option>
									</select>
								</div>
								<div>
									<label class="form-label small text-muted mb-1">Keterangan</label>
									<textarea id="swal-keterangan" class="form-control form-control-sm" rows="2">${escapeHtml(keterangan)}</textarea>
								</div>
							</div>
						</div>
					`,
					focusConfirm: false,
					showCancelButton: true,
					confirmButtonText: 'Simpan',
					cancelButtonText: 'Batal',
					confirmButtonColor: '#0d6efd',
					cancelButtonColor: '#6c757d',
					width: 420,
					background: '#ffffff',
					customClass: { popup: 'p-0', confirmButton: 'btn btn-primary btn-sm', cancelButton: 'btn btn-secondary btn-sm' },
					didOpen: () => {
						document.getElementById('swal-tipe').value = tipe;
						document.getElementById('swal-status').value = status;
					},
					preConfirm: () => {
						const namaVal = document.getElementById('swal-nama').value.trim();
						const tipeVal = document.getElementById('swal-tipe').value;
						const kapasitasVal = document.getElementById('swal-kapasitas').value.trim();
						const statusVal = document.getElementById('swal-status').value;
						const ketVal = document.getElementById('swal-keterangan').value.trim();

						if (!namaVal || !tipeVal || !kapasitasVal || !statusVal) {
							Swal.showValidationMessage('Semua field wajib diisi!');
							return false;
						}

						return { nama: namaVal, tipe: tipeVal, kapasitas: kapasitasVal, status: statusVal, keterangan: ketVal };
					}
				}).then(async (res) => {
					if (!res.isConfirmed) return;
					const values = res.value;
					const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
					const formData = new URLSearchParams();
					formData.append('_method', 'PATCH');
					formData.append('nama_kandang', values.nama);
					formData.append('tipe_kandang', values.tipe);
					formData.append('kapasitas_maksimal', values.kapasitas);
					formData.append('status', values.status);
					formData.append('keterangan', values.keterangan);

					try {
						const resp = await fetch(`{{ url('admin/kandang') }}/${id}`, {
							method: 'POST',
							headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': token },
							body: formData.toString()
						});
						if (!resp.ok) throw new Error('HTTP ' + resp.status);
						Swal.fire({ icon: 'success', title: 'Tersimpan', text: 'Data kandang berhasil diperbarui', confirmButtonColor: '#0d6efd' }).then(() => location.reload());
					} catch (e) {
						console.error(e);
						Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat menyimpan perubahan' });
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

