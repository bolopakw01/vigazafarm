@extends('admin.layouts.app')

@section('title', 'Decision Support')

@php
	$breadcrumbs = [
		['label' => 'Backoffice', 'link' => route('admin.dashboard')],
		['label' => 'Decision Support'],
	];

	$mode = $dssMode ?? 'config';
	$eggInsights = $mode === 'config' ? ($insights['eggs'] ?? []) : [];
	$feedInsights = $mode === 'config' ? ($insights['feed'] ?? []) : [];
	$mortalityAlerts = $mode === 'config' ? ($insights['mortality'] ?? []) : [];
	$mlRecommendations = $mode === 'ml' ? data_get($mlResponse, 'recommendations', []) : [];
	$mlMetadata = $mode === 'ml' ? data_get($mlResponse, 'metadata', []) : [];
	$mlStatus = $mode === 'ml' ? data_get($mlResponse, 'status', 'waiting_for_training') : null;
	$modelVersion = $mode === 'ml' ? data_get($mlResponse, 'model_version', 'untrained') : null;
	$mlPredictions = $mode === 'ml' ? data_get($mlResponse, 'predictions', []) : [];
	$eggPrediction = data_get($mlPredictions, 'eggs', []);
	$feedPrediction = data_get($mlPredictions, 'feed', []);
	$mortalityPrediction = data_get($mlPredictions, 'mortality', []);
	$pricingPrediction = data_get($mlPredictions, 'pricing', []);
	$mlAlerts = $mode === 'ml' ? data_get($mlResponse, 'alerts', []) : [];
	$mlCapabilities = $mode === 'ml' ? data_get($settings, 'ml.capabilities', []) : [];
	$mlCapabilityMap = [
		'egg_forecast' => 'Prediksi produksi telur',
		'feed_prediction' => 'Prediksi kebutuhan pakan',
		'mortality_detection' => 'Deteksi mortalitas',
		'pricing_optimizer' => 'Optimasi harga jual',
		'explainability' => 'Alert & Explainability',
	];
	$summaryMeta = [
		'eggs' => ['label' => 'Penetasan Telur', 'icon' => 'fa-egg'],
		'feed' => ['label' => 'Konsumsi Pakan', 'icon' => 'fa-wheat-awn'],
		'mortality' => ['label' => 'Kematian', 'icon' => 'fa-skull-crossbones'],
	];
	$trendSeries = $mode === 'config' ? ($trendSeries ?? []) : [];
	$trendLabels = data_get($trendSeries, 'labels', []);
	$trendData = data_get($trendSeries, 'data', []);
	$trendMeta = data_get($trendSeries, 'meta', []);
	$trendHasData = data_get($trendSeries, 'has_data', false);
	$trendRange = data_get($trendSeries, 'date_range', []);
	$trendRangeLabel = null;
	if (!empty($trendRange['start']) || !empty($trendRange['end'])) {
		$parts = array_filter([$trendRange['start'] ?? null, $trendRange['end'] ?? null]);
		$trendRangeLabel = implode(' – ', $parts);
	}
	$trendSeriesPayload = collect($trendMeta)
		->map(function ($meta, $key) use ($trendData) {
			return [
				'key' => $key,
				'label' => $meta['label'] ?? ucfirst($key),
				'color' => $meta['color'] ?? '#0f172a',
				'unit' => $meta['unit'] ?? '',
				'data' => array_values($trendData[$key] ?? []),
			];
		})
		->values();
@endphp

