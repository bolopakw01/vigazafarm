@php
    $analyticsKey = $analyticsKey ?? ('analytics_' . \Illuminate\Support\Str::random(6));
    $dataset = collect($dataset ?? [])->map(function ($row) {
        if (is_array($row)) {
            return $row;
        }
        if ($row instanceof Illuminate\Contracts\Support\Arrayable) {
            return $row->toArray();
        }
        if (is_object($row)) {
            return (array) $row;
        }
        return [];
    });

    $chartLabels = array_merge([
        'telur' => 'Telur (butir)',
        'pakan' => 'Pakan (kg)',
        'vitamin' => 'Vitamin (L)',
        'kematian' => 'Kematian (ekor)',
        'penjualan' => 'Penjualan',
        'tray' => 'Tray'
    ], $chartLabels ?? []);

    $seriesDefinitions = collect($seriesDefinitions ?? [
        ['key' => 'telur', 'field' => 'telur', 'label' => $chartLabels['telur'] ?? 'Telur (butir)', 'color' => '#2563eb'],
        ['key' => 'pakan', 'field' => 'pakan', 'label' => $chartLabels['pakan'] ?? 'Pakan (kg)', 'color' => '#22c55e'],
        ['key' => 'kematian', 'field' => 'kematian', 'label' => $chartLabels['kematian'] ?? 'Kematian (ekor)', 'color' => '#ef4444'],
    ])->map(function ($definition) use ($chartLabels) {
        $key = $definition['key'] ?? $definition['field'] ?? null;
        return [
            'key' => $key,
            'field' => $definition['field'] ?? $key,
            'label' => $definition['label'] ?? ($key ? ($chartLabels[$key] ?? ucfirst(str_replace('_', ' ', $key))) : 'Series'),
            'color' => $definition['color'] ?? '#2563eb',
        ];
    })->filter(fn ($definition) => !empty($definition['key']) && !empty($definition['field']))->values();

    $activeSeries = collect($activeSeries ?? $seriesDefinitions->pluck('key'))->filter(function ($key) use ($seriesDefinitions) {
        return $seriesDefinitions->contains('key', $key);
    })->values()->all();

    $firstPoint = $dataset->first();
    $lastPoint = $dataset->last();
    $effectiveRange = $dateRange ?? [
        'start' => $firstPoint['display'] ?? null,
        'end' => $lastPoint['display'] ?? null,
    ];

    $chartId = $analyticsKey . '-chart';
    $chartPaneId = $analyticsKey . '-pane-grafik';
    $analysisPaneId = $analyticsKey . '-pane-analisis';

    $stats = collect($stats ?? []);
    $analysis = collect($analysis ?? []);

    $chartPayload = [
        'chartId' => $chartId,
        'categories' => $dataset->pluck('display')->values(),
        'seriesDefinitions' => $seriesDefinitions,
        'dataset' => $dataset,
        'activeSeries' => $activeSeries,
        'range' => $effectiveRange,
    ];
@endphp

