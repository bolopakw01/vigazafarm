@extends('admin.layouts.app')

@section('title', 'Set Performance')

@php
	$breadcrumbs = [
		['label' => 'Backoffice', 'link' => route('admin.dashboard')],
		['label' => 'Sistem', 'link' => route('admin.sistem')],
		['label' => 'Set Performance'],
	];
@endphp

@php
	$performance = $performance ?? [];
	$startMonth = $performance['start_month'] ?? now()->startOfMonth()->subMonths(5)->format('Y-m');
	$endMonth = $performance['end_month'] ?? now()->format('Y-m');
	$colors = $performance['colors'] ?? ['#0d6efd', '#ffc107', '#198754'];
	$enabled = $performance['enabled'] ?? true;
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
	.toggle-switch { position:relative; display:inline-block; width:52px; height:28px; }
	.toggle-switch input { opacity:0; width:0; height:0; }
	.toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; transition:.3s; border-radius:28px; }
	.toggle-slider:before { position:absolute; content:""; height:20px; width:20px; left:4px; bottom:4px; background:#fff; transition:.3s; border-radius:50%; }
	input:checked + .toggle-slider { background:#2563eb; }
	input:checked + .toggle-slider:before { transform:translateX(24px); }
	.toggle-label { font-size:0.9rem; font-weight:600; color:#0f172a; margin-left:12px; }
	@media (max-width:768px){ .performance-card .card-header, .form-footer { flex-direction:column; align-items:flex-start; } .series-meta { flex-direction:column; } .series-color { max-width:100%; } }
</style>
@endpush

@section('content')
<div class="performance-wrapper">
	<div class="performance-card">
		<div class="card-header">
			<div>
				<h1 class="performance-title"><i class="fas fa-chart-radar"></i> Rentang Grafik Performance</h1>
				<p class="performance-sub">Pilih bulan mulai dan selesai untuk grafik pendapatan, pengeluaran, dan laba.</p>
			</div>
			<a href="{{ route('admin.sistem') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
		</div>
		<div class="card-body">
			@if(session('success'))
				<div class="alert-msg alert-success" role="alert">{{ session('success') }}</div>
			@endif
			@if ($errors->any())
				<div class="alert-msg alert-error" role="alert">
					<strong>Periksa input:</strong>
					<ul class="mb-0 mt-2 ps-4">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<div class="alert-msg alert-success" role="alert">
				<i class="fas fa-info-circle me-2"></i>
				Grafik performance di dashboard menarik data finansial otomatis. Anda hanya memilih rentang bulan (maksimal 6 bulan) dan warna legendanya, serta bisa menonaktifkannya.
			</div>

			<form method="POST" action="{{ route('admin.sistem.performance.update') }}" id="performanceRangeForm">
				@csrf
				@method('PUT')

				<div class="section-box">
					<h2 class="section-title"><i class="fas fa-calendar-alt"></i> Rentang Bulan</h2>
					@php
						$currentStatus = filter_var(old('enabled', $enabled), FILTER_VALIDATE_BOOLEAN);
					@endphp
					<div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded border bg-light flex-wrap gap-3">
						<div>
							<h6 class="mb-1 fw-semibold">Status Grafik Performance</h6>
							<small class="text-muted">Aktifkan atau nonaktifkan tampilan grafik pada dashboard utama</small>
						</div>
						<div class="d-flex align-items-center gap-3">
							<input type="hidden" name="enabled" value="0">
							<label class="toggle-switch mb-0" aria-label="Toggle performance chart">
								<input type="checkbox" name="enabled" value="1" id="enabledToggle" {{ $currentStatus ? 'checked' : '' }}>
								<span class="toggle-slider"></span>
							</label>
							<span class="toggle-label" id="enabledLabel">{{ $currentStatus ? 'Aktif' : 'Nonaktif' }}</span>
						</div>
					</div>
					<div class="form-grid">
						<div>
							<label class="form-label">Mulai</label>
							<input type="month" name="start_month" id="startMonth" class="form-control" value="{{ old('start_month', $startMonth) }}" required>
						</div>
						<div>
							<label class="form-label">Selesai</label>
							<input type="month" name="end_month" id="endMonth" class="form-control" value="{{ old('end_month', $endMonth) }}" required>
						</div>
					</div>
					<p class="helper-text" style="margin-top:12px;"><i class="fas fa-lightbulb text-warning"></i> Rentang maksimal 6 bulan. Jika lebih, sistem otomatis memotong ke 6 bulan terakhir.</p>
				</div>

				<div class="section-box">
					<h2 class="section-title"><i class="fas fa-palette"></i> Warna Legenda</h2>
					<div class="form-grid">
						<div>
							<label class="form-label">Revenue</label>
							<input type="color" class="form-control" name="colors[0]" value="{{ old('colors.0', $colors[0] ?? '#0d6efd') }}">
						</div>
						<div>
							<label class="form-label">Expenses</label>
							<input type="color" class="form-control" name="colors[1]" value="{{ old('colors.1', $colors[1] ?? '#ffc107') }}">
						</div>
						<div>
							<label class="form-label">Profit</label>
							<input type="color" class="form-control" name="colors[2]" value="{{ old('colors.2', $colors[2] ?? '#198754') }}">
						</div>
					</div>
					<p class="helper-text" style="margin-top:12px;"><i class="fas fa-info-circle text-primary"></i> Warna dipakai untuk legenda dan garis grafik di dashboard.</p>
				</div>

				<div class="section-box">
					<h2 class="section-title"><i class="fas fa-chart-line"></i> Cara kerja</h2>
					<ul class="mb-0" style="padding-left:18px; color:#475569;">
						<li>Label bulan memakai format MMM YYYY sesuai rentang yang Anda pilih.</li>
						<li>Pendapatan: pencatatan produksi aktif + pendapatan harian batch aktif.</li>
						<li>Pengeluaran: pakan (batch & produksi) + kesehatan + biaya pakan/vitamin harian batch aktif.</li>
						<li>Laba: pendapatan dikurangi pengeluaran per bulan.</li>
					</ul>
				</div>

				<div class="form-footer">
					<div class="helper-text"><i class="fas fa-lightbulb text-warning"></i> Ubah data produksi/pembesaran untuk mempengaruhi angka grafik.</div>
					<div class="d-flex gap-2 flex-wrap">
						<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Dashboard</a>
						<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Rentang</button>
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
  const startInput = document.getElementById('startMonth');
  const endInput = document.getElementById('endMonth');
  const form = document.getElementById('performanceRangeForm');
	const maxMonths = 6;
	const enabledToggle = document.getElementById('enabledToggle');
	const enabledLabel = document.getElementById('enabledLabel');

  function clampRange(){
    if(!startInput.value || !endInput.value){ return; }
    const start = new Date(startInput.value + '-01');
    const end = new Date(endInput.value + '-01');

    if(start > end){
      endInput.value = startInput.value;
    }

    const diffMonths = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth()) + 1;
    if(diffMonths > maxMonths){
      const adjusted = new Date(end.getFullYear(), end.getMonth() - (maxMonths - 1), 1);
      const ym = `${adjusted.getFullYear()}-${String(adjusted.getMonth() + 1).padStart(2,'0')}`;
      startInput.value = ym;
    }
  }

  startInput?.addEventListener('change', clampRange);
  endInput?.addEventListener('change', clampRange);
  form?.addEventListener('submit', clampRange);

	enabledToggle?.addEventListener('change', function(){
		if(!enabledLabel) return;
		enabledLabel.textContent = enabledToggle.checked ? 'Aktif' : 'Nonaktif';
	});
});
</script>
@endpush

