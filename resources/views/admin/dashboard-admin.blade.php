@extends('admin.layouts.app')

@section('title', 'Backoffice')

@php
$breadcrumbs = [['label' => 'Backoffice', 'link' => route('admin.dashboard')]];
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('bolopa/plugin/fontawesome/css/all.min.css') }}">
<style>
	.activity-row {
		align-items: stretch !important;
	}

	.activity-chart-card,
	.goals-column {
		display: flex;
		flex-direction: column;
		width: 100%;
	}

	.goals-panel {
		flex: 1 1 auto;
		display: flex;
		flex-direction: column;
		gap: 12px;
	}

	.goals-panel .goal-block {
		flex-shrink: 0;
	}

	.goals-empty {
		flex: 1 1 auto;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		text-align: center;
		padding: 32px 16px;
		border: 1px dashed #e5e7eb;
		border-radius: 10px;
	}

	.goals-empty i {
		font-size: 32px;
		margin-bottom: 12px;
		color: #cbd5f5;
	}

	.goals-empty p,
	.goals-empty small {
		color: #cbd5f5;
	}

	.performance-empty {
		border: 1px dashed #e5e7eb;
		border-radius: 12px;
		padding: 28px 16px;
		width: 100%;
		min-height: 220px;
		background: #f8fafc;
	}

	.performance-empty i {
		color: #cbd5f5;
	}

	.performance-empty p,
	.performance-empty small {
		color: #94a3b8;
	}

	.performance-section .app-card {
		overflow: visible;
	}

	.performance-section .box-body {
		min-height: 420px;
		padding: 16px;
	}

	#radarChart {
		min-height: 380px;
		padding: 12px;
		overflow: visible;
	}

	.mini-kpi-cards {
		overflow: visible !important;
		position: relative;
		z-index: 20;
	}

	.table-filter-panel {
		display: none;
		margin-top: 10px;
	}

	.table-filter-panel.show {
		display: block;
	}

	.table-filter-panel .input-group-text {
		background: #fff;
		border-right: 0;
	}

	.table-filter-panel .table-filter-input {
		border-left: 0;
	}

	.table-filter-panel .table-filter-reset {
		color: #64748b;
	}

	@media (max-width: 991.98px) {
		.goals-panel {
			flex-direction: column;
		}

		.goals-empty {
			border-style: solid;
		}
	}

	.kpi-icon {
		transition: filter 0.2s ease;
	}

	.kpi-indicator-success {
		filter: hue-rotate(90deg) saturate(1.2);
	}

	.kpi-indicator-warning {
		filter: hue-rotate(35deg) saturate(1.1);
	}

	.kpi-indicator-danger {
		filter: hue-rotate(-30deg) saturate(1.3);
	}

	/* .page-content {
               padding-top: 0.5rem !important;
              } */
	.table-wrap table td,
	.table-wrap table th {
		word-break: break-word;
	}

	.table-dashboard-compact tbody td {
		font-size: 0.85rem;
		line-height: 1.2;
	}

	.badge-type {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 0.25rem 0.7rem;
		border-radius: 999px;
		font-size: 0.75rem;
		font-weight: 600;
		line-height: 1;
		white-space: nowrap;
	}

	.badge-type-telur {
		background: #fff4d6;
		color: #b46900;
	}

	.badge-type-puyuh {
		background: #e0ebff;
		color: #1a4fa3;
	}

	.table-dashboard-compact .text-truncate {
		max-width: 150px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	@media (max-width: 767.98px) {

		.mini-kpi-cards,
		.activity-section,
		.kpi-matrix-section,
		.performance-section {
			display: none !important;
		}
	}

	.kai-kontrol-wrapper {
		position: relative;
		z-index: 30;
	}

	.kai-kontrol-card {
		background-color: #111827 !important;
		color: #f9fafb !important;
	}

	.kai-kontrol-card .mini-value {
		font-size: 0.82rem;
		font-weight: 500;
		color: #e5e7eb;
	}

	.kai-kontrol-card .mini-label {
		font-size: 0.9rem;
		font-weight: 400;
	}

	.kai-kontrol-popover {
		position: absolute;
		top: 100%;
		right: 0;
		margin-top: 6px;
		min-width: 220px;
		max-width: 260px;
		background: #111827;
		color: #f9fafb;
		border-radius: 12px;
		box-shadow: 0 18px 45px rgba(15, 23, 42, 0.45);
		padding: 12px 14px;
		z-index: 40;
		border: 1px solid rgba(75, 85, 99, 0.8);
		transform-origin: top right;
		transform: scale(0.9);
		opacity: 0;
		transition: opacity 0.16s ease-out, transform 0.16s ease-out;
	}

	.kai-kontrol-popover.show {
		opacity: 1;
		transform: scale(1);
	}

	.kai-kontrol-popover-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-bottom: 6px;
	}

	.kai-kontrol-popover-title {
		font-size: 0.8rem;
		font-weight: 600;
		color: #e5e7eb;
	}

	.kai-kontrol-popover-close {
		border: 0;
		background: transparent;
		color: #9ca3af;
		font-size: 0.9rem;
		line-height: 1;
		padding: 2px 4px;
	}

	.kai-kontrol-popover .form-label {
		font-size: 0.75rem;
		color: #d1d5db;
	}

	.kai-kontrol-popover .form-select {
		font-size: 0.8rem;
		background-color: #020617;
		color: #f9fafb;
		border-color: #4b5563;
	}

	.kai-kontrol-popover .btn {
		font-size: 0.78rem;
	}
</style>
@endpush

@section('content')

@if (data_get($lookerEmbed ?? [], 'enabled'))
<div class="app-card section-gap mb-3" style="border:1px solid #e2e8f0;">
	<div class="card-head d-flex justify-content-between align-items-center">
		{{-- <h2 class="card-title mb-0">BolopaKW</h2> --}}
		<span class="status-dot status-green" title="Looker embed aktif"></span>
	</div>
	<div class="ratio ratio-16x9" style="min-height:400px;">
		<iframe src="{{ data_get($lookerEmbed, 'url') }}" frameborder="0" style="border:0;width:100%;height:100%;"
			allowfullscreen
			sandbox="allow-storage-access-by-user-activation allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"></iframe>
	</div>
</div>
@endif

