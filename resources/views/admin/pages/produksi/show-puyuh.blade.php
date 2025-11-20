@extends('admin.layouts.app')

@section('title', 'Detail Produksi Puyuh - ' . ($produksi->batch_produksi_id ?? 'Tanpa Kode Batch'))

@push('styles')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-puyuh-produksi.css') }}">
@endpush

@section('content')
    @php
        $formatNumber = fn ($value, $decimals = 0) => number_format((float) ($value ?? 0), $decimals, ',', '.');

        $startDate = $produksi->tanggal_mulai ?? $produksi->tanggal ?? optional($produksi->pembesaran)->tanggal_siap;
        $startDateFormatted = $startDate ? \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y') : '-';
        $endDate = $produksi->tanggal_akhir;
        $endDateFormatted = $endDate ? \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y') : '-';

        $kandangName = $produksi->kandang->nama_kandang ?? 'nama kandang';
        $batchCode = $produksi->batch_produksi_id ?? 'Tanpa Kode Batch';
        $initialPopulation = $produksi->jumlah_indukan ?? (($produksi->jumlah_jantan ?? 0) + ($produksi->jumlah_betina ?? 0));
        $totalTelur = $summary['total_telur'] ?? 0;
        $totalPakanKg = $laporanHarian->sum('konsumsi_pakan_kg');
        $totalVitaminL = $laporanHarian->sum('vitamin_terpakai');
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
                        <div class="card-kai kai-indigo" id="kai-telur-card">
                            <div>
                                <div class="value" id="kai-telur-value">{{ $formatNumber($totalTelur) }}</div>
                                <div class="label" id="kai-telur-label">Total Telur (butir)</div>
                            </div>
                            <i class="fa-solid fa-chart-line icon-faint"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="card-kai kai-yellow" id="kai-pakan-card">
                            <div>
                                <div class="value" id="kai-pakan-value">
                                    <span class="kai-main">{{ $formatNumber($totalPakanKg, 2) }} kg</span>
                                    <span class="kai-sub">{{ $formatNumber($totalVitaminL, 2) }} L</span>
                                </div>
                                <div class="label" id="kai-pakan-label">Pakan + Vitamin Terpakai</div>
                            </div>
                            <i class="fa-solid fa-bowl-food icon-faint"></i>
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
                'defaultKonsumsiPakan' => old('konsumsi_pakan_kg', optional($todayLaporan)->konsumsi_pakan_kg),
                'defaultSisaPakan' => old('sisa_pakan_kg', optional($todayLaporan)->sisa_pakan_kg),
                'defaultVitaminTerpakai' => old('vitamin_terpakai', optional($todayLaporan)->vitamin_terpakai),
                'defaultSisaVitamin' => old('sisa_vitamin_liter', optional($todayLaporan)->sisa_vitamin_liter),
                'defaultJumlahKematian' => old('jumlah_kematian', optional($todayLaporan)->jumlah_kematian),
                'defaultPenjualanPuyuh' => old('penjualan_puyuh_ekor', optional($todayLaporan)->penjualan_puyuh_ekor),
                'defaultPendapatan' => old('pendapatan_harian', optional($todayLaporan)->pendapatan_harian),
                'defaultCatatan' => old('catatan_kejadian', optional($todayLaporan)->catatan_kejadian),
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
                                <div class="text-muted small">Belum ada pencatatan.</div>
                            @else
                                <div class="list-timeline">
                                    @foreach ($laporanHarian as $laporan)
                                        @php
                                            // Create separate entries for each data type that has values
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
                                            
                                            if (($laporan->jumlah_kematian ?? 0) > 0) {
                                                $entries[] = [
                                                    'type' => 'kematian',
                                                    'value' => $laporan->jumlah_kematian,
                                                    'unit' => 'ekor kematian',
                                                    'meta' => [
                                                        'gender' => $laporan->jenis_kelamin_kematian,
                                                    ],
                                                ];
                                            }
                                            
                                            // If no specific data but has notes, show as general laporan
                                            if (empty($entries) && $laporan->catatan_kejadian) {
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
                                                    'kematian' => 'fa-skull-crossbones',
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
                                                } else {
                                                    $ringkasan = $entry['value'] 
                                                        ? $formatNumber($entry['value'], $entry['type'] === 'pakan' || $entry['type'] === 'vitamin' ? 2 : 0) . ' ' . $entry['unit']
                                                        : 'Laporan';
                                                }

                                                $createdAtFormatted = $laporan->dibuat_pada
                                                    ? 'Tercatat ' . $laporan->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A')
                                                    : '—';
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
                                                        @if ($laporan->catatan_kejadian && $entry['type'] !== 'laporan')
                                                            <span class="badge bg-light text-muted">{{ \Illuminate\Support\Str::limit($laporan->catatan_kejadian, 48) }}</span>
                                                        @endif

                                                        @if ($entry['type'] === 'laporan')
                                                            @php
                                                                $catatanDetail = $entry['meta']['catatan'] ?? $laporan->catatan_kejadian;
                                                                $catatanTanggal = $entry['meta']['tanggal'] ?? $laporan->tanggal->locale('id')->translatedFormat('d F Y');
                                                                $catatanCreated = $entry['meta']['created_at'] ?? ($laporan->dibuat_pada
                                                                    ? $laporan->dibuat_pada->locale('id')->format('d/m/Y, g:i:s A')
                                                                    : '—');
                                                            @endphp
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-info"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#detailCatatanModal"
                                                                data-catatan="{{ e($catatanDetail) }}"
                                                                data-tanggal="{{ e($catatanTanggal) }}"
                                                                data-created="{{ e($catatanCreated) }}">
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
    
    <div class="modal fade" id="detailCatatanModal" tabindex="-1" aria-labelledby="detailCatatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailCatatanModalLabel">
                        Detail Catatan Laporan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Tanggal</dt>
                        <dd class="col-sm-9" id="detailCatatanTanggal">-</dd>
                        <dt class="col-sm-3">Tercatat</dt>
                        <dd class="col-sm-9" id="detailCatatanCreated">-</dd>
                        <dt class="col-sm-3">Catatan</dt>
                        <dd class="col-sm-9">
                            <div id="detailCatatanContent" class="border rounded p-3 bg-light" style="white-space: pre-wrap;"></div>
                        </dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

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
                    'pakan': 'Pakan',
                    'vitamin': 'Vitamin',
                    'kematian': 'Kematian',
                    'laporan': 'Laporan'
                };

                const tabColors = {
                    'telur': 'text-telur',
                    'pakan': 'text-pakan',
                    'vitamin': 'text-vitamin',
                    'kematian': 'text-kematian',
                    'laporan': 'text-laporan'
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
                            case 'vitamin':
                                shouldShow = entryData.includes('L vitamin') && entry.classList.contains('entry-vitamin');
                                break;
                            case 'kematian':
                                shouldShow = entryData.includes('ekor kematian') && entry.classList.contains('entry-kematian');
                                break;
                            case 'laporan':
                                shouldShow = entry.classList.contains('entry-laporan');
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
            const savedActiveTab = localStorage.getItem('activeProduksiTab');
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
                    localStorage.setItem('activeProduksiTab', targetId);
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

            // Confirmation for reset and delete actions
            document.querySelectorAll('.reset-laporan-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Konfirmasi Reset',
                        text: 'Yakin ingin mereset entri ini? Nilai pada entri ini akan dikembalikan ke 0 dan Menu KAI akan terupdate.',
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
                        text: 'Yakin ingin menghapus histori ini? Aksi ini akan menghapus entri dari rekap, tetapi tidak mengubah total kumulatif lain selain mengurangi kontribusi entri yang dihapus.',
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

            const detailModalEl = document.getElementById('detailCatatanModal');
            if (detailModalEl) {
                detailModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    if (!button) {
                        return;
                    }

                    const catatan = button.getAttribute('data-catatan') || '-';
                    const tanggal = button.getAttribute('data-tanggal') || '-';
                    const created = button.getAttribute('data-created') || '-';

                    const contentEl = detailModalEl.querySelector('#detailCatatanContent');
                    const tanggalEl = detailModalEl.querySelector('#detailCatatanTanggal');
                    const createdEl = detailModalEl.querySelector('#detailCatatanCreated');

                    if (contentEl) {
                        contentEl.textContent = catatan;
                    }
                    if (tanggalEl) {
                        tanggalEl.textContent = tanggal;
                    }
                    if (createdEl) {
                        createdEl.textContent = created;
                    }
                });
            }
        });
    </script>
@endpush

