@extends('admin.layouts.app')

@section('title', 'Penetasan')

@push('styles')
	<link rel="stylesheet" href="{{ asset('bolopa/css/admin-penetasan.css') }}">
@endpush

@section('content')
<div class="bolopa-tabel-wrapper">
	<div class="bolopa-tabel-container">
		<header>
			<h1><img src="{{ asset('bolopa/img/icon/game-icons--nest-eggs.svg') }}" class="bolopa-tabel-brand-icon" alt="logo"> Sistem Manajemen Data</h1>
			<a href="#" class="bolopa-tabel-btn bolopa-tabel-btn-primary" id="btnTambah"><img src="{{ asset('bolopa/img/icon/line-md--plus-square-filled.svg') }}" alt="tambah"> Tambah Data</a>
		</header>

		<div class="bolopa-tabel-controls">
			<div class="bolopa-tabel-left-controls">
				<div class="bolopa-tabel-entries-select">
					<span>Tampilkan</span>
					<select id="entriesSelect">
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
						<option value="all">Semua</option>
					</select>
					<span>entri</span>
				</div>

				<div class="bolopa-tabel-search-box">
					<img src="{{ asset('bolopa/img/icon/line-md--file-search-filled.svg') }}" class="bolopa-tabel-search-icon" alt="search">
					<input type="text" id="searchInput" placeholder="Cari data...">
				</div>
			</div>

			<div class="bolopa-tabel-right-controls">
				<button class="bolopa-tabel-btn bolopa-tabel-btn-success" id="btnExport"><img src="{{ asset('bolopa/img/icon/line-md--file-export-filled.svg') }}" alt="export"> Export</button>
				<button class="bolopa-tabel-btn bolopa-tabel-btn-primary" id="btnPrint"><img src="{{ asset('bolopa/img/icon/line-md--cloud-alt-print-twotone-loop.svg') }}" alt="print"> Print</button>
			</div>
		</div>

	<div class="bolopa-tabel-table-responsive">
	<table id="dataTable">
			<thead>
				<tr>
						<th data-sort="no">No
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="sort up">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="sort down">
							</span>
						</th>
						<th data-sort="tanggal_simpan_telur">Tanggal Simpan Telur
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="sort up">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="sort down">
							</span>
						</th>
						<th data-sort="jumlah_telur">Jumlah Telur
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="sort up">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="sort down">
							</span>
						</th>
						<th data-sort="tanggal_menetas">Tanggal Menetas
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="sort up">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="sort down">
							</span>
						</th>
						<th data-sort="jumlah_menetas">Jumlah Menetas
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="sort up">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="sort down">
							</span>
						</th>
						<th data-sort="jumlah_doc">Jumlah DOC
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="sort up">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="sort down">
							</span>
						</th>
						<th data-sort="dibuat_pada">Dibuat
							<span class="bolopa-tabel-sort-wrap">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-up" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-up.svg') }}" alt="sort up">
								<img class="bolopa-tabel-sort-icon bolopa-tabel-sort-down" src="{{ asset('bolopa/img/icon/typcn--arrow-sorted-down.svg') }}" alt="sort down">
							</span>
						</th>
						<th>Aksi</th>
					</tr>
			</thead>
			<tbody>
				@if(isset($penetasan) && $penetasan->count())
					@php
						$start = $penetasan->firstItem() ?? 1;
						$end = $penetasan->lastItem() ?? $penetasan->count();
					@endphp
					@foreach($penetasan as $i => $p)
						<tr>
							<td class="text-center">{{ $start + $i }}</td>
							<td>{{ isset($p->tanggal_simpan_telur) ? \Carbon\Carbon::parse($p->tanggal_simpan_telur)->format('d/m/Y') : '-' }}</td>
							<td class="text-right">{{ $p->jumlah_telur ?? 0 }}</td>
							<td>{{ isset($p->tanggal_menetas) ? \Carbon\Carbon::parse($p->tanggal_menetas)->format('d/m/Y') : '-' }}</td>
							<td class="text-right">{{ $p->jumlah_menetas ?? 0 }}</td>
							<td class="text-right">{{ $p->jumlah_doc ?? 0 }}</td>
							<td>{{ isset($p->dibuat_pada) ? \Carbon\Carbon::parse($p->dibuat_pada)->format('d/m/Y H:i') : '' }}</td>
							<td class="bolopa-tabel-actions">
								<a href="{{ route('admin.penetasan.show', $p->id) ?? '#' }}" class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action" title="Lihat">
									<img src="{{ asset('bolopa/img/icon/line-md--watch.svg') }}" class="bolopa-tabel-action-icon" alt="lihat">
								</a>
								<a href="{{ route('admin.penetasan.edit', $p->id) ?? '#' }}" class="bolopa-tabel-btn bolopa-tabel-btn-warning bolopa-tabel-btn-action" title="Edit">
									<img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" class="bolopa-tabel-action-icon" alt="edit">
								</a>
								<form action="{{ route('admin.penetasan.destroy', $p->id) ?? '#' }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus data ini?')">
									@csrf
									@method('DELETE')
									<button type="submit" class="bolopa-tabel-btn bolopa-tabel-btn-danger bolopa-tabel-btn-action" title="Hapus">
										<img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" class="bolopa-tabel-action-icon" alt="hapus">
									</button>
								</form>
							</td>
						</tr>
					@endforeach
				@else
					<tr>
						<td>1</td>
						<td><span class="bolopa-tabel-status-indicator bolopa-tabel-status-active"></span> Budi Santoso</td>
						<td>budi@example.com</td>
						<td>08123456789</td>
						<td>IT</td>
						<td>12/05/2023</td>
						<td><span class="bolopa-tabel-badge bolopa-tabel-badge-success">Aktif</span></td>
						<td class="bolopa-tabel-actions">
							<button class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action" title="Lihat"><img src="{{ asset('bolopa/img/icon/line-md--watch.svg') }}" class="bolopa-tabel-action-icon" alt="lihat"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-warning bolopa-tabel-btn-action" title="Edit"><img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" class="bolopa-tabel-action-icon" alt="edit"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-danger bolopa-tabel-btn-action" title="Hapus"><img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" class="bolopa-tabel-action-icon" alt="hapus"></button>
						</td>
					</tr>
					<tr>
						<td>2</td>
						<td><span class="bolopa-tabel-status-indicator bolopa-tabel-status-active"></span> Siti Rahayu</td>
						<td>siti@example.com</td>
						<td>08234567890</td>
						<td>HR</td>
						<td>15/05/2023</td>
						<td><span class="bolopa-tabel-badge bolopa-tabel-badge-success">Aktif</span></td>
						<td class="bolopa-tabel-actions">
							<button class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action" title="Lihat"><img src="{{ asset('bolopa/img/icon/line-md--watch.svg') }}" class="bolopa-tabel-action-icon" alt="lihat"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-warning bolopa-tabel-btn-action" title="Edit"><img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" class="bolopa-tabel-action-icon" alt="edit"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-danger bolopa-tabel-btn-action" title="Hapus"><img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" class="bolopa-tabel-action-icon" alt="hapus"></button>
						</td>
					</tr>
					<tr>
						<td>3</td>
						<td><span class="bolopa-tabel-status-indicator bolopa-tabel-status-inactive"></span> Agus Prasetyo</td>
						<td>agus@example.com</td>
						<td>08345678901</td>
						<td>Finance</td>
						<td>18/05/2023</td>
						<td><span class="bolopa-tabel-badge bolopa-tabel-badge-danger">Nonaktif</span></td>
						<td class="bolopa-tabel-actions">
							<button class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action" title="Lihat"><img src="{{ asset('bolopa/img/icon/line-md--watch.svg') }}" class="bolopa-tabel-action-icon" alt="lihat"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-warning bolopa-tabel-btn-action" title="Edit"><img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" class="bolopa-tabel-action-icon" alt="edit"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-danger bolopa-tabel-btn-action" title="Hapus"><img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" class="bolopa-tabel-action-icon" alt="hapus"></button>
						</td>
					</tr>
					<tr>
						<td>4</td>
						<td><span class="bolopa-tabel-status-indicator bolopa-tabel-status-active"></span> Dewi Lestari</td>
						<td>dewi@example.com</td>
						<td>08456789012</td>
						<td>Marketing</td>
						<td>20/05/2023</td>
						<td><span class="bolopa-tabel-badge bolopa-tabel-badge-success">Aktif</span></td>
						<td class="bolopa-tabel-actions">
							<button class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action" title="Lihat"><img src="{{ asset('bolopa/img/icon/line-md--watch.svg') }}" class="bolopa-tabel-action-icon" alt="lihat"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-warning bolopa-tabel-btn-action" title="Edit"><img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" class="bolopa-tabel-action-icon" alt="edit"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-danger bolopa-tabel-btn-action" title="Hapus"><img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" class="bolopa-tabel-action-icon" alt="hapus"></button>
						</td>
					</tr>
					<tr>
						<td>5</td>
						<td><span class="bolopa-tabel-status-indicator bolopa-tabel-status-active"></span> Rudi Hermawan</td>
						<td>rudi@example.com</td>
						<td>08567890123</td>
						<td>Operations</td>
						<td>22/05/2023</td>
						<td><span class="bolopa-tabel-badge bolopa-tabel-badge-success">Aktif</span></td>
						<td class="bolopa-tabel-actions">
							<button class="bolopa-tabel-btn bolopa-tabel-btn-info bolopa-tabel-btn-action" title="Lihat"><img src="{{ asset('bolopa/img/icon/line-md--watch.svg') }}" class="bolopa-tabel-action-icon" alt="lihat"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-warning bolopa-tabel-btn-action" title="Edit"><img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" class="bolopa-tabel-action-icon" alt="edit"></button>
							<button class="bolopa-tabel-btn bolopa-tabel-btn-danger bolopa-tabel-btn-action" title="Hapus"><img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" class="bolopa-tabel-action-icon" alt="hapus"></button>
						</td>
					</tr>
				@endif
			</tbody>
	</table>
	</div>

		<div class="bolopa-tabel-pagination">
				<div class="bolopa-tabel-pagination-info">
					@if(isset($penetasan) && $penetasan->count())
						Menampilkan {{ $start }} sampai {{ $end }} dari {{ $penetasan->total() }} entri
					@else
						Menampilkan 1 sampai 5 dari 50 entri
					@endif
				</div>
				<div class="bolopa-tabel-pagination-buttons">
					@if(isset($penetasan) && $penetasan->count())
						<a href="{{ $penetasan->previousPageUrl() }}" class="btn-link" @if(!$penetasan->previousPageUrl()) aria-disabled="true" @endif>
							<button @if(!$penetasan->previousPageUrl()) disabled @endif>
								<img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" class="bolopa-tabel-nav-icon" alt="previous">
							</button>
						</a>
						@php
							$last = $penetasan->lastPage();
							$current = $penetasan->currentPage();
							$from = max(1, $current - 2);
							$to = min($last, $current + 2);
						@endphp
						@for($page = $from; $page <= $to; $page++)
							<a href="{{ $penetasan->url($page) }}"><button class="@if($page == $current) bolopa-tabel-active @endif">{{ $page }}</button></a>
						@endfor
						<a href="{{ $penetasan->nextPageUrl() }}" class="btn-link" @if(!$penetasan->nextPageUrl()) aria-disabled="true" @endif>
							<button @if(!$penetasan->nextPageUrl()) disabled @endif>
								<img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" class="bolopa-tabel-nav-icon" alt="next">
							</button>
						</a>
					@else
						<button disabled><img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" class="bolopa-tabel-nav-icon" alt="previous"></button>
						<button class="bolopa-tabel-active">1</button>
						<button>2</button>
						<button>3</button>
						<button>4</button>
						<button>5</button>
						<button><img src="{{ asset('bolopa/img/icon/line-md--chevron-small-right.svg') }}" class="bolopa-tabel-nav-icon" alt="next"></button>
					@endif
				</div>
			</div>
	</div>
</div>

@push('scripts')
<script>
	// minimal client side behavior: wire search to hide rows (same as original sample)
	const searchInput = document.getElementById('searchInput');
	const table = document.getElementById('dataTable');
	if (searchInput) {
		searchInput.addEventListener('input', function(){
			const q = this.value.toLowerCase();
			const rows = table.querySelectorAll('tbody tr');
			rows.forEach(r => {
				const text = r.textContent.toLowerCase();
				r.style.display = text.includes(q) ? '' : 'none';
			});
		});
	}
</script>
@endpush

@endsection