@push('styles')
	<style>
		.dss-wrapper {
			margin-top: 16px;
		}
		.dss-shell {
			background: #ffffff;
			border-radius: 8px;
			padding: 28px;
			box-shadow: 0 35px 65px rgba(15, 23, 42, 0.12);
		}
		.dss-header {
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			justify-content: space-between;
			gap: 14px;
			padding-bottom: 18px;
			border-bottom: 1px solid #e2e8f0;
			margin-bottom: 22px;
		}
		.dss-header h1 {
			font-size: 1.65rem;
			margin-bottom: 4px;
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.dss-header h1 img {
			width: 34px;
			height: 34px;
		}
		.dss-header .subtitle {
			color: #475569;
			font-size: 0.95rem;
		}
		.dss-header .meta {
			text-align: right;
			color: #0f172a;
			font-weight: 600;
		}
		.dss-header .meta small {
			display: block;
			color: #94a3b8;
			font-weight: 500;
		}
		.summary-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
			gap: 16px;
		}
		.summary-card {
			border-radius: 22px;
			padding: 20px;
			background: linear-gradient(165deg, #f8fafc, #ffffff);
			border: 1px solid #edf2f7;
			display: flex;
			flex-direction: column;
			gap: 8px;
		}
		.summary-card .label {
			font-size: 0.9rem;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			color: #94a3b8;
		}
		.summary-card .value {
			font-size: 2.4rem;
			font-weight: 700;
			color: #0f172a;
			line-height: 1;
		}
		.summary-card .indicator {
			width: 46px;
			height: 46px;
			border-radius: 14px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
			font-size: 1.2rem;
		}
		.summary-card.eggs .indicator { background: linear-gradient(135deg, #f97316, #fb923c); }
		.summary-card.feed .indicator { background: linear-gradient(135deg, #16a34a, #4ade80); }
		.summary-card.mortality .indicator { background: linear-gradient(135deg, #dc2626, #f87171); }
		.chart-card {
			margin-top: 24px;
		}
		.chart-wrap {
			position: relative;
			min-height: 320px;
			padding: 8px 0;
		}
		.chart-wrap canvas {
			width: 100% !important;
			height: 100% !important;
		}
		.ml-meta-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
			gap: 16px;
			margin-top: 18px;
		}
		.ml-meta-item {
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 14px 16px;
			background: #f8fafc;
		}
		.ml-meta-item .label {
			text-transform: uppercase;
			font-size: 0.7rem;
			letter-spacing: 0.08em;
			color: #94a3b8;
			margin-bottom: 4px;
		}
		.ml-meta-item .value {
			font-weight: 600;
			color: #0f172a;
		}
		.capability-pills {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			margin-top: 12px;
		}
		.capability-pill {
			border-radius: 999px;
			padding: 6px 14px;
			font-size: 0.8rem;
			font-weight: 600;
			border: 1px solid #cbd5f5;
			color: #475569;
			background: #fff;
		}
		.capability-pill.active {
			background: #e0e7ff;
			border-color: #c7d2fe;
			color: #4338ca;
		}
		.capability-pill.muted {
			opacity: 0.6;
		}
		.ml-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
			gap: 18px;
			margin-top: 24px;
		}
		.ml-card {
			border-radius: 24px;
			border: 1px solid #e2e8f0;
			padding: 20px;
			background: linear-gradient(165deg, #ffffff, #f8fafc);
			box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
		}
		.ml-card h3 {
			font-size: 1rem;
			margin-bottom: 6px;
		}
		.ml-value {
			font-size: 2rem;
			font-weight: 700;
			margin-bottom: 4px;
		}
		.ml-value small {
			font-size: 0.9rem;
			color: #475569;
		}
		.mini-list {
			margin: 12px 0 0;
			padding-left: 18px;
			font-size: 0.86rem;
			color: #475569;
		}
		.ml-alert-stack {
			display: flex;
			flex-direction: column;
			gap: 12px;
			margin-top: 18px;
		}
		.ml-alert {
			border-radius: 16px;
			padding: 14px 16px;
			border: 1px solid #e2e8f0;
			background: #f8fafc;
		}
		.ml-alert.level-warning { border-color: #fcd34d; background: #fffbeb; }
		.ml-alert.level-critical { border-color: #f87171; background: #fef2f2; }
		.ml-alert.level-info { border-color: #bae6fd; background: #ecfeff; }
		.ml-reco {
			border-radius: 18px;
			border: 1px solid #e2e8f0;
			padding: 16px;
			margin-bottom: 16px;
		}
		.ml-reco:last-child { margin-bottom: 0; }
		.ml-chip {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 4px 10px;
			border-radius: 999px;
			background: #eef2ff;
			color: #3730a3;
			font-size: 0.75rem;
			font-weight: 700;
			text-transform: uppercase;
		}
		.section-card {
			border-radius: 24px;
			background: #fff;
			border: 1px solid #e2e8f0;
			padding: 22px 24px;
			margin-top: 24px;
		}
		.section-card .card-head {
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			align-items: center;
			gap: 12px;
		}
		.section-card h2 {
			font-size: 1.15rem;
			margin-bottom: 4px;
		}
		.status-pill {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			border-radius: 999px;
			padding: 0.2rem 0.85rem;
			font-size: 0.78rem;
			font-weight: 600;
			text-transform: uppercase;
		}
		.status-pill.ok { background: #dcfce7; color: #15803d; }
		.status-pill.warning { background: #fef3c7; color: #b45309; }
		.status-pill.critical { background: #fee2e2; color: #b91c1c; }
		.status-pill.info { background: #e0f2fe; color: #0369a1; }
		.table-wrap table { font-size: 0.92rem; }
		.table-wrap th { background: #f8fafc; font-weight: 600; }
		.table-wrap td { vertical-align: middle; }
		.empty-state {
			padding: 32px;
			text-align: center;
			color: #94a3b8;
			border: 1px dashed #e2e8f0;
			border-radius: 12px;
			background: #f8fafc;
		}
		@media (max-width: 767.98px) {
			.dss-shell { padding: 20px; border-radius: 20px; }
			.section-card { padding: 18px; }
			.ml-grid { grid-template-columns: 1fr; }
		}
	</style>
@endpush

@section('content')
<div class="container-app container">
	<div class="dss-wrapper">
		<div class="dss-shell">
			<div class="dss-header">
				<div>
					<h1>
						<img src="{{ asset('bolopa/img/icon/fluent--chart-multiple-32-filled.svg') }}" alt="DSS Icon">
						Decision Support System
					</h1>
					<p class="subtitle mb-0">Insight terkurasi untuk membantu keputusan operasional harian.</p>
				</div>
				<div class="meta">
					<div class="mb-2">
						<span class="status-pill {{ $mode === 'ml' ? 'info' : 'ok' }}">Mode {{ strtoupper($mode) }}</span>
					</div>
					<small>Terakhir diperbarui</small>
					{{ optional($lastUpdated)->format('d/m/Y H:i') }}
				</div>
			</div>

			@if($mode === 'config')
			<div class="summary-grid">
				@foreach($summaryMeta as $key => $meta)
					@php $data = $summary[$key] ?? ['total' => 0, 'alerts' => 0]; @endphp
					<div class="summary-card {{ $key }}">
						<div class="d-flex justify-content-between align-items-start">
							<div class="indicator">
								<i class="fa-solid {{ $meta['icon'] }}" aria-hidden="true"></i>
							</div>
							<div class="text-end">
								<div class="label">{{ $meta['label'] }}</div>
								<div class="value">{{ $data['alerts'] }}</div>
								<div class="text-muted" style="font-size:0.85rem;">Alert dari {{ $data['total'] }} data</div>
							</div>
						</div>
						<p class="mb-0 text-muted" style="font-size:0.85rem;">{{ $data['alerts'] > 0 ? 'Perlu perhatian' : 'Semua dalam batas wajar' }}</p>
					</div>
				@endforeach
			</div>

			<div class="section-card chart-card">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Tren Produksi & Risiko</h2>
						<small class="text-muted">
							{{ $trendRangeLabel ? 'Rentang ' . $trendRangeLabel : 'Rentang 7 hari terakhir' }}
						</small>
					</div>
					<div class="text-end">
						<span class="status-pill info">Line Trend</span>
						<div class="text-muted" style="font-size:0.8rem;">Update otomatis harian</div>
					</div>
				</div>
				<div class="chart-wrap mt-3">
					@if($trendHasData && $trendSeriesPayload->isNotEmpty())
						<canvas id="dss-trend-chart"
							data-labels='@json($trendLabels)'
							data-series='@json($trendSeriesPayload)'></canvas>
					@else
						<div class="empty-state">
							<i class="fa-solid fa-chart-line" style="font-size:2rem;"></i>
							<p class="mb-0 mt-2">Belum ada data historis yang cukup. Pastikan pencatatan telur, pakan, dan mortalitas rutin.</p>
						</div>
					@endif
				</div>
			</div>

			<div class="section-card">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Status Penetasan Telur</h2>
						<small class="text-muted">Pantau jadwal hatcher dan rasio tetas terbaru.</small>
					</div>
				</div>
				<div class="table-wrap mt-3">
					@if(count($eggInsights))
						<div class="table-responsive">
							<table class="table align-middle">
								<thead>
									<tr>
										<th>Batch</th>
										<th>Kandang / Fase</th>
										<th>Jadwal Hatcher</th>
										<th>Rasio Tetas</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
									@foreach($eggInsights as $item)
										@php
											$statusLevel = data_get($item, 'status.level', 'info');
											$statusMessage = data_get($item, 'status.message');
											$statusStage = data_get($item, 'status.stage');
											$daysToHatcher = $item['days_to_hatcher'];
											$daysLabel = null;
											if(!is_null($daysToHatcher)) {
												$daysLabel = $daysToHatcher >= 0
													? $daysToHatcher . ' hari lagi'
													: 'Terlambat ' . abs($daysToHatcher) . ' hari';
											}
										@endphp
										<tr>
											<td class="fw-semibold">{{ $item['batch'] }}</td>
											<td>
												<div class="fw-semibold">{{ $item['kandang'] }}</div>
												<small class="text-muted">{{ $item['fase'] }}</small>
											</td>
											<td>
												<div class="fw-semibold">{{ $item['target_hatcher'] ?? '-' }}</div>
												<small class="text-muted">
													{{ $daysLabel ?? 'Jadwal belum diisi' }}
												</small>
											</td>
											<td>
												<div class="fw-semibold">
													{{ $item['hatch_rate'] !== null ? $item['hatch_rate'] . '%' : '-' }}
												</div>
												<small class="text-muted">{{ number_format($item['jumlah_menetas']) }} / {{ number_format($item['jumlah_telur']) }} telur</small>
											</td>
											<td>
												<span class="status-pill {{ $statusLevel }}">{{ $statusStage ?? ucfirst($statusLevel) }}</span>
												<p class="mb-0 text-muted" style="font-size:0.8rem;">{{ $statusMessage }}</p>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="empty-state">
							<i class="fa-regular fa-egg" style="font-size:2rem;"></i>
							<p class="mb-0 mt-2">Belum ada penetasan aktif atau catatan jadwal hatcher.</p>
						</div>
					@endif
				</div>
			</div>

			<div class="section-card">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Insight Konsumsi Pakan</h2>
						<small class="text-muted">Perbandingan target vs realisasi per batch aktif.</small>
					</div>
				</div>
				<div class="table-wrap mt-3">
					@if(count($feedInsights))
						<div class="table-responsive">
							<table class="table align-middle">
								<thead>
									<tr>
										<th>Batch</th>
										<th>Kandang / Fase</th>
										<th>Target vs Aktual</th>
										<th>Selisih</th>
										<th>Status</th>
										<th>Rekomendasi</th>
									</tr>
								</thead>
								<tbody>
									@foreach($feedInsights as $item)
										@php
											$statusLevel = data_get($item, 'status.level', 'info');
											$statusMessage = data_get($item, 'status.message');
										@endphp
										<tr>
											<td>
												<div class="fw-semibold">{{ $item['batch'] }}</div>
												<small class="text-muted">Populasi {{ number_format($item['populasi']) }} ekor</small>
											</td>
											<td>
												<div class="fw-semibold">{{ $item['kandang'] }}</div>
												<small class="text-muted">{{ $item['fase'] }} • {{ $item['umur_hari'] ?? '-' }} hari</small>
											</td>
											<td>
												<div class="fw-semibold">{{ number_format($item['actual_kg'], 2) }} kg</div>
												<small class="text-muted">Target {{ number_format($item['target_kg'], 2) }} kg • Rata2 {{ number_format($item['avg7day_kg'], 2) }} kg</small>
											</td>
											<td>
												<div class="fw-semibold">{{ number_format($item['delta_kg'], 2) }} kg</div>
												<small class="text-muted">{{ data_get($item, 'status.delta_pct', 0) }}%</small>
											</td>
											<td>
												<span class="status-pill {{ $statusLevel }}">{{ ucfirst($statusLevel) }}</span>
												<p class="mb-0 text-muted" style="font-size:0.8rem;">{{ $statusMessage }}</p>
											</td>
											<td>
												<p class="mb-0" style="font-size:0.85rem; color:#475569;">{{ data_get($item, 'status.recommendation', '-') }}</p>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="empty-state">
							<i class="fa-regular fa-clipboard" style="font-size:2rem;"></i>
							<p class="mb-0 mt-2">Belum ada data konsumsi pakan yang siap dianalisis.</p>
						</div>
					@endif
				</div>
			</div>

			<div class="section-card mt-4">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Alert Mortalitas</h2>
						<small class="text-muted">Pantau deviasi mortalitas {{ data_get($settings, 'mortality.window_days', 3) }} hari terakhir.</small>
					</div>
				</div>
				<div class="table-wrap mt-3">
					@if(count($mortalityAlerts))
						<div class="table-responsive">
							<table class="table align-middle">
								<thead>
									<tr>
										<th>Batch</th>
										<th>Kandang / Fase</th>
										<th>Rentang Tanggal</th>
										<th>Mortalitas</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
									@foreach($mortalityAlerts as $item)
										<tr>
											<td class="fw-semibold">{{ $item['batch'] }}</td>
											<td>
												<div class="fw-semibold">{{ $item['kandang'] }}</div>
												<small class="text-muted">Fase {{ $item['fase'] }}</small>
											</td>
											<td>
												<div class="fw-semibold">{{ $item['date_range']['start'] }} &ndash; {{ $item['date_range']['end'] }}</div>
												<small class="text-muted">{{ number_format($item['total_kematian']) }} ekor</small>
											</td>
											<td>
												<div class="fw-semibold">{{ $item['mortality_rate'] }}%</div>
												<small class="text-muted">Standar {{ $item['standard_rate'] }}%</small>
											</td>
											<td>
												<span class="status-pill {{ $item['status']['level'] }}">{{ ucfirst($item['status']['level']) }}</span>
												<p class="mb-0 text-muted" style="font-size:0.8rem;">{{ $item['status']['message'] }}</p>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="empty-state">
							<i class="fa-regular fa-heart" style="font-size:2rem;"></i>
							<p class="mb-0 mt-2">Belum ada laporan kematian di rentang waktu yang dipantau.</p>
						</div>
					@endif
				</div>
			</div>

			@else
			@php
				$statusClass = match($mlStatus) {
					'ready' => 'ok',
					'waiting_for_data' => 'warning',
					default => 'info',
				};
			@endphp
			<div class="section-card">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Mesin Prediksi ML</h2>
						<small class="text-muted">Model version: {{ $modelVersion }} • Status: {{ strtoupper($mlStatus) }}</small>
					</div>
					<span class="status-pill {{ $statusClass }}">{{ strtoupper($mlStatus) }}</span>
				</div>
				<div class="ml-meta-grid">
					<div class="ml-meta-item">
						<div class="label">Artifact</div>
						<div class="value">{{ data_get($settings, 'ml.artifact_label', '-') ?: '-' }}</div>
					</div>
					<div class="ml-meta-item">
						<div class="label">Default Phase</div>
						<div class="value">{{ data_get($settings, 'ml.default_phase', 'grower') }}</div>
					</div>
					<div class="ml-meta-item">
						<div class="label">Catatan</div>
						<div class="value">{{ data_get($settings, 'ml.notes', '-') ?: '-' }}</div>
					</div>
					<div class="ml-meta-item">
						<div class="label">Rekam Data</div>
						<div class="value">{{ number_format(data_get($mlMetadata, 'records_used', 0)) }} baris</div>
					</div>
				</div>
				<div class="capability-pills">
					@foreach($mlCapabilityMap as $key => $label)
						@php $active = data_get($mlCapabilities, $key, true); @endphp
						<span class="capability-pill {{ $active ? 'active' : 'muted' }}">{{ $label }}</span>
					@endforeach
				</div>
			</div>

			<div class="ml-grid">
				<div class="ml-card">
					<h3>Prediksi Produksi Telur</h3>
					<div class="ml-value">
						{{ $eggPrediction ? number_format((float) data_get($eggPrediction, 'forecast', 0)) : '—' }}
						<small>butir / hari</small>
					</div>
					<p class="text-muted mb-2">Confidence {{ $eggPrediction ? number_format((float) data_get($eggPrediction, 'confidence', 0), 2) : '0.00' }} • Tren {{ strtoupper(data_get($eggPrediction, 'trend', 'FLAT')) }}</p>
					@if($eggPrediction && !empty($eggPrediction['drivers']))
						<ul class="mini-list">
							@foreach($eggPrediction['drivers'] as $driver)
								<li>{{ data_get($driver, 'label') }} • {{ number_format((float) data_get($driver, 'value', 0)) }} butir</li>
							@endforeach
						</ul>
					@else
						<p class="text-muted mb-0">Belum ada catatan produksi yang dapat dianalisis.</p>
					@endif
				</div>
				<div class="ml-card">
					<h3>Kebutuhan Pakan</h3>
					<div class="ml-value">
						{{ $feedPrediction ? number_format((float) data_get($feedPrediction, 'total_required_kg', 0), 2) : '—' }}
						<small>kg / hari</small>
					</div>
					<p class="text-muted mb-2">Harga rata-rata {{ $feedPrediction && data_get($feedPrediction, 'avg_price_per_kg') ? 'Rp ' . number_format((float) data_get($feedPrediction, 'avg_price_per_kg', 0), 2) . '/kg' : 'belum tersedia' }}</p>
					@if($feedPrediction)
						<ul class="mini-list">
							@foreach(array_slice(data_get($feedPrediction, 'per_batch', []), 0, 3) as $batch)
								<li>Batch {{ $batch['batch'] }} • {{ number_format((float) $batch['required_kg'], 2) }} kg ({{ ucfirst($batch['status']) }})</li>
							@endforeach
						</ul>
					@else
						<p class="text-muted mb-0">Belum ada konsumsi pakan yang tercatat.</p>
					@endif
				</div>
				<div class="ml-card">
					<h3>Mortalitas & Outbreak</h3>
					@php $topMortality = collect(data_get($mortalityPrediction, 'alerts', []))->first(); @endphp
					<div class="ml-value">
						{{ $topMortality ? strtoupper($topMortality['risk']) : 'LOW' }}
						<small>risk score {{ $topMortality ? $topMortality['score'] : '0.00' }}</small>
					</div>
					@if($topMortality)
						<p class="text-muted mb-0">Batch {{ $topMortality['batch'] }}: {{ $topMortality['message'] }}</p>
					@else
						<p class="text-muted mb-0">Tidak ada lonjakan mortalitas pada jendela {{ data_get($mortalityPrediction, 'window_days', 7) }} hari.</p>
					@endif
				</div>
				<div class="ml-card">
					<h3>Optimasi Harga Jual</h3>
					<div class="ml-value">
						{{ $pricingPrediction ? 'Rp ' . number_format((float) data_get($pricingPrediction, 'optimal_price', 0), 2) : '—' }}
						<small>per butir</small>
					</div>
					@if($pricingPrediction)
						<p class="text-muted mb-1">Profit diproyeksikan {{ 'Rp ' . number_format((float) data_get($pricingPrediction, 'expected_profit', 0), 0) }}</p>
						<p class="text-muted mb-0">{{ data_get($pricingPrediction, 'notes') }}</p>
					@else
						<p class="text-muted mb-0">Butuh histori harga dan pakan untuk rekomendasi harga.</p>
					@endif
				</div>
			</div>

			<div class="section-card mt-4">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Alert & Explainability</h2>
						<small class="text-muted">Menggabungkan rules & sinyal ML untuk highlight prioritas.</small>
					</div>
				</div>
				<div class="ml-alert-stack">
					@forelse($mlAlerts as $alert)
						@php $level = data_get($alert, 'level', 'info'); @endphp
						<div class="ml-alert level-{{ $level }}">
							<div class="d-flex justify-content-between align-items-center mb-1">
								<strong>{{ data_get($alert, 'title', 'Insight') }}</strong>
								<span class="status-pill {{ $level === 'critical' ? 'critical' : ($level === 'warning' ? 'warning' : 'info') }}">{{ strtoupper($level) }}</span>
							</div>
							<p class="mb-1 text-muted">{{ data_get($alert, 'detail', 'Tidak ada detail tambahan') }}</p>
							@if($tags = data_get($alert, 'tags', []))
								<div class="capability-pills mt-2">
									@foreach($tags as $tag)
										<span class="capability-pill">{{ strtoupper($tag) }}</span>
									@endforeach
								</div>
							@endif
						</div>
					@empty
						<div class="empty-state">
							<i class="fa-solid fa-shield-heart" style="font-size:2rem;"></i>
							<p class="mb-0 mt-2">Tidak ada alert aktif dari model dan rules.</p>
						</div>
					@endforelse
				</div>
			</div>

			<div class="section-card mt-4">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Action Recommendation</h2>
						<small class="text-muted">Daftar prioritas yang dapat langsung dioperasionalkan.</small>
					</div>
				</div>
				<div class="mt-3">
					@if(count($mlRecommendations))
						@foreach($mlRecommendations as $recommendation)
							<div class="ml-reco">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<span class="ml-chip">{{ data_get($recommendation, 'category', 'Insight') }}</span>
									<span class="status-pill info">ML</span>
								</div>
								<p class="mb-2 fw-semibold">{{ data_get($recommendation, 'summary', 'Tidak ada ringkasan') }}</p>
								@php $actions = data_get($recommendation, 'action_items', []); @endphp
								@if(is_array($actions) && count($actions))
									<ul class="mb-0 ps-4 text-muted" style="font-size:0.9rem;">
										@foreach($actions as $action)
											<li>{{ $action }}</li>
										@endforeach
									</ul>
								@endif
							</div>
						@endforeach
					@else
						<div class="empty-state">
							<i class="fa-solid fa-robot" style="font-size:2rem;"></i>
							<p class="mb-0 mt-2">Belum ada rekomendasi dari model. Pastikan artefak sudah dilatih dan diunggah.</p>
						</div>
					@endif
				</div>
			</div>
			@endif
		</div>
	</div>
</div>
@endsection

@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const trendCanvas = document.getElementById('dss-trend-chart');
			if (!trendCanvas || typeof Chart === 'undefined') {
				return;
			}
			let labels = [];
			let series = [];
			try {
				labels = JSON.parse(trendCanvas.dataset.labels || '[]');
				series = JSON.parse(trendCanvas.dataset.series || '[]');
			} catch (error) {
				labels = [];
				series = [];
			}
			if (!Array.isArray(labels) || labels.length === 0 || !Array.isArray(series) || series.length === 0) {
				return;
			}
			const ctx = trendCanvas.getContext('2d');
			const datasets = series.map((item) => ({
				label: item.label,
				data: item.data || [],
				borderColor: item.color || '#2563eb',
				backgroundColor: (item.color || '#2563eb') + '33',
				borderWidth: 2,
				tension: 0.35,
				fill: 'start',
				pointRadius: 3,
				pointHoverRadius: 5,
				pointBackgroundColor: item.color || '#2563eb',
				pointBorderColor: '#fff',
				unit: item.unit || '',
			}));
			new Chart(ctx, {
				type: 'line',
				data: {
					labels,
					datasets,
				},
				options: {
					maintainAspectRatio: false,
					responsive: true,
					interaction: {
						mode: 'index',
						intersect: false,
					},
					scales: {
						y: {
							beginAtZero: true,
							grid: {
								color: 'rgba(226, 232, 240, 0.6)',
							},
							ticks: {
								precision: 0,
							},
						},
						x: {
							grid: {
								color: 'rgba(248, 250, 252, 0.8)',
							},
						},
					},
					plugins: {
						legend: {
							position: 'bottom',
							labels: {
								usePointStyle: true,
							},
						},
						tooltip: {
							callbacks: {
								label(context) {
									const dataset = context.dataset || {};
									const suffix = dataset.unit ? ` ${dataset.unit}` : '';
									return `${dataset.label}: ${context.formattedValue}${suffix}`;
								},
							},
						},
					},
				},
			});
		});
	</script>
@endpush
