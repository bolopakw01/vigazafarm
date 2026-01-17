@extends('admin.layouts.app')

@section('title', 'Decision Support')

@php
	$breadcrumbs = [
		['label' => 'Backoffice', 'link' => route('admin.dashboard')],
		['label' => 'Decision Support'],
	];

	$mode = $dssMode ?? 'config';
	$summary = $summary ?? [];
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
	$simulation = $simulation ?? null;
	$simInput = $simulation['input'] ?? [
		'umur_hari' => request('umur_hari'),
		'pakan_g_per_hari' => request('pakan_g_per_hari'),
		'protein_persen' => request('protein_persen'),
		'berat_badan_g' => request('berat_badan_g'),
		'harga_pakan_per_kg' => request('harga_pakan_per_kg'),
		'margin_persen' => request('margin_persen'),
	];
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
	$dataSource = $mode === 'ml' ? data_get($mlResponse, 'predictions.eggs.data_source', 'live') : 'live';
	$summaryChartPayload = collect($summaryMeta)
		->map(function ($meta, $key) use ($summary) {
			$datum = $summary[$key] ?? ['total' => 0, 'alerts' => 0];
			return [
				'label' => $meta['label'],
				'alerts' => $datum['alerts'] ?? 0,
				'total' => $datum['total'] ?? 0,
			];
		})
		->values();
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

		$mlMetaBadges = [
			['label' => 'Sumber Data', 'value' => strtoupper($dataSource)],
			['label' => 'Window', 'value' => (data_get($mlResponse, 'predictions.eggs.window_days') ?? 14) . ' hari'],
			['label' => 'Dicetak', 'value' => data_get($mlResponse, 'metadata.generated_at')],
		];
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
		.sim-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
			gap: 14px;
			margin-top: 10px;
		}
		.sim-result-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
			gap: 16px;
			margin-top: 14px;
		}
		.sim-grid .form-control {
			background: #f8fafc;
			border-color: #e2e8f0;
			box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
		}
		.sim-grid label {
			font-weight: 600;
			color: #0f172a;
		}
		.sim-grid small {
			color: #64748b;
		}
		.sim-section-highlight {
			background: linear-gradient(135deg, #f8fafc, #ffffff);
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 16px;
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
			display: flex;
			align-items: center;
			gap: 8px;
		}
		.ml-card.eggs {
			border-color: #0ea5e9;
			background: linear-gradient(165deg, #ffffff, #f0f9ff);
		}
		.ml-card.eggs h3 i {
			color: #0ea5e9;
		}
		.ml-card.cost {
			border-color: #ef4444;
			background: linear-gradient(165deg, #ffffff, #fef2f2);
		}
		.ml-card.cost h3 i {
			color: #ef4444;
		}
		.ml-card.price {
			border-color: #22c55e;
			background: linear-gradient(165deg, #ffffff, #f0fdf4);
		}
		.ml-card.price h3 i {
			color: #22c55e;
		}
		.sim-ops-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
			gap: 16px;
			margin-top: 16px;
		}
		.sim-ops-box {
			border: 1px solid #e2e8f0;
			border-radius: 14px;
			padding: 14px 16px;
			background: #f8fafc;
		}
		.sim-ops-box h6 {
			font-size: 0.9rem;
			margin-bottom: 8px;
			text-transform: uppercase;
			letter-spacing: 0.08em;
			color: #475569;
		}
		.sim-ops-box ul {
			margin: 0;
			padding-left: 18px;
			color: #0f172a;
		}
		.sim-ops-box .pill-stack {
			display: flex;
			flex-direction: column;
			gap: 6px;
		}
		.sim-quick-actions {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
		}
		.sim-quick-actions button {
			border-radius: 10px;
			border: 1px solid #cbd5e1;
			background: #fff;
			padding: 8px 12px;
			font-weight: 600;
			color: #0f172a;
			transition: all 0.15s ease;
		}
		.sim-quick-actions button:hover {
			background: #e0f2fe;
			border-color: #38bdf8;
			color: #0ea5e9;
		}
		.ml-value {
			font-size: 2rem;
			font-weight: 700;
			margin-bottom: 4px;
		}
		.ml-value.empty {
			color: #cbd5e1;
			font-size: 1.5rem;
		}
		.ml-value small {
			font-size: 0.9rem;
			color: #475569;
		}
		.ml-alert.level-warning { border-color: #fcd34d; background: #fffbeb; }
		.ml-alert.level-critical { border-color: #f87171; background: #fef2f2; }
		.ml-alert.level-info { border-color: #bae6fd; background: #ecfeff; }
		.ml-reco {
			border-radius: 18px;
			border: 1px solid #e2e8f0;
			padding: 16px;
			margin-bottom: 16px;
			background: #fff;
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
		.action-item.critical { border-color: #b91c1c !important; }
		.action-item.warning { border-color: #b45309 !important; }
		.action-item.ok { border-color: #15803d !important; }
		.action-item.info { border-color: #0369a1 !important; }
		.table-wrap table { font-size: 0.85rem; table-layout: fixed; max-width: none; min-width: 1500px; }
		.table-wrap .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
		.table-wrap th { background: #f8fafc; font-weight: 600; }
		.table-wrap td { vertical-align: middle; white-space: nowrap; overflow: visible; text-overflow: clip; }
		.table-wrap .table th:nth-child(1) { width: 80px; text-align: left; } /* Batch */
		.table-wrap .table td:nth-child(1) { text-align: left; }
		.table-wrap .table th:nth-child(2) { width: 60px; text-align: left; } /* Kandang/Fase */
		.table-wrap .table td:nth-child(2) { text-align: left; }
		.table-wrap .table th:nth-child(3) { width: 80px; text-align: left; } /* Jadwal Hatcher / Target vs Aktual / Rentang Tanggal */
		.table-wrap .table td:nth-child(3) { text-align: left; }
		.table-wrap .table th:nth-child(4) { width: 50px; text-align: right; } /* Rasio Tetas / Selisih / Mortalitas */
		.table-wrap .table td:nth-child(4) { text-align: right; }
		.table-wrap .table th:nth-child(5) { width: 50px; text-align: center; } /* Status */
		.table-wrap .table td:nth-child(5) { text-align: center; }
		.table-wrap .table th:nth-child(6) { width: 100px; text-align: left; } /* Tindakan / Rekomendasi */
		.table-wrap .table td:nth-child(6) { text-align: left; white-space: normal; word-wrap: break-word; overflow: visible; text-overflow: clip; }
		.empty-state {
			padding: 32px;
			text-align: center;
			color: #94a3b8;
			border: 1px dashed #e2e8f0;
			border-radius: 12px;
			background: #f8fafc;
		}
		.chart-empty-state {
			padding: 40px 20px;
			text-align: center;
			color: #94a3b8;
			border: 1px dashed #e2e8f0;
			border-radius: 12px;
			background: #f8fafc;
		}
		.chart-empty-state .chart-placeholder-icon {
			font-size: 4rem;
			color: #cbd5e1;
			margin-bottom: 16px;
		}
		.chart-empty-state h4 {
			color: #475569;
			margin-bottom: 8px;
			font-size: 1.1rem;
			font-weight: 600;
		}
		.chart-empty-state p {
			color: #64748b;
			max-width: 300px;
			margin: 0 auto;
			font-size: 0.9rem;
		}
		.collapsible-content {
			transition: all 0.3s ease;
			overflow: hidden;
		}
		.collapsible-content.collapsed {
			max-height: 0;
			padding-top: 0;
			padding-bottom: 0;
			margin-top: 0;
			margin-bottom: 0;
		}
		.toggle-btn {
			background: none;
			border: none;
			color: #64748b;
			cursor: pointer;
			padding: 4px;
			border-radius: 4px;
			transition: background-color 0.2s ease;
		}
		.toggle-btn:hover {
			background: #f1f5f9;
		}
		.toggle-btn i {
			transition: transform 0.3s ease;
		}
		.toggle-btn.collapsed i {
			transform: rotate(180deg);
		}
	</style>
@endpush

@section('content')
<div class="container-app container">
	<div class="dss-wrapper">
		<div class="dss-shell">
			@php
				$priorityMap = ['critical' => 3, 'warning' => 2, 'ok' => 1, 'info' => 0];
				$levelLabelMap = [
					'critical' => 'Darurat',
					'warning' => 'Perlu Perhatian',
					'ok' => 'Aman',
					'normal' => 'Aman',
					'info' => 'Info',
				];
				$displayLevel = function ($level) use ($levelLabelMap) {
					return $levelLabelMap[$level] ?? ucfirst($level);
				};
				$todayActions = [];
				foreach ($eggInsights as $item) {
					$level = data_get($item, 'status.level', 'info');
					if (($priorityMap[$level] ?? 0) >= 2) {
						$todayActions[] = [
							'level' => $level,
							'icon' => 'fa-egg',
							'title' => 'Penetasan ' . ($item['batch'] ?? '-'),
							'detail' => data_get($item, 'status.message', 'Periksa jadwal hatcher dan rasio tetas.'),
							'hint' => ($item['kandang'] ?? '-') . ' • ' . ($item['fase'] ?? '-')
						];
					}
				}
				foreach ($feedInsights as $item) {
					$level = data_get($item, 'status.level', 'info');
					if (($priorityMap[$level] ?? 0) >= 2) {
						$todayActions[] = [
							'level' => $level,
							'icon' => 'fa-wheat-awn',
							'title' => 'Pakan ' . ($item['batch'] ?? '-'),
							'detail' => data_get($item, 'status.recommendation', data_get($item, 'status.message', 'Periksa selisih konsumsi pakan.')),
							'hint' => ($item['kandang'] ?? '-') . ' • ' . ($item['fase'] ?? '-')
						];
					}
				}
				foreach ($mortalityAlerts as $item) {
					$level = data_get($item, 'status.level', 'info');
					if (($priorityMap[$level] ?? 0) >= 2) {
						$todayActions[] = [
							'level' => $level,
							'icon' => 'fa-skull-crossbones',
							'title' => 'Mortalitas ' . ($item['batch'] ?? '-'),
							'detail' => data_get($item, 'status.message', 'Cek peningkatan kematian dan lakukan investigasi cepat.'),
							'hint' => ($item['kandang'] ?? '-') . ' • ' . ($item['fase'] ?? '-')
						];
					}
				}
				usort($todayActions, function ($a, $b) use ($priorityMap) {
					return ($priorityMap[$b['level']] ?? 0) <=> ($priorityMap[$a['level']] ?? 0);
				});
				$todayActions = array_slice($todayActions, 0, 5);
			@endphp
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
			<div class="section-card">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Apa yang harus dilakukan hari ini</h2>
						<small class="text-muted">Prioritas tindakan harian berdasarkan alert config-mode.</small>
						@if(count($todayActions))
							@php
								$criticalCount = collect($todayActions)->where('level', 'critical')->count();
								$warningCount = collect($todayActions)->where('level', 'warning')->count();
							@endphp
							<div class="mt-2">
								<small class="text-muted">{{ count($todayActions) }} tindakan prioritas: {{ $criticalCount }} Critical, {{ $warningCount }} Warning</small>
							</div>
						@endif
					</div>
					<button type="button" class="toggle-btn" data-target="daily-actions-content" title="Toggle visibility">
						<i class="fa-solid fa-chevron-up"></i>
					</button>
				</div>
				<div id="daily-actions-content" class="mt-3 collapsible-content">
					@if(count($todayActions))
						<ul class="list-unstyled mb-0" style="display:flex; flex-direction:column; gap:10px;">
							@foreach($todayActions as $act)
								<li class="action-item {{ $act['level'] }}" style="border:1px solid #e2e8f0; border-radius:12px; padding:12px 14px; background:#f8fafc;">
									<div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
										<div class="d-flex align-items-start gap-2">
											<i class="fa-solid {{ $act['icon'] }}" style="color:#64748b; margin-top:2px;"></i>
											<div>
												<div class="fw-semibold" style="color:#0f172a;">{{ $act['title'] }}</div>
												<small class="text-muted">{{ $act['hint'] }}</small>
											</div>
										</div>
										<span class="status-pill {{ $act['level'] }} text-uppercase">{{ $displayLevel($act['level']) }}</span>
									</div>
									<p class="mb-0 mt-2" style="color:#475569;">{{ $act['detail'] }}</p>
								</li>
							@endforeach
						</ul>
					@else
						<div class="empty-state" style="margin:0;">
							<i class="fa-regular fa-circle-check" style="font-size:2rem;"></i>
							<p class="mb-0 mt-2">Tidak ada tindakan prioritas untuk hari ini.</p>
						</div>
					@endif
				</div>
			</div>
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
						<h2 class="card-title mb-0">Diagram Ringkasan Alert</h2>
						<small class="text-muted">Telur, pakan, dan kematian dalam satu grafik.</small>
					</div>
					<div class="text-end">
						<span class="status-pill info">Bar Chart</span>
						<div class="text-muted" style="font-size:0.8rem;">Sumber dari ringkasan terkini</div>
					</div>
				</div>
				<div class="chart-wrap mt-3">
					<canvas id="dss-summary-chart" data-summary='@json($summaryChartPayload)'></canvas>
				</div>
			</div>

			<div class="section-card chart-card">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Tren Produksi & Status</h2>
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
										<th>Rekomendasi</th>
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
												<span class="status-pill {{ $statusLevel }}">{{ $statusStage ?? $displayLevel($statusLevel) }}</span>
											</td>
											<td>
												<p class="mb-0" style="font-size:0.85rem; color:#475569;">{{ $statusMessage }}</p>
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
												<span class="status-pill {{ $statusLevel }}">{{ $displayLevel($statusLevel) }}</span>
											</td>
											<td>
												<p class="mb-0" style="font-size:0.85rem; color:#475569;">{{ data_get($item, 'status.recommendation', $statusMessage ?? '-') }}</p>
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
										<th>Rekomendasi</th>
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
												<span class="status-pill {{ $item['status']['level'] }}">{{ $displayLevel($item['status']['level']) }}</span>
											</td>
											<td>
												<p class="mb-0" style="font-size:0.85rem; color:#475569;">{{ $item['status']['message'] }}</p>
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

			<div class="section-card mt-4">
				<div class="card-head">
					<div>
						<h2 class="card-title mb-0">Simulasi Produksi</h2>
						<small class="text-muted">Isi cepat, lihat prediksi telur, biaya, harga, lalu putuskan.</small>
					</div>
					<span class="status-pill info">Simulasi</span>
				</div>

				<form method="GET" action="{{ url()->current() }}" class="mt-3 sim-section-highlight">
					<input type="hidden" name="mode" value="ml">
					<div class="sim-grid">
						<div>
							<label class="form-label">Umur Puyuh (hari)</label>
							<input type="number" min="0" step="1" name="umur_hari" value="{{ $simInput['umur_hari'] ?? '' }}" placeholder="Masukan Umur Puyuh" class="form-control" aria-label="Umur hari">
							<small class="text-muted">Umur puyuh saat ini (dalam hari)</small>
						</div>
						<div>
							<label class="form-label">Berat Puyuh (gram)</label>
							<input type="number" min="0" step="1" name="berat_badan_g" value="{{ $simInput['berat_badan_g'] ?? '' }}" placeholder="Masukan Berat Puyuh" class="form-control" aria-label="Berat badan">
							<small class="text-muted">Berat rata-rata puyuh saat ini</small>
						</div>
						<div>
							<label class="form-label">Pakan (gram/hari)</label>
							<input type="number" min="0" step="0.1" name="pakan_g_per_hari" value="{{ $simInput['pakan_g_per_hari'] ?? '' }}" placeholder="Masukan Pakan" class="form-control" aria-label="Konsumsi pakan">
							<small class="text-muted">Jumlah pakan yang dimakan per ekor setiap hari</small>
						</div>
						<div>
							<label class="form-label">Protein (%)</label>
							<input type="number" min="0" step="0.1" name="protein_persen" value="{{ $simInput['protein_persen'] ?? '' }}" placeholder="Masukan Protein" class="form-control" aria-label="Protein ransum">
							<small class="text-muted">Kadar protein dalam pakan</small>
						</div>
						<div>
							<label class="form-label">Harga Pakan / Kg</label>
							<input type="number" min="0" step="1" name="harga_pakan_per_kg" value="{{ $simInput['harga_pakan_per_kg'] ?? '' }}" placeholder="Masukan Harga Pakan" class="form-control" aria-label="Harga pakan">
							<small class="text-muted">Harga pakan per kilogram</small>
						</div>
						<div>
							<label class="form-label">Target Margin (%)</label>
							<input type="number" min="0" step="0.1" name="margin_persen" value="{{ $simInput['margin_persen'] ?? '' }}" placeholder="Masukan Target Margin" class="form-control" aria-label="Margin target">
							<small class="text-muted">Keuntungan yang diinginkan %</small>
						</div>
					</div>
					<div class="d-flex gap-2 mt-3 justify-content-end flex-wrap">
						<button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
							<i class="fa-solid fa-calculator"></i>
							Hitung Simulasi
						</button>
						<a href="{{ url()->current() }}?mode=ml" class="btn btn-outline-secondary d-flex align-items-center gap-2">
							<i class="fa-solid fa-rotate-left"></i>
							Reset
						</a>
					</div>
				</form>

				@php $simPred = $simulation['prediction'] ?? null; @endphp
				<div class="sim-result-grid mt-4">
					<div class="ml-card eggs">
						<h3><i class="fa-solid fa-egg"></i> Prediksi Telur</h3>
						@if($simPred)
							<div class="ml-value">{{ number_format((float) $simPred['telur_per_hari']) }}<small> butir / hari</small></div>
						@else
							<div class="ml-value empty">—</div>
							<small class="text-muted">Isi form untuk melihat prediksi Telur</small>
						@endif
					</div>
					<div class="ml-card cost">
						<h3><i class="fa-solid fa-calculator"></i> Biaya Produksi</h3>
						@if($simPred)
							<div class="ml-value">{{ number_format((float) $simPred['biaya_per_butir'], 0) }}<small> rupiah / butir</small></div>
						@else
							<div class="ml-value empty">—</div>
							<small class="text-muted">Isi form untuk melihat biaya</small>
						@endif
					</div>
					<div class="ml-card price">
						<h3><i class="fa-solid fa-tag"></i> Harga Jual Rekomendasi</h3>
						@if($simPred)
							<div class="ml-value">{{ number_format((float) $simPred['harga_rekomendasi'], 0) }}<small> rupiah / butir</small></div>
						@else
							<div class="ml-value empty">—</div>
							<small class="text-muted">Isi form untuk melihat rekomendasi harga</small>
						@endif
					</div>
				</div>

					<div class="mt-4 sim-section-highlight">
						<div class="chart-wrap" style="min-height:320px;">
							@if($simulation && data_get($simulation, 'chart.labels'))
								<div id="simChartApex" style="height:320px;"></div>
							@else
								<div class="empty-state chart-empty-state">
									<div class="chart-placeholder-icon">
										<i class="fa-solid fa-chart-bar"></i>
									</div>
									<h4>Grafik Keputusan</h4>
									<p>Isi form simulasi di atas untuk melihat prediksi produksi dan biaya dalam bentuk grafik interaktif.</p>
								</div>
							@endif
						</div>
					</div>

					@php
						$simInputDisplay = [
							'Umur' => $simInput['umur_hari'] ?? null,
							'Pakan' => $simInput['pakan_g_per_hari'] ?? null,
							'Protein' => $simInput['protein_persen'] ?? null,
							'Margin' => $simInput['margin_persen'] ?? null,
						];
						$simPredMarginPct = null;
						$statusMarginLevel = 'info';
						$statusBiayaLevel = 'info';
						$statusProduksiLevel = 'info';
						$systemStatusLabel = ($mlStatus === 'ready' && ($modelVersion ?? 'untrained') !== 'untrained') ? 'Ready' : (($mlStatus === 'ready') ? 'Untrained' : 'Not Ready');
						if($simPred) {
							$marginNom = ($simPred['harga_rekomendasi'] ?? 0) - ($simPred['biaya_per_butir'] ?? 0);
							$simPredMarginPct = ($simPred['biaya_per_butir'] ?? 0) > 0
								? ($marginNom / (float) $simPred['biaya_per_butir']) * 100
								: null;
							$statusMarginLevel = $simPredMarginPct === null
								? 'info'
								: ($simPredMarginPct >= 20 ? 'ok' : ($simPredMarginPct >= 10 ? 'warning' : 'critical'));
							$statusBiayaLevel = ($simPred['biaya_per_butir'] ?? 0) <= 350
								? 'ok'
								: (($simPred['biaya_per_butir'] ?? 0) <= 450 ? 'warning' : 'critical');
							$statusProduksiLevel = ($simPred['telur_per_hari'] ?? 0) >= 850
								? 'ok'
								: (($simPred['telur_per_hari'] ?? 0) >= 700 ? 'warning' : 'critical');
						}
						$actionLine = null;
						if($statusProduksiLevel === 'critical') {
							$actionLine = 'Produksi berada di bawah batas sehat. Pertimbangkan naikkan kualitas pakan dan evaluasi manajemen kandang.';
						} elseif($statusBiayaLevel === 'critical') {
							$actionLine = 'Biaya produksi terlalu tinggi. Cek efisiensi konsumsi pakan dan bandingkan harga pemasok.';
						} elseif($statusMarginLevel === 'critical') {
							$actionLine = 'Margin berisiko. Tekan biaya atau naikkan harga jual secara bertahap.';
						} elseif($statusProduksiLevel === 'warning') {
							$actionLine = 'Produksi mendekati batas waspada. Pastikan pakan dan lingkungan stabil.';
						} elseif($statusBiayaLevel === 'warning') {
							$actionLine = 'Biaya mendekati batas. Optimalkan feed conversion dan negosiasi harga pakan.';
						} elseif($statusMarginLevel === 'warning') {
							$actionLine = 'Margin masih tipis. Cari peluang efisiensi atau penyesuaian harga kecil.';
						} else {
							$actionLine = 'Kondisi produksi dan biaya dalam batas ideal. Pertahankan pola pakan dan perawatan saat ini.';
						}
					@endphp
					<div class="section-card mt-3">
						<h2 class="card-title mb-3"><i class="fa-solid fa-lightbulb text-warning"></i> Rekomendasi Tindakan</h2>
						@if($simPred)
							<div class="alert alert-info border-0 bg-light">
								<p class="mb-0">{{ $actionLine }}</p>
							</div>
						@else
							<div class="alert alert-secondary border-0">
								<p class="mb-0">Lakukan simulasi terlebih dahulu untuk mendapatkan rekomendasi tindakan yang sesuai dengan kondisi operasional.</p>
							</div>
						@endif
					</div>
					<div class="mt-4 sim-section-highlight">
						<div class="sim-ops-grid">
							<div class="sim-ops-box">
								<h6><i class="fa-solid fa-tachometer"></i> Status Operasional</h6>
								@if($simPred)
									<div class="pill-stack">
										<span class="status-pill {{ $statusProduksiLevel }}">Produksi: {{ $statusProduksiLevel === 'ok' ? 'Sehat' : ($statusProduksiLevel === 'warning' ? 'Waspada' : 'Rendah') }}</span>
										<span class="status-pill {{ $statusBiayaLevel }}">Biaya: {{ $statusBiayaLevel === 'ok' ? 'Terkendali' : ($statusBiayaLevel === 'warning' ? 'Waspada' : 'Tinggi') }}</span>
										<span class="status-pill {{ $statusMarginLevel }}">Margin: @if($simPredMarginPct === null) N/A @else {{ number_format($simPredMarginPct, 1) }}% @endif</span>
										<span class="status-pill {{ $statusClass }}">Status: {{ $systemStatusLabel }}</span>
										<small class="text-muted">Model: {{ $modelVersion ?? 'untrained' }} • Data: {{ strtoupper($dataSource ?? 'live') }}</small>
									</div>
								@else
									<p class="mb-0 text-muted">Status operasional belum tersedia. Jalankan simulasi untuk melihat indikator kondisi sistem berdasarkan prediksi ML.</p>
								@endif
							</div>
							<div class="sim-ops-box">
								<h6><i class="fa-solid fa-clipboard-list"></i> Catatan Simulasi</h6>
								@if($simPred)
									<p>Input: {{ $simInputDisplay['Umur'] ?? '—' }} hari, {{ $simInputDisplay['Pakan'] ?? '—' }} g, protein {{ $simInputDisplay['Protein'] ?? '—' }}%, margin {{ $simInputDisplay['Margin'] ?? '—' }}%</p>
									<p>Hasil: {{ number_format((float) $simPred['telur_per_hari']) }} butir ({{ $statusProduksiLevel === 'ok' ? 'Sehat' : ($statusProduksiLevel === 'warning' ? 'Waspada' : 'Rendah') }}), biaya {{ number_format((float) $simPred['biaya_per_butir'], 0) }} Rp/butir ({{ $statusBiayaLevel === 'ok' ? 'Terkendali' : ($statusBiayaLevel === 'warning' ? 'Waspada' : 'Tinggi') }}), harga {{ number_format((float) $simPred['harga_rekomendasi'], 0) }} Rp/butir (Margin {{ $simPredMarginPct === null ? 'N/A' : number_format($simPredMarginPct, 1) . '%' }})</p>
								@else
									<p class="mb-0 text-muted">Belum ada catatan. Jalankan simulasi dulu.</p>
								@endif
							</div>
						</div>
					</div>
					<div class="section-card mt-3">
						<h2 class="card-title mb-3"><i class="fa-solid fa-rocket"></i> Skenario Cepat</h2>
						<div class="sim-quick-actions">
							<button type="button" class="sim-quick-action" data-field="pakan_g_per_hari" data-delta="5"><i class="fa-solid fa-plus"></i> Naikkan pakan +5g</button>
							<button type="button" class="sim-quick-action" data-field="margin_persen" data-delta="-5"><i class="fa-solid fa-minus"></i> Turunkan margin -5%</button>
							<button type="button" class="sim-quick-action" data-payload='{"pakan_g_per_hari":-3}'><i class="fa-solid fa-leaf"></i> Simulasi pakan hemat</button>
							<button type="button" class="sim-quick-action" data-payload='{"pakan_g_per_hari":5,"protein_persen":1}'><i class="fa-solid fa-chart-line"></i> Simulasi produksi maksimal</button>
						</div>
					</div>
			</div>
			@endif
		</div>
	</div>
</div>
@endsection

@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const hasChart = typeof Chart !== 'undefined';

			// ApexChart for simulation
			@if($simulation)
			const simLabels = @json($simulation['chart']['labels'] ?? []);
			const simEggs = @json($simulation['chart']['eggs'] ?? []);
			const simCost = @json($simulation['chart']['cost'] ?? []);
				const simApexEl = document.querySelector('#simChartApex');
				if (simApexEl && simLabels.length) {
					const apexOptions = {
						chart: { type: 'line', height: 320, toolbar: { show: false }, width: '100%', margin: { left: 5, right: 5, top: 5, bottom: 5 }, offsetX: 0, offsetY: 0, animations: { enabled: false } },
						theme: { mode: 'light' },
						plotOptions: {
							bar: {
								horizontal: false,
								columnWidth: '48%',
								borderRadius: 6,
							}
						},
						dataLabels: { enabled: false },
						stroke: { width: [0, 3], curve: 'smooth' },
						colors: ['#2563eb', '#f97316'],
						series: [
							{ name: 'Produksi telur', type: 'column', data: simEggs },
							{ name: 'Biaya (Rp/butir)', type: 'line', data: simCost },
						],
						xaxis: { categories: simLabels, labels: { rotate: 0 } },
						yaxis: [
							{ title: { text: 'Telur (butir)' } },
							{ opposite: true, title: { text: 'Biaya (Rp/butir)' } },
						],
						tooltip: { shared: true, intersect: false },
						legend: { position: 'top' },
						responsive: [{
							breakpoint: 480,
							options: {
								chart: { width: '100%' },
								legend: { position: 'bottom' }
							}
						}]
					};
					window.simChart = new ApexCharts(simApexEl, apexOptions);
					// Delay render to ensure container is ready
					setTimeout(() => {
						window.simChart.render();
						// Force resize after render
						setTimeout(() => window.simChart.resize(), 50);
					}, 100);

					// Update chart on resize and sidebar toggle
					window.addEventListener('resize', () => {
						if (window.simChart) {
							window.simChart.resize();
						}
					});

					window.addEventListener('load', () => {
						if (window.simChart) {
							window.simChart.resize();
						}
					});

					// Observe sidebar changes (assuming sidebar toggle changes body class or container)
					const observer = new MutationObserver(() => {
						if (window.simChart) {
							setTimeout(() => window.simChart.resize(), 300);
						}
					});
					observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
				}
			@endif

			// Summary bar chart
			if (hasChart) {
				const summaryCanvas = document.getElementById('dss-summary-chart');
				if (summaryCanvas) {
					let summaryData = [];
					try {
						summaryData = JSON.parse(summaryCanvas.dataset.summary || '[]');
					} catch (error) {
						summaryData = [];
					}
					if (Array.isArray(summaryData) && summaryData.length) {
						const summaryLabels = summaryData.map((item) => item.label || '-');
						const alertValues = summaryData.map((item) => Number(item.alerts || 0));
						const totalValues = summaryData.map((item) => Number(item.total || 0));
						const ctxSummary = summaryCanvas.getContext('2d');
						new Chart(ctxSummary, {
							type: 'bar',
							data: {
								labels: summaryLabels,
								datasets: [
									{
										label: 'Alert',
										data: alertValues,
										backgroundColor: '#ef4444',
										borderColor: '#dc2626',
										borderWidth: 1,
									},
									{
										label: 'Total Data',
										data: totalValues,
										backgroundColor: '#3b82f6',
										borderColor: '#1d4ed8',
										borderWidth: 1,
									},
								],
							},
							options: {
								maintainAspectRatio: false,
								responsive: true,
								scales: {
									y: {
										beginAtZero: true,
										precision: 0,
									},
									x: {
										grid: {
											display: false,
										},
									},
								},
								plugins: {
									legend: {
										position: 'top',
									},
									tooltip: {
										callbacks: {
											label(context) {
												return `${context.dataset.label}: ${context.formattedValue}`;
											},
										},
									},
								},
							},
						});
					}
				}

				const trendCanvas = document.getElementById('dss-trend-chart');
				if (trendCanvas) {
					let labels = [];
					let series = [];
					try {
						labels = JSON.parse(trendCanvas.dataset.labels || '[]');
						series = JSON.parse(trendCanvas.dataset.series || '[]');
					} catch (error) {
						labels = [];
						series = [];
					}
					if (Array.isArray(labels) && labels.length && Array.isArray(series) && series.length) {
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
								interaction: { mode: 'index', intersect: false },
								scales: {
									y: {
										beginAtZero: true,
										grid: { color: 'rgba(226, 232, 240, 0.6)' },
										ticks: { precision: 0 },
									},
									x: { grid: { color: 'rgba(248, 250, 252, 0.8)' } },
								},
								plugins: {
									legend: { position: 'bottom', labels: { usePointStyle: true } },
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
					}
				}
			}

			// Quick scenario buttons: tweak inputs then submit
			const simForm = document.querySelector('.sim-section-highlight form') || document.querySelector('form.sim-section-highlight');
			const actionButtons = document.querySelectorAll('.sim-quick-action');
			actionButtons.forEach((btn) => {
				btn.addEventListener('click', () => {
					if (!simForm) return;
					if (btn.dataset.reset) {
						simForm.reset();
						simForm.submit();
						return;
					}
					const payload = btn.dataset.payload ? JSON.parse(btn.dataset.payload) : null;
					if (payload && typeof payload === 'object') {
						Object.entries(payload).forEach(([field, delta]) => {
							const input = simForm.querySelector(`[name="${field}"]`);
							if (!input) return;
							const current = Number(input.value || 0);
							const next = Math.max(0, current + Number(delta || 0));
							input.value = Number.isFinite(next) ? next : current;
						});
						simForm.submit();
						return;
					}
					const field = btn.dataset.field;
					const delta = Number(btn.dataset.delta || 0);
					if (!field) return;
					const input = simForm.querySelector(`[name="${field}"]`);
					if (!input) return;
					const current = Number(input.value || 0);
					const next = Math.max(0, current + delta);
					input.value = Number.isFinite(next) ? next : current;
					simForm.submit();
				});
			});

			// Collapsible sections
			const toggleButtons = document.querySelectorAll('.toggle-btn');
			toggleButtons.forEach(btn => {
				btn.addEventListener('click', () => {
					const targetId = btn.dataset.target;
					const target = document.getElementById(targetId);
					if (target) {
						const isCollapsed = target.classList.contains('collapsed');
						if (isCollapsed) {
							target.classList.remove('collapsed');
							btn.classList.remove('collapsed');
						} else {
							target.classList.add('collapsed');
							btn.classList.add('collapsed');
						}
					}
				});
			});
		});
	</script>
@endpush