@if (!data_get($lookerEmbed ?? [], 'enabled'))
<div class="container-app container">
	<!-- Header Card -->
	{{-- <div class="app-card section-gap header-with-rt">
		<div class="header-rt" role="button" tabindex="0" title="Refresh page" aria-label="Refresh page">
			<img src="{{ asset('bolopa/img/icon/line-md--speed-loop.svg') }}" class="header-rt-img" alt="Refresh Icon">
		</div>
		<div class="card-head header-left">
			<img src="{{ asset('bolopa/img/icon/ic--outline-dashboard.svg') }}" class="header-icon" width="64"
				height="64" alt="Dashboard Icon">
			<div class="meta">
				<div class="title-row">
					<h1 class="card-title mb-0">Dashboard</h1>
					<span id="statusDot" class="status-dot status-green" title="Click to change status"></span>
				</div>
				<div style="font-size:.95rem;color:#6b7280;margin-top:2px;">
					<div>vigazafarm — peternakan burung buyuh</div>
				</div>
			</div>
		</div>
	</div> --}}

	<!-- MINI KPI / MENU CARDS -->
	@php
	$selectedMonth = $selectedMonth ?? now()->month;
	$selectedYear = $selectedYear ?? now()->year;
	$monthOptions = [
	1 => 'Januari',
	2 => 'Februari',
	3 => 'Maret',
	4 => 'April',
	5 => 'Mei',
	6 => 'Juni',
	7 => 'Juli',
	8 => 'Agustus',
	9 => 'September',
	10 => 'Oktober',
	11 => 'November',
	12 => 'Desember',
	];
	$fullMonthOptions = [
	1 => 'Januari',
	2 => 'Februari',
	3 => 'Maret',
	4 => 'April',
	5 => 'Mei',
	6 => 'Juni',
	7 => 'Juli',
	8 => 'Agustus',
	9 => 'September',
	10 => 'Oktober',
	11 => 'November',
	12 => 'Desember',
	];
	$currentYear = now()->year;
	$yearOptions = range($currentYear, $currentYear - 4);
	@endphp
	<div class="app-card section-gap mini-kpi-cards">
		<div class="box-body">
			<div class="row g-3">
				<div class="col-6 col-md-4 col-lg-2">
					<div class="mini-kpi-card theme-yellow" style="cursor:pointer;">
						<div class="mini-value" data-target="{{ \App\Models\Penetasan::count() }}">0</div>
						<div class="mini-label">Penetasan</div>
						<i class="fa-solid fa-egg mini-icon" aria-hidden="true"></i>
					</div>
				</div>

				<div class="col-6 col-md-4 col-lg-2">
					<div class="mini-kpi-card theme-green" style="cursor:pointer;">
						<div class="mini-value" data-target="{{ \App\Models\Pembesaran::count() }}">0</div>
						<div class="mini-label">Pembesaran</div>
						<i class="fa-solid fa-seedling mini-icon" aria-hidden="true"></i>
					</div>
				</div>

				<div class="col-6 col-md-4 col-lg-2">
					<div class="mini-kpi-card theme-blue" style="cursor:pointer;">
						<div class="mini-value" data-target="{{ \App\Models\Produksi::count() }}">0</div>
						<div class="mini-label">Produksi</div>
						<i class="fa-solid fa-industry mini-icon" aria-hidden="true"></i>
					</div>
				</div>

				<div class="col-6 col-md-4 col-lg-2">
					<div class="mini-kpi-card theme-indigo" style="cursor:pointer;">
						<div class="mini-value" data-target="{{ \App\Models\Kandang::count() }}">0</div>
						<div class="mini-label">Kandang</div>
						<i class="fa-solid fa-warehouse mini-icon" aria-hidden="true"></i>
					</div>
				</div>

				<div class="col-6 col-md-4 col-lg-2">
					<div class="mini-kpi-card theme-red" style="cursor:pointer;">
						<div class="mini-value" data-target="{{ \App\Models\User::count() }}">0</div>
						<div class="mini-label">User</div>
						<i class="fa-solid fa-user mini-icon" aria-hidden="true"></i>
					</div>
				</div>

				<div class="col-6 col-md-4 col-lg-2">
					<div class="kai-kontrol-wrapper">
						<div class="mini-kpi-card kai-kontrol-card" id="kaiKontrolCard" style="cursor:pointer;">
							<div class="d-flex justify-content-between align-items-start">
								<div>
									<div class="mini-value mb-0">
										+
									</div>
									<div class="mini-label">
										{{ $periodLabel ?? $fullMonthOptions[now()->month] . ' ' . now()->year }}
									</div>
								</div>
								<img src="{{ asset('bolopa/img/icon/line-md--cog-loop.svg') }}" class="mini-icon"
									alt="KAI Kontrol"
									style="filter: invert(100%) sepia(0%) saturate(0%) hue-rotate(312deg) brightness(103%) contrast(106%); width: 48px; height: 48px;">
							</div>
						</div>
						<div class="kai-kontrol-popover d-none" id="kaiKontrolPopover">
							<div class="kai-kontrol-popover-header">
								<span class="kai-kontrol-popover-title">Ubah periode matriks</span>
								<button type="button" class="kai-kontrol-popover-close" aria-label="Tutup"
									id="kaiKontrolCloseBtn">
									<i class="fa-solid fa-xmark"></i>
								</button>
							</div>
							<form action="{{ route('admin.dashboard') }}" method="GET">
								<div class="mb-2">
									<label for="kaiKontrolMonth" class="form-label">Bulan</label>
									<select name="month" id="kaiKontrolMonth" class="form-select form-select-sm">
										@foreach ($monthOptions as $value => $label)
										<option value="{{ $value }}" {{ (int) $selectedMonth===(int) $value ? 'selected'
											: '' }}>
											{{ $label }}</option>
										@endforeach
									</select>
								</div>
								<div class="mb-3">
									<label for="kaiKontrolYear" class="form-label">Tahun</label>
									<select name="year" id="kaiKontrolYear" class="form-select form-select-sm">
										@foreach ($yearOptions as $value)
										<option value="{{ $value }}" {{ (int) $selectedYear===(int) $value ? 'selected'
											: '' }}>
											{{ $value }}</option>
										@endforeach
									</select>
								</div>
								<div class="d-flex justify-content-end gap-2">
									<button type="button" class="btn btn-outline-light btn-sm"
										id="kaiKontrolCancelBtn">Batal</button>
									<button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="app-card section-gap activity-section">
		<div class="card-head">
			<div class="w-100 d-flex justify-content-between align-items-center">
				<h2 class="card-title mb-0">Activity</h2>
				<small class="text-muted">Main chart & goals</small>
			</div>
		</div>

		<div class="box-body">
			<div class="row g-4 activity-row">
				<div class="col-12 col-lg-8 d-flex">
					<div class="activity-chart-card">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<div class="segmented" id="chartFilter">
								<button class="seg-btn is-active" data-filter="bulan">Bulan</button>
								<button class="seg-btn" data-filter="tahun">Tahun</button>
								<button class="seg-btn" data-filter="hari">Hari</button>
							</div>
						</div>
						<div id="mainChart"></div>
					</div>
				</div>

				<div class="col-12 col-lg-4 d-flex">
					<div class="goals-column">
						<div class="subtitle d-flex justify-content-between align-items-center">
							<span>Goals</span>
							<small class="text-muted d-none d-lg-inline">Progress realtime</small>
						</div>

						<div id="goalsList" class="goals-panel">
							@if (isset($goals) && count($goals) > 0)
							@foreach ($goals as $goal)
							<div class="goal-block">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<span class="goal-item">{{ $goal['title'] }}</span>
									<div class="d-flex align-items-center">
										<small class="text-muted me-2 goal-text" data-current="{{ $goal['current'] }}"
											data-target="{{ $goal['target'] }}">
											<span class="current">0</span> / <span class="target">{{
												number_format($goal['target']) }}</span>
										</small>
									</div>
								</div>
								<div class="progress">
									<div class="progress-bar" style="background-color: {{ $goal['color'] }}; width:0%">
									</div>
								</div>
							</div>
							@endforeach
							@else
							<div class="goals-empty text-muted">
								<i class="fas fa-bullseye"></i>
								<p class="mb-1">Belum ada goals yang ditetapkan</p>
								@if (auth()->user()->peran === 'owner')
								<small>Silakan atur goals di menu Sistem &gt; Dashboard</small>
								@else
								<small>Silakan tunggu update goals dari owner</small>
								@endif
							</div>
							@endif
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- KPI Matrix Card -->
	@php
	$matrixCards = $matrixCards ?? [];
	$matrixEnabled = $matrixEnabled ?? true;
	$activityDatasets = $activityDatasets ?? [];
	$performanceChart = $performanceChart ?? [
	'labels' => [],
	'series' => [],
	'colors' => [],
	'enabled' => true,
	];
	$performanceEnabled = $performanceChart['enabled'] ?? true;
	$performanceSeries = $performanceChart['series'] ?? [];
	$performanceHasData =
	$performanceEnabled &&
	!empty($performanceChart['labels']) &&
	collect($performanceSeries)->contains(fn($serie) => !empty($serie['data']));
	@endphp
	@if ($matrixEnabled)
	{{-- <div class="d-flex bg-white justify-content-between align-items-center flex-wrap gap-2 mb-3">
		<h2 class="card-title mb-0">Matriks</h2>
		<small class="text-muted">Periode:
			{{ $periodLabel ?? $fullMonthOptions[now()->month] . ' ' . now()->year }}</small>
	</div> --}}
	<div class="app-card section-gap kpi-card kpi-matrix-section">
		<div class="box-body" style="padding: 12px 18px;">
			@if (count($matrixCards))
			<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
				@foreach ($matrixCards as $card)
				@php
				$trendValue = $card['trend'] ?? 'left';
				$percentTarget = max(0, (int) round($card['percent'] ?? 0));
				$valueTarget = (int) round($card['actual'] ?? 0);
				$targetValue = (int) round($card['target'] ?? 0);
				$label = strtoupper($card['label'] ?? ($card['key'] ?? ''));
				$isGoalsCard = ($card['key'] ?? '') === 'goals';
				$targetText = $isGoalsCard
				? 'Target ' .
				number_format($targetValue, 0, ',', '.') .
				' Goal' .
				($targetValue === 1 ? '' : 's')
				: 'Target Rp ' . number_format($targetValue, 0, ',', '.');
				$comparison =
				$card['comparison'] ??
				($valueTarget > $targetValue
				? 'above'
				: ($valueTarget < $targetValue ? 'below' : 'equal' )); if ($comparison==='equal' ) {
					$trendClass='kpi left' ; } elseif ($trendValue==='down' ) { $trendClass='kpi down' ; } elseif
					($trendValue==='left' ) { $trendClass='kpi left' ; } else { $trendClass='kpi up' ; }
					$indicatorClass='kpi-indicator-warning' ; if ($comparison==='above' ) {
					$indicatorClass='kpi-indicator-success' ; } elseif ($comparison==='below' ) {
					$indicatorClass='kpi-indicator-danger' ; } @endphp <div class="col">
					<div class="{{ $trendClass }}">
						<div class="delta">
							<img src="{{ asset('bolopa/img/icon/line-md--hazard-lights-filled-loop.svg') }}"
								class="kpi-icon {{ $indicatorClass }}" alt="KPI Icon">
							<span class="kpi-delta" data-target="{{ $percentTarget }}">0</span>%
						</div>
						<div class="value">
							@if ($isGoalsCard)
							<span class="kpi-value" data-static="true">
								{{ $valueTarget }}/{{ $targetValue }}
							</span>
							@else
							<span class="kpi-value" data-currency="Rp " data-target="{{ $valueTarget }}">
								Rp {{ number_format($valueTarget, 0, ',', '.') }}
							</span>
							@endif
						</div>
						<div class="label">{{ $label }}</div>
					</div>
			</div>
			@endforeach
		</div>
		@else
		<div class="text-center text-muted py-4">
			Data matriks belum tersedia. Silakan set target di menu Sistem &gt; Set Matriks.
		</div>
		@endif
	</div>
