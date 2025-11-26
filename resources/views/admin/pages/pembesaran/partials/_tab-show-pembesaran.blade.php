{{-- Helper untuk format mortalitas --}}
@php
    $mortalitasFormatted = $mortalitas == floor($mortalitas) 
        ? number_format($mortalitas, 0) 
        : rtrim(rtrim(number_format($mortalitas, 2), '0'), '.');
    
    // Hitung FCR (Feed Conversion Ratio)
    // FCR = Total Pakan Konsumsi (kg) / Total Pertambahan Berat Badan (kg)
    // Rumus: FCR = Total Pakan / (Populasi Saat Ini × (Berat Akhir - Berat Awal))
    
    $beratAwalKg = 0.009; // Berat awal DOC puyuh standar = 9 gram = 0.009 kg
    $beratAkhirKg = ($pembesaran->berat_rata_rata ?? 0) / 1000; // konversi gram ke kg
    $pertambahanBeratPerEkor = $beratAkhirKg - $beratAwalKg; // kg per ekor
    $totalPertambahanBerat = $pertambahanBeratPerEkor * $populasiSaatIni; // total kg
    
    // Hitung FCR - cek jika total pakan dan pertambahan berat > 0
    if ($totalPakan > 0 && $totalPertambahanBerat > 0 && $beratAkhirKg > $beratAwalKg) {
        $fcr = $totalPakan / $totalPertambahanBerat;
    } else {
        $fcr = 0;
    }
    
    // Format FCR
    $fcrFormatted = $fcr == floor($fcr) 
        ? number_format($fcr, 0) 
        : number_format($fcr, 2);
    
    // Cek status batch - jika selesai, disable semua form
    $isStatusSelesai = strtolower($pembesaran->status_batch ?? 'aktif') === 'selesai';
    $disabledAttr = $isStatusSelesai ? 'disabled' : '';
    $readonlyAttr = $isStatusSelesai ? 'readonly' : '';
@endphp

@push('styles')
<style>
    .lopa-tab-pane {
        background-color: #ffffff;
        padding: 1.5rem;
        border-radius: 0 0 1rem 1rem;
        margin-top: -1px;
    }

    @media (max-width: 576px) {
        .lopa-tab-pane {
            padding: 1rem;
        }
    }

    /* Custom styling for sub-tabs */
    .lopa-subtabs .nav-link {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 #dee2e6 #dee2e6 !important;
        color: #6c757d !important;
    }
    .lopa-subtabs .nav-link.active {
        background-color: #fff !important;
        color: #495057 !important;
        border-color: #dee2e6 #dee2e6 #fff !important;
        border-bottom: 2px solid #fff !important;
    }

    /* Custom styling for main tabs */
    .lopa-nav-tabs .nav-link {
        border-color: #dee2e6 #dee2e6 #dee2e6 !important;
    }
    .lopa-nav-tabs .nav-link.active {
        background-color: #fff !important;
        color: #495057 !important;
        border-color: #dee2e6 #dee2e6 #fff !important;
        border-bottom: 2px solid #fff !important;
    }

    /* Modal styling for catatan */
    .catatan-content {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
    }

    /* Styling for detail button */
    .btn-detail-catatan {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
        transition: all 0.2s ease !important;
    }
    .btn-detail-catatan:hover {
        background-color: #5a6268 !important;
        border-color: #5a6268 !important;
        color: white !important;
    }
</style>
@endpush>

