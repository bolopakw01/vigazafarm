{{-- Helper untuk format mortalitas --}}
@php
    $mortalitasFormatted = $mortalitas == floor($mortalitas) 
        ? number_format($mortalitas, 0) 
        : rtrim(rtrim(number_format($mortalitas, 2), '0'), '.');
@endphp

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
            <button class="nav-link" id="tab-chart" data-bs-toggle="tab" data-bs-target="#grafikAnalisis" type="button" role="tab" aria-controls="grafikAnalisis" aria-selected="false">
                <i class="fa-solid fa-chart-line"></i> Grafik & Analisis
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Tab 1: Info Batch --}}
        <div class="tab-pane fade show active" id="infoBatch" role="tabpanel" aria-labelledby="tab-info">
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
                                    <div class="value lopa-value">{{ number_format($totalMati) }}</div>
                                    <div class="label lopa-label">Total Kematian</div>
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
                                    <div class="fw-bold text-end" style="font-size: 1rem; font-weight: 700 !important;">{{ number_format($totalPakan, 2) }} kg</div>
                                </div>
                                
                                <!-- Total Biaya Pakan -->
                                <div class="mb-3">
                                    <div class="text-muted mb-1" style="font-size: 0.85rem;">Total Biaya Pakan</div>
                                    <div class="fw-bold text-end" style="font-size: 1rem; font-weight: 700 !important;">Rp {{ number_format($totalBiayaPakan, 0, ',', '.') }}</div>
                                </div>
                                
                                <!-- Biaya Kesehatan -->
                                <div class="mb-3">
                                    <div class="text-muted mb-1" style="font-size: 0.85rem;">Biaya Kesehatan & Vaksinasi</div>
                                    <div class="fw-bold text-end" style="font-size: 1rem; font-weight: 700 !important;">Rp {{ number_format($totalBiayaKesehatan ?? 0, 0, ',', '.') }}</div>
                                </div>
                                
                                <!-- Total Keseluruhan -->
                                <div class="pt-3" style="border-top: 2px solid #495057;">
                                    <div class="text-muted mb-1" style="font-size: 0.85rem;">Total Biaya Keseluruhan</div>
                                    <div class="fw-bold text-end" style="font-size: 1rem; font-weight: 800 !important; color: #198754;">Rp {{ number_format(($totalBiayaPakan + ($totalBiayaKesehatan ?? 0)), 0, ',', '.') }}</div>
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
            </div>
        </div>

        {{-- Tab 2: Recording Harian --}}
        <div class="tab-pane fade" id="recordHarian" role="tabpanel" aria-labelledby="tab-daily">
            {{-- Konsumsi Pakan Harian --}}
            <div class="card mb-4 lopa-card">
                <h5 class="section-title lopa-section-title">
                    <i class="fa-solid fa-bowl-food" style="color:var(--accent)"></i> 
                    Konsumsi Pakan Harian
                </h5>
                <form class="form-card p-3 mb-3 lopa-form-card" aria-label="Form pencatatan pakan harian">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Jenis Pakan <span class="text-danger">*</span></label>
                            <select class="form-select" name="stok_pakan_id" required>
                                <option value="">-- Pilih Pakan --</option>
                                @foreach($stokPakanList as $stok)
                                <option value="{{ $stok->id }}" data-harga="{{ $stok->harga_per_kg }}">
                                    {{ $stok->nama_pakan }} ({{ $stok->jenis_pakan }}) - Stok: {{ number_format($stok->stok_kg, 0) }} kg
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Jumlah (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="jumlah_kg" placeholder="0.00" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Jumlah Karung</label>
                            <input type="number" class="form-control" name="jumlah_karung" placeholder="0" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Harga per kg</label>
                            <input type="number" class="form-control" name="harga_per_kg" placeholder="Rp 0" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Total Biaya</label>
                            <input type="text" class="form-control" name="total_biaya" placeholder="Rp 0" readonly />
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Simpan Pakan
                        </button>
                    </div>
                </form>

                <div class="note-panel alt lopa-note-panel lopa-alt" id="pakan-history-container">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="cursor: pointer;" onclick="toggleHistory('pakan')">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-clock-rotate-left me-1" style="color:#10b981;"></i>
                            History Pakan (30 hari terakhir)
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="toggle-pakan">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                    <div id="pakan-history-content" style="display: block;">
                        <p class="text-muted small mb-0">Loading...</p>
                    </div>
                </div>
            </div>

            {{-- Pencatatan Kematian --}}
            <div class="card mb-4 lopa-card">
                <h5 class="section-title lopa-section-title">
                    <i class="fa-solid fa-skull-crossbones" style="color:#ef4444"></i> 
                    Pencatatan Kematian
                </h5>
                <form class="form-card p-3 mb-3 lopa-form-card" aria-label="Form pencatatan kematian harian">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Jumlah Ekor <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="jumlah_ekor" placeholder="0" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Penyebab <span class="text-danger">*</span></label>
                            <select class="form-select" name="penyebab" required>
                                <option value="">-- Pilih Penyebab --</option>
                                <option value="penyakit">Penyakit</option>
                                <option value="stress">Stress</option>
                                <option value="kecelakaan">Kecelakaan</option>
                                <option value="usia">Usia Tua</option>
                                <option value="tidak_diketahui">Tidak Diketahui</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-save"></i> Simpan Kematian
                        </button>
                    </div>
                </form>

                <div class="d-flex flex-wrap gap-3 mb-3">
                    <div class="status-compact lopa-status-compact">
                        <div class="icon lopa-icon" style="background:#fde8e8; color:#991b1b;">
                            <i class="fa-solid fa-skull"></i>
                        </div>
                        <div class="text lopa-text">Total Kematian: <strong>{{ number_format($totalMati) }} ekor</strong></div>
                    </div>
                    <div class="status-compact lopa-status-compact">
                        <div class="icon lopa-icon" style="background:#fff4e6; color:#92400e;">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                        <div class="text lopa-text">Mortalitas Kumulatif: <strong>{{ $mortalitasFormatted }}%</strong></div>
                    </div>
                </div>

                <div class="note-panel alt lopa-note-panel lopa-alt" id="kematian-history-container">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="cursor: pointer;" onclick="toggleHistory('kematian')">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-clock-rotate-left me-1" style="color:#ef4444;"></i>
                            History Kematian (30 hari terakhir)
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="toggle-kematian">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                    <div id="kematian-history-content" style="display: block;">
                        <p class="text-muted small mb-0">Loading...</p>
                    </div>
                </div>
            </div>

            {{-- Generate Laporan Harian --}}
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
                            <input type="date" class="form-control" name="tanggal_laporan" id="tanggal_laporan" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Catatan Laporan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan" id="catatan_laporan" rows="6" placeholder="Klik tombol 'Generate Catatan' untuk membuat laporan otomatis berdasarkan data pakan dan kematian hari ini..." required></textarea>
                            <small class="form-text text-muted">
                                <i class="fa-solid fa-lightbulb"></i> Tip: Klik tombol <strong>Generate Catatan</strong> untuk membuat laporan otomatis, lalu sesuaikan jika perlu sebelum menyimpan.
                            </small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-info me-2" id="btn-generate-catatan">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Catatan
                            </button>
                            <button type="submit" class="btn btn-success">
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

        {{-- Tab 3: Recording Mingguan --}}
        <div class="tab-pane fade" id="recordMingguan" role="tabpanel" aria-labelledby="tab-weekly">
            {{-- Sampling Berat --}}
            <div class="card mb-4 lopa-card">
                <h5 class="section-title lopa-section-title">
                    <i class="fa-solid fa-weight-scale" style="color:var(--accent)"></i> 
                    Sampling Berat Rata-Rata
                </h5>
                <form class="form-card p-3 lopa-form-card" aria-label="Form pencatatan mingguan - sampling berat">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Umur (hari) <span class="text-danger">*</span></label>
                            @php
                                $tanggalMasuk = \Carbon\Carbon::parse($pembesaran->tanggal_masuk);
                                $umurHari = floor($tanggalMasuk->diffInDays(now()));
                            @endphp
                            <input type="number" class="form-control" name="umur_hari" value="{{ intval($umurHari) }}" min="0" required readonly />
                            <small class="text-muted">Otomatis dihitung dari tanggal masuk: {{ $tanggalMasuk->format('d/m/Y') }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Berat Rata-rata (gram) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="berat_rata_rata" placeholder="0.00" min="0" required />
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Berat
                        </button>
                    </div>
                </form>

                <div class="note-panel alt mt-3 lopa-note-panel lopa-alt" id="berat-history-container">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="cursor: pointer;" onclick="toggleHistory('berat')">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-clock-rotate-left me-1" style="color:#8b5cf6;"></i>
                            History Sampling Berat (50 data terakhir)
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="toggle-berat">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                    <div id="berat-history-content" style="display: block;">
                        <p class="text-muted small mb-0">Loading...</p>
                    </div>
                </div>
            </div>

            {{-- Monitoring Lingkungan --}}
            <div class="card mb-4 lopa-card">
                <h5 class="section-title lopa-section-title">
                    <i class="fa-solid fa-cloud-sun" style="color:var(--accent)"></i> 
                    Monitoring Lingkungan
                </h5>
                <form class="form-card p-3 lopa-form-card" aria-label="Form monitoring lingkungan">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Waktu <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="waktu" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Suhu (°C) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" class="form-control" name="suhu" placeholder="28.0" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Kelembaban (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" class="form-control" name="kelembaban" placeholder="65.0" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Intensitas Cahaya (Lux)</label>
                            <input type="number" step="0.1" class="form-control" name="intensitas_cahaya" placeholder="50" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Kondisi Ventilasi</label>
                            <select class="form-select" name="kondisi_ventilasi">
                                <option value="">-- Pilih --</option>
                                <option value="Baik">Baik</option>
                                <option value="Cukup">Cukup</option>
                                <option value="Kurang">Kurang</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan kondisi lingkungan..."></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-save"></i> Simpan Monitoring
                        </button>
                    </div>
                </form>

                <div class="note-panel alt mt-3 lopa-note-panel lopa-alt" id="monitoring-history-container">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="cursor: pointer;" onclick="toggleHistory('monitoring')">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-clock-rotate-left me-1" style="color:#8b5cf6;"></i>
                            History Monitoring (50 data terakhir)
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="toggle-monitoring">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                    <div id="monitoring-history-content" style="display: block;">
                        <p class="text-muted small mb-0">Loading...</p>
                    </div>
                </div>
            </div>

            {{-- Kesehatan & Vaksinasi --}}
            <div class="card mb-4 lopa-card">
                <h5 class="section-title lopa-section-title">
                    <i class="fa-solid fa-syringe" style="color:var(--accent)"></i> 
                    Kesehatan & Vaksinasi
                </h5>

                <div class="note-panel mb-3 lopa-note-panel">
                    <h6>⏰ Reminder Vaksinasi:</h6>
                    <ul class="small mb-0">
                        <li>ND (Newcastle Disease) — Umur 7 hari <span class="badge bg-secondary">belum waktunya</span></li>
                        <li>ND + IB (Infectious Bronchitis) — Umur 14 hari <span class="badge bg-secondary">belum waktunya</span></li>
                        <li>Fowl Pox — Umur 28 hari <span class="badge bg-secondary">belum waktunya</span></li>
                    </ul>
                </div>

                <form class="form-card p-3 lopa-form-card" aria-label="Form kesehatan & vaksinasi">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Jenis Tindakan <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_kegiatan" required>
                                <option value="">-- Pilih --</option>
                                <option value="vaksinasi">Vaksinasi</option>
                                <option value="pengobatan">Pengobatan</option>
                                <option value="pemeriksaan_rutin">Pemeriksaan Rutin</option>
                                <option value="karantina">Karantina</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Nama Vaksin/Obat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_vaksin_obat" placeholder="Nama vaksin/obat" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Jumlah Burung <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="jumlah_burung" placeholder="0" min="1" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Biaya</label>
                            <input type="number" class="form-control" name="biaya" placeholder="0" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Petugas</label>
                            <input type="text" class="form-control" name="petugas" placeholder="Nama petugas" />
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Gejala/Kondisi</label>
                            <textarea class="form-control" name="gejala" rows="2" placeholder="Deskripsi kondisi atau gejala..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Diagnosa</label>
                            <textarea class="form-control" name="diagnosa" rows="2" placeholder="Hasil diagnosa..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Tindakan</label>
                            <textarea class="form-control" name="tindakan" rows="2" placeholder="Tindakan yang dilakukan..."></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-notes-medical"></i> Simpan Kesehatan
                        </button>
                    </div>
                </form>

                <div class="note-panel alt mt-3 lopa-note-panel lopa-alt" id="kesehatan-history-container">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="cursor: pointer;" onclick="toggleHistory('kesehatan')">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-clock-rotate-left me-1" style="color:#8b5cf6;"></i>
                            Riwayat Kesehatan & Vaksinasi (50 data terakhir)
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="toggle-kesehatan">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                    <div id="kesehatan-history-content" style="display: block;">
                        <p class="text-muted small mb-0">Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 4: Grafik & Analisis --}}
        <div class="tab-pane fade" id="grafikAnalisis" role="tabpanel" aria-labelledby="tab-chart">
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
        const validTabs = ['infoBatch', 'recordHarian', 'recordMingguan', 'grafikAnalisis'];
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
});
</script>