</div>
@else
<div class="app-card section-gap kpi-card">
	<div class="box-body text-center py-4">
		<div class="d-flex align-items-center justify-content-center gap-2">
			<img src="{{ asset('bolopa/img/icon/line-md--loading-twotone-loop.svg') }}" alt="Loading"
				style="width: 20px; height: 20px; filter: invert(46%) sepia(8%) saturate(642%) hue-rotate(182deg) brightness(93%) contrast(87%);">
			<p class="text-muted mb-0" style="color: #6b7280 !important;">Matriks sedang update</p>
		</div>
	</div>
</div>
@endif

<!-- SECTION 2: Table Produksi + Radar Chart -->
<div class="row g-4">
	<div class="col-12 col-lg-6">
		<div class="app-card h-100 section-gap">
			<div class="card-head">
				<div class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-2">
					<h2 class="card-title mb-0">Produksi</h2>
					<div class="d-flex gap-2 flex-wrap">
						<button type="button" class="btn btn-sm rounded-pill px-3 border table-filter-toggle"
							data-table-key="produksi"><i class="fa-solid fa-filter me-2"></i>Filter</button>
						<button type="button" class="btn btn-sm rounded-pill px-3 border table-export-btn"
							data-table-key="produksi"><i
								class="fa-solid fa-up-right-from-square me-2"></i>Export</button>
					</div>
				</div>
			</div>
			<div class="table-filter-panel" id="filter-panel-produksi" data-table-key="produksi">
				<div class="input-group input-group-sm">
					<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
					<input type="search" class="form-control table-filter-input"
						placeholder="Cari batch, tanggal, atau jumlah" aria-label="Filter data produksi">
					<button type="button" class="btn btn-primary table-filter-apply">Terapkan</button>
					<button type="button" class="btn btn-link table-filter-reset">Reset</button>
				</div>
			</div>
			<div class="table-wrap">
				<div class="table-responsive">
					<table class="table align-middle table-dashboard-compact" id="table-produksi">
						<thead>
							<tr>
								<th class="text-center">Tanggal</th>
								<th class="text-center">Batch</th>
								<th class="text-center">Jumlah</th>
								<th class="text-center">Tipe</th>
							</tr>
						</thead>
						<tbody>
							@php $produksiData = \App\Models\Produksi::with('batchProduksi')->latest()->take(5)->get();
							@endphp
							@if ($produksiData->isEmpty())
							<tr data-empty-state="true">
								<td colspan="4" class="text-center py-4">
									<img src="{{ asset('bolopa/img/icon/streamline-sharp--archive-box-solid.svg') }}"
										alt="Produksi" class="mb-2"
										style="width: 32px; height: 32px; filter: invert(65%) sepia(13%) saturate(19%) hue-rotate(174deg) brightness(99%) contrast(100%);">
									<p class="text-muted">Belum ada data produksi</p>
								</td>
							</tr>
							@else
							@foreach ($produksiData as $row)
							@php
							$dateSource =
							$row->tanggal_mulai ??
							($row->tanggal ?? $row->created_at);
							$batchLabel = $row->batchProduksi?->kode_batch ?? '-';
							$tipeProduksi = strtolower(
							$row->tipe_produksi ?? ($row->jenis_input ?? 'telur'),
							);
							$jumlahTelur = $row->jumlah_telur ?? null;
							$jumlahPuyuh =
							$row->jumlah_indukan ??
							($row->jumlah_jantan ?? ($row->jumlah_betina ?? null));
							$jumlahFallback = $row->jumlah ?? 0;
							$jumlahUtama =
							$tipeProduksi === 'telur'
							? $jumlahTelur ?? ($jumlahPuyuh ?? $jumlahFallback)
							: $jumlahPuyuh ?? ($jumlahTelur ?? $jumlahFallback);
							$satuan = $tipeProduksi === 'telur' ? 'butir' : 'ekor';
							$tipeLabel = ucfirst($tipeProduksi);
							@endphp
							<tr>
								<td class="text-center text-truncate"
									style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
									{{ $dateSource ?
									optional(\Illuminate\Support\Carbon::parse($dateSource))->format('d/m/Y') : '-' }}
								</td>
								<td class="text-center text-truncate"
									style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
									{{ $batchLabel ?: '-' }}</td>
								<td class="text-center text-truncate"
									style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
									{{ number_format($jumlahUtama, 0, ',', '.') }} {{ $satuan }}
								</td>
								@php
								$badgeClass =
								$tipeProduksi === 'telur'
								? 'badge-type badge-type-telur'
								: 'badge-type badge-type-puyuh';
								@endphp
								<td class="text-center"><span class="{{ $badgeClass }}">{{ $tipeLabel }}</span></td>
							</tr>
							@endforeach
							@endif
						</tbody>
					</table>
				</div>
				<p class="hint ms-1">
					Data Produksi Terbaru.
					<a href="{{ route('admin.produksi') }}" class="text-decoration-none" style="color:#0b74da;">Lihat
						Semua</a>
				</p>
			</div>
		</div>
	</div>

	<div class="col-12 col-lg-6 performance-section">
		<div class="app-card h-100 section-gap">
			<div class="card-head">
				<div class="w-100 d-flex justify-content-between align-items-center">
					<h2 class="chart-title mb-0">Performance</h2>
					<div class="d-flex align-items-center gap-2">
						<button id="toggleChartMode" class="btn btn-sm btn-outline-dark" type="button">Bar Chart</button>
						<div class="chart-menu"><i class="fa-solid fa-list"></i></div>
					</div>
				</div>
			</div>
			<div class="box-body d-flex align-items-center justify-content-center" style="height: calc(100% - 80px);">
				<div id="radarChart" class="{{ $performanceHasData ? '' : 'd-none' }}" style="width: 100%;">
				</div>
				<div id="performanceEmptyState"
					class="goals-empty performance-empty {{ $performanceHasData ? 'd-none' : '' }}">
					<i class="fas fa-bullseye"></i>
					<p class="mb-1">Belum ada data performance</p>

				</div>
			</div>
		</div>
	</div>