{{-- Notebook Container with Tabs --}}
<div class="notebook lopa-notebook">
    <ul class="nav nav-tabs lopa-nav-tabs" id="batchTabs" role="tablist" aria-label="Batch tabs">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-info" data-bs-toggle="tab" data-bs-target="#infoBatch" type="button" role="tab" aria-controls="infoBatch" aria-selected="true">
                <i class="fa-solid fa-circle-info"></i> Info Batch
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-daily" data-bs-toggle="tab" data-bs-target="#recordHarian" type="button" role="tab" aria-controls="recordHarian" aria-selected="false">
                <i class="fa-solid fa-clipboard-list"></i> Recording Harian
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-weekly" data-bs-toggle="tab" data-bs-target="#recordMingguan" type="button" role="tab" aria-controls="recordMingguan" aria-selected="false">
                <i class="fa-solid fa-calendar-week"></i> Recording Mingguan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-laporan" data-bs-toggle="tab" data-bs-target="#laporanBatch" type="button" role="tab" aria-controls="laporanBatch" aria-selected="false">
                <i class="fa-solid fa-file-lines"></i> Laporan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-chart" data-bs-toggle="tab" data-bs-target="#grafikAnalisis" type="button" role="tab" aria-controls="grafikAnalisis" aria-selected="false">
                <i class="fa-solid fa-chart-line"></i> Grafik & Analisis
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Tab 1: Info Batch --}}
        <div class="tab-pane fade show active lopa-tab-pane" id="infoBatch" role="tabpanel" aria-labelledby="tab-info">
            <div class="card mb-4 lopa-card">
                <div class="header-row lopa-header-row">
                    <div class="title-wrap lopa-title-wrap" style="text-align:left;">
                        <h5 class="section-title lopa-section-title">
                            <i class="fa-solid fa-book-open-reader" style="color:var(--accent)"></i> 
                            Informasi Batch Pembesaran
                        </h5>
                    </div>

                    <div class="right-corner d-flex align-items-center" style="gap:.6rem;">
                        <div class="status-compact lopa-status-compact">
                            <div class="icon lopa-icon">🐣</div>
                            <div class="text lopa-text">{{ number_format($populasiSaatIni) }} ekor</div>
                        </div>
                        <div class="status-compact lopa-status-compact">
                            <div class="icon lopa-icon">{{ $mortalitas > 5 ? '⚠️' : '✅' }}</div>
                            <div class="text lopa-text">{{ $mortalitasFormatted }}%</div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-4 mb-3">
                        <div class="note-panel h-100 lopa-note-panel">
                            <h5 class="mb-3 fw-bold">Detail Batch</h5>
                            <dl class="row kv mb-0 lopa-kv" style="font-size: 0.95rem;">
                                <dt class="col-sm-5">ID Batch:</dt>
                                <dd class="col-sm-7">{{ $pembesaran->batch_produksi_id }}</dd>
                                
                                <dt class="col-sm-5">Kandang:</dt>
                                <dd class="col-sm-7">{{ $pembesaran->kandang->nama_kandang ?? '-' }}</dd>
                                
                                <dt class="col-sm-5">Asal:</dt>
                                <dd class="col-sm-7">
                                    @if($pembesaran->penetasan_id && $pembesaran->penetasan)
                                        <span class="badge" style="background-color: #17a2b8; color: #ffffff;">
                                            <i class="fa-solid fa-egg me-1"></i>
                                            {{ $pembesaran->penetasan->batch }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-5">Tanggal Masuk:</dt>
                                <dd class="col-sm-7">{{ \Carbon\Carbon::parse($pembesaran->tanggal_masuk)->format('d/m/Y') }}</dd>
                                
                                <dt class="col-sm-5">Umur:</dt>
                                <dd class="col-sm-7">{{ (int)$umurHari }} hari</dd>
                                
                                <dt class="col-sm-5">Jenis Kelamin:</dt>
                                <dd class="col-sm-7">
                                    @php
                                        $jenisKelamin = strtolower($pembesaran->jenis_kelamin ?? 'campuran');
                                    @endphp
                                    @if($jenisKelamin === 'jantan')
                                        <span class="badge bg-primary">♂ Jantan</span>
                                    @elseif($jenisKelamin === 'betina')
                                        <span class="badge bg-danger">♀ Betina</span>
                                    @else
                                        <span class="badge bg-secondary">⚥ Campuran</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-5">Status:</dt>
                                <dd class="col-sm-7">
                                    @php
                                        $status = $pembesaran->status_batch ?? 'Aktif';
                                        $badgeClass = strtolower($status) === 'aktif' ? 'bg-success' : 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="col-lg-5 mb-3">
                        <div class="h-100">
                            <div class="info-stats mb-2 lopa-info-stats">
                                <div class="stat-card lopa-stat-card">
                                    <div class="value lopa-value">{{ number_format($populasiSaatIni) }}</div>
                                    <div class="label lopa-label">Populasi Saat Ini</div>
                                </div>
                                <div class="stat-card lopa-stat-card">
                                    <div class="value lopa-value">
                                        @if($fcr > 0)
                                            {{ $fcrFormatted }}
                                            @if($fcr <= 2.5)
                                                <span style="color: #28a745; font-size: 0.6rem;">●</span>
                                            @elseif($fcr <= 3.5)
                                                <span style="color: #0d6efd; font-size: 0.6rem;">●</span>
                                            @elseif($fcr <= 4.5)
                                                <span style="color: #ffc107; font-size: 0.6rem;">●</span>
                                            @else
                                                <span style="color: #dc3545; font-size: 0.6rem;">●</span>
                                            @endif
                                        @else
                                            <span style="font-size: 1.2rem; color: #6c757d;">-</span>
                                        @endif
                                    </div>
                                    <div class="label lopa-label" style="display: flex; align-items: center; justify-content: flex-start; gap: 0.25rem;">
                                        FCR
                                        @if($fcr == 0)
                                            <i class="fa-solid fa-info-circle" style="font-size: 0.7rem; color: #6c757d;" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-html="true"
                                               title="<strong>FCR akan muncul jika:</strong><br>1. Ada data konsumsi pakan<br>2. Berat rata-rata > 9 gram<br>3. Ada populasi aktif"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="stat-card lopa-stat-card">
                                    <div class="value lopa-value">{{ $pembesaran->berat_rata_rata ? number_format($pembesaran->berat_rata_rata, 0) : 0 }}g</div>
                                    <div class="label lopa-label">Berat Rata-rata</div>
                                </div>
                                <div class="stat-card lopa-stat-card">
                                    <div class="value lopa-value">
                                        {{ $mortalitasFormatted }}%
                                    </div>
                                    <div class="label lopa-label">Mortalitas</div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="status-panel lopa-status-panel">
                                    <div class="icon lopa-icon">
                                        <i class="fa-solid {{ $pembesaran->status_batch === 'Aktif' ? 'fa-check-circle' : 'fa-pause-circle' }}"></i>
                                    </div>
                                    <div class="content lopa-content">
                                        <div class="title lopa-title">
                                            {{ $pembesaran->status_batch }}
                                            <span class="pill lopa-status-pill">Umur {{ (int)$umurHari }} hari</span>
                                        </div>
                                        <div class="subtitle lopa-subtitle">Batch pembesaran berjalan normal</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 mb-3">
                        <div class="note-panel alt h-100 lopa-note-panel lopa-alt">
                            <h5 class="mb-3 fw-bold">Ringkasan Biaya</h5>
                            <div style="font-size: 0.9rem;">
                                <!-- Total Konsumsi Pakan -->
                                <div class="mb-3">
                                    <div class="text-muted mb-1" style="font-size: 0.85rem;">Total Konsumsi Pakan</div>
                                    <div class="fw-bold text-end" style="font-size: 1rem; font-weight: 700 !important;">
                                        <span id="info-total-pakan-kg" data-value="{{ $totalPakan }}">{{ number_format($totalPakan, 2) }}</span> kg
                                    </div>
                                </div>
                                
                                <!-- Total Biaya Pakan -->
                                <div class="mb-3">
                                    <div class="text-muted mb-1" style="font-size: 0.85rem;">Total Biaya Pakan</div>
                                    <div class="fw-bold" style="font-size: 1rem; font-weight: 700 !important; display: flex; justify-content: space-between; align-items: flex-start;">
                                        <span style="font-size: 0.75rem; color: #6c757d; margin-top: 0.1rem;">Rp</span>
                                        <span id="info-total-biaya-pakan" data-value="{{ $totalBiayaPakan }}" style="text-align: right;">{{ number_format($totalBiayaPakan, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Biaya Kesehatan -->
                                <div class="mb-3">
                                    <div class="text-muted mb-1" style="font-size: 0.85rem;">Biaya Kesehatan & Vaksinasi</div>
                                    <div class="fw-bold" style="font-size: 1rem; font-weight: 700 !important; display: flex; justify-content: space-between; align-items: flex-start;">
                                        <span style="font-size: 0.75rem; color: #6c757d; margin-top: 0.1rem;">Rp</span>
                                        <span id="info-total-biaya-kesehatan" data-value="{{ $totalBiayaKesehatan ?? 0 }}" style="text-align: right;">{{ number_format($totalBiayaKesehatan ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Total Keseluruhan -->
                                <div class="pt-3" style="border-top: 2px solid #495057;">
                                    <div class="text-muted mb-1" style="font-size: 0.85rem;">Total Biaya Keseluruhan</div>
                                    <div class="fw-bold" id="info-total-keseluruhan-wrapper" data-health-total="{{ $totalBiayaKesehatan ?? 0 }}" style="font-size: 1rem; font-weight: 800 !important; color: #198754; display: flex; justify-content: space-between; align-items: flex-start;">
                                        <span style="font-size: 0.75rem; margin-top: 0.1rem;">Rp</span>
                                        <span id="info-total-biaya-keseluruhan" data-value="{{ $totalBiayaPakan + ($totalBiayaKesehatan ?? 0) }}" style="text-align: right;">{{ number_format(($totalBiayaPakan + ($totalBiayaKesehatan ?? 0)), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mt-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fa-solid fa-file-invoice me-1"></i> Lihat Detail Biaya
                                    </button>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-success btn-sm w-100">
                                        <i class="fa-solid fa-file-csv me-1"></i> Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                @php
                    $user = auth()->user();
                    $isOwnerOrSuperAdmin = $user && ($user->peran === 'owner' || $user->peran === 'super_admin');
                    $isAktif = strtolower($pembesaran->status_batch ?? 'aktif') === 'aktif';
                    $targetUmur = 35;
                    $targetTercapai = $umurHari >= $targetUmur && (!$pembesaran->target_berat_akhir || $pembesaran->berat_rata_rata >= $pembesaran->target_berat_akhir);
                @endphp

                @if($isAktif)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info" role="alert" style="padding: 1rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                                        <i class="fa-solid fa-info-circle" style="font-size: 1.5rem; flex-shrink: 0;"></i>
                                        <div style="flex: 1;">
                                            @if($targetTercapai)
                                                <strong>Target Tercapai!</strong> Batch sudah mencapai target umur ({{ $umurHari }}/{{ $targetUmur }} hari) 
                                                @if($pembesaran->target_berat_akhir)
                                                    dan berat ({{ $pembesaran->berat_rata_rata }}/{{ $pembesaran->target_berat_akhir }}g)
                                                @endif
                                                . Anda dapat menyelesaikan batch ini.
                                            @else
                                                <strong>Batch Sedang Berjalan</strong> - 
                                                Umur: {{ $umurHari }}/{{ $targetUmur }} hari
                                                @if($pembesaran->target_berat_akhir)
                                                    | Berat: {{ $pembesaran->berat_rata_rata }}/{{ $pembesaran->target_berat_akhir }}g
                                                @endif
                                                @if($isOwnerOrSuperAdmin)
                                                    <br>
                                                    <span class="badge mt-2" style="background-color: #fbbf24; color: #000000a9;">
                                                        <i class="fa-solid fa-crown" style="margin-right: 0.35rem;"></i>Overdrive Mode: Anda dapat menyelesaikan kapan pun
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    @if($isOwnerOrSuperAdmin || $targetTercapai)
                                        <div style="flex-shrink: 0;">
                                            <form action="{{ route('admin.pembesaran.selesaikan', $pembesaran) }}" method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan batch ini? Status akan berubah menjadi Selesai.')">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" style="white-space: nowrap;">
                                                    <i class="fa-solid fa-check-circle me-1"></i> Selesaikan Batch
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($pembesaran->status_batch === 'Selesai')
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fa-solid fa-check-circle me-3" style="font-size: 1.5rem; flex-shrink: 0;"></i>
                                <div>
                                    <strong>Batch Selesai</strong> - 
                                    Diselesaikan pada: {{ $pembesaran->tanggal_selesai ? \Carbon\Carbon::parse($pembesaran->tanggal_selesai)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab 2: Recording Harian --}}
        <div class="tab-pane fade lopa-tab-pane" id="recordHarian" role="tabpanel" aria-labelledby="tab-daily">
            @if($isStatusSelesai)
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-lock me-3" style="font-size: 1.5rem; flex-shrink: 0;"></i>
                    <div>
                        <strong>Batch Sudah Selesai</strong> - Pencatatan tidak dapat dilakukan lagi. Anda hanya dapat melihat data historis.
                    </div>
                </div>
            @endif

            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h5 class="section-title lopa-section-title mb-0">
                        <i class="fa-solid fa-list-check" style="color:var(--accent)"></i>
                        Pencatatan Harian
                    </h5>
                    <span class="text-muted small">Pisahkan pencatatan harian berdasarkan kategori</span>
                </div>

                <ul class="nav nav-tabs lopa-subtabs gap-2 flex-wrap" id="dailySubTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="daily-subtab-pakan" data-bs-toggle="tab" data-bs-target="#dailyPanePakan" type="button" role="tab" aria-controls="dailyPanePakan" aria-selected="true">
                            <i class="fa-solid fa-bowl-food me-1"></i>Pakan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="daily-subtab-kematian" data-bs-toggle="tab" data-bs-target="#dailyPaneKematian" type="button" role="tab" aria-controls="dailyPaneKematian" aria-selected="false">
                            <i class="fa-solid fa-skull-crossbones me-1"></i>Kematian
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="dailySubTabsContent">
                    <div class="tab-pane fade show active" id="dailyPanePakan" role="tabpanel" aria-labelledby="daily-subtab-pakan">
                        @include('admin.pages.pembesaran.partials.daily.pakan-card')
                    </div>
                    <div class="tab-pane fade" id="dailyPaneKematian" role="tabpanel" aria-labelledby="daily-subtab-kematian">
                        @include('admin.pages.pembesaran.partials.daily.kematian-card')
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 3: Recording Mingguan --}}
        <div class="tab-pane fade lopa-tab-pane" id="recordMingguan" role="tabpanel" aria-labelledby="tab-weekly">
            @if($isStatusSelesai)
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-lock me-3" style="font-size: 1.5rem; flex-shrink: 0;"></i>
                    <div>
                        <strong>Batch Sudah Selesai</strong> - Pencatatan tidak dapat dilakukan lagi. Anda hanya dapat melihat data historis.
                    </div>
                </div>
            @endif

            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h5 class="section-title lopa-section-title mb-0">
                        <i class="fa-solid fa-calendar-week" style="color:var(--accent)"></i>
                        Pencatatan Mingguan
                    </h5>
                    <span class="text-muted small">Pantau data mingguan per kategori</span>
                </div>

                <ul class="nav nav-tabs lopa-subtabs gap-2 flex-wrap" id="weeklySubTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="weekly-subtab-sampling" data-bs-toggle="tab" data-bs-target="#weeklyPaneSampling" type="button" role="tab" aria-controls="weeklyPaneSampling" aria-selected="true">
                            <i class="fa-solid fa-weight-scale me-1"></i>Sampling Berat
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="weekly-subtab-monitoring" data-bs-toggle="tab" data-bs-target="#weeklyPaneMonitoring" type="button" role="tab" aria-controls="weeklyPaneMonitoring" aria-selected="false">
                            <i class="fa-solid fa-cloud-sun me-1"></i>Monitoring Lingkungan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="weekly-subtab-kesehatan" data-bs-toggle="tab" data-bs-target="#weeklyPaneKesehatan" type="button" role="tab" aria-controls="weeklyPaneKesehatan" aria-selected="false">
                            <i class="fa-solid fa-syringe me-1"></i>Kesehatan &amp; Vaksinasi
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="weeklySubTabsContent">
                    <div class="tab-pane fade show active" id="weeklyPaneSampling" role="tabpanel" aria-labelledby="weekly-subtab-sampling">
                        @include('admin.pages.pembesaran.partials.weekly.sampling-card')
                    </div>
                    <div class="tab-pane fade" id="weeklyPaneMonitoring" role="tabpanel" aria-labelledby="weekly-subtab-monitoring">
                        @include('admin.pages.pembesaran.partials.weekly.monitoring-card')
                    </div>
                    <div class="tab-pane fade" id="weeklyPaneKesehatan" role="tabpanel" aria-labelledby="weekly-subtab-kesehatan">
                        @include('admin.pages.pembesaran.partials.weekly.kesehatan-card')
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 4: Laporan --}}
        <div class="tab-pane fade lopa-tab-pane" id="laporanBatch" role="tabpanel" aria-labelledby="tab-laporan">
            @if($isStatusSelesai)
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-lock me-3" style="font-size: 1.5rem; flex-shrink: 0;"></i>
                    <div>
                        <strong>Batch Sudah Selesai</strong> - Pencatatan tidak dapat dilakukan lagi. Anda hanya dapat melihat data historis.
                    </div>
                </div>
            @endif

            <div class="card mb-4 lopa-card">
                <h5 class="section-title lopa-section-title">
                    <i class="fa-solid fa-file-lines" style="color:var(--accent)"></i> 
                    Generate Laporan Harian
                </h5>
                <form class="form-card p-3 lopa-form-card" id="form-laporan-harian" aria-label="Form laporan harian">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Tanggal Laporan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_laporan" id="tanggal_laporan" value="{{ date('Y-m-d') }}" {{ $disabledAttr }} required />
                            <small class="form-text text-muted">Tanggal laporan dibuat atau dirangkum.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Catatan Laporan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan" id="catatan_laporan" rows="6" placeholder="Klik tombol 'Generate Catatan' untuk membuat laporan otomatis berdasarkan data pakan dan kematian hari ini..." {{ $disabledAttr }} required></textarea>
                            <small class="form-text text-muted">
                                <i class="fa-solid fa-lightbulb"></i> Tip: Klik tombol <strong>Generate Catatan</strong> untuk membuat laporan otomatis, lalu sesuaikan jika perlu sebelum menyimpan.
                            </small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-info me-2" id="btn-generate-catatan" {{ $disabledAttr }}>
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Catatan
                            </button>
                            <button type="submit" class="btn btn-success" {{ $disabledAttr }}>
                                <i class="fa-solid fa-save"></i> Simpan Laporan
                            </button>
                        </div>
                    </div>
                </form>

                <div class="note-panel alt lopa-note-panel lopa-alt" id="laporan-history-container">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="cursor: pointer;" onclick="toggleHistory('laporan')">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-clock-rotate-left me-1" style="color:#3b82f6;"></i>
                            History Laporan Harian (30 hari terakhir)
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="toggle-laporan">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                    <div id="laporan-history-content" style="display: block;">
                        <p class="text-muted small mb-0">Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 5: Grafik & Analisis --}}
        <div class="tab-pane fade lopa-tab-pane" id="grafikAnalisis" role="tabpanel" aria-labelledby="tab-chart">
            <div class="card p-3 lopa-card">
                <h5 class="section-title lopa-section-title">📈 Grafik & Analisis Performa</h5>

                <div class="row g-3 mt-2">
                    <div class="col-lg-6">
                        <div class="note-panel lopa-note-panel">
                            <h6>Konsumsi Pakan Harian</h6>
                            <div id="chartFeed" style="min-height: 240px;"></div>
                            <p class="small text-muted mt-2 mb-0" id="feedAnalysis">Loading data...</p>
                            <p class="small text-danger mt-2 mb-0" id="feedError" style="display:none;">Tidak ada data pakan untuk ditampilkan.</p>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="note-panel lopa-note-panel">
                            <h6>Mortalitas Kumulatif</h6>
                            <div id="chartMortality" style="min-height: 240px;"></div>
                            <p class="small text-muted mt-2 mb-0" id="mortalityAnalysis">Loading data...</p>
                            <p class="small text-danger mt-2 mb-0" id="mortalityError" style="display:none;">Tidak ada data mortalitas untuk ditampilkan.</p>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="note-panel lopa-note-panel">
                            <h6>Suhu & Kelembaban</h6>
                            <div id="chartEnv" style="min-height: 240px;"></div>
                            <p class="small text-muted mt-2 mb-0" id="envAnalysis">Loading data...</p>
                            <p class="small text-danger mt-2 mb-0" id="envError" style="display:none;">Tidak ada data lingkungan untuk ditampilkan.</p>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="note-panel lopa-note-panel">
                            <h6>Perkembangan Berat</h6>
                            <div id="chartWeight" style="min-height: 240px;"></div>
                            <p class="small text-muted mt-2 mb-0" id="weightAnalysis">Loading data...</p>
                            <p class="small text-danger mt-2 mb-0" id="weightError" style="display:none;">Tidak ada data berat untuk ditampilkan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle Show/Hide History Sections
function toggleHistory(section) {
    const content = document.getElementById(`${section}-history-content`);
    const toggleBtn = document.getElementById(`toggle-${section}`);
    const icon = toggleBtn.querySelector('i');
    
    if (content.style.display === 'none') {
        // Show
        content.style.display = 'block';
        icon.className = 'fa-solid fa-chevron-down';
        // Save state to localStorage
        localStorage.setItem(`history-${section}-visible`, 'true');
    } else {
        // Hide
        content.style.display = 'none';
        icon.className = 'fa-solid fa-chevron-right';
        // Save state to localStorage
        localStorage.setItem(`history-${section}-visible`, 'false');
    }
}

// Restore saved toggle states on page load
document.addEventListener('DOMContentLoaded', function() {
    ['pakan', 'kematian', 'laporan', 'monitoring', 'kesehatan', 'berat'].forEach(section => {
        const savedState = localStorage.getItem(`history-${section}-visible`);
        if (savedState === 'false') {
            // If previously hidden, hide it again
            const content = document.getElementById(`${section}-history-content`);
            const toggleBtn = document.getElementById(`toggle-${section}`);
            const icon = toggleBtn?.querySelector('i');
            
            if (content) content.style.display = 'none';
            if (icon) icon.className = 'fa-solid fa-chevron-right';
        }
    });

    // Tab persistence implementation (simple approach - just trigger click)
    const pembesaranId = window.vigazaConfig?.pembesaranId || '{{ $pembesaran->id ?? "" }}';
    const tabStorageKey = `pembesaran_active_tab_${pembesaranId}`;
    
    // Function to activate tab safely by triggering click
    function activateTab(tabId) {
        const tabElement = document.querySelector(`button[data-bs-target="${tabId}"]`);
        if (tabElement && !tabElement.classList.contains('active')) {
            // Simply click the tab button - Bootstrap handles the rest
            tabElement.click();
        }
    }
    
    // Restore tab from URL hash or localStorage
    const urlHash = window.location.hash;
    let targetTab = null;
    
    if (urlHash && urlHash.startsWith('#')) {
        const hashTabId = urlHash.substring(1);
        const validTabs = ['infoBatch', 'recordHarian', 'recordMingguan', 'laporanBatch', 'grafikAnalisis'];
        if (validTabs.includes(hashTabId)) {
            targetTab = `#${hashTabId}`;
        }
    }
    
    // Fallback to localStorage if no hash or hash invalid
    if (!targetTab) {
        const savedTab = localStorage.getItem(tabStorageKey);
        if (savedTab) {
            targetTab = savedTab;
        }
    }
    
    // Activate the determined tab (with small delay to ensure DOM is ready)
    if (targetTab && targetTab !== '#infoBatch') {
        setTimeout(() => activateTab(targetTab), 150);
    }
    
    // Listen to tab changes and save to localStorage + update URL hash
    const tabButtons = document.querySelectorAll('#batchTabs button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(event) {
            const targetId = event.target.getAttribute('data-bs-target');
            if (targetId) {
                localStorage.setItem(tabStorageKey, targetId);
                // Update URL hash without scrolling
                if (history.replaceState) {
                    history.replaceState(null, null, targetId);
                } else {
                    window.location.hash = targetId;
                }
            }
        });
    });

    // Sub-tab persistence for daily tabs
    const dailySubTabStorageKey = `pembesaran_daily_subtab_${pembesaranId}`;
    const dailySubTabButtons = document.querySelectorAll('#dailySubTabs button[data-bs-toggle="tab"]');
    dailySubTabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(event) {
            const targetId = event.target.getAttribute('data-bs-target');
            if (targetId) {
                localStorage.setItem(dailySubTabStorageKey, targetId);
            }
        });
    });

    // Sub-tab persistence for weekly tabs
    const weeklySubTabStorageKey = `pembesaran_weekly_subtab_${pembesaranId}`;
    const weeklySubTabButtons = document.querySelectorAll('#weeklySubTabs button[data-bs-toggle="tab"]');
    weeklySubTabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(event) {
            const targetId = event.target.getAttribute('data-bs-target');
            if (targetId) {
                localStorage.setItem(weeklySubTabStorageKey, targetId);
            }
        });
    });

    // Restore sub-tab states
    const savedDailySubTab = localStorage.getItem(dailySubTabStorageKey);
    if (savedDailySubTab) {
        setTimeout(() => {
            const dailyTabElement = document.querySelector(`button[data-bs-target="${savedDailySubTab}"]`);
            if (dailyTabElement && !dailyTabElement.classList.contains('active')) {
                dailyTabElement.click();
            }
        }, 200);
    }

    const savedWeeklySubTab = localStorage.getItem(weeklySubTabStorageKey);
    if (savedWeeklySubTab) {
        setTimeout(() => {
            const weeklyTabElement = document.querySelector(`button[data-bs-target="${savedWeeklySubTab}"]`);
            if (weeklyTabElement && !weeklyTabElement.classList.contains('active')) {
                weeklyTabElement.click();
            }
        }, 200);
    }
});
</script>
