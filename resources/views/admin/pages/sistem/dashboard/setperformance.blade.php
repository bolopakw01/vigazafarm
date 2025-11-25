@extends('admin.layouts.app')

@section('title', 'Set Performance')

@php
	$performance = $performance ?? [];
	$series = $performance['series'] ?? [];
	$categories = $performance['categories'] ?? [];
@endphp

@push('styles')
<style>
	.performance-wrapper { padding: 24px; }
	.performance-card { background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(15,23,42,0.08); border:1px solid #e2e8f0; margin-bottom:24px; }
	.performance-card .card-header { padding:26px 32px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
	.performance-card .card-body { padding:32px; }
	.performance-title { font-size:1.8rem; font-weight:600; margin:0; display:flex; align-items:center; gap:12px; color:#0f172a; }
	.performance-sub { color:#475569; margin:0; font-size:0.95rem; }
	.alert-msg { padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:0.95rem; }
	.alert-success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
	.alert-error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
	.section-title { font-size:1.1rem; font-weight:600; margin-bottom:16px; color:#1e293b; display:flex; align-items:center; gap:8px; }
	.section-box { border:1px solid #e2e8f0; border-radius:14px; padding:24px; margin-bottom:28px; background:#f8fafc; }
	.section-box.dark { background:#fff; }
	.form-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:18px; }
	.form-label { font-size:0.9rem; font-weight:600; color:#0f172a; margin-bottom:6px; display:flex; align-items:center; gap:6px; }
	.form-control { width:100%; border:2px solid #e2e8f0; border-radius:10px; padding:10px 14px; font-size:0.95rem; transition:border-color .2s; }
	.form-control:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
	input[type="color"].form-control { padding:0; height:44px; }
	.series-row { background:#fff; border-radius:12px; padding:16px; border:1px solid #e2e8f0; display:flex; flex-direction:column; gap:12px; }
	.series-meta { display:flex; gap:12px; }
	.series-meta .form-control { flex:1; }
	.series-color { max-width:120px; }
	.category-table-wrap { overflow-x:auto; }
	.category-table { width:100%; border-collapse:separate; border-spacing:0; }
	.category-table th, .category-table td { border:1px solid #e2e8f0; padding:12px; text-align:left; font-size:0.92rem; background:#fff; }
	.category-table th { background:#f1f5f9; font-weight:600; }
	.category-table td input { width:100%; }
	.table-actions { width:110px; text-align:center; }
	.btn { border:none; border-radius:999px; padding:10px 18px; font-weight:600; display:inline-flex; align-items:center; gap:6px; cursor:pointer; transition:opacity .2s ease, transform .2s ease; }
	.btn-primary { background:#2563eb; color:#fff; }
	.btn-outline { background:transparent; color:#dc2626; border:1px solid #fecaca; border-radius:999px; padding:6px 14px; font-size:0.8rem; }
	.btn-secondary { background:#e2e8f0; color:#0f172a; }
	.btn:hover { opacity:0.9; transform:translateY(-1px); }
	.form-footer { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top:12px; }
	.helper-text { font-size:0.85rem; color:#475569; display:flex; align-items:center; gap:6px; }
	@media (max-width:768px){ .performance-card .card-header, .form-footer { flex-direction:column; align-items:flex-start; } .series-meta { flex-direction:column; } .series-color { max-width:100%; } }
</style>
@endpush

@section('content')
<div class="performance-wrapper">
	<div class="performance-card">
		<div class="card-header">
			<div>
				<h1 class="performance-title"><i class="fas fa-chart-radar"></i> Konfigurasi Grafik Performance</h1>
				<p class="performance-sub">Sesuaikan label seri dan nilai radar chart agar mencerminkan performa operasional terbaru.</p>
			</div>
			<a href="{{ route('admin.sistem') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
		</div>
		<div class="card-body">
			@if(session('success'))
				<div class="alert-msg alert-success">{{ session('success') }}</div>
			@endif
			@if ($errors->any())
				<div class="alert-msg alert-error">
					<strong>Periksa input Anda:</strong>
					<ul class="mb-0 mt-2 ps-4">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<form method="POST" action="{{ route('admin.sistem.performance.update') }}" id="performanceForm">
				@csrf
				@method('PUT')

				<div class="section-box">
					<h2 class="section-title"><i class="fas fa-layer-group"></i> Pengaturan Seri</h2>
					<p class="helper-text"><i class="fas fa-info-circle text-primary"></i> Nama seri akan muncul di legenda radar chart. Maksimal 4 seri.</p>
					<div class="form-grid">
						@foreach($series as $index => $serie)
							<div class="series-row">
								<input type="hidden" name="series[{{ $index }}][key]" value="{{ $serie['key'] }}">
								<div class="form-label">Seri {{ $index + 1 }}</div>
								<div class="series-meta">
									<div class="flex-1">
										<label class="form-label">Nama Seri</label>
										<input type="text" name="series[{{ $index }}][label]" value="{{ $serie['label'] }}" class="form-control series-label-input" data-series-key="{{ $serie['key'] }}" maxlength="50" required>
									</div>
									<div class="series-color">
										<label class="form-label">Warna</label>
										<input type="color" name="series[{{ $index }}][color]" value="{{ $serie['color'] }}" class="form-control" required>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>

				<div class="section-box dark">
					<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
						<h2 class="section-title mb-0"><i class="fas fa-chart-pie"></i> Nilai Radar Chart</h2>
						<button type="button" class="btn btn-primary" id="addCategoryRow"><i class="fas fa-plus"></i> Tambah Kategori</button>
					</div>
					<p class="helper-text"><i class="fas fa-info-circle text-primary"></i> Nilai berada pada rentang 0 - 200. Gunakan nama kategori singkat agar mudah dibaca.</p>
					<div class="category-table-wrap">
						<table class="category-table">
							<thead>
								<tr>
									<th style="min-width:180px;">Nama Kategori</th>
									@foreach($series as $serie)
										<th data-header-series="{{ $serie['key'] }}">{{ $serie['label'] }}</th>
									@endforeach
									<th class="table-actions">Aksi</th>
								</tr>
							</thead>
							<tbody id="categoryTableBody" data-last-index="{{ count($categories) ? count($categories) - 1 : -1 }}">
								@foreach($categories as $catIndex => $category)
								<tr>
									<td>
										<input type="text" name="categories[{{ $catIndex }}][label]" value="{{ $category['label'] }}" class="form-control" maxlength="60" required>
									</td>
									@foreach($series as $serie)
									<td>
										<input type="number" name="categories[{{ $catIndex }}][values][{{ $serie['key'] }}]" value="{{ $category['values'][$serie['key']] ?? 0 }}" class="form-control" min="0" max="200" step="1" required>
									</td>
									@endforeach
									<td class="text-center">
										<button type="button" class="btn btn-outline remove-category"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				<div class="form-footer">
					<div class="helper-text"><i class="fas fa-lightbulb text-warning"></i> Simpan perubahan sebelum meninggalkan halaman.</div>
					<div class="d-flex gap-2 flex-wrap">
						<a href="{{ route('admin.sistem') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
						<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Konfigurasi</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function(){
		const seriesMeta = @json($series);
		const categoryBody = document.getElementById('categoryTableBody');
		const addCategoryBtn = document.getElementById('addCategoryRow');
		if(!categoryBody) { return; }
		const maxCategories = 8;
		let lastIndex = parseInt(categoryBody.dataset.lastIndex ?? (categoryBody.rows.length - 1));

		function updateHeaderLabel(seriesKey, value){
			const header = document.querySelector(`[data-header-series="${seriesKey}"]`);
			if(header){ header.textContent = value || 'Seri'; }
		}

		document.querySelectorAll('.series-label-input').forEach(input => {
			input.addEventListener('input', (e)=> updateHeaderLabel(e.target.dataset.seriesKey, e.target.value.trim()));
		});

		function buildCategoryRow(index){
			let cells = seriesMeta.map((serie) => {
				return `<td><input type="number" name="categories[${index}][values][${serie.key}]" class="form-control" min="0" max="200" step="1" value="0" required></td>`;
			}).join('');

			return `
				<tr>
					<td><input type="text" name="categories[${index}][label]" class="form-control" maxlength="60" placeholder="Nama kategori" required></td>
					${cells}
					<td class="text-center"><button type="button" class="btn btn-outline remove-category"><i class="fas fa-trash"></i></button></td>
				</tr>
			`;
		}

		function attachRemoveHandlers(){
			categoryBody.querySelectorAll('.remove-category').forEach(btn => {
				btn.onclick = function(){
					const rows = categoryBody.querySelectorAll('tr').length;
					if(rows <= 1){
						alert('Minimal harus ada satu kategori.');
						return;
					}
					btn.closest('tr').remove();
				};
			});
		}

		attachRemoveHandlers();

		addCategoryBtn?.addEventListener('click', function(){
			const currentRows = categoryBody.querySelectorAll('tr').length;
			if(currentRows >= maxCategories){
				alert('Maksimal 8 kategori.');
				return;
			}
			lastIndex += 1;
			categoryBody.insertAdjacentHTML('beforeend', buildCategoryRow(lastIndex));
			attachRemoveHandlers();
		});
	});
</script>
@endpush