</div>

<!-- Penetasan Table Card -->
<div class="row g-4">
	<div class="col-12">
		<div class="app-card section-gap mt-penetasan">
			<div class="card-head">
				<div class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-2">
					<h2 class="card-title mb-0">Penetasan</h2>
					<div class="d-flex gap-2 flex-wrap">
						<button type="button" class="btn btn-sm rounded-pill px-3 border table-filter-toggle"
							data-table-key="penetasan"><i class="fa-solid fa-filter me-2"></i>Filter</button>
						<button type="button" class="btn btn-sm rounded-pill px-3 border table-export-btn"
							data-table-key="penetasan"><i
								class="fa-solid fa-up-right-from-square me-2"></i>Export</button>
					</div>
				</div>
			</div>
			<div class="table-filter-panel" id="filter-panel-penetasan" data-table-key="penetasan">
				<div class="input-group input-group-sm">
					<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
					<input type="search" class="form-control table-filter-input"
						placeholder="Cari batch, kandang, atau status" aria-label="Filter data penetasan">
					<button type="button" class="btn btn-primary table-filter-apply">Terapkan</button>
					<button type="button" class="btn btn-link table-filter-reset">Reset</button>
				</div>
			</div>
			<div class="box-body">
				<div class="table-wrap penetasan-table">
					<div class="table-responsive">
						<table class="table align-middle table-dashboard-compact" id="table-penetasan">
							<thead>
								<tr>
									<th class="text-center">Tanggal</th>
									<th class="text-center">Kandang</th>
									<th class="text-center">Batch</th>
									<th class="text-center">Jumlah Telur</th>
									<th class="text-center">Status</th>
								</tr>
							</thead>
							<tbody>
								@php $penetasanData = \App\Models\Penetasan::latest()->take(5)->get(); @endphp
								@if ($penetasanData->isEmpty())
								<tr data-empty-state="true">
									<td colspan="5" class="text-center py-4">
										<img src="{{ asset('bolopa/img/icon/game-icons--nest-eggs.svg') }}"
											alt="Penetasan" class="mb-2"
											style="width: 32px; height: 32px; filter: invert(65%) sepia(13%) saturate(19%) hue-rotate(174deg) brightness(99%) contrast(100%);">
										<p class="text-muted">Belum ada data penetasan</p>
									</td>
								</tr>
								@else
								@foreach ($penetasanData as $r)
								@php
								$createdAt = $r->created_at;
								$jumlahTelur = $r->jumlah_telur ?? 0;
								@endphp
								<tr>
									<td class="text-center">
										{{ optional($createdAt)->format('d/m/Y') ?? '-' }}</td>
									<td class="text-center text-truncate">
										{{ $r->kandang?->nama_kandang ?? '-' }}</td>
									<td class="text-center text-truncate">{{ $r->batch ?? '—' }}</td>
									<td class="text-center">
										{{ number_format($jumlahTelur, 0, ',', '.') }}</td>
									<td class="text-center"><span class="badge badge-pill badge-aktif">Aktif</span></td>
								</tr>
								@endforeach
								@endif
							</tbody>
						</table>
					</div>
					<p class="hint ms-1">Data penetasan terbaru. <a href="{{ route('admin.penetasan') }}"
							class="text-decoration-none" style="color:#0b74da;">Lihat Semua</a></p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Pembesaran Table Card -->
