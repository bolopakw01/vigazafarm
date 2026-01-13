@extends('admin.layouts.app')

@section('title', 'Detail Produksi Puyuh - ' . ($produksi->batch_produksi_id ?? 'Tanpa Kode Batch'))

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Produksi', 'link' => route('admin.produksi')],
        ['label' => 'Detail Produksi', 'badge' => $produksi->batch_produksi_id],
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
                return number_format($scaled, $precision, ',', '.') . '<small style="font-size:0.45em;"> M</small>';
            }
            if ($value >= 1000000) {
                $scaled = $allowDecimals
                    ? round($value / 1000000, 1)
                    : round($value / 1000000);
                return number_format($scaled, $precision, ',', '.') . '<small style="font-size:0.45em;"> Jt</small>';
            }
            if ($value >= 1000) {
                $scaled = $allowDecimals
                    ? round($value / 1000, 1)
                    : round($value / 1000);
                return number_format($scaled, $precision, ',', '.') . '<small style="font-size:0.45em;"> Rb</small>';
            }
            return number_format($value, 0, ',', '.');
        };

        $startDate = $produksi->tanggal_mulai ?? $produksi->tanggal ?? optional($produksi->pembesaran)->tanggal_siap;
        $startDateFormatted = $startDate ? \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y') : '-';
        $endDate = $produksi->tanggal_akhir;
        $endDateFormatted = $endDate ? \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y') : '-';

        $kandangName = $produksi->kandang->nama_kandang ?? 'nama kandang';
        $batchCode = $produksi->batch_produksi_id ?? 'Tanpa Kode Batch';
        $initialPopulation = $produksi->jumlah_indukan ?? (($produksi->jumlah_jantan ?? 0) + ($produksi->jumlah_betina ?? 0));
        $totalTelur = $summary['total_telur'] ?? 0;
        $totalPendapatan = $summary['total_pendapatan'] ?? 0;
        $totalPakanKg = $laporanHarian->sum('konsumsi_pakan_kg');
        $totalVitaminL = $laporanHarian->sum('vitamin_terpakai');
        $totalBiayaPakan = $laporanHarian->sum('biaya_pakan_harian');
        $totalBiayaVitamin = $laporanHarian->sum('biaya_vitamin_harian');
        $totalPengeluaran = $totalBiayaPakan + $totalBiayaVitamin;
        $fcrValue = ($totalTelur > 0 && $totalPakanKg > 0)
            ? $totalPakanKg / max($totalTelur, 1)
            : null;
        $latestPakanPrice = optional($laporanHarian->first(fn ($laporan) => $laporan->harga_pakan_per_kg !== null))->harga_pakan_per_kg;
        $latestVitaminPrice = optional($laporanHarian->first(fn ($laporan) => $laporan->harga_vitamin_per_liter !== null))->harga_vitamin_per_liter;
        $totalKematian = $summary['total_kematian'] ?? 0;
        $currentPopulation = $summary['current_population'] ?? max($initialPopulation - $totalKematian, 0);

        $initialJantan = $summary['initial_jantan'] ?? ($produksi->jumlah_jantan ?? 0);
        $initialBetina = $summary['initial_betina'] ?? ($produksi->jumlah_betina ?? 0);
        $jumlahJantan = $summary['current_jantan'] ?? $initialJantan;
        $jumlahBetina = $summary['current_betina'] ?? $initialBetina;
        $mortalitasPercent = $initialPopulation > 0 ? ($totalKematian / $initialPopulation) * 100 : 0;
        $mortalitasDisplay = floor($mortalitasPercent) == $mortalitasPercent
            ? $formatNumber($mortalitasPercent, 0)
            : $formatNumber($mortalitasPercent, 2);
        
        // Calculate percentage ratio
        $totalRatio = $jumlahJantan + $jumlahBetina;
        $persenJantan = $totalRatio > 0 ? round(($jumlahJantan / $totalRatio) * 100) : 0;
        $persenBetina = $totalRatio > 0 ? round(($jumlahBetina / $totalRatio) * 100) : 0;
        
        $ratioLabel = $jumlahJantan + $jumlahBetina > 0
            ? $formatNumber($jumlahJantan) . ' Jantan | ' . $formatNumber($jumlahBetina) . ' Betina'
            : 'Data rasio belum tersedia';

        $puyuhAnalyticsSeries = collect($laporanHarian ?? [])
            ->filter(fn ($laporan) => $laporan->tanggal)
            ->sortBy('tanggal')
            ->groupBy(fn ($laporan) => $laporan->tanggal->format('Y-m-d'))
            ->map(function ($items) {
                $first = $items->first();
                $dateValue = optional($first)->tanggal;
                $displayDate = $dateValue ? $dateValue->locale('id')->translatedFormat('d M Y') : '-';

                return [
                    'date' => $dateValue ? $dateValue->format('Y-m-d') : null,
                    'display' => $displayDate,
                    'telur' => (float) $items->sum('produksi_telur'),
                    'pakan' => (float) $items->sum('konsumsi_pakan_kg'),
                    'vitamin' => (float) $items->sum('vitamin_terpakai'),
                    'kematian' => (float) $items->sum('jumlah_kematian'),
                    'penjualan' => (float) $items->sum('penjualan_puyuh_ekor'),
                ];
            })
            ->filter(fn ($row) => $row['date'])
            ->values();

        $totalTelurDicatat = $puyuhAnalyticsSeries->sum('telur');
        $totalPakanDicatat = $puyuhAnalyticsSeries->sum('pakan');
        $totalVitaminDicatat = $puyuhAnalyticsSeries->sum('vitamin');
        $totalKematianDicatat = $puyuhAnalyticsSeries->sum('kematian');
        $totalPenjualanDicatat = $puyuhAnalyticsSeries->sum('penjualan');
        $avgTelurDicatat = $puyuhAnalyticsSeries->avg('telur') ?? 0;
        $avgPakanDicatat = $puyuhAnalyticsSeries->avg('pakan') ?? 0;
        $avgVitaminDicatat = $puyuhAnalyticsSeries->avg('vitamin') ?? 0;
        $peakTelurPoint = $puyuhAnalyticsSeries->sortByDesc('telur')->first();
        $peakPakanPoint = $puyuhAnalyticsSeries->sortByDesc('pakan')->first();
        $peakVitaminPoint = $puyuhAnalyticsSeries->sortByDesc('vitamin')->first();
        $peakKematianPoint = $puyuhAnalyticsSeries->sortByDesc('kematian')->first();
        $peakPenjualanPoint = $puyuhAnalyticsSeries->sortByDesc('penjualan')->first();
        $peakTelurValue = is_array($peakTelurPoint) ? ($peakTelurPoint['telur'] ?? 0) : 0;
        $peakTelurLabel = is_array($peakTelurPoint) ? ($peakTelurPoint['display'] ?? '-') : '-';
        $peakPakanValue = is_array($peakPakanPoint) ? ($peakPakanPoint['pakan'] ?? 0) : 0;
        $peakPakanLabel = is_array($peakPakanPoint) ? ($peakPakanPoint['display'] ?? '-') : '-';
        $peakVitaminValue = is_array($peakVitaminPoint) ? ($peakVitaminPoint['vitamin'] ?? 0) : 0;
        $peakVitaminLabel = is_array($peakVitaminPoint) ? ($peakVitaminPoint['display'] ?? '-') : '-';
        $peakKematianValue = is_array($peakKematianPoint) ? ($peakKematianPoint['kematian'] ?? 0) : 0;
        $peakKematianLabel = is_array($peakKematianPoint) ? ($peakKematianPoint['display'] ?? '-') : '-';
        $peakPenjualanValue = is_array($peakPenjualanPoint) ? ($peakPenjualanPoint['penjualan'] ?? 0) : 0;
        $peakPenjualanLabel = is_array($peakPenjualanPoint) ? ($peakPenjualanPoint['display'] ?? '-') : '-';
        $feedPerEgg = $totalTelurDicatat > 0 && $totalPakanDicatat > 0
            ? $totalPakanDicatat / $totalTelurDicatat
            : null;
        $mortalitasPercentAnalytics = ($initialPopulation ?? 0) > 0
            ? ($totalKematianDicatat / max($initialPopulation, 1)) * 100
            : 0;

        $puyuhAnalyticsStats = [
            [
                'label' => 'Total Telur',
                'value' => $formatNumber($totalTelurDicatat),
                'suffix' => 'butir',
                'meta' => $puyuhAnalyticsSeries->count() ? 'Rata-rata ' . $formatNumber($avgTelurDicatat, 0) . ' butir/hari' : null,
            ],
            [
                'label' => 'Total Pakan',
                'value' => $formatNumber($totalPakanDicatat, 2),
                'suffix' => 'kg',
                'meta' => $puyuhAnalyticsSeries->count() ? 'Rata-rata ' . $formatNumber($avgPakanDicatat, 2) . ' kg/hari' : null,
            ],
            [
                'label' => 'Total Vitamin',
                'value' => $formatNumber($totalVitaminDicatat, 2),
                'suffix' => 'L',
                'meta' => $puyuhAnalyticsSeries->count() ? 'Rata-rata ' . $formatNumber($avgVitaminDicatat, 2) . ' L/hari' : null,
            ],
            [
                'label' => 'Total Kematian',
                'value' => $formatNumber($totalKematianDicatat),
                'suffix' => 'ekor',
                'meta' => $initialPopulation > 0
                    ? 'Mortalitas ' . number_format($mortalitasPercentAnalytics, 2, ',', '.') . '%'
                    : null,
            ],
            [
                'label' => 'Penjualan Puyuh',
                'value' => $formatNumber($totalPenjualanDicatat),
                'suffix' => 'ekor',
                'meta' => $totalPendapatan > 0 ? 'Total pendapatan Rp ' . strip_tags($formatLargeNumber($totalPendapatan, false)) : null,
            ],
        ];

        $puyuhAnalysisNotes = [
            [
                'icon' => 'fa-egg',
                'title' => 'Produksi Telur',
                'text' => $totalTelurDicatat > 0
                    ? 'Rata-rata ' . $formatNumber($avgTelurDicatat, 0) . ' butir/hari; puncak '
                        . $formatNumber($peakTelurValue, 0) . ' butir (' . $peakTelurLabel . ').'
                    : 'Belum ada pencatatan telur yang dapat dianalisis.',
            ],
            [
                'icon' => 'fa-bowl-food',
                'title' => 'Konsumsi Pakan',
                'text' => $totalPakanDicatat > 0
                    ? 'Rata-rata ' . $formatNumber($avgPakanDicatat, 2) . ' kg/hari; tertinggi '
                        . $formatNumber($peakPakanValue, 2) . ' kg (' . $peakPakanLabel . ').'
                        . ($feedPerEgg ? ' Rasio pakan/telur ~' . number_format($feedPerEgg, 3, ',', '.') . ' kg per butir.' : '')
                    : 'Belum ada catatan pakan.',
            ],
            [
                'icon' => 'fa-capsules',
                'title' => 'Pemakaian Vitamin',
                'text' => $totalVitaminDicatat > 0
                    ? 'Rata-rata ' . $formatNumber($avgVitaminDicatat, 2) . ' L/hari; tertinggi '
                        . $formatNumber($peakVitaminValue, 2) . ' L (' . $peakVitaminLabel . ').'
                    : 'Belum ada catatan vitamin.',
            ],
            [
                'icon' => 'fa-skull-crossbones',
                'title' => 'Kematian',
                'text' => $totalKematianDicatat > 0
                    ? 'Total ' . $formatNumber($totalKematianDicatat) . ' ekor; puncak '
                        . $formatNumber($peakKematianValue) . ' ekor (' . $peakKematianLabel . ').'
                    : 'Tidak ada kematian yang tercatat.',
            ],
            [
                'icon' => 'fa-cash-register',
                'title' => 'Penjualan Puyuh',
                'text' => $totalPenjualanDicatat > 0
                    ? $formatNumber($totalPenjualanDicatat) . ' ekor terjual; aktivitas tertinggi '
                        . $formatNumber($peakPenjualanValue) . ' ekor (' . $peakPenjualanLabel . ').'
                    : 'Belum ada catatan penjualan puyuh.',
            ],
        ];

        $firstAnalyticsPoint = $puyuhAnalyticsSeries->first();
        $lastAnalyticsPoint = $puyuhAnalyticsSeries->last();
        $puyuhAnalyticsRange = [
            'start' => is_array($firstAnalyticsPoint) ? ($firstAnalyticsPoint['display'] ?? null) : null,
            'end' => is_array($lastAnalyticsPoint) ? ($lastAnalyticsPoint['display'] ?? null) : null,
        ];
    @endphp

    <div class="container">
        <div class="page-wrapper">
            <div class="card pu_header mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="pu_icon">
                                <i class="fa-solid fa-dove" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                    <span class="ref-badge hide-on-narrow">
                                        <i class="fa-solid fa-tags"></i> {{ $batchCode }}
                                    </span>
                                    <span class="badge bg-success rounded-pill hide-on-narrow">{{ ucfirst($produksi->status ?? 'aktif') }}</span>
                                </div>
                                <h5 class="mb-0">
                                    Produksi Puyuh
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
                                    <div class="text-muted small hide-on-narrow">
                                        <i class="fa-solid fa-dove me-1" aria-hidden="true"></i>Populasi Awal:
                                        <span id="populasi-awal" class="fw-semibold ms-1">{{ $formatNumber($initialPopulation) }} ekor</span>
                                    </div>
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
                                <div class="value" id="kai-telur-value">{{ $formatNumber($totalTelur) }}</div>
                                <div class="label" id="kai-telur-label">Total Telur (butir)</div>
                            </div>
                            <i class="fa-solid fa-egg icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-ratio" id="kai-ratio-card" data-current-male="{{ $jumlahJantan }}" data-current-female="{{ $jumlahBetina }}" data-initial-male="{{ $initialJantan }}" data-initial-female="{{ $initialBetina }}">
                            <div>
                                <div class="value" id="kai-ratio-value">
                                    {{ $formatNumber($currentPopulation) }}
                                    <span class="ratio-small">
                                        {{ $totalRatio > 0 ? $persenJantan . ':' . $persenBetina : '—' }}
                                    </span>
                                </div>
                                <div class="label" id="kai-ratio-label">{{ $ratioLabel }}</div>
                            </div>
                            <i class="fa-solid fa-venus-mars icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-red" id="kai-mortalitas-card">
                            <div>
                                <div class="value" id="kai-mortalitas-value">
                                    {{ $mortalitasDisplay }}<small style="font-size:0.45em;">%</small>
                                </div>
                                <div class="label" id="kai-mortalitas-label">
                                    Mortalitas ({{ $formatNumber($totalKematian) }} ekor)
                                </div>
                            </div>
                            <i class="fa-solid fa-skull-crossbones icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-green" id="kai-penjualan-card">
                            <div>
                                <div class="value" id="kai-penjualan-value">Rp {!! $formatLargeNumber($totalPendapatan, false) !!}</div>
                                <div class="label" id="kai-penjualan-label">Total Pendapatan</div>
                            </div>
                            <i class="fa-solid fa-money-bill-wave icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-yellow" id="kai-pakan-card">
                            <div>
                                <div class="value" id="kai-pakan-value">
                                    <span class="kai-main">{{ $formatNumber($totalPakanKg, 2) }}<small style="font-size:0.45em;"> kg</small></span>
                                    <span class="kai-sub">Rp {{ $formatNumber($totalBiayaPakan) }}</span>
                                </div>
                                <div class="label" id="kai-pakan-label">Pakan Terpakai</div>
                                {{-- @if ($latestPakanPrice)
                                    <div class="footnote">Rp {{ $formatNumber($latestPakanPrice) }} / kg</div>
                                @endif --}}
                            </div>
                            <i class="fa-solid fa-bowl-food icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-violet" id="kai-vitamin-card">
                            <div>
                                <div class="value" id="kai-vitamin-value">
                                    <span class="kai-main">{{ $formatNumber($totalVitaminL, 2) }}<small style="font-size:0.45em;"> L</small></span>
                                    <span class="kai-sub">Rp {{ $formatNumber($totalBiayaVitamin) }}</span>
                                </div>
                                <div class="label" id="kai-vitamin-label">Vitamin Terpakai</div>
                                {{-- @if ($latestVitaminPrice)
                                    <div class="footnote">Rp {{ $formatNumber($latestVitaminPrice) }} / L</div>
                                @endif --}}
                            </div>
                            <i class="fa-solid fa-capsules icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-cyan" id="kai-fcr-card">
                            <div>
                                <div class="value" id="kai-fcr-value">
                                    {{ $fcrValue !== null ? $formatNumber($fcrValue, 2) : '—' }}<small style="font-size:0.45em;"> FCR</small>
                                </div>
                                <div class="label" id="kai-fcr-label"> </div>
                                <div class="footnote">{{ $formatNumber($totalPakanKg, 2) }} kg / {{ $formatNumber($totalTelur) }} butir</div>
                            </div>
                            <i class="fa-solid fa-balance-scale icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-brown" id="kai-pengeluaran-card">
                            <div>
                                <div class="value" id="kai-pengeluaran-value">Rp {!! $formatLargeNumber($totalPengeluaran, false) !!}</div>
                                <div class="label" id="kai-pengeluaran-label">Total Pengeluaran</div>
                                @if ($totalBiayaPakan || $totalBiayaVitamin)
                                    <div class="footnote">
                                        {{-- @if ($totalBiayaPakan)
                                            Pakan Rp {{ $formatNumber($totalBiayaPakan) }}
                                        @endif
                                        @if ($totalBiayaPakan && $totalBiayaVitamin)
                                            <span class="mx-1">•</span>
                                        @endif
                                        @if ($totalBiayaVitamin)
                                            Vitamin Rp {{ $formatNumber($totalBiayaVitamin) }}
                                        @endif --}}
                                    </div>
                                @endif
                            </div>
                            <i class="fa-solid fa-credit-card icon-faint"></i>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $penjualanGenderRaw = optional($todayLaporan)->jenis_kelamin_penjualan;
                $defaultCampuranJantan = null;
                $defaultCampuranBetina = null;
                $defaultJenisKelaminPenjualan = $penjualanGenderRaw;

                if (is_string($penjualanGenderRaw) && str_starts_with(strtolower($penjualanGenderRaw), 'campuran')) {
                    if (preg_match('/jantan\s*=\s*(\d+)/i', $penjualanGenderRaw, $mJ)) {
                        $defaultCampuranJantan = (int) $mJ[1];
                    }
                    if (preg_match('/betina\s*=\s*(\d+)/i', $penjualanGenderRaw, $mB)) {
                        $defaultCampuranBetina = (int) $mB[1];
                    }
                    if ($defaultCampuranJantan === null || $defaultCampuranBetina === null) {
                        if (preg_match('/campuran\s*:?\s*(\d+)\s*[:|,]\s*(\d+)/i', $penjualanGenderRaw, $legacy)) {
                            $defaultCampuranJantan = $defaultCampuranJantan ?? (int) $legacy[1];
                            $defaultCampuranBetina = $defaultCampuranBetina ?? (int) $legacy[2];
                        }
                    }
                    $defaultJenisKelaminPenjualan = 'campuran';
                }
            @endphp

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
                'defaultJenisKelaminPenjualan' => old('jenis_kelamin_penjualan', $defaultJenisKelaminPenjualan),
                'defaultPenjualanJantanCampuran' => old('penjualan_puyuh_jantan', $defaultCampuranJantan),
                'defaultPenjualanBetinaCampuran' => old('penjualan_puyuh_betina', $defaultCampuranBetina),
                'defaultCatatan' => old('catatan_kejadian', optional($todayLaporan)->catatan_kejadian),
                'defaultHargaPerButir' => old(
                    'harga_penjualan',
                    optional($todayLaporan)->harga_per_butir ?? $produksi->harga_per_pcs ?? null
                ),
                'tabVariant' => 'puyuh',
                'feedOptions' => $feedOptions,
                'vitaminOptions' => $vitaminOptions,
                'analyticsConfig' => [
                    'analyticsKey' => 'puyuh',
                    'title' => 'Grafik & Analisis Produksi Puyuh',
                    'subtitle' => 'Monitor Telur, Pakan, dan Kematian per hari',
                    'dataset' => $puyuhAnalyticsSeries,
                    'stats' => $puyuhAnalyticsStats,
                    'analysis' => $puyuhAnalysisNotes,
                    'dateRange' => $puyuhAnalyticsRange,
                    'seriesDefinitions' => [
                        ['key' => 'telur', 'field' => 'telur', 'label' => 'Telur (butir)', 'color' => '#2563eb'],
                        ['key' => 'pakan', 'field' => 'pakan', 'label' => 'Pakan (kg)', 'color' => '#22c55e'],
                        ['key' => 'vitamin', 'field' => 'vitamin', 'label' => 'Vitamin (L)', 'color' => '#a855f7'],
                        ['key' => 'kematian', 'field' => 'kematian', 'label' => 'Kematian (ekor)', 'color' => '#ef4444'],
                        ['key' => 'penjualan', 'field' => 'penjualan', 'label' => 'Penjualan Puyuh (ekor)', 'color' => '#f97316'],
                    ],
                    'activeSeries' => ['telur', 'pakan', 'kematian'],
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
                            @if ($laporanHarian->isEmpty())
                                <div class="text-muted small"><i class="fa-solid fa-clipboard-list me-1"></i>Belum ada pencatatan.</div>
                            @else
                                @php
                                    $visibleLaporan = $laporanHarian->filter(fn ($item) => $item->tampilkan_di_histori !== false);
                                @endphp
                                @if ($visibleLaporan->isEmpty())
                                    <div class="text-muted small"><i class="fa-solid fa-eye-slash me-1"></i>Data pernah dicatat namun disembunyikan. Input ulang untuk menampilkan kembali.</div>
                                @else
                                @php
                                    $dailyKaiSnapshots = $visibleLaporan
                                        ->groupBy(function ($item) {
                                            return optional($item->tanggal)->toDateString();
                                        })
                                        ->map(function ($items) use ($produksi) {
                                            $population = $items->filter(function ($entry) {
                                                return !is_null($entry->jumlah_burung);
                                            })->max('jumlah_burung');

                                            if (!$population) {
                                                $population = $produksi->jumlah_indukan ?? 0;
                                            }

                                            $totalTelur = $items->sum('produksi_telur');
                                            $totalPakan = $items->sum('konsumsi_pakan_kg');
                                            $totalVitamin = $items->sum('vitamin_terpakai');
                                            $totalKematian = $items->sum('jumlah_kematian');
                                            $mortalityPercent = $population > 0 ? ($totalKematian / $population) * 100 : 0;

                                            return [
                                                'population' => $population,
                                                'telur' => $totalTelur,
                                                'pakan' => $totalPakan,
                                                'vitamin' => $totalVitamin,
                                                'kematian' => $totalKematian,
                                                'mortality_percent' => $mortalityPercent,
                                            ];
                                        });
                                @endphp
                                <div class="list-timeline">
                                    @foreach ($visibleLaporan as $laporan)
                                        @php
                                            // Create separate entries for each data type that has values
                                            $entries = [];
                                            $parsePenjualanCampuran = function ($value) {
                                                if (!is_string($value)) {
                                                    return null;
                                                }
                                                $value = strtolower(trim($value));
                                                if (!str_starts_with($value, 'campuran')) {
                                                    return null;
                                                }
                                                $jantan = null;
                                                $betina = null;
                                                if (preg_match('/jantan\s*=\s*(\d+)/', $value, $m)) {
                                                    $jantan = (int) $m[1];
                                                }
                                                if (preg_match('/betina\s*=\s*(\d+)/', $value, $m)) {
                                                    $betina = (int) $m[1];
                                                }
                                                if (($jantan === null || $betina === null) && preg_match('/campuran\s*:?\s*(\d+)\s*[:|,]\s*(\d+)/', $value, $legacy)) {
                                                    $jantan = $jantan === null ? (int) $legacy[1] : $jantan;
                                                    $betina = $betina === null ? (int) $legacy[2] : $betina;
                                                }
                                                if ($jantan === null && $betina === null) {
                                                    return null;
                                                }
                                                return [
                                                    'jantan' => max(0, $jantan ?? 0),
                                                    'betina' => max(0, $betina ?? 0),
                                                ];
                                            };
                                            
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
                                                    'unit' => 'kg pakan',
                                                    'meta' => [
                                                        'biaya' => $laporan->biaya_pakan_harian,
                                                        'harga' => $laporan->harga_pakan_per_kg,
                                                    ],
                                                ];
                                            }
                                            
                                            if (($laporan->vitamin_terpakai ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'vitamin',
                                                    'value' => $laporan->vitamin_terpakai,
                                                    'unit' => 'L vitamin',
                                                    'meta' => [
                                                        'biaya' => $laporan->biaya_vitamin_harian,
                                                        'harga' => $laporan->harga_vitamin_per_liter,
                                                    ],
                                                ];
                                            }
                                            
                                            if (($laporan->penjualan_puyuh_ekor ?? 0) > 0) {
                                                $jantan = $laporan->penjualan_puyuh_jantan ?? 0;
                                                $betina = $laporan->penjualan_puyuh_betina ?? 0;
                                                $campuranParsed = null;
                                                $genderNormalized = 'puyuh'; // default

                                                if ($jantan > 0 && $betina > 0) {
                                                    $genderNormalized = 'campuran';
                                                    $campuranParsed = ['jantan' => $jantan, 'betina' => $betina];
                                                } elseif ($jantan > 0) {
                                                    $genderNormalized = 'jantan';
                                                } elseif ($betina > 0) {
                                                    $genderNormalized = 'betina';
                                                }

                                                $entries[] = [
                                                    'type' => 'penjualan',
                                                    'value' => $laporan->penjualan_puyuh_ekor,
                                                    'unit' => 'ekor terjual',
                                                    'meta' => [
                                                        'gender' => $genderNormalized,
                                                        'campuran' => $campuranParsed,
                                                        'pendapatan' => $laporan->pendapatan_harian,
                                                        'harga' => $laporan->harga_per_butir,
                                                    ],
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
                                            
                                            // Selalu tambahkan entri laporan jika ada catatan
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
                                                    'penjualan' => 'fa-cash-register',
                                                    'kematian' => 'fa-skull-crossbones',
                                                    'laporan' => 'fa-file-lines',
                                                ][$entry['type']] ?? 'fa-file-lines';

                                                $tanggalEntry = $laporan->tanggal->locale('id')->translatedFormat('d F Y');
                                                
                                                if ($entry['type'] === 'pakan') {
                                                    $consumed = $formatNumber($entry['value'], 2);
                                                    $remaining = $formatNumber($laporan->sisa_pakan_kg ?? 0, 2);
                                                    $ringkasan = $consumed . ' kg pakan (sisa: ' . $remaining . ' kg)';
                                                    $feedCost = $entry['meta']['biaya'] ?? null;
                                                    if ($feedCost) {
                                                        $ringkasan .= ' — Rp ' . $formatNumber($feedCost);
                                                        $unitPrice = $entry['meta']['harga'] ?? null;
                                                        if ($unitPrice) {
                                                            $ringkasan .= ' (Rp ' . $formatNumber($unitPrice) . ' / kg)';
                                                        }
                                                    }
                                                } elseif ($entry['type'] === 'vitamin') {
                                                    $consumed = $formatNumber($entry['value'], 2);
                                                    $remaining = $formatNumber($laporan->sisa_vitamin_liter ?? 0, 2);
                                                    $ringkasan = $consumed . ' L vitamin (sisa: ' . $remaining . ' L)';
                                                    $vitCost = $entry['meta']['biaya'] ?? null;
                                                    if ($vitCost) {
                                                        $ringkasan .= ' — Rp ' . $formatNumber($vitCost);
                                                        $unitPrice = $entry['meta']['harga'] ?? null;
                                                        if ($unitPrice) {
                                                            $ringkasan .= ' (Rp ' . $formatNumber($unitPrice) . ' / L)';
                                                        }
                                                    }
                                                } elseif ($entry['type'] === 'penjualan') {
                                                    $pendapatan = $entry['meta']['pendapatan'] ?? null;
                                                    $harga = $entry['meta']['harga'] ?? null;
                                                    $ringkasan = $formatNumber($entry['value']) . ' ekor terjual';
                                                    if ($pendapatan !== null && $pendapatan !== '') {
                                                        $ringkasan .= ' — Rp ' . $formatNumber($pendapatan);
                                                    } elseif ($harga !== null && $harga !== '') {
                                                        $ringkasan .= ' @ Rp ' . $formatNumber($harga);
                                                    }
                                                } elseif ($entry['type'] === 'kematian') {
                                                    $gender = $entry['meta']['gender'] ?? null;
                                                    $genderLabel = $gender ? ' (' . ucfirst($gender) . ')' : '';
                                                    $ringkasan = $formatNumber($entry['value']) . ' ekor kematian' . $genderLabel;
                                                } else {
                                                    $ringkasan = $entry['value'] 
                                                        ? $formatNumber($entry['value'], $entry['type'] === 'pakan' || $entry['type'] === 'vitamin' ? 2 : 0) . ' ' . $entry['unit']
                                                        : 'Laporan';
                                                }

                                                $createdAtFormatted = $laporan->dibuat_pada
                                                    ? 'Tercatat ' . $laporan->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A')
                                                    : '—';

                                                // Sertakan informasi jenis kelamin untuk entri penjualan
                                                if ($entry['type'] === 'penjualan') {
                                                    $genderRaw = trim((string)($laporan->jenis_kelamin_penjualan ?? ''));
                                                    if ($genderRaw === '') {
                                                        // Fallback ke kolom breakdown jika jenis_kelamin_penjualan kosong
                                                        $jantan = $laporan->penjualan_puyuh_jantan ?? 0;
                                                        $betina = $laporan->penjualan_puyuh_betina ?? 0;
                                                        if ($jantan > 0 && $betina > 0) {
                                                            $genderNormalized = 'campuran';
                                                            $campuranCounts = ['jantan' => $jantan, 'betina' => $betina];
                                                        } elseif ($jantan > 0) {
                                                            $genderNormalized = 'jantan';
                                                            $campuranCounts = null;
                                                        } elseif ($betina > 0) {
                                                            $genderNormalized = 'betina';
                                                            $campuranCounts = null;
                                                        } else {
                                                            $genderNormalized = 'puyuh';
                                                            $campuranCounts = null;
                                                        }
                                                    } else {
                                                        $campuranParsed = $parsePenjualanCampuran($genderRaw);
                                                        $genderNormalized = $campuranParsed ? 'campuran' : strtolower($genderRaw);
                                                        $campuranCounts = $campuranParsed;
                                                    }

                                                    if ($campuranCounts) {
                                                        $createdAtFormatted .= ' (Campuran : Jantan ' . $formatNumber($campuranCounts['jantan']) . ' & Betina ' . $formatNumber($campuranCounts['betina']) . ')';
                                                    } else {
                                                        if ($genderNormalized === 'jantan') {
                                                            $genderLabel = 'Jantan';
                                                        } elseif ($genderNormalized === 'betina') {
                                                            $genderLabel = 'Betina';
                                                        } elseif ($genderNormalized === 'puyuh') {
                                                            $genderLabel = 'Puyuh';
                                                        } elseif (in_array($genderNormalized, ['campuran', 'mix', 'mixed'])) {
                                                            $genderLabel = 'Campuran';
                                                        } else {
                                                            $genderLabel = $genderRaw !== '' ? $genderRaw : '';
                                                        }

                                                        if ($genderLabel !== '') {
                                                            $createdAtFormatted .= ' (' . $genderLabel . ')';
                                                        }
                                                    }
                                                }

                                                if ($entry['type'] === 'kematian' && !empty($entry['meta']['keterangan'])) {
                                                    $createdAtFormatted .= ' (' . $entry['meta']['keterangan'] . ')';
                                                }
                                            @endphp
                                            <div class="entry entry-{{ $entry['type'] }}">
                                                <div class="entry-left">
                                                    <div class="dot">
                                                        <i class="fa-solid {{ $entryIcon }}"></i>
                                                    </div>
                                                    <div class="entry-body">
                                                        <div class="title">{{ $tanggalEntry }} — {{ $ringkasan }}</div>
                                                        <small>{{ $createdAtFormatted }}</small>
                                                    </div>
                                                </div>
                                                <div class="entry-right">
                                                    <div class="d-flex gap-2 align-items-center">
                                                        {{-- Catatan badge di histori dihilangkan sesuai permintaan --}}

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
                                                                $snapshotKey = optional($laporan->tanggal)->toDateString();
                                                                $snapshot = $dailyKaiSnapshots->get($snapshotKey) ?? [
                                                                    'population' => $produksi->jumlah_indukan ?? 0,
                                                                    'telur' => 0,
                                                                    'pakan' => 0,
                                                                    'vitamin' => 0,
                                                                    'kematian' => 0,
                                                                    'mortality_percent' => 0,
                                                                ];
                                                                $populasiValue = $formatNumber($snapshot['population']);
                                                                $feedValue = $formatNumber($snapshot['pakan'], 2);
                                                                $deathValue = $formatNumber($snapshot['kematian']);
                                                                $telurValue = $formatNumber($snapshot['telur']);
                                                                $vitaminValue = $formatNumber($snapshot['vitamin'], 2);
                                                                $mortalityPercent = number_format($snapshot['mortality_percent'], 2, ',', '.');
                                                                $salesQty = $formatNumber($laporan->penjualan_puyuh_ekor ?? 0);
                                                                $salesRevenue = $laporan->pendapatan_harian ?? (($laporan->penjualan_puyuh_ekor ?? 0) * ($laporan->harga_per_butir ?? 0));
                                                                $salesRevenueValue = $formatNumber($salesRevenue);
                                                                $salesPrice = $formatNumber($laporan->harga_per_butir ?? 0);
                                                                $feedCostValue = $formatNumber($laporan->biaya_pakan_harian ?? 0);
                                                                $feedUnitPriceValue = $formatNumber($laporan->harga_pakan_per_kg ?? 0);
                                                                $vitaminCostValue = $formatNumber($laporan->biaya_vitamin_harian ?? 0);
                                                                $vitaminUnitPriceValue = $formatNumber($laporan->harga_vitamin_per_liter ?? 0);
                                                            @endphp
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-info detail-catatan-btn"
                                                                data-catatan="{{ e($catatanDetail) }}"
                                                                data-tanggal="{{ e($catatanTanggal) }}"
                                                                data-created="{{ e($catatanCreated) }}"
                                                                data-user="{{ e($catatanUser) }}"
                                                                data-populasi="{{ e($populasiValue) }}"
                                                                data-feed="{{ e($feedValue) }}"
                                                                data-death="{{ e($deathValue) }}"
                                                                data-telur="{{ e($telurValue) }}"
                                                                data-vitamin="{{ e($vitaminValue) }}"
                                                                data-mortality="{{ e($mortalityPercent) }}"
                                                                data-feed-cost="{{ e($feedCostValue) }}"
                                                                data-feed-price="{{ e($feedUnitPriceValue) }}"
                                                                data-vitamin-cost="{{ e($vitaminCostValue) }}"
                                                                data-vitamin-price="{{ e($vitaminUnitPriceValue) }}"
                                                                data-sales-qty="{{ e($salesQty) }}"
                                                                data-sales-revenue="{{ e($salesRevenueValue) }}"
                                                                data-sales-price="{{ e($salesPrice) }}">
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
                                                                <input type="hidden" name="reset_tab" value="">
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

            // Function to filter history entries based on active tab
            function filterHistoryEntries(activeTabId) {
                const tabTitles = {
                    'telur': 'Telur',
                    'penjualan': 'Penjualan',
                    'pakan': 'Pakan',
                    'vitamin': 'Vitamin',
                    'kematian': 'Kematian',
                    'laporan': 'Laporan',
                    'analytics': 'Grafik & Analisis'
                };

                const tabColors = {
                    'telur': 'text-telur',
                    'penjualan': 'text-penjualan',
                    'pakan': 'text-pakan',
                    'vitamin': 'text-vitamin',
                    'kematian': 'text-kematian',
                    'laporan': 'text-laporan',
                    'analytics': 'text-primary'
                };

                // Update title
                if (activeTabId && tabTitles[activeTabId]) {
                    historyTitle.innerHTML = `(<span class="${tabColors[activeTabId]}">${tabTitles[activeTabId]}</span>)`;
                } else {
                    historyTitle.innerHTML = '(<span class="text-muted">Semua</span>)';
                }

                // Filter entries based on tab-specific data
                historyEntries.forEach(entry => {
                    let shouldShow = false;

                    if (!activeTabId) {
                        // Show all entries when no specific tab
                        shouldShow = true;
                    } else {
                        // Check if entry has data relevant to the active tab
                        const entryData = entry.querySelector('.entry-body .title').textContent;

                        switch (activeTabId) {
                            case 'telur':
                                shouldShow = entryData.includes('butir telur') && entry.classList.contains('entry-telur');
                                break;
                            case 'pakan':
                                shouldShow = entryData.includes('kg pakan') && entry.classList.contains('entry-pakan');
                                break;
                            case 'penjualan':
                                shouldShow = entryData.includes('ekor terjual') && entry.classList.contains('entry-penjualan');
                                break;
                            case 'vitamin':
                                shouldShow = entryData.includes('L vitamin') && entry.classList.contains('entry-vitamin');
                                break;
                            case 'kematian':
                                shouldShow = entryData.includes('ekor kematian') && entry.classList.contains('entry-kematian');
                                break;
                            case 'laporan':
                                shouldShow = entry.classList.contains('entry-laporan');
                                break;
                            case 'analytics':
                                shouldShow = true;
                                break;
                        }
                    }

                    entry.style.display = shouldShow ? 'flex' : 'none';
                });
            }

            // Helper to sync hidden active tab input when we manually switch tabs
            function updateActiveTabInput(tabId) {
                const activeTabInput = document.getElementById('activeTabInput');
                if (activeTabInput && tabId) {
                    activeTabInput.value = tabId;
                }
            }

            // Restore active tab from localStorage
            let savedActiveTab;
            try {
                savedActiveTab = localStorage.getItem('activeProduksiTab');
            } catch (e) {
                // localStorage blocked by tracking prevention
                savedActiveTab = null;
            }
            if (savedActiveTab) {
                const savedTabElement = document.querySelector(`#pencatatanTabs .nav-link[data-bs-target="#${savedActiveTab}"]`);
                if (savedTabElement) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(savedTabElement);
                        tabInstance.show();
                    } else {
                        // Fallback if Bootstrap JS is unavailable
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

            // Listen for form tab changes
            const formTabs = document.querySelectorAll('#pencatatanTabs .nav-link');
            formTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.getAttribute('data-bs-target').substring(1);
                    // Save active tab to localStorage
                    try {
                        localStorage.setItem('activeProduksiTab', targetId);
                    } catch (e) {
                        // localStorage blocked by tracking prevention
                    }
                    filterHistoryEntries(targetId);
                });
            });

            // Set initial filter based on active tab (after restoration)
            const activeTab = document.querySelector('#pencatatanTabs .nav-link.active');
            if (activeTab) {
                const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                updateActiveTabInput(activeTabId);
                filterHistoryEntries(activeTabId);
            } else {
                filterHistoryEntries('telur');
                updateActiveTabInput('telur');
            }

            // Helper get active tab id
            const getActiveTabId = () => {
                const active = document.querySelector('#pencatatanTabs .nav-link.active');
                if (!active) return 'telur';
                const target = active.getAttribute('data-bs-target') || '';
                return target.startsWith('#') ? target.substring(1) : (target || 'telur');
            };

            // Confirmation for reset and delete actions
            document.querySelectorAll('.reset-laporan-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const tabInput = form.querySelector('input[name="reset_tab"]') || (() => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'reset_tab';
                        form.appendChild(hidden);
                        return hidden;
                    })();
                    tabInput.value = getActiveTabId();
                    
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
                    const populasi = data.populasi || '-';
                    const feed = data.feed || '-';
                    const mortality = data.mortality || '0,00';
                    const death = data.death || '0';
                    const telur = data.telur || '0';
                    const vitamin = data.vitamin || '0,00';
                    const feedCost = data.feedCost || '0';
                    const feedPrice = data.feedPrice || '';
                    const vitaminCost = data.vitaminCost || '0';
                    const vitaminPrice = data.vitaminPrice || '';
                    const salesQty = data.salesQty || '0';
                    const salesRevenue = data.salesRevenue || '0';
                    const salesPrice = data.salesPrice || '';
                    const catatanRaw = data.catatan || '-';
                    const catatanHtml = escapeHtml(catatanRaw).replace(/\n/g, '<br>');

                    const detailHtml = `
                        <div class="swal-detail-card">
                            <div class="swal-detail-header">
                                <div class="title">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                    <span>Detail Laporan Harian</span>
                                </div>
                            </div>
                            <div class="swal-detail-body">
                                <div class="swal-detail-section">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                                        <div class="text-start">
                                            <small class="text-muted"><i class="fa-solid fa-calendar-days me-1"></i>Tanggal Laporan</small>
                                            <div class="fw-semibold">${escapeHtml(tanggal)}</div>
                                        </div>
                                        <div class="text-md-end">
                                            <small class="text-muted"><i class="fa-solid fa-user me-1"></i>Dibuat Oleh</small>
                                            <div class="fw-semibold">${escapeHtml(user)}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swal-detail-section">
                                    <div class="swal-detail-stats">
                                        <div class="swal-detail-stat-card">
                                            <div class="stat-label">Jumlah Puyuh</div>
                                            <div class="stat-value">${escapeHtml(populasi)}</div>
                                        </div>
                                        <div class="swal-detail-stat-card">
                                            <div class="stat-label">Jumlah Telur</div>
                                            <div class="stat-value">${escapeHtml(telur)}</div>
                                        </div>
                                        <div class="swal-detail-stat-card">
                                            <div class="stat-label">Kematian</div>
                                            <div class="stat-value">${escapeHtml(death)}</div>
                                        </div>
                                        <div class="swal-detail-stat-card">
                                            <div class="stat-label">Total Penjualan</div>
                                            <div class="stat-value">${escapeHtml(salesQty)}<small class="text-muted"> ekor</small></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swal-detail-section">
                                    <div class="fw-semibold mb-2">Catatan Lengkap</div>
                                    <div class="swal-detail-note-card">
                                        <div class="card-body">
                                            <div class="note-content">${catatanHtml}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swal-detail-footer">
                                    <div>Dibuat ${escapeHtml(created)}</div>
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

