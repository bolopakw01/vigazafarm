@extends('admin.layouts.app')

@section('title', 'Decision Support')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Decision Support'],
    ];

    $feedInsights = $insights['feed'] ?? [];
    $stockAlerts = $insights['stock'] ?? [];
    $environmentAlerts = $insights['environment'] ?? [];
    $healthAlerts = $insights['health'] ?? [];
@endphp

@push('styles')
    <style>
        .dss-wrapper {
            margin-top: 16px;
        }
        .dss-shell {
            background: #ffffff;
            border-radius: 28px;
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
        .summary-card.feed .indicator { background: linear-gradient(135deg, #ea580c, #fb923c); }
        .summary-card.stock .indicator { background: linear-gradient(135deg, #16a34a, #86efac); }
        .summary-card.environment .indicator { background: linear-gradient(135deg, #2563eb, #93c5fd); }
        .summary-card.health .indicator { background: linear-gradient(135deg, #dc2626, #fca5a5); }
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
        .list-group-item { background: transparent; border: none; border-bottom: 1px solid #f1f5f9; }
        .list-group-item:last-child { border-bottom: none; }
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
                        <img src="{{ asset('bolopa/img/icon/line-md--monitor-screenshot.svg') }}" alt="DSS Icon">
                        Decision Support System
                    </h1>
                    <p class="subtitle mb-0">Insight terkurasi untuk membantu keputusan operasional harian.</p>
                </div>
                <div class="meta">
                    <small>Terakhir diperbarui</small>
                    {{ optional($lastUpdated)->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="summary-grid">
                @php
                    $summaryMeta = [
                        'feed' => ['label' => 'Konsumsi Pakan', 'icon' => 'fa-wheat-awn'],
                        'stock' => ['label' => 'Stok Pakan', 'icon' => 'fa-boxes-stacked'],
                        'environment' => ['label' => 'Lingkungan', 'icon' => 'fa-temperature-three-quarters'],
                        'health' => ['label' => 'Kesehatan', 'icon' => 'fa-heartbeat'],
                    ];
                @endphp
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

            <div class="row g-4 mt-1">
                <div class="col-12 col-lg-6">
                    <div class="section-card h-100">
                        <div class="card-head">
                            <div>
                                <h2 class="card-title mb-0">Alert Stok Pakan</h2>
                                <small class="text-muted">Prioritas restock & masa kadaluarsa.</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            @if(count($stockAlerts))
                                <div class="list-group list-group-flush">
                                    @foreach($stockAlerts as $alert)
                                        @php $level = $alert['status'] ?? 'info'; @endphp
                                        <div class="list-group-item d-flex flex-column gap-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-semibold">{{ $alert['nama'] }}</div>
                                                    <small class="text-muted">{{ number_format($alert['stok_kg'], 1) }} kg • {{ $alert['stok_karung'] ?? 0 }} karung</small>
                                                </div>
                                                <span class="status-pill {{ $level }}">{{ ucfirst($level) }}</span>
                                            </div>
                                            <p class="mb-1 text-muted" style="font-size:0.85rem;">{{ $alert['message'] }}</p>
                                            @if(!empty($alert['cover_days']))
                                                <small class="text-muted">Perkiraan cukup {{ $alert['cover_days'] }} hari</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fa-solid fa-warehouse" style="font-size:2rem;"></i>
                                    <p class="mb-0 mt-2">Data stok pakan tidak ditemukan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="section-card h-100">
                        <div class="card-head">
                            <div>
                                <h2 class="card-title mb-0">Monitoring Lingkungan</h2>
                                <small class="text-muted">Snapshot suhu & kelembaban per kandang.</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            @if(count($environmentAlerts))
                                <div class="list-group list-group-flush">
                                    @foreach($environmentAlerts as $record)
                                        @php $level = $record['status'] ?? 'info'; @endphp
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="fw-semibold">{{ $record['kandang'] }}</div>
                                                    <small class="text-muted">{{ $record['fase'] }} • {{ $record['waktu'] ?? '-' }}</small>
                                                </div>
                                                <span class="status-pill {{ $level }}">{{ ucfirst($level) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2" style="font-size:0.9rem;">
                                                <div>
                                                    <strong>Suhu:</strong> {{ number_format($record['suhu'], 1) }}°C
                                                    <div class="text-muted" style="font-size:0.8rem;">{{ data_get($record, 'temperature.message') }}</div>
                                                </div>
                                                <div>
                                                    <strong>Kelembaban:</strong> {{ number_format($record['kelembaban'], 1) }}%
                                                    <div class="text-muted" style="font-size:0.8rem;">{{ data_get($record, 'humidity.message') }}</div>
                                                </div>
                                            </div>
                                            @if(!empty($record['ventilasi']))
                                                <small class="text-muted">Ventilasi: {{ $record['ventilasi'] }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fa-solid fa-temperature-three-quarters" style="font-size:2rem;"></i>
                                    <p class="mb-0 mt-2">Belum ada pencatatan monitoring lingkungan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-card mt-4">
                <div class="card-head">
                    <div>
                        <h2 class="card-title mb-0">Alert Kesehatan / Mortalitas</h2>
                        <small class="text-muted">Mendeteksi lonjakan kematian dalam {{ config('dss.health.window_days') }} hari.</small>
                    </div>
                </div>
                <div class="mt-3">
                    @if(count($healthAlerts))
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Batch</th>
                                        <th>Kandang</th>
                                        <th>Mortalitas</th>
                                        <th>Status</th>
                                        <th>Rekomendasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($healthAlerts as $alert)
                                        @php $level = $alert['status'] ?? 'info'; @endphp
                                        <tr>
                                            <td>{{ $alert['batch'] }}</td>
                                            <td>{{ $alert['kandang'] }}</td>
                                            <td>
                                                <strong>{{ $alert['total_mati'] }}</strong> ekor
                                                <small class="text-muted d-block">{{ number_format($alert['mortalitas_pct'], 2) }}% dari populasi {{ number_format($alert['populasi']) }}</small>
                                            </td>
                                            <td>
                                                <span class="status-pill {{ $level }}">{{ ucfirst($level) }}</span>
                                                <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ $alert['message'] }}</p>
                                            </td>
                                            <td>
                                                <p class="mb-0" style="font-size:0.85rem; color:#475569;">{{ $alert['recommendation'] }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa-solid fa-heartbeat" style="font-size:2rem;"></i>
                            <p class="mb-0 mt-2">Tidak ada alert kesehatan dalam periode terbaru.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