<div class="row g-4">
	<div class="col-12">
		<div class="app-card section-gap mt-penetasan">
			<div class="card-head">
				<div class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-2">
					<h2 class="card-title mb-0">Pembesaran</h2>
					<div class="d-flex gap-2 flex-wrap">
						<button type="button" class="btn btn-sm rounded-pill px-3 border table-filter-toggle"
							data-table-key="pembesaran"><i class="fa-solid fa-filter me-2"></i>Filter</button>
						<button type="button" class="btn btn-sm rounded-pill px-3 border table-export-btn"
							data-table-key="pembesaran"><i
								class="fa-solid fa-up-right-from-square me-2"></i>Export</button>
					</div>
				</div>
			</div>
			<div class="table-filter-panel" id="filter-panel-pembesaran" data-table-key="pembesaran">
				<div class="input-group input-group-sm">
					<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
					<input type="search" class="form-control table-filter-input"
						placeholder="Cari kandang, jenis, atau jumlah" aria-label="Filter data pembesaran">
					<button type="button" class="btn btn-primary table-filter-apply">Terapkan</button>
					<button type="button" class="btn btn-link table-filter-reset">Reset</button>
				</div>
			</div>
			<div class="box-body">
				<div class="table-wrap penetasan-table">
					<div class="table-responsive">
						<table class="table align-middle table-dashboard-compact" id="table-pembesaran">
							<thead>
								<tr>
									<th class="text-center">Tanggal</th>
									<th class="text-center">Kandang</th>
									<th class="text-center">Jenis</th>
									<th class="text-center">Jumlah</th>
									<th class="text-center">Keterangan</th>
								</tr>
							</thead>
							<tbody>
								@php $pembesaranData = \App\Models\Pembesaran::latest()->take(5)->get(); @endphp
								@if ($pembesaranData->isEmpty())
								<tr data-empty-state="true">
									<td colspan="5" class="text-center py-4">
										<img src="{{ asset('bolopa/img/icon/game-icons--nest-birds.svg') }}"
											alt="Pembesaran" class="mb-2"
											style="width: 32px; height: 32px; filter: invert(65%) sepia(13%) saturate(19%) hue-rotate(174deg) brightness(99%) contrast(100%);">
										<p class="text-muted">Belum ada data pembesaran</p>
									</td>
								</tr>
								@else
								@foreach ($pembesaranData as $pr)
								@php
								$createdAt = $pr->created_at;
								$jenisKelamin = $pr->jenis_kelamin ?? ($pr->jenis ?? '-');
								$jumlahAnak =
								$pr->jumlah_anak_ayam ??
								($pr->jumlah_siap ?? ($pr->jumlah ?? 0));
								$keterangan = $pr->status_batch ?? ($pr->catatan ?? 'Aktif');
								@endphp
								<tr>
									<td class="text-center">
										{{ optional($createdAt)->format('d/m/Y') ?? '-' }}</td>
									<td class="text-center text-truncate">
										{{ $pr->kandang?->nama_kandang ?? '-' }}</td>
									<td class="text-center">{{ ucfirst($jenisKelamin) }}</td>
									<td class="text-center">
										{{ number_format($jumlahAnak, 0, ',', '.') }}</td>
									<td class="text-center text-truncate"><span class="badge badge-pill badge-aktif">{{
											ucfirst($keterangan) }}</span>
									</td>
								</tr>
								@endforeach
								@endif
							</tbody>
						</table>
					</div>
					<p class="hint ms-1">Data pembesaran terbaru. <a href="{{ route('admin.pembesaran') }}"
							class="text-decoration-none" style="color:#0b74da;">Lihat Semua</a></p>
				</div>
			</div>
		</div>
	</div>
</div>

