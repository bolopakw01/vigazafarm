@extends('admin.layouts.app')

@section('title', 'Data Karyawan')

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-karyawan.css') }}">
<!-- Bootstrap for SweetAlert form styling -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="bolopa-tabel-wrapper">
	<div class="bolopa-tabel-container">
		<header class="d-flex justify-content-between align-items-center">
			<h1>
				<img src="{{ asset('bolopa/img/icon/fluent--person-note-20-filled.svg') }}" alt="Karyawan" style="width:28px;height:28px;vertical-align:middle;margin-right:10px">
				Karyawan
			</h1>
			<a href="{{ route('admin.karyawan.create') }}" class="bolopa-tabel-btn bolopa-tabel-btn-primary">
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
				<button class="bolopa-tabel-btn bolopa-tabel-btn-primary" id="btnPrint" onclick="window.print()">
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
						<th data-sort="foto" style="width: 10%; min-width: 80px;" class="bolopa-tabel-text-center">
							Foto
						</th>
						<th data-sort="nama" style="width: 20%; min-width: 150px;" class="bolopa-tabel-text-left">
							Nama
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="username" style="width: 15%; min-width: 120px;" class="bolopa-tabel-text-left">
							Username
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="email" style="width: 20%; min-width: 150px;" class="bolopa-tabel-text-left">
							Email
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="peran" style="width: 12%; min-width: 100px;" class="bolopa-tabel-text-center">
							Peran
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="alamat" style="width: 20%; min-width: 150px;" class="bolopa-tabel-text-left">
							Alamat
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="Sort Up" width="10" height="9">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="Sort Down" width="10" height="9">
							</span>
						</th>
						<th data-sort="aksi" style="width: 15%; min-width: 180px;" class="bolopa-tabel-text-center">Aksi</th>
					</tr>
				</thead>
				<tbody>
					@forelse($karyawan as $i => $user)
					<tr>
						<td class="bolopa-tabel-text-center" style="text-align: center;">{{ $karyawan->firstItem() + $i }}</td>
						@php
							$employeeNameSource = $user->nama ?: $user->nama_pengguna ?: 'A';
							$employeeInitial = mb_strtoupper(mb_substr($employeeNameSource, 0, 1));
						@endphp
						<td class="bolopa-tabel-text-center" style="text-align: center;">
							<div class="employee-table-avatar">
								<div class="employee-avatar-inner">
									@if($user->foto_profil)
										<img src="{{ asset('foto_profil/' . $user->foto_profil) }}" alt="Foto {{ $user->nama }}" class="profile-avatar">
									@else
										<div class="profile-avatar-placeholder">{{ $employeeInitial }}</div>
									@endif
								</div>
							</div>
						</td>
						<td class="bolopa-tabel-text-left" style="text-align: left;">{{ $user->nama }}</td>
						<td class="bolopa-tabel-text-left" style="text-align: left;">{{ $user->nama_pengguna }}</td>
						<td class="bolopa-tabel-text-left" style="text-align: left;">{{ $user->surel }}</td>
						<td class="bolopa-tabel-text-center" style="text-align: center;">
							@if($user->peran === 'owner')
								<span class="bolopa-tabel-badge bolopa-tabel-badge-info">Owner</span>
							@else
								<span class="bolopa-tabel-badge bolopa-tabel-badge-success">Operator</span>
							@endif
						</td>
						<td class="bolopa-tabel-text-left" style="text-align: left;">{{ $user->alamat ?: '-' }}</td>
						<td class="bolopa-tabel-text-center" style="text-align: center;">
								<button type="button" data-id="{{ $user->id }}" data-nama="{{ $user->nama }}" data-username="{{ $user->nama_pengguna }}" data-email="{{ $user->surel }}" data-peran="{{ $user->peran }}" data-alamat="{{ $user->alamat }}" data-foto="{{ $user->foto_profil }}" class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-info btn-view" title="Lihat">
									<img src="{{ asset('bolopa/img/icon/el--eye-open.svg') }}" alt="View">
								</button>
								<a href="{{ route('admin.karyawan.edit', $user->id) }}" class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-warning" title="Edit">
									<img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" alt="Edit">
								</a>
								@if($user->id !== auth()->id())
								<button class="bolopa-tabel-btn bolopa-tabel-btn-action bolopa-tabel-btn-danger" title="Hapus" onclick="confirmDelete(this, {{ $user->id }})">
									<img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" alt="Delete">
								</button>
								@endif
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="8" style="text-align: center; padding: 40px;">
							<img src="{{ asset('bolopa/img/icon/fluent--person-note-20-filled.svg') }}" alt="No Data" width="64" height="64" style="opacity: 0.3;">
							<p style="margin-top: 16px; color: #6c757d;">Tidak ada data karyawan</p>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="bolopa-tabel-pagination">
			<div class="bolopa-tabel-pagination-info">
				Menampilkan {{ $karyawan->firstItem() ?? 0 }} sampai {{ $karyawan->lastItem() ?? 0 }} dari {{ $karyawan->total() }} entri
			</div>
			<div class="bolopa-tabel-pagination-buttons">
				<a href="{{ $karyawan->previousPageUrl() }}" class="bolopa-tabel-pagination-btn {{ $karyawan->onFirstPage() ? 'disabled' : '' }}">
					<img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Previous" width="18" height="18">
				</a>

				@foreach($karyawan->getUrlRange(1, $karyawan->lastPage()) as $page => $url)
					<a href="{{ $url }}" class="bolopa-tabel-pagination-btn {{ $page == $karyawan->currentPage() ? 'bolopa-tabel-active' : '' }}">
						{{ $page }}
					</a>
				@endforeach

				<a href="{{ $karyawan->nextPageUrl() }}" class="bolopa-tabel-pagination-btn {{ !$karyawan->hasMorePages() ? 'disabled' : '' }}">
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
        const userName = button.closest('tr').cells[2].textContent.trim() || 'Data Karyawan';

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus karyawan "${userName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/karyawan') }}/${id}`;
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
			const username = this.getAttribute('data-username') || '-';
			const email = this.getAttribute('data-email') || '-';
			const peran = this.getAttribute('data-peran') || '-';
			const alamat = this.getAttribute('data-alamat') || '-';
			const foto = this.getAttribute('data-foto');

			const peranBadge = (() => {
				if (peran === 'owner') return `<span class="badge bg-info px-3 py-2"><i class="fa-solid fa-crown me-1"></i>Owner</span>`;
				if (peran === 'operator') return `<span class="badge bg-success px-3 py-2"><i class="fa-solid fa-user-gear me-1"></i>Operator</span>`;
				return `<span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-question me-1"></i>${escapeHtml(peran)}</span>`;
			})();

			const fotoHtml = foto ?
				`<img src="{{ asset('foto_profil/') }}/${foto}" alt="Foto ${nama}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e9ecef;">` :
				`<div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px; font-weight: bold; color: white;">${nama.charAt(0).toUpperCase()}</div>`;

			Swal.fire({
				title: `
					<div class="d-flex align-items-center justify-content-center gap-2 mb-2">
						<img src="{{ asset('bolopa/img/icon/fluent--person-note-20-filled.svg') }}" alt="Karyawan" style="width:32px;height:32px;">
						<h5 class="fw-semibold mb-0 text-dark">Detail Karyawan</h5>
					</div>
				`,
				html: `
					<div class="card shadow-sm border-0 p-3 text-start" style="border-radius: 1rem; max-width: 650px;">
						<div class="card-body">

							<!-- Header -->
							<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
								<div class="d-flex align-items-center gap-3">
									${fotoHtml}
									<div>
										<h5 class="fw-semibold mb-0">${nama}</h5>
										<small class="text-muted">@${username}</small>
									</div>
								</div>
								${peranBadge}
							</div>

							<!-- Grid Info -->
							<div class="row g-3 mb-3">
								<!-- Username -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-at text-primary fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Username</small>
											<span class="fw-semibold">${username}</span>
										</div>
									</div>
								</div>

								<!-- Email -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-envelope text-info fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Email</small>
											<span class="fw-semibold">${email}</span>
										</div>
									</div>
								</div>

								<!-- Peran -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-user-shield text-success fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Peran</small>
											<span class="fw-semibold">${peran === 'owner' ? 'Owner' : 'Operator'}</span>
										</div>
									</div>
								</div>

								<!-- Tanggal Dibuat -->
								<div class="col-md-6">
									<div class="d-flex align-items-center bg-light rounded p-3 h-100">
										<i class="fa-solid fa-calendar-plus text-warning fa-lg me-3"></i>
										<div>
											<small class="text-muted d-block">Bergabung</small>
											<span class="fw-semibold">{{ \Carbon\Carbon::parse($user->dibuat_pada ?? now())->format('d/m/Y') }}</span>
										</div>
									</div>
								</div>

								<!-- Alamat -->
								<div class="col-12">
									<div class="d-flex align-items-start bg-light rounded p-3 h-100">
										<i class="fa-solid fa-map-marker-alt text-danger fa-lg me-3 mt-1"></i>
										<div class="flex-grow-1">
											<small class="text-muted d-block">Alamat</small>
											<span class="fw-semibold">${alamat}</span>
										</div>
									</div>
								</div>
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
@endpush