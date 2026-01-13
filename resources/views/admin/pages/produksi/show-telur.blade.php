@extends('admin.layouts.app')

@section('title', 'Detail Produksi Telur - ' . ($produksi->batch_label ?? 'Tanpa Kode Batch'))

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Produksi', 'link' => route('admin.produksi')],
        ['label' => 'Detail Produksi'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('bolopa/plugin/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-produksi.css') }}">
@endpush

@section('content')
    @php
        $formatNumber = fn ($value, $decimals = 0) => number_format((float) ($value ?? 0), $decimals, ',', '.');
        $formatLargeNumber = function ($value, $allowDecimals = true) {
            $value = (float) ($value ?? 0);
            $precision = $allowDecimals ? 1 : 0;

            if ($value >= 1000000000) {
                $scaled = $allowDecimals
                    ? round($value / 1000000000, 1)
                    : round($value / 1000000000);
                return number_format($scaled, $precision, ',', '.') . 'M';
            }

            if ($value >= 1000000) {
                $scaled = $allowDecimals
                    ? round($value / 1000000, 1)
                    : round($value / 1000000);
                return number_format($scaled, $precision, ',', '.') . 'Jt';
            }

            if ($value >= 1000) {
                $scaled = $allowDecimals
                    ? round($value / 1000, 1)
                    : round($value / 1000);
                return number_format($scaled, $precision, ',', '.') . 'Rb';
            }

            return number_format($value, 0, ',', '.');
        };

        $startDate = $produksi->tanggal_mulai ?? $produksi->tanggal ?? optional($produksi->pembesaran)->tanggal_siap;
        $startDateFormatted = $startDate ? \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y') : '-';
        $endDate = $produksi->tanggal_akhir;
        $endDateFormatted = $endDate ? \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y') : '-';

        $kandangName = $produksi->kandang->nama_kandang ?? 'nama kandang';
        $batchCode = $produksi->batch_label ?? 'Tanpa Kode Batch';
        $initialPopulation = $produksi->jumlah_indukan ?? (($produksi->jumlah_jantan ?? 0) + ($produksi->jumlah_betina ?? 0));

        $totalTelur = $summary['total_telur'] ?? 0;
        $eggsPerTray = $summary['eggs_per_tray'] ?? 100;
        $totalTray = $summary['total_tray'] ?? ($eggsPerTray > 0 ? $totalTelur / $eggsPerTray : 0);
        $totalPendapatan = $summary['total_pendapatan'] ?? 0;
        $totalTelurRusak = $summary['total_telur_rusak'] ?? 0;
        $sisaTelur = $summary['sisa_telur'] ?? 0;

        $asalProduksiLabel = null;
        if (($produksi->jenis_input ?? '') === 'dari_produksi' && $produksi->produksi_sumber_id) {
            $asalProduksiLabel = optional($produksi->produksiSumber)->batch_label
                ?? ('#' . $produksi->produksi_sumber_id);
        }

        $trayEntries = ($laporanHarian ?? collect())
            ->filter(fn ($item) => ($item->produksi_telur ?? 0) > 0 && !empty($item->nama_tray))
            ->filter(fn ($item) => !$soldTrayIds->contains($item->id))
            ->map(function ($item) use ($eggsPerTray, $soldTrayIds) {
                $trayEstimate = $eggsPerTray > 0 ? ($item->produksi_telur / $eggsPerTray) : 0;

                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal
                        ? $item->tanggal->locale('id')->translatedFormat('d M Y')
                        : '-',
                    'nama_tray' => $item->nama_tray,
                    'jumlah_telur' => (int) ($item->produksi_telur ?? 0),
                    'estimasi_tray' => $trayEstimate,
                    'keterangan_tray' => $item->keterangan_tray,
                    'dibuat_pada' => $item->dibuat_pada
                        ? $item->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A')
                        : '—',
                    'diperbarui_pada' => $item->diperbarui_pada
                        ? $item->diperbarui_pada->locale('id')->format('d/m/Y, g:i:s A')
                        : '—',
                    'is_sold' => $soldTrayIds->contains($item->id),
                ];
            })
            ->values();

        $telurAnalyticsSeries = collect($laporanHarian ?? [])
            ->filter(fn ($laporan) => $laporan->tanggal)
            ->sortBy('tanggal')
            ->groupBy(fn ($laporan) => $laporan->tanggal->format('Y-m-d'))
            ->map(function ($items) use ($eggsPerTray) {
                $first = $items->first();
                $dateValue = optional($first)->tanggal;
                $displayDate = $dateValue ? $dateValue->locale('id')->translatedFormat('d M Y') : '-';
                $telurHarian = (float) $items->sum('produksi_telur');
                $trayEstimate = $eggsPerTray > 0 ? $telurHarian / $eggsPerTray : 0;
                $penjualanTelur = (float) $items->sum('penjualan_telur_butir');

                return [
                    'date' => $dateValue ? $dateValue->format('Y-m-d') : null,
                    'display' => $displayDate,
                    'telur' => $telurHarian,
                    'tray' => $trayEstimate,
                    'penjualan' => $penjualanTelur,
                ];
            })
            ->filter(fn ($row) => $row['date'])
            ->values();

        $totalTelurDicatat = $telurAnalyticsSeries->sum('telur');
        $totalTrayDicatat = $telurAnalyticsSeries->sum('tray');
        $totalPenjualanDicatat = $telurAnalyticsSeries->sum('penjualan');
        $avgTelurDicatat = $telurAnalyticsSeries->avg('telur') ?? 0;
        $avgTrayDicatat = $telurAnalyticsSeries->avg('tray') ?? 0;
        $avgPenjualanDicatat = $telurAnalyticsSeries->avg('penjualan') ?? 0;
        $peakTelurPoint = $telurAnalyticsSeries->sortByDesc('telur')->first();
        $peakTrayPoint = $telurAnalyticsSeries->sortByDesc('tray')->first();
        $peakPenjualanPoint = $telurAnalyticsSeries->sortByDesc('penjualan')->first();
        $peakTelurValue = is_array($peakTelurPoint) ? ($peakTelurPoint['telur'] ?? 0) : 0;
        $peakTelurLabel = is_array($peakTelurPoint) ? ($peakTelurPoint['display'] ?? '-') : '-';
        $peakTrayValue = is_array($peakTrayPoint) ? ($peakTrayPoint['tray'] ?? 0) : 0;
        $peakTrayLabel = is_array($peakTrayPoint) ? ($peakTrayPoint['display'] ?? '-') : '-';
        $peakPenjualanValue = is_array($peakPenjualanPoint) ? ($peakPenjualanPoint['penjualan'] ?? 0) : 0;
        $peakPenjualanLabel = is_array($peakPenjualanPoint) ? ($peakPenjualanPoint['display'] ?? '-') : '-';

        $telurAnalyticsStats = [
            [
                'label' => 'Total Telur',
                'value' => $formatNumber($totalTelurDicatat),
                'suffix' => 'butir',
                'meta' => $telurAnalyticsSeries->count() ? 'Rata-rata ' . $formatNumber($avgTelurDicatat, 0) . ' butir/hari' : null,
            ],
            [
                'label' => 'Estimasi Tray',
                'value' => $formatNumber($totalTrayDicatat, 2),
                'suffix' => 'tray',
                'meta' => $telurAnalyticsSeries->count() ? 'Rata-rata ' . $formatNumber($avgTrayDicatat, 2) . ' tray/hari' : null,
            ],
            [
                'label' => 'Penjualan Telur',
                'value' => $formatNumber($totalPenjualanDicatat),
                'suffix' => 'butir',
                'meta' => $totalPendapatan > 0 ? 'Pendapatan Rp ' . $formatNumber($totalPendapatan, 0) : null,
            ],
        ];

        $telurAnalysisNotes = [
            [
                'icon' => 'fa-egg',
                'title' => 'Produksi Telur',
                'text' => $totalTelurDicatat > 0
                    ? 'Rata-rata ' . $formatNumber($avgTelurDicatat, 0) . ' butir/hari; puncak '
                        . $formatNumber($peakTelurValue, 0) . ' butir (' . $peakTelurLabel . ').'
                    : 'Belum ada pencatatan telur yang dapat dianalisis.',
            ],
            [
                'icon' => 'fa-layer-group',
                'title' => 'Konversi Tray',
                'text' => $totalTrayDicatat > 0
                    ? 'Estimasi ' . $formatNumber($totalTrayDicatat, 2) . ' tray terbentuk; puncak '
                        . $formatNumber($peakTrayValue, 2) . ' tray (' . $peakTrayLabel . ').'
                    : 'Belum ada data tray yang dapat dianalisis.',
            ],
            [
                'icon' => 'fa-cash-register',
                'title' => 'Penjualan Telur',
                'text' => $totalPenjualanDicatat > 0
                    ? 'Rata-rata ' . $formatNumber($avgPenjualanDicatat, 0) . ' butir terjual/hari; puncak '
                        . $formatNumber($peakPenjualanValue, 0) . ' butir (' . $peakPenjualanLabel . ').'
                    : 'Belum ada catatan penjualan telur.',
            ],
        ];

        if ($totalTelurRusak > 0) {
            $telurAnalysisNotes[] = [
                'icon' => 'fa-triangle-exclamation',
                'title' => 'Telur Rusak',
                'text' => 'Total ' . $formatNumber($totalTelurRusak) . ' butir perlu dipilah sebelum penjualan.',
            ];
        }

        $firstTelurAnalyticsPoint = $telurAnalyticsSeries->first();
        $lastTelurAnalyticsPoint = $telurAnalyticsSeries->last();
        $telurAnalyticsRange = [
            'start' => is_array($firstTelurAnalyticsPoint) ? ($firstTelurAnalyticsPoint['display'] ?? null) : null,
            'end' => is_array($lastTelurAnalyticsPoint) ? ($lastTelurAnalyticsPoint['display'] ?? null) : null,
        ];
    @endphp

    <div class="container">
        <div class="page-wrapper">
            <div class="card pu_header mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="pu_icon">
                                <i class="fa-solid fa-egg" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                    <span class="ref-badge hide-on-narrow">
                                        <i class="fa-solid fa-tags"></i> {{ $batchCode }}
                                    </span>
                                    <span class="badge bg-success rounded-pill hide-on-narrow">{{ ucfirst($produksi->status ?? 'aktif') }}</span>
                                </div>
                                <h5 class="mb-0">
                                    Produksi Telur
                                    <small class="d-block">#{{ $kandangName }}</small>
                                </h5>

                                <div class="mt-2 d-flex flex-wrap gap-3 align-items-center">
                                    <div class="text-muted small hide-on-narrow">
                                        <i class="fa-regular fa-calendar-days me-1"></i>Mulai:
                                        <span class="fw-semibold ms-1">{{ $startDateFormatted }}</span>
                                    </div>
                                    <div class="text-muted small hide-on-narrow">
                                        <i class="fa-solid fa-clock me-1" aria-hidden="true"></i>Selesai:
                                        <span class="fw-semibold ms-1">{{ $endDateFormatted }}</span>
                                    </div>
                                    @if ($asalProduksiLabel)
                                        <div class="text-muted small hide-on-narrow">
                                            <i class="fa-solid fa-diagram-project me-1" aria-hidden="true"></i>Asal:
                                            <span class="fw-semibold ms-1">{{ $asalProduksiLabel }}</span>
                                        </div>
                                    @else
                                        <div class="text-muted small hide-on-narrow">
                                            <i class="fa-solid fa-egg me-1" aria-hidden="true"></i>Total Awal Telur:
                                            <span id="populasi-awal" class="fw-semibold ms-1">{{ $formatNumber($summary['total_telur_awal']) }} butir</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ url()->previous() }}" class="back-btn" aria-label="Kembali">
                                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                <span class="back-label">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kai-cards mb-4">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-indigo" id="kai-telur-card">
                            <div>
                                <div class="value" id="kai-telur-value">{{ $formatNumber($sisaTelur) }}<small style="font-size:0.45em; opacity:0.7;">/{{ $formatNumber($summary['total_telur_awal']) }}</small></div>
                                <div class="label" id="kai-telur-label">Sisa Telur (butir)</div>
                            </div>
                            <i class="fa-solid fa-egg icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-teal" id="kai-tray-card">
                            <div>
                                <div class="value" id="kai-tray-value">{!! $formatLargeNumber($totalTray) !!}</div>
                                <div class="label" id="kai-tray-label">Total Tray ({{ $eggsPerTray }} butir/tray)</div>
                            </div>
                            <i class="fa-solid fa-layer-group icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-red" id="kai-rusak-card">
                            <div>
                                <div class="value" id="kai-rusak-value">{{ $formatNumber($totalTelurRusak) }}</div>
                                <div class="label" id="kai-rusak-label">Total Telur Rusak</div>
                            </div>
                            <i class="fa-solid fa-triangle-exclamation icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-green" id="kai-pendapatan-card">
                            <div>
                                <div class="value" id="kai-pendapatan-value">Rp {!! $formatLargeNumber($totalPendapatan, false) !!}</div>
                                <div class="label" id="kai-pendapatan-label">Total Pendapatan</div>
                            </div>
                            <i class="fa-solid fa-coins icon-faint"></i>
                        </div>
                    </div>
                </div>
            </div>

            @include('admin.pages.produksi.partials.show-form.daily-report-form', [
                'produksi' => $produksi,
                'defaultTanggal' => old(
                    'tanggal',
                    optional($todayLaporan?->tanggal)->format('Y-m-d') ?? optional($latestLaporan?->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d')
                ),
                'defaultProduksiTelur' => old('produksi_telur', optional($todayLaporan)->produksi_telur),
                'defaultPenjualanTelur' => old('penjualan_telur_butir', optional($todayLaporan)->penjualan_telur_butir),
                'defaultSisaTelur' => old('sisa_telur', optional($todayLaporan)->sisa_telur),
                'defaultSisaTrayBal' => old('sisa_tray_bal', optional($todayLaporan)->sisa_tray_bal),
                'defaultSisaTrayLembar' => old('sisa_tray_lembar', optional($todayLaporan)->sisa_tray_lembar),
                'defaultKonsumsiPakan' => old('konsumsi_pakan_kg', optional($todayLaporan)->konsumsi_pakan_kg),
                'defaultSisaPakan' => old('sisa_pakan_kg', optional($todayLaporan)->sisa_pakan_kg),
                'defaultHargaPakan' => old('harga_pakan_per_kg', optional($todayLaporan)->harga_pakan_per_kg),
                'defaultVitaminTerpakai' => old('vitamin_terpakai', optional($todayLaporan)->vitamin_terpakai),
                'defaultSisaVitamin' => old('sisa_vitamin_liter', optional($todayLaporan)->sisa_vitamin_liter),
                'defaultHargaVitamin' => old('harga_vitamin_per_liter', optional($todayLaporan)->harga_vitamin_per_liter),
                'defaultJumlahKematian' => old('jumlah_kematian', optional($todayLaporan)->jumlah_kematian),
                'defaultPenjualanPuyuh' => old('penjualan_puyuh_ekor', optional($todayLaporan)->penjualan_puyuh_ekor),
                'defaultJenisKelaminPenjualan' => old('jenis_kelamin_penjualan', optional($todayLaporan)->jenis_kelamin_penjualan),
                'defaultCatatan' => old('catatan_kejadian', optional($todayLaporan)->catatan_kejadian),
                'defaultHargaPerButir' => old(
                    'harga_penjualan',
                    optional($todayLaporan)->harga_per_butir ?? $produksi->harga_per_pcs ?? null
                ),
                'trayEntries' => $trayEntries,
                'eggsPerTray' => $eggsPerTray,
                'tabVariant' => 'telur',
                'feedOptions' => $feedOptions,
                'vitaminOptions' => $vitaminOptions,
                'analyticsConfig' => [
                    'analyticsKey' => 'telur',
                    'title' => 'Grafik & Analisis Produksi Telur',
                    'subtitle' => 'Pantau Telur, Tray, dan Penjualan per hari',
                    'dataset' => $telurAnalyticsSeries,
                    'stats' => $telurAnalyticsStats,
                    'analysis' => $telurAnalysisNotes,
                    'dateRange' => $telurAnalyticsRange,
                    'seriesDefinitions' => [
                        ['key' => 'telur', 'field' => 'telur', 'label' => 'Telur (butir)', 'color' => '#2563eb'],
                        ['key' => 'tray', 'field' => 'tray', 'label' => 'Tray (est.)', 'color' => '#14b8a6'],
                        ['key' => 'penjualan', 'field' => 'penjualan', 'label' => 'Penjualan Telur (butir)', 'color' => '#f97316'],
                    ],
                    'activeSeries' => ['telur', 'tray', 'penjualan'],
                ],
            ])

            <div class="card mb-4" id="history-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">Rekap Pencatatan <small id="history-title" class="text-telur ms-2">(Telur)</small></h6>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary me-2" id="history-toggle"
                                type="button" data-bs-toggle="collapse" data-bs-target="#history-collapse"
                                aria-expanded="true" aria-controls="history-collapse"
                                data-hide-text="Sembunyikan" data-show-text="Tampilkan">
                                Sembunyikan
                            </button>

                            @if ($historyClearRoute)
                                <form action="{{ route('admin.produksi.laporan.clear', $produksi) }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" id="history-clear">Bersihkan</button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="history-clear" disabled>Bersihkan</button>
                            @endif
                        </div>
                    </div>

                    <div id="history-collapse" class="collapse show">
                        <div id="history-content" class="mt-3">
                            @php
                                $trayHistoryCollection = $trayHistories ?? collect();
                                $normalizeTrayName = function ($value) {
                                    if ($value === null) {
                                        return null;
                                    }
                                    $trimmed = trim($value);
                                    if ($trimmed === '') {
                                        return null;
                                    }
                                    return function_exists('mb_strtolower') ? mb_strtolower($trimmed) : strtolower($trimmed);
                                };
                                $visibleLaporan = $laporanHarian->filter(fn ($item) => $item->tampilkan_di_histori !== false);
                            @endphp
                            @if ($laporanHarian->isEmpty() && $trayHistoryCollection->isEmpty())
                                <div class="text-muted small"><i class="fa-solid fa-clipboard-list me-1"></i>Belum ada pencatatan.</div>
                            @elseif ($visibleLaporan->isEmpty() && $trayHistoryCollection->isEmpty())
                                <div class="text-muted small"><i class="fa-solid fa-eye-slash me-1"></i>Data pernah dicatat namun disembunyikan. Input ulang untuk menampilkan kembali.</div>
                            @else
                                <div class="list-timeline">
                                    @foreach ($trayHistoryCollection as $trayHistory)
                                        @php
                                            $trayActionLabelMap = ['created' => 'Ditambahkan', 'updated' => 'Diedit', 'deleted' => 'Dihapus'];
                                            $actionLabel = $trayActionLabelMap[$trayHistory->action] ?? ucfirst($trayHistory->action);
                                            $trayHistoryTitle = ($trayHistory->nama_tray ?? 'Tray tanpa nama') . ' — ' . $actionLabel;
                                            $trayHistoryDate = optional($trayHistory->tanggal)->locale('id')->translatedFormat('d F Y') ?? '-';
                                            $trayHistoryTimestamp = optional($trayHistory->created_at)->locale('id')->format('d/m/Y, g:i:s A') ?? '-';
                                            $trayHistoryAmount = $trayHistory->jumlah_telur ? number_format($trayHistory->jumlah_telur, 0, ',', '.') . ' butir' : null;
                                            $trayActionClass = $trayHistory->action === 'deleted' ? 'text-danger' : 'text-info';
                                            $trayHistoryUser = optional($trayHistory->pengguna)->nama_pengguna ?? '—';

                                            // Determine icon and color based on action
                                            $trayIcon = 'fa-layer-group'; // default
                                            $trayColorClass = 'entry-tray-default';
                                            $wasSoldTray = false;

                                            if ($trayHistory->laporan_harian_id !== null && isset($soldTrayIds)) {
                                                $wasSoldTray = $soldTrayIds->contains($trayHistory->laporan_harian_id);
                                            }

                                            if (!$wasSoldTray && isset($soldTrayNames)) {
                                                $normalizedCurrentName = $normalizeTrayName($trayHistory->nama_tray);
                                                $normalizedOldName = $normalizeTrayName($trayHistory->old_nama_tray);

                                                if ($normalizedCurrentName && $soldTrayNames->contains($normalizedCurrentName)) {
                                                    $wasSoldTray = true;
                                                } elseif ($normalizedOldName && $soldTrayNames->contains($normalizedOldName)) {
                                                    $wasSoldTray = true;
                                                }
                                            }
                                            
                                            if ($trayHistory->action === 'updated') {
                                                $trayIcon = 'fa-edit';
                                                $trayColorClass = 'entry-tray-updated';
                                            } elseif ($trayHistory->action === 'deleted') {
                                                $trayIcon = 'fa-trash';
                                                $trayColorClass = $wasSoldTray ? 'entry-tray-deleted-sold' : 'entry-tray-deleted';
                                            }

                                            // Build title based on action
                                            if ($trayHistory->action === 'updated') {
                                                $namaChanged = $trayHistory->old_nama_tray !== $trayHistory->nama_tray;
                                                $jumlahChanged = $trayHistory->old_jumlah_telur !== $trayHistory->jumlah_telur;

                                                $arrowStyled = ' <span class="text-connector">→</span> ';
                                                $pipeStyled = ' <span class="text-connector">|</span> ';

                                                $oldName = $trayHistory->old_nama_tray ?? '—';
                                                $currentName = $trayHistory->nama_tray ?? '—';
                                                $nameSegment = $namaChanged
                                                    ? '(' . $oldName . $arrowStyled . '<span class="text-changed">' . $currentName . '</span>)'
                                                    : '(' . ($oldName !== '—' ? $oldName : $currentName) . ')';

                                                $formatAmount = function ($value) {
                                                    return $value !== null
                                                        ? number_format($value, 0, ',', '.')
                                                        : '—';
                                                };

                                                $oldAmount = $formatAmount($trayHistory->old_jumlah_telur);
                                                $currentAmount = $formatAmount($trayHistory->jumlah_telur);
                                                $amountSegment = $jumlahChanged
                                                    ? '(' . $oldAmount . $arrowStyled . '<span class="text-changed">' . $currentAmount . '</span>)'
                                                    : '(' . ($currentAmount !== '—' ? $currentAmount : $oldAmount) . ')';

                                                $trayHistoryTitle = $nameSegment . $pipeStyled . $amountSegment;
                                            } elseif ($trayHistory->action === 'deleted') {
                                                $pipeStyled = ' <span class="text-connector">|</span> ';

                                                $formatAmount = function ($value) {
                                                    return $value !== null
                                                        ? number_format($value, 0, ',', '.')
                                                        : '—';
                                                };

                                                $deletedName = $trayHistory->nama_tray ?? '—';
                                                $deletedAmount = $formatAmount($trayHistory->jumlah_telur);
                                                $trayHistoryTitle = '(' . $deletedName . ')' . $pipeStyled . '(' . $deletedAmount . ')';
                                            } else {
                                                $trayHistoryTitle = ($trayHistory->nama_tray ?? 'Tray tanpa nama') . ' — Ditambahkan';
                                            }

                                            $keteranganText = trim($trayHistory->keterangan ?? '');
                                            if ($keteranganText === '') {
                                                if ($trayHistory->action === 'updated') {
                                                    $keteranganText = 'diubah';
                                                } elseif ($trayHistory->action === 'deleted') {
                                                    $keteranganText = 'di Hapus';
                                                }
                                            }
                                        @endphp
                                        <div class="entry entry-tray {{ $trayColorClass }}" data-entry-type="tray">
                                            <div class="entry-left">
                                                <div class="dot">
                                                    <i class="fa-solid {{ $trayIcon }}"></i>
                                                </div>
                                                <div class="entry-body">
                                                    <div class="title {{ $trayHistory->action === 'deleted' ? 'text-danger' : '' }}">
                                                        @if(in_array($trayHistory->action, ['updated', 'deleted']))
                                                            {!! $trayHistoryTitle !!}
                                                        @else
                                                            {{ $trayHistoryDate }} — <span class="{{ $trayActionClass }}">{{ $trayHistoryTitle }}</span>
                                                        @endif
                                                    </div>
                                                    @if ($keteranganText !== '')
                                                        <div class="small text-muted">Keterangan: ({{ $keteranganText }})</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="entry-right">
                                                <div class="text-end">
                                                    <div class="small text-muted">{{ $trayHistoryTimestamp }}</div>
                                                    <div class="small text-muted">oleh {{ $trayHistoryUser }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @foreach ($visibleLaporan as $laporan)
                                        @php
                                            $entries = [];
                                            
                                            if (($laporan->produksi_telur ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'telur',
                                                    'value' => $laporan->produksi_telur,
                                                    'unit' => 'butir telur'
                                                ];
                                            }
                                            
                                            if (($laporan->konsumsi_pakan_kg ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'pakan',
                                                    'value' => $laporan->konsumsi_pakan_kg,
                                                    'unit' => 'kg pakan'
                                                ];
                                            }
                                            
                                            if (($laporan->vitamin_terpakai ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'vitamin',
                                                    'value' => $laporan->vitamin_terpakai,
                                                    'unit' => 'L vitamin'
                                                ];
                                            }
                                            
                                            if (($laporan->sisa_tray_bal ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'tray',
                                                    'value' => $laporan->sisa_tray_bal,
                                                    'unit' => 'bal tray'
                                                ];
                                            }
                                            
                                            if (($laporan->sisa_tray_lembar ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'tray',
                                                    'value' => $laporan->sisa_tray_lembar,
                                                    'unit' => 'lembar tray'
                                                ];
                                            }
                                            
                                            if (($laporan->sisa_telur ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'tray',
                                                    'value' => $laporan->sisa_telur,
                                                    'unit' => 'butir telur sisa'
                                                ];
                                            }
                                            
                                            if (($laporan->jumlah_kematian ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'kematian',
                                                    'value' => $laporan->jumlah_kematian,
                                                    'unit' => 'ekor kematian',
                                                    'meta' => [
                                                        'gender' => $laporan->jenis_kelamin_kematian,
                                                        'keterangan' => $laporan->keterangan_kematian,
                                                    ],
                                                ];
                                            }
                                            
                                            if (($laporan->penjualan_telur_butir ?? 0) > 0) {
                                                $totalPendapatan = ($laporan->penjualan_telur_butir ?? 0) * ($laporan->harga_per_butir ?? 0);
                                                $entries[] = [
                                                    'type' => 'penjualan',
                                                    'value' => $totalPendapatan,
                                                    'unit' => 'pendapatan penjualan',
                                                    'meta' => [
                                                        'jumlah' => $laporan->penjualan_telur_butir,
                                                        'harga' => $laporan->harga_per_butir,
                                                    ],
                                                ];
                                            }
                                            
                                            if ($laporan->catatan_kejadian) {
                                                $entries[] = [
                                                    'type' => 'laporan',
                                                    'value' => null,
                                                    'unit' => 'laporan',
                                                    'meta' => [
                                                        'catatan' => $laporan->catatan_kejadian,
                                                        'tanggal' => $laporan->tanggal->locale('id')->translatedFormat('d F Y'),
                                                        'created_at' => $laporan->dibuat_pada
                                                            ? $laporan->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A')
                                                            : '—',
                                                    ]
                                                ];
                                            }
                                        @endphp
                                        
                                        @foreach ($entries as $entry)
                                            @php
                                                $entryIcon = [
                                                    'telur' => 'fa-egg',
                                                    'pakan' => 'fa-bowl-food',
                                                    'vitamin' => 'fa-capsules',
                                                    'tray' => 'fa-layer-group',
                                                    'kematian' => 'fa-skull-crossbones',
                                                    'penjualan' => 'fa-cash-register',
                                                    'laporan' => 'fa-file-lines',
                                                ][$entry['type']] ?? 'fa-file-lines';

                                                $tanggalEntry = $laporan->tanggal->locale('id')->translatedFormat('d F Y');
                                                
                                                if ($entry['type'] === 'pakan') {
                                                    $consumed = $formatNumber($entry['value'], 2);
                                                    $remaining = $formatNumber($laporan->sisa_pakan_kg ?? 0, 2);
                                                    $ringkasan = $consumed . ' kg pakan (sisa: ' . $remaining . ' kg)';
                                                } elseif ($entry['type'] === 'vitamin') {
                                                    $consumed = $formatNumber($entry['value'], 2);
                                                    $remaining = $formatNumber($laporan->sisa_vitamin_liter ?? 0, 2);
                                                    $ringkasan = $consumed . ' L vitamin (sisa: ' . $remaining . ' L)';
                                                } elseif ($entry['type'] === 'kematian') {
                                                    $gender = $entry['meta']['gender'] ?? null;
                                                    $genderLabel = $gender ? ' (' . ucfirst($gender) . ')' : '';
                                                    $ringkasan = $formatNumber($entry['value']) . ' ekor kematian' . $genderLabel;
                                                } elseif ($entry['type'] === 'penjualan') {
                                                    $jumlah = $entry['meta']['jumlah'] ?? 0;
                                                    $harga = $entry['meta']['harga'] ?? 0;
                                                    $ringkasan = 'Rp ' . $formatNumber($entry['value'], 0);
                                                } else {
                                                    $ringkasan = $entry['value'] 
                                                        ? $formatNumber($entry['value'], $entry['type'] === 'pakan' || $entry['type'] === 'vitamin' ? 2 : 0) . ' ' . $entry['unit']
                                                        : 'Laporan';
                                                }

                                                $createdAtFormatted = $laporan->dibuat_pada
                                                    ? 'Tercatat ' . $laporan->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A')
                                                    : '—';

                                                if ($entry['type'] === 'kematian' && !empty($entry['meta']['keterangan'])) {
                                                    $createdAtFormatted .= ' (' . $entry['meta']['keterangan'] . ')';
                                                }
                                            @endphp
                                            <div class="entry entry-{{ $entry['type'] }}" data-entry-type="{{ $entry['type'] }}">
                                                <div class="entry-left">
                                                    <div class="dot">
                                                        <i class="fa-solid {{ $entryIcon }}"></i>
                                                    </div>
                                                    <div class="entry-body">
                                                        <div class="title">{{ $tanggalEntry }} — {{ $ringkasan }}</div>
                                                        @if ($entry['type'] === 'penjualan')
                                                            <div class="small text-muted">Keterangan: ({{ $laporan->nama_tray_penjualan ?? 'Penjualan Telur' }} | {{ $formatNumber($jumlah) }} butir @ Rp {{ $formatNumber($harga, 0) }})</div>
                                                        @endif
                                                        @if ($entry['type'] === 'telur')
                                                            <div class="small text-muted">Keterangan: ({{ $laporan->nama_tray ?? 'Tray Telur' }})</div>
                                                        @endif
                                                        @if ($entry['type'] !== 'penjualan' && $entry['type'] !== 'telur')
                                                            <small>{{ $createdAtFormatted }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="entry-right">
                                                    <div class="d-flex gap-2 align-items-center">
                                                        @if ($laporan->catatan_kejadian && $entry['type'] !== 'laporan')
                                                            <span class="badge bg-light text-muted">{{ \Illuminate\Support\Str::limit($laporan->catatan_kejadian, 48) }}</span>
                                                        @endif

                                                        @if ($entry['type'] === 'penjualan')
                                                            <div class="text-end">
                                                                <div class="small text-muted">{{ $laporan->dibuat_pada ? $laporan->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A') : '—' }}</div>
                                                                <div class="small text-muted">oleh {{ optional($laporan->pengguna)->nama_pengguna ?? '—' }}</div>
                                                            </div>
                                                        @endif

                                                        @if ($entry['type'] === 'telur')
                                                            <div class="text-end">
                                                                <div class="small text-muted">{{ $laporan->dibuat_pada ? $laporan->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A') : '—' }}</div>
                                                                <div class="small text-muted">oleh {{ optional($laporan->pengguna)->nama_pengguna ?? '—' }}</div>
                                                            </div>
                                                        @endif

                                                        @if ($entry['type'] === 'laporan')
                                                            @php
                                                                $catatanDetail = $entry['meta']['catatan'] ?? $laporan->catatan_kejadian;
                                                                $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                                                $hariIndex = $laporan->tanggal ? $laporan->tanggal->dayOfWeek : 0;
                                                                $namaHari = isset($hariIndonesia[$hariIndex]) ? $hariIndonesia[$hariIndex] : 'Unknown';
                                                                $tanggalFormatted = $laporan->tanggal ? $laporan->tanggal->locale('id')->translatedFormat('d F Y') : 'Unknown';
                                                                $catatanTanggal = $namaHari . ', ' . $tanggalFormatted;
                                                                $catatanCreated = $entry['meta']['created_at'] ?? ($laporan->dibuat_pada
                                                                    ? $laporan->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A')
                                                                    : '—');
                                                                $catatanUser = optional($laporan->pengguna)->username
                                                                    ?? optional($laporan->pengguna)->nama_pengguna
                                                                    ?? '—';
                                                                
                                                                // Hitung data KAI untuk produksi telur
                                                                $totalTelurHarian = $laporanHarian->where('tanggal', $laporan->tanggal)->sum('produksi_telur');
                                                                $totalTrayHarian = $laporanHarian->where('tanggal', $laporan->tanggal)->whereNotNull('nama_tray')->where('produksi_telur', '>', 0)->count();
                                                                $totalPendapatanHarian = $laporanHarian->where('tanggal', $laporan->tanggal)->sum('pendapatan_harian');
                                                                $totalTelurRusakHarian = $laporanHarian->where('tanggal', $laporan->tanggal)->sum('telur_rusak');
                                                                
                                                                // Hitung total telur awal (total produksi + telur rusak)
                                                                $totalTelurAwalHarian = $totalTelurHarian + $totalTelurRusakHarian;
                                                                
                                                                // Hitung sisa telur berdasarkan data saat laporan dibuat
                                                                $totalTelurAktifHarian = $laporanHarian->where('tanggal', $laporan->tanggal)
                                                                    ->whereNotIn('id', $soldTrayIds)
                                                                    ->whereNotNull('nama_tray')
                                                                    ->sum('produksi_telur');
                                                                $sisaTelurHarian = max(0, ($summary['total_telur_awal'] ?? 0) - $totalTelurAktifHarian);
                                                                
                                                                $sisaTelurValue = $formatNumber($sisaTelurHarian);
                                                                $totalTelurAwalValue = $formatNumber($totalTelurAwalHarian);
                                                                $totalTrayValue = $formatNumber($totalTrayHarian);
                                                                $totalPendapatanValue = $formatLargeNumber($totalPendapatanHarian, false);
                                                                $totalTelurRusakValue = $formatNumber($totalTelurRusakHarian);
                                                            @endphp
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-info detail-catatan-btn"
                                                                data-catatan="{{ e($catatanDetail) }}"
                                                                data-tanggal="{{ e($catatanTanggal) }}"
                                                                data-created="{{ e($catatanCreated) }}"
                                                                data-user="{{ e($catatanUser) }}"
                                                                data-sisa-telur="{{ e($sisaTelurValue) }}"
                                                                data-total-telur-awal="{{ e($totalTelurAwalValue) }}"
                                                                data-total-tray="{{ e($totalTrayValue) }}"
                                                                data-total-pendapatan="{{ e($totalPendapatanValue) }}"
                                                                data-telur-rusak="{{ e($totalTelurRusakValue) }}">
                                                                Detail
                                                            </button>

                                                            <form action="{{ route('admin.produksi.laporan.destroy', [$produksi->id, $laporan->id]) }}" method="POST" class="m-0 p-0 delete-laporan-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('admin.produksi.laporan.reset', [$produksi->id, $laporan->id]) }}" method="POST" class="m-0 p-0 reset-laporan-form">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-warning">Reset</button>
                                                            </form>

                                                            <form action="{{ route('admin.produksi.laporan.destroy', [$produksi->id, $laporan->id]) }}" method="POST" class="m-0 p-0 delete-laporan-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .swal2-popup.swal-detail-laporan {
            padding: 0;
            background: transparent;
            max-width: 760px;
        }

        .swal-detail-card {
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        }

        .swal-detail-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .swal-detail-header .title {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            font-weight: 600;
        }

        .swal-detail-header .btn-close {
            margin-left: auto;
        }

        .swal-detail-body {
            padding: 1.5rem;
        }

        .swal-detail-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .swal-detail-section:last-child {
            margin-bottom: 0;
        }

        .swal-detail-divider {
            margin: 1rem 0;
            border-top: 1px solid #f1f5f9;
        }

        .swal-detail-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }

        .swal-detail-stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.5rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 60px;
        }

        .swal-detail-stat-card.combined {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 60px;
        }

        .swal-detail-stat-card .stat-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
            margin-bottom: 0.15rem;
            line-height: 1.2;
        }

        .swal-detail-stat-card .stat-value {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        .swal-detail-note-card {
            border: 1px solid #e2e8f0;
            border-radius: 0.65rem;
            background: #fff;
        }

        .swal-detail-note-card .card-body {
            padding: 1rem 1.25rem;
        }

        .swal-detail-note-card .note-content {
            white-space: pre-line;
            color: #1e293b;
            font-size: 0.95rem;
            line-height: 1.6;
            text-align: left;
        }

        .swal-detail-footer {
            margin-top: 1.25rem;
            font-size: 0.82rem;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 576px) {
            .swal2-popup.swal-detail-laporan {
                max-width: 95vw;
            }

            .swal-detail-header,
            .swal-detail-body {
                padding: 1rem;
            }
        }

        .entry {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .entry:last-child {
            border-bottom: none;
        }

        .entry-left {
            flex: 1;
            min-width: 0;
        }

        .entry-right {
            flex-shrink: 0;
            text-align: right;
        }

        .dot {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            margin-right: 0.75rem;
            float: left;
        }

        .entry-body {
            overflow: hidden;
        }

        .entry .title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .entry small {
            color: #64748b;
        }

        .text-changed {
            color: #0ea5a4 !important;
            font-weight: normal;
        }

        .text-connector {
            font-weight: normal;
            color: inherit;
        }

        /* Background colors for entry dots */
        .entry-telur .dot { background-color: var(--accent); } /* telur theme color */
        .entry-tray .dot { background-color: #0ea5a4; } /* tray theme color - teal */
        .entry-tray-default .dot { background-color: #0ea5a4; } /* tray default theme color */
        .entry-tray-updated .dot { background-color: #0ea5a4; } /* tray updated theme color */
        .entry-penjualan .dot { background-color: #10b981; } /* penjualan theme color - green (same as KAI income) */
        .entry-pakan .dot { background-color: #f97316; } /* orange */
        .entry-vitamin .dot { background-color: #8b5cf6; } /* violet */
        .entry-kematian .dot { background-color: #ef4444; } /* red */
        .entry-laporan .dot { background-color: #6b7280; } /* gray */
        .entry-tray-deleted .dot { background-color: #ef4444; } /* red for deleted trays */
        .entry-tray-deleted-sold .dot { background-color: #6c757d; } /* gray for deleted sold trays */

        .tray-card-sold {
            background-color: #f8f9fa !important;
            opacity: 0.6;
        }

        .tray-card-sold .tray-card-header {
            background-color: #6c757d !important;
            opacity: 1 !important;
        }

        .tray-card-sold .tray-card-title {
            color: white !important;
        }

        .tray-card-sold .tray-card-updated .text-light {
            color: #e9ecef !important;
        }

        .tray-card-grid.tray-card-sold .egg-background {
            background-color: #adb5bd !important; /* lighter gray */
        }

        .tray-card-grid.tray-card-sold .tray-date {
            color: black !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('history-toggle');
            const collapseElement = document.getElementById('history-collapse');
            const historyTitle = document.getElementById('history-title');
            const historyEntries = document.querySelectorAll('.list-timeline .entry');

            if (toggleButton && collapseElement) {
                collapseElement.addEventListener('hide.bs.collapse', function () {
                    toggleButton.textContent = toggleButton.dataset.showText;
                });

                collapseElement.addEventListener('show.bs.collapse', function () {
                    toggleButton.textContent = toggleButton.dataset.hideText;
                });
            }

            function filterHistoryEntries(activeTabId) {
                const tabTitles = {
                    'telur': 'Telur',
                    'tray': 'Tray',
                    'penjualan': 'Penjualan',
                    'pakan': 'Pakan',
                    'vitamin': 'Vitamin',
                    'kematian': 'Kematian',
                    'laporan': 'Laporan',
                    'analytics': 'Grafik & Analisis'
                };

                const tabColors = {
                    'telur': 'text-telur',
                    'tray': 'text-tray',
                    'penjualan': 'text-laporan',
                    'pakan': 'text-pakan',
                    'vitamin': 'text-vitamin',
                    'kematian': 'text-kematian',
                    'laporan': 'text-laporan',
                    'analytics': 'text-primary'
                };

                if (activeTabId && tabTitles[activeTabId]) {
                    historyTitle.innerHTML = `(<span class="${tabColors[activeTabId]}">${tabTitles[activeTabId]}</span>)`;
                } else {
                    historyTitle.innerHTML = '(<span class="text-muted">Semua</span>)';
                }

                historyEntries.forEach(entry => {
                    const entryType = entry.dataset.entryType || '';
                    const shouldShow = !activeTabId || activeTabId === 'analytics' || entryType === activeTabId;
                    entry.style.display = shouldShow ? 'flex' : 'none';
                });
            }

            function updateActiveTabInput(tabId) {
                const activeTabInput = document.getElementById('activeTabInput');
                if (activeTabInput && tabId) {
                    activeTabInput.value = tabId;
                }
            }

            const savedActiveTab = localStorage.getItem('activeProduksiTab');
            if (savedActiveTab) {
                const savedTabElement = document.querySelector(`#pencatatanTabs .nav-link[data-bs-target="#${savedActiveTab}"]`);
                if (savedTabElement) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(savedTabElement);
                        tabInstance.show();
                    } else {
                        document.querySelectorAll('#pencatatanTabs .nav-link').forEach(tab => {
                            tab.classList.remove('active');
                        });
                        document.querySelectorAll('#pencatatanTabsContent .tab-pane').forEach(pane => {
                            pane.classList.remove('active', 'show');
                        });
                        savedTabElement.classList.add('active');
                        const targetPane = document.getElementById(savedActiveTab);
                        if (targetPane) {
                            targetPane.classList.add('active', 'show');
                        }
                        updateActiveTabInput(savedActiveTab);
                        filterHistoryEntries(savedActiveTab);
                    }
                }
            }

            const formTabs = document.querySelectorAll('#pencatatanTabs .nav-link');
            formTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.getAttribute('data-bs-target').substring(1);
                    localStorage.setItem('activeProduksiTab', targetId);
                    filterHistoryEntries(targetId);
                });
            });

            const activeTab = document.querySelector('#pencatatanTabs .nav-link.active');
            if (activeTab) {
                const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                updateActiveTabInput(activeTabId);
                filterHistoryEntries(activeTabId);
            } else {
                filterHistoryEntries('telur');
                updateActiveTabInput('telur');
            }

            document.querySelectorAll('.reset-laporan-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Konfirmasi Reset',
                        text: 'Yakin ingin mereset entri ini? Nilai pada entri ini akan dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ffc107',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Reset',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('.delete-laporan-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: 'Konfirmasi menghapus dan data tidak bisa dikembalikan.',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        
            const detailButtons = document.querySelectorAll('.detail-catatan-btn');
            const escapeHtml = (unsafe = '') => unsafe
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            detailButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const data = this.dataset;
                    const tanggal = data.tanggal || '-';
                    const created = data.created || '-';
                    const user = data.user || '—';
                    const sisaTelur = data.sisaTelur || '0';
                    const totalTelurAwal = data.totalTelurAwal || '0';
                    const totalTray = data.totalTray || '0';
                    const totalPendapatan = data.totalPendapatan || '0';
                    const telurRusak = data.telurRusak || '0';
                    const catatanRaw = data.catatan || '-';
                    const catatanHtml = escapeHtml(catatanRaw).replace(/\n/g, '<br>');

                    const detailHtml = `
                        <div class="swal-detail-card">
                            <div class="swal-detail-header">
                                <div class="title">
                                    <i class="fa-solid fa-egg"></i>
                                    <span>Detail Produksi Telur</span>
                                </div>
                            </div>
                            <div class="swal-detail-body">
                                <div class="swal-detail-section">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                                        <div class="text-start">
                                            <small class="text-muted"><i class="fa-solid fa-calendar-days me-1"></i>Tanggal Produksi</small>
                                            <div class="fw-semibold">${escapeHtml(tanggal)}</div>
                                        </div>
                                        <div class="text-md-end">
                                            <small class="text-muted"><i class="fa-solid fa-user me-1"></i>Dicatat Oleh</small>
                                            <div class="fw-semibold">${escapeHtml(user)}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swal-detail-section">
                                    <div class="swal-detail-stats">
                                        <div class="swal-detail-stat-card">
                                            <div class="stat-label">Sisa Telur</div>
                                            <div class="stat-value">${escapeHtml(sisaTelur)}</div>
                                        </div>
                                        <div class="swal-detail-stat-card combined">
                                            <div class="stat-label">Total Telur</div>
                                            <div class="stat-value">${escapeHtml(telurRusak)}<small class="text-muted"> rusak</small></div>
                                        </div>
                                        <div class="swal-detail-stat-card">
                                            <div class="stat-label">Total Tray</div>
                                            <div class="stat-value">${escapeHtml(totalTray)}</div>
                                        </div>
                                        <div class="swal-detail-stat-card">
                                            <div class="stat-label">Total Pendapatan</div>
                                            <div class="stat-value">Rp ${escapeHtml(totalPendapatan)}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swal-detail-section">
                                    <div class="fw-semibold mb-2">Catatan Produksi</div>
                                    <div class="swal-detail-note-card">
                                        <div class="card-body">
                                            <div class="note-content">${catatanHtml}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swal-detail-footer">
                                    <div>Dicatat ${escapeHtml(created)}</div>
                                    <button type="button" class="btn btn-sm btn-primary" data-action="close-detail">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>`;

                    Swal.fire({
                        html: detailHtml,
                        width: 760,
                        customClass: {
                            popup: 'swal-detail-laporan'
                        },
                        showConfirmButton: false,
                        showCloseButton: false,
                        focusConfirm: false,
                        didRender: () => {
                            const closeBtn = Swal.getPopup()?.querySelector('[data-action="close-detail"]');
                            if (closeBtn) {
                                closeBtn.addEventListener('click', () => Swal.close());
                            }
                        }
                    });
                });
            });
        });
    </script>
@endpush