@once
    @push('styles')
        <style>
            .analytics-card {
                border: none;
                border-radius: 1.25rem;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
                margin-bottom: 1.5rem;
            }

            .analytics-card .card-body {
                padding: 1.75rem;
            }

            .analytics-tabs .nav-link {
                border: none;
                border-bottom: 2px solid transparent;
                color: #94a3b8;
                font-weight: 600;
            }

            .analytics-tabs .nav-link.active {
                color: #0f172a;
                border-color: #2563eb;
                background: transparent;
            }

            .analytics-stat-card {
                background: #f8fafc;
                border-radius: 1rem;
                padding: 1rem 1.25rem;
                height: 100%;
                border: 1px solid rgba(148, 163, 184, 0.2);
            }

            .analytics-stat-label {
                font-size: 0.85rem;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .analytics-stat-value {
                font-size: 1.5rem;
                font-weight: 700;
                color: #0f172a;
            }

            .analytics-chart-area {
                min-height: 320px;
            }

            .analytics-chart-empty {
                min-height: 260px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #94a3b8;
                font-style: italic;
                background: #f8fafc;
                border-radius: 1rem;
            }

            .analysis-list {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .analysis-item {
                display: flex;
                gap: 1rem;
                padding: 0.75rem 1rem;
                border-radius: 0.85rem;
                border: 1px solid rgba(148, 163, 184, 0.3);
                background: #fff;
                align-items: flex-start;
            }

            .analysis-item i {
                font-size: 1.2rem;
                color: #2563eb;
                margin-top: 0.2rem;
            }

            .analysis-item .text-muted {
                font-size: 0.9rem;
            }

            .analytics-series-filter {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-bottom: 0.75rem;
            }

            .analytics-series-filter .badge-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                display: inline-block;
                flex-shrink: 0;
            }

            .dropdown-menu .dropdown-item .form-check {
                margin-bottom: 0;
            }

            .dropdown-menu .dropdown-item .form-check-label {
                cursor: pointer;
                width: 100%;
            }

            @media (max-width: 768px) {
                .analytics-card .card-body {
                    padding: 1.25rem;
                }

                .analytics-stat-card {
                    margin-bottom: 0.75rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('bolopa/plugin/apexcharts/apexcharts.min.js') }}"></script>
    @endpush
@endonce

<div class="card analytics-card" id="{{ $analyticsKey }}-card">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
            <div>
                <h5 class="mb-1">{{ $title ?? 'Grafik & Analisis' }}</h5>
                <p class="mb-0 text-muted small">{{ $subtitle ?? 'Visualisasi data Telur, Pakan, dan Kematian' }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if (($chartPayload['range']['start'] ?? null) && ($chartPayload['range']['end'] ?? null))
                    <span class="badge bg-light text-dark fw-semibold">
                        <i class="fa-regular fa-calendar me-1"></i>
                        {{ $chartPayload['range']['start'] }} — {{ $chartPayload['range']['end'] }}
                    </span>
                @endif
                @if($seriesDefinitions->count() > 1)
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="{{ $analyticsKey }}-filter-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <i class="fa-solid fa-filter me-1"></i> Pilih Grafik
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="{{ $analyticsKey }}-filter-dropdown">
                            @foreach ($seriesDefinitions as $definition)
                                @php
                                    $seriesId = $analyticsKey . '-series-' . $definition['key'];
                                    $isChecked = in_array($definition['key'], $activeSeries, true);
                                @endphp
                                <li>
                                    <div class="dropdown-item">
                                        <div class="form-check">
                                            <input class="form-check-input analytics-series-toggle" type="checkbox" value="{{ $definition['key'] }}" id="{{ $seriesId }}" {{ $isChecked ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex align-items-center" for="{{ $seriesId }}">
                                                <span class="badge-dot me-2" style="background-color: {{ $definition['color'] }}"></span>
                                                {{ $definition['label'] }}
                                            </label>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <ul class="nav nav-tabs analytics-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="{{ $chartPaneId }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $chartPaneId }}" type="button" role="tab" aria-controls="{{ $chartPaneId }}" aria-selected="true">
                    <i class="fa-solid fa-chart-line me-1"></i> Grafik
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="{{ $analysisPaneId }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $analysisPaneId }}" type="button" role="tab" aria-controls="{{ $analysisPaneId }}" aria-selected="false">
                    <i class="fa-solid fa-lightbulb me-1"></i> Analisis
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="{{ $chartPaneId }}" role="tabpanel" aria-labelledby="{{ $chartPaneId }}-tab">
                <div id="{{ $chartId }}" class="analytics-chart-area"></div>
                <div class="row g-3 mt-2">
                    @forelse ($stats as $stat)
                        <div class="col-md-4">
                            <div class="analytics-stat-card h-100">
                                <div class="analytics-stat-label text-uppercase">{{ $stat['label'] ?? '-' }}</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <div class="analytics-stat-value">{{ $stat['value'] ?? '0' }}</div>
                                    @if (!empty($stat['suffix']))
                                        <small class="text-muted">{{ $stat['suffix'] }}</small>
                                    @endif
                                </div>
                                @if (!empty($stat['meta']))
                                    <small class="text-muted">{{ $stat['meta'] }}</small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="analytics-chart-empty">Belum ada ringkasan statistik.</div>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="tab-pane fade" id="{{ $analysisPaneId }}" role="tabpanel" aria-labelledby="{{ $analysisPaneId }}-tab">
                <div class="analysis-list">
                    @forelse ($analysis as $item)
                        <div class="analysis-item">
                            <i class="fa-solid {{ $item['icon'] ?? 'fa-circle-info' }}"></i>
                            <div>
                                <p class="mb-1 fw-semibold">{{ $item['title'] ?? 'Catatan' }}</p>
                                <p class="mb-0 text-muted">{{ $item['text'] ?? '-' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="analytics-chart-empty">Belum ada data untuk dianalisis.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const payload = @json($chartPayload);

            function buildSeries(activeKeys) {
                const filteredDefs = payload.seriesDefinitions.filter(def => activeKeys.includes(def.key));
                if (!filteredDefs.length && payload.seriesDefinitions.length) {
                    filteredDefs.push(payload.seriesDefinitions[0]);
                }

                const colors = filteredDefs.map(def => def.color || '#2563eb');
                const series = filteredDefs.map(def => ({
                    name: def.label,
                    data: payload.dataset.map(row => {
                        const value = row?.[def.field] ?? 0;
                        const numeric = parseFloat(value);
                        return Number.isFinite(numeric) ? numeric : 0;
                    })
                }));

                return { series, colors };
            }

            function renderAnalyticsChart(activeKeys = payload.activeSeries || []) {
                const container = document.getElementById(payload.chartId);
                if (!container) {
                    return;
                }

                if (payload.categories.length === 0) {
                    container.innerHTML = '<div class="analytics-chart-empty">Belum ada data pencatatan.</div>';
                    return;
                }

                if (typeof ApexCharts === 'undefined') {
                    container.innerHTML = '<div class="analytics-chart-empty">Grafik membutuhkan ApexCharts.</div>';
                    return;
                }

                if (!window.produksiAnalyticsCharts) {
                    window.produksiAnalyticsCharts = {};
                }

                if (window.produksiAnalyticsCharts[payload.chartId]) {
                    window.produksiAnalyticsCharts[payload.chartId].destroy();
                }

                const { series, colors } = buildSeries(activeKeys);

                const options = {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: { show: false },
                        foreColor: '#6b7280'
                    },
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    },
                    dataLabels: { enabled: false },
                    colors,
                    series,
                    legend: {
                        show: true,
                        position: 'top'
                    },
                    grid: {
                        strokeDashArray: 4,
                        borderColor: 'rgba(148, 163, 184, 0.3)'
                    },
                    xaxis: {
                        categories: payload.categories,
                        labels: { rotate: -15 }
                    },
                    yaxis: {
                        labels: {
                            formatter: (val) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val)
                        }
                    },
                    tooltip: {
                        shared: true,
                        x: {
                            formatter: (value, opts) => payload.categories[opts.dataPointIndex] || value
                        },
                        y: {
                            formatter: (val) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val)
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    }
                };

                const chart = new ApexCharts(container, options);
                window.produksiAnalyticsCharts[payload.chartId] = chart;
                chart.render();
            }

            function initializeChart() {
                renderAnalyticsChart(payload.activeSeries || []);

                const toggles = document.querySelectorAll('#{{ $analyticsKey }}-card .analytics-series-toggle');
                if (toggles.length) {
                    toggles.forEach(toggle => {
                        toggle.addEventListener('change', () => {
                            const key = toggle.value;
                            const activeSet = new Set(payload.activeSeries || []);

                            if (toggle.checked) {
                                activeSet.add(key);
                            } else if (activeSet.size > 1) {
                                activeSet.delete(key);
                            } else {
                                // Prevent removing the last series
                                toggle.checked = true;
                                return;
                            }

                            payload.activeSeries = Array.from(activeSet);
                            renderAnalyticsChart(payload.activeSeries);
                        });
                    });
                }
            }

            if (document.readyState !== 'loading') {
                initializeChart();
            } else {
                document.addEventListener('DOMContentLoaded', initializeChart);
            }

            document.addEventListener('shown.bs.tab', function(event) {
                const target = event.target?.getAttribute('data-bs-target');
                if (target === '#{{ $chartPaneId }}') {
                    renderAnalyticsChart(payload.activeSeries || []);
                }
            });
        })();
    </script>
@endpush