</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('bolopa/plugin/apexcharts/apexcharts.min.js') }}"></script>
<script>
	// replicate mainChart + filters and radar chart + export menu and animations from lopadashboard.html
        document.addEventListener('DOMContentLoaded', function() {
            // Check if chart containers exist
            const mainChartContainer = document.querySelector('#mainChart');
            const radarChartContainer = document.querySelector('#radarChart');
            const performanceEmptyState = document.getElementById('performanceEmptyState');
            const kaiKontrolCard = document.getElementById('kaiKontrolCard');
            const kaiKontrolPopover = document.getElementById('kaiKontrolPopover');
            const kaiKontrolCloseBtn = document.getElementById('kaiKontrolCloseBtn');
            const kaiKontrolCancelBtn = document.getElementById('kaiKontrolCancelBtn');
            const performanceHasData = {{ $performanceHasData ? 'true' : 'false' }};

            const toggleKaiKontrolPopover = (force = null) => {
                if (!kaiKontrolPopover) return;
                const isShown = kaiKontrolPopover.classList.contains('show');
                const willShow = force === null ? !isShown : force;
                if (willShow) {
                    kaiKontrolPopover.classList.remove('d-none');
                    // small delay to allow transition
                    requestAnimationFrame(() => kaiKontrolPopover.classList.add('show'));
                } else {
                    kaiKontrolPopover.classList.remove('show');
                    setTimeout(() => {
                        kaiKontrolPopover.classList.add('d-none');
                    }, 160);
                }
            };

            if (kaiKontrolCard && kaiKontrolPopover) {
                kaiKontrolCard.addEventListener('click', () => toggleKaiKontrolPopover());
            }

            if (kaiKontrolCloseBtn) {
                kaiKontrolCloseBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleKaiKontrolPopover(false);
                });
            }

            if (kaiKontrolCancelBtn) {
                kaiKontrolCancelBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleKaiKontrolPopover(false);
                });
            }

            document.addEventListener('click', (e) => {
                if (!kaiKontrolPopover || !kaiKontrolCard) return;
                const withinCard = kaiKontrolCard.contains(e.target);
                const withinPopover = kaiKontrolPopover.contains(e.target);
                if (!withinCard && !withinPopover) {
                    toggleKaiKontrolPopover(false);
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    toggleKaiKontrolPopover(false);
                }
            });

            if (!mainChartContainer) {
                console.warn('Main chart container not found, skipping chart initialization');
                return;
            }

            const rawDatasets = @json($activityDatasets ?? []);
            const performanceConfig = @json($performanceChart ?? []);

            const buildMonthLabels = () => {
                const formatter = new Intl.DateTimeFormat('id-ID', {
                    month: 'short'
                });
                return Array.from({
                    length: 12
                }, (_, idx) => formatter.format(new Date(2000, idx, 1)));
            };

            const buildYearLabels = (count = 5) => {
                const currentYear = new Date().getFullYear();
                const startYear = currentYear - (count - 1);
                return Array.from({
                    length: count
                }, (_, idx) => String(startYear + idx));
            };

            const buildDayLabels = (days = 7) => {
                const formatter = new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
                const today = new Date();
                return Array.from({
                    length: days
                }, (_, idx) => {
                    const date = new Date(today);
                    date.setDate(date.getDate() - (days - 1 - idx));
                    return formatter.format(date);
                });
            };

            const buildEmptySeries = (length) => [{
                    name: 'Produksi',
                    type: 'column',
                    data: Array(length).fill(0)
                },
                {
                    name: 'Penetasan',
                    type: 'area',
                    data: Array(length).fill(0)
                },
                {
                    name: 'Pembesaran',
                    type: 'line',
                    data: Array(length).fill(0)
                }
            ];

            const fallbackLabels = {
                bulan: buildMonthLabels(),
                tahun: buildYearLabels(),
                hari: buildDayLabels(),
            };

            const safeDataset = (key) => {
                const source = rawDatasets && rawDatasets[key] ? rawDatasets[key] : {};
                const labels = Array.isArray(source.labels) && source.labels.length ? source.labels : (
                    fallbackLabels[key] ?? []);
                const series = Array.isArray(source.series) && source.series.length ? source.series :
                    buildEmptySeries(labels.length);
                return {
                    labels,
                    series
                };
            };

            const datasets = {
                bulan: safeDataset('bulan'),
                tahun: safeDataset('tahun'),
                hari: safeDataset('hari'),
            };
            const mainData = datasets.bulan;

            const mainOpts = {
                series: mainData.series,
                chart: {
                    type: 'line',
                    height: 340,
                    stacked: false,
                    toolbar: {
                        show: false
                    },
                    foreColor: '#6c757d'
                },
                colors: ['#0d6efd', '#20c997', '#ffc107'],
                labels: mainData.labels,
                stroke: {
                    width: [0, 2, 4],
                    curve: 'smooth'
                },
                fill: {
                    opacity: [1, 0.25, 1],
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                plotOptions: {
                    bar: {
                        columnWidth: '48%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                markers: {
                    size: 0
                },
                grid: {
                    borderColor: '#dee2e6'
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: '#495057'
                        }
                    },
                    axisBorder: {
                        color: '#dee2e6'
                    },
                    axisTicks: {
                        color: '#dee2e6'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah'
                    },
                    labels: {
                        style: {
                            colors: '#495057'
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    offsetY: 8,
                    markers: {
                        radius: 12
                    },
                    itemMargin: {
                        horizontal: 14,
                        vertical: 6
                    }
                },
                responsive: [{
                    breakpoint: 992,
                    options: {
                        chart: {
                            height: 320
                        },
                        legend: {
                            itemMargin: {
                                vertical: 4
                            }
                        }
                    }
                }, {
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 300
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '55%'
                            }
                        }
                    }
                }]
            };

            // Initialize main chart with error handling
            let mainChart;
            try {
                mainChart = new ApexCharts(mainChartContainer, mainOpts);
                mainChart.render();

                // Ensure chart is visible after rendering
                setTimeout(() => {
                    if (mainChartContainer.querySelector('svg')) {
                        console.log('Main chart rendered successfully');
                    } else {
                        console.warn('Main chart SVG not found after render');
                    }
                }, 100);
            } catch (error) {
                console.error('Error initializing main chart:', error);
                // Fallback: show a message in the container
                mainChartContainer.innerHTML =
                    '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6b7280; font-size: 14px;">Chart sedang dimuat...</div>';
            }

            const chartFilter = document.getElementById('chartFilter');
            chartFilter?.addEventListener('click', (e) => {
                if (e.target.classList.contains('seg-btn')) {
                    chartFilter.querySelectorAll('.seg-btn').forEach(b => b.classList.remove('is-active'));
                    e.target.classList.add('is-active');
                    const f = e.target.getAttribute('data-filter');
                    const dataset = datasets[f] ?? {
                        labels: [],
                        series: []
                    };

                    try {
                        if (mainChart) {
                            // Update both series and options
                            mainChart.updateOptions({
                                series: dataset.series,
                                labels: dataset.labels
                            });
                        }
                    } catch (error) {
                        console.error('Error updating chart:', error);
                    }
                }
            });

            // table filter + export helpers
            (function() {
                const toggleButtons = document.querySelectorAll('.table-filter-toggle');
                toggleButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const key = btn.dataset.tableKey;
                        const panel = document.querySelector(`#filter-panel-${key}`);
                        if (!panel) {
                            return;
                        }
                        panel.classList.toggle('show');
                        if (panel.classList.contains('show')) {
                            panel.querySelector('.table-filter-input')?.focus();
                        }
                    });
                });

                document.querySelectorAll('.table-filter-panel').forEach(panel => {
                    const key = panel.dataset.tableKey;
                    const table = document.querySelector(`#table-${key}`);
                    const input = panel.querySelector('.table-filter-input');
                    const applyBtn = panel.querySelector('.table-filter-apply');
                    const resetBtn = panel.querySelector('.table-filter-reset');
                    if (!table) {
                        return;
                    }
                    const tbody = table.querySelector('tbody');
                    if (!tbody) {
                        return;
                    }

                    const ensureNoMatchRow = () => {
                        let row = tbody.querySelector('.table-no-match');
                        if (!row) {
                            const colSpan = table.querySelectorAll('thead th').length || 1;
                            row = document.createElement('tr');
                            row.className = 'table-no-match d-none';
                            row.innerHTML =
                                `<td colspan="${colSpan}" class="text-center py-4 text-muted"><i class="fa-solid fa-circle-info me-2"></i>Tidak ada data sesuai filter</td>`;
                            tbody.appendChild(row);
                        }
                        return row;
                    };

                    const getDataRows = () => Array.from(tbody.querySelectorAll('tr')).filter(row => !
                        row.dataset.emptyState && !row.classList.contains('table-no-match'));
                    const noMatchRow = ensureNoMatchRow();

                    const applyFilter = () => {
                        const term = (input?.value || '').trim().toLowerCase();
                        const rows = getDataRows();
                        if (!rows.length) {
                            noMatchRow.classList.add('d-none');
                            return;
                        }
                        if (!term) {
                            rows.forEach(row => row.style.display = '');
                            noMatchRow.classList.add('d-none');
                            return;
                        }
                        let matches = 0;
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            const hit = text.includes(term);
                            row.style.display = hit ? '' : 'none';
                            if (hit) {
                                matches += 1;
                            }
                        });
                        noMatchRow.classList.toggle('d-none', matches > 0);
                    };

                    applyBtn?.addEventListener('click', applyFilter);
                    input?.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            applyFilter();
                        }
                    });
                    resetBtn?.addEventListener('click', () => {
                        if (input) {
                            input.value = '';
                        }
                        getDataRows().forEach(row => row.style.display = '');
                        noMatchRow.classList.add('d-none');
                    });
                });

                document.querySelectorAll('.table-export-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const key = btn.dataset.tableKey;
                        const table = document.querySelector(`#table-${key}`);
                        if (!table) {
                            return;
                        }
                        const rows = Array.from(table.querySelectorAll('tr')).filter(row => row
                            .offsetParent !== null && !row.classList.contains(
                                'table-no-match') && !row.dataset.emptyState);
                        if (!rows.length) {
                            alert('Tidak ada data untuk diexport.');
                            return;
                        }
                        const csv = rows.map(row => {
                            const cells = row.querySelectorAll('th,td');
                            return Array.from(cells).map(cell => {
                                const text = cell.innerText.replace(/\s+/g, ' ')
                                    .trim();
                                return '"' + text.replace(/"/g, '""') + '"';
                            }).join(',');
                        }).join('\n');
                        const blob = new Blob([csv], {
                            type: 'text/csv;charset=utf-8;'
                        });
                        const link = document.createElement('a');
                        const timestamp = new Date().toISOString().slice(0, 10);
                        link.href = URL.createObjectURL(blob);
                        link.download = `dashboard-${key}-${timestamp}.csv`;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        setTimeout(() => URL.revokeObjectURL(link.href), 1500);
                    });
                });
            })();

            // radar chart
            const defaultRadarColors = ['#0d6efd', '#20c997', '#ffc107'];

            if (radarChartContainer && performanceHasData) {
                performanceEmptyState?.classList.add('d-none');
                radarChartContainer.classList.remove('d-none');
                const resolvedRadarSeries = Array.isArray(performanceConfig.series) ? performanceConfig.series : [];
                const resolvedRadarLabels = Array.isArray(performanceConfig.labels) ? performanceConfig.labels : [];
                const resolvedRadarColors = Array.isArray(performanceConfig.colors) && performanceConfig.colors
                    .length ?
                    performanceConfig.colors :
                    defaultRadarColors;
                const radarOptions = {
                    chart: {
                        type: 'radar',
                        height: '100%',
                        toolbar: {
                            show: false
                        },
                        foreColor: '#6c757d',
                        parentHeightOffset: 0
                    },
                    series: resolvedRadarSeries,
                    labels: resolvedRadarLabels,
                    colors: resolvedRadarColors,
                    stroke: {
                        width: 2
                    },
                    fill: {
                        opacity: 0.3
                    },
                    markers: {
                        size: 4
                    },
                    dataLabels: {
                        enabled: false
                    },
                    yaxis: {
                        show: false
                    },
                    grid: {
                        show: false
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontWeight: 700,
                        fontSize: '14px',
                        markers: {
                            width: 14,
                            height: 14,
                            radius: 12
                        },
                        itemMargin: {
                            horizontal: 16,
                            vertical: 8
                        },
                        offsetY: 20
                    },
                    xaxis: {
                        labels: {
                            style: {
                                fontSize: '13px',
                                fontWeight: 600,
                                colors: '#495057'
                            }
                        }
                    },
                    plotOptions: {
                        radar: {
                            size: 180,
                            polygons: {
                                strokeColors: '#e9ecef',
                                connectorColors: '#e9ecef',
                                strokeWidth: 1
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 992,
                        options: {
                            plotOptions: {
                                radar: {
                                    size: 150
                                }
                            }
                        }
                    }, {
                        breakpoint: 576,
                        options: {
                            plotOptions: {
                                radar: {
                                    size: 120
                                }
                            },
                            legend: {
                                itemMargin: {
                                    horizontal: 12,
                                    vertical: 6
                                }
                            }
                        }
                    }]
                };

                // Initialize radar chart with error handling
                let radarChart;
                let currentChartType = 'radar';
                try {
                    radarChart = new ApexCharts(radarChartContainer, radarOptions);
                    radarChart.render();
                } catch (error) {
                    console.error('Error initializing radar chart:', error);
                    // Fallback: show a message in the container
                    radarChartContainer.innerHTML =
                        '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6b7280; font-size: 14px;">Chart sedang dimuat...</div>';
                }

                // Toggle chart mode
                const toggleChartModeBtn = document.getElementById('toggleChartMode');
                if (toggleChartModeBtn && radarChart) {
                    toggleChartModeBtn.addEventListener('click', function() {
                        currentChartType = currentChartType === 'radar' ? 'bar' : 'radar';
                        this.textContent = currentChartType === 'radar' ? 'Bar Chart' : 'Radar Chart';
                        radarChart.updateOptions({
                            chart: {
                                type: currentChartType
                            },
                            plotOptions: currentChartType === 'bar' ? {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    endingShape: 'rounded',
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            } : {
                                radar: {
                                    size: 180,
                                    polygons: {
                                        strokeColors: '#e9ecef',
                                        connectorColors: '#e9ecef',
                                        strokeWidth: 1
                                    }
                                }
                            },
                            yaxis: currentChartType === 'bar' ? {
                                show: true,
                                title: {
                                    text: 'Nilai'
                                },
                                labels: {
                                    style: {
                                        colors: '#495057'
                                    }
                                }
                            } : {
                                show: false
                            },
                            grid: currentChartType === 'bar' ? {
                                show: true,
                                borderColor: '#dee2e6'
                            } : {
                                show: false
                            },
                            dataLabels: currentChartType === 'bar' ? {
                                enabled: true,
                                formatter: function (val) {
                                    return val.toLocaleString();
                                },
                                offsetY: -20,
                                style: {
                                    fontSize: '12px',
                                    colors: ["#304758"]
                                }
                            } : {
                                enabled: false
                            }
                        });
                    });
                }

                // chart export menu
                (function() {
                    const menu = document.querySelector('.chart-menu');
                    if (!menu) return;
                    menu.setAttribute('role', 'button');
                    menu.setAttribute('tabindex', '0');
                    menu.setAttribute('title', 'Export chart');
                    const parent = menu.parentElement || document.body;
                    if (getComputedStyle(parent).position === 'static') parent.style.position = 'relative';
                    const box = document.createElement('div');
                    box.className = 'chart-export-box';
                    Object.assign(box.style, {
                        position: 'absolute',
                        right: '0',
                        top: '36px',
                        background: '#fff',
                        border: '1px solid #e6e9ee',
                        borderRadius: '6px',
                        boxShadow: '0 8px 20px rgba(15,23,42,0.06)',
                        padding: '6px',
                        zIndex: 9999,
                        display: 'none',
                        minWidth: '160px'
                    });
                    box.innerHTML = `
						<button class="chart-export-btn" data-type="png" style="display:block;width:100%;text-align:left;padding:8px;border:0;background:transparent;cursor:pointer">Download PNG</button>
						<button class="chart-export-btn" data-type="svg" style="display:block;width:100%;text-align:left;padding:8px;border:0;background:transparent;cursor:pointer">Download SVG</button>
						<button class="chart-export-btn" data-type="csv" style="display:block;width:100%;text-align:left;padding:8px;border:0;background:transparent;cursor:pointer">Export CSV</button>
					`;
                    parent.appendChild(box);

                    function toggle() {
                        box.style.display = box.style.display === 'none' ? 'block' : 'none';
                    }

                    function closeBox() {
                        box.style.display = 'none';
                    }
                    document.addEventListener('click', (e) => {
                        if (!box.contains(e.target) && e.target !== menu) closeBox();
                    });
                    menu.addEventListener('click', function(e) {
                        e.stopPropagation();
                        toggle();
                    });
                    menu.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
                            e.preventDefault();
                            toggle();
                        }
                    });

                    function downloadHref(href, filename) {
                        const a = document.createElement('a');
                        a.href = href;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    }

                    function downloadBlob(blob, filename) {
                        const url = URL.createObjectURL(blob);
                        downloadHref(url, filename);
                        setTimeout(() => URL.revokeObjectURL(url), 15000);
                    }
                    box.addEventListener('click', function(e) {
                        const btn = e.target.closest('.chart-export-btn');
                        if (!btn) return;
                        const type = btn.getAttribute('data-type');
                        closeBox();
                        if (type === 'png') {
                            if (typeof radarChart?.dataURI === 'function') {
                                radarChart.dataURI().then(({
                                    imgURI
                                }) => downloadHref(imgURI, 'performance-chart.png')).catch(() =>
                                    alert('Unable to export PNG'));
                            } else alert('Chart export not available');
                        } else if (type === 'svg') {
                            const svgEl = document.querySelector('#radarChart svg');
                            if (!svgEl) {
                                alert('SVG not found');
                                return;
                            }
                            const svgText = svgEl.outerHTML;
                            const blob = new Blob([svgText], {
                                type: 'image/svg+xml;charset=utf-8'
                            });
                            downloadBlob(blob, 'performance-chart.svg');
                        } else if (type === 'csv') {
                            try {
                                const labels = (typeof radarOptions !== 'undefined' && radarOptions
                                    .labels) || [];
                                const series = (typeof radarOptions !== 'undefined' && radarOptions
                                    .series) || [];
                                let csv = ['label'].concat(series.map(s => '"' + s.name.replace(/"/g,
                                    '""') + '"')).join(',') + '\n';
                                for (let i = 0; i < labels.length; i++) {
                                    const row = ['"' + String(labels[i]).replace(/"/g, '""') + '"'];
                                    series.forEach(s => row.push(s.data[i] != null ? s.data[i] : ''));
                                    csv += row.join(',') + '\n';
                                }
                                const blob = new Blob([csv], {
                                    type: 'text/csv;charset=utf-8'
                                });
                                downloadBlob(blob, 'performance-chart.csv');
                            } catch (err) {
                                console.error(err);
                                alert('Unable to export CSV');
                            }
                        }
                    });
                })();
            } else {
                if (!radarChartContainer) {
                    console.warn('Radar chart container not found, skipping radar initialization');
                }
                if (!performanceHasData) {
                    radarChartContainer?.classList.add('d-none');
                    performanceEmptyState?.classList.remove('d-none');
                }
            }

            // header-rt refresh
            (function() {
                const btn = document.querySelector('.header-rt');
                if (!btn) return;
                btn.addEventListener('click', () => location.reload());
                btn.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
                        e.preventDefault();
                        location.reload();
                    }
                });
            })();

            // status dot cycling pulse
            (function() {
                const dot = document.getElementById('statusDot');
                if (!dot) return;
                const classes = ['status-green', 'status-yellow', 'status-red'];
                let idx = classes.findIndex(c => dot.classList.contains(c));
                if (idx < 0) idx = 0;
                setTimeout(() => {
                    idx = (idx + 1) % classes.length;
                    classes.forEach(c => dot.classList.remove(c));
                    dot.classList.add(classes[idx]);
                    dot.classList.add('status-pulse-short');
                    setTimeout(() => dot.classList.remove('status-pulse-short'), 380);
                }, 300);
                setInterval(() => {
                    idx = (idx + 1) % classes.length;
                    classes.forEach(c => dot.classList.remove(c));
                    dot.classList.add(classes[idx]);
                    dot.classList.add('status-pulse-short');
                    setTimeout(() => dot.classList.remove('status-pulse-short'), 380);
                }, 2000);
            })();

            // animate goals and mini-kpi and kpi numbers (same as original)
            (function() {
                function animateNumeric(el, start, end, duration) {
                    const range = end - start;
                    let startTime = null;

                    function step(timestamp) {
                        if (!startTime) startTime = timestamp;
                        const progress = Math.min((timestamp - startTime) / duration, 1);
                        const value = Math.floor(start + range * progress);
                        el.textContent = value;
                        if (progress < 1) requestAnimationFrame(step);
                        else el.textContent = end;
                    }
                    requestAnimationFrame(step);
                }

                function formatNumber(n) {
                    return n.toLocaleString('id-ID');
                }

                function animateValue(el, start, end, duration, isCurrency) {
                    const range = end - start;
                    let startTime = null;

                    function step(timestamp) {
                        if (!startTime) startTime = timestamp;
                        const progress = Math.min((timestamp - startTime) / duration, 1);
                        const value = Math.floor(start + range * progress);
                        el.textContent = isCurrency ? ('Rp ' + formatNumber(value)) : formatNumber(value);
                        if (progress < 1) window.requestAnimationFrame(step);
                        else el.textContent = isCurrency ? ('Rp ' + formatNumber(end)) : formatNumber(end);
                    }
                    window.requestAnimationFrame(step);
                }

                function animate(el, start, end, dur, isCurrency, currency) {
                    const range = end - start;
                    let s = null;

                    function step(t) {
                        if (!s) s = t;
                        const p = Math.min((t - s) / dur, 1);
                        const v = Math.floor(start + range * p);
                        el.textContent = isCurrency ? (currency + v.toLocaleString('en-US')) : v.toLocaleString(
                            'en-US');
                        if (p < 1) requestAnimationFrame(step);
                        else el.textContent = isCurrency ? (currency + end.toLocaleString('en-US')) : end
                            .toLocaleString('en-US');
                    }
                    requestAnimationFrame(step);
                }
                const goals = document.querySelectorAll('.goal-text');
                goals.forEach((textEl, i) => {
                    const current = parseFloat(textEl.getAttribute('data-current')) || 0;
                    const target = parseFloat(textEl.getAttribute('data-target')) || 0;
                    const pct = target > 0 ? Math.max(0, Math.min(100, (current / target) * 100)) : 0;
                    const currentSpan = textEl.querySelector('.current');
                    const bar = textEl.closest('.goal-block').querySelector('.progress-bar');
                    const delay = i * 160;
                    setTimeout(() => {
                        animateNumeric(currentSpan, 0, current, 900);
                        bar.style.width = pct + '%';
                    }, delay);
                });
                const items = document.querySelectorAll('.mini-kpi-card .mini-value[data-target]');
                items.forEach((el, idx) => {
                    const target = parseInt(el.getAttribute('data-target')) || 0;
                    const isCurrency = el.closest('.mini-kpi-card').querySelector('.mini-label')
                        .textContent.trim().toLowerCase() === 'cost';
                    const delay = idx * 120;
                    setTimeout(() => animateValue(el, 0, target, 900, isCurrency), delay);
                });
                const deltas = document.querySelectorAll('.kpi-delta');
                deltas.forEach((d, i) => {
                    const target = parseInt(d.getAttribute('data-target')) || 0;
                    setTimeout(() => animate(d, 0, target, 700, false), i * 120);
                });
                const values = document.querySelectorAll('.kpi-value');
                values.forEach((v, i) => {
                    if (v.dataset.static === 'true') {
                        return;
                    }
                    const target = parseInt(v.getAttribute('data-target')) || 0;
                    const currency = v.getAttribute('data-currency') || '';
                    setTimeout(() => animate(v, 0, target, 900, currency !== '', currency), i * 140);
                });
            })();
        });
</script>
<script src="{{ asset('bolopa/js/bootstrap.bundle.min.js') }}"></script>
@endpush
