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
                            <div class="text lopa-text">{{ number_format($mortalitas, 2) }}%</div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-4 mb-3">
                        <div class="note-panel h-100 lopa-note-panel">
                            <h6>Detail Batch</h6>
                            <dl class="row kv mb-0 lopa-kv">
                                <dt class="col-sm-5">Batch ID:</dt>
                                <dd class="col-sm-7">{{ $pembesaran->batch_produksi_id }}</dd>
                                <dt class="col-sm-5">Kandang:</dt>
                                <dd class="col-sm-7">{{ $pembesaran->kandang->nama_kandang ?? '-' }}</dd>
                                <dt class="col-sm-5">Tgl Mulai:</dt>
                                <dd class="col-sm-7">{{ \Carbon\Carbon::parse($pembesaran->tanggal_mulai)->format('d/m/Y') }}</dd>
                                <dt class="col-sm-5">Umur:</dt>
                                <dd class="col-sm-7">{{ (int)$umurHari }} hari</dd>
                                <dt class="col-sm-5">Status:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge {{ $pembesaran->status_batch === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $pembesaran->status_batch }}
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
                                    <div class="value lopa-value">{{ number_format($mortalitas, 2) }}%</div>
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
                            <h6>Ringkasan Biaya</h6>
                            <table class="w-100 small cost-table lopa-cost-table">
                                <tr>
                                    <td>Total Pakan:</td>
                                    <td class="text-end"><strong>Rp {{ number_format($totalBiayaPakan, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Biaya Lain:</td>
                                    <td class="text-end"><strong>Rp 0</strong></td>
                                </tr>
                                <tr style="border-top: 1px solid #ddd;">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($totalBiayaPakan, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                            <div class="d-grid gap-2 mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-file-invoice"></i> Lihat Detail Biaya
                                </button>
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
                            <select class="form-select" name="jenis_pakan" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Starter">Starter</option>
                                <option value="Grower">Grower</option>
                                <option value="Layer">Layer</option>
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

                <div class="note-panel alt lopa-note-panel lopa-alt">
                    <h6>History Pakan (30 hari terakhir)</h6>
                    <p class="text-muted small mb-0">Belum ada data pakan</p>
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
                                <option value="Sakit">Sakit</option>
                                <option value="Kecelakaan">Kecelakaan</option>
                                <option value="Tidak Diketahui">Tidak Diketahui</option>
                                <option value="Lainnya">Lainnya</option>
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
                        <div class="text lopa-text">Mortalitas Kumulatif: <strong>{{ number_format($mortalitas, 2) }}%</strong></div>
                    </div>
                </div>

                <div class="note-panel alt lopa-note-panel lopa-alt">
                    <h6>History Kematian (30 hari terakhir)</h6>
                    <p class="text-muted small mb-0">Belum ada data kematian</p>
                </div>
            </div>

            {{-- Generate Laporan Harian --}}
            <div class="card mb-4 lopa-card">
                <h5 class="section-title lopa-section-title">
                    <i class="fa-solid fa-file-lines" style="color:var(--accent)"></i> 
                    Generate Laporan Harian
                </h5>
                <form class="form-card p-3 lopa-form-card" aria-label="Form laporan harian">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Tanggal Laporan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_laporan" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Catatan Khusus</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan tambahan untuk laporan..."></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-gears"></i> Generate Laporan
                        </button>
                    </div>
                </form>

                <div class="note-panel alt lopa-note-panel lopa-alt">
                    <h6>History Laporan Harian (30 hari terakhir)</h6>
                    <p class="text-muted small mb-0">Belum ada laporan harian</p>
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
                            <label class="form-label lopa-form-label">Tanggal Sampling <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_sampling" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label lopa-form-label">Berat Rata-rata (gram) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="berat_rata" placeholder="0.00" required />
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Berat
                        </button>
                    </div>
                </form>
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
                            <label class="form-label lopa-form-label">Kualitas Udara</label>
                            <select class="form-select" name="kualitas_udara">
                                <option value="">-- Pilih --</option>
                                <option value="Baik">Baik</option>
                                <option value="Cukup">Cukup</option>
                                <option value="Buruk">Buruk</option>
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

                <div class="note-panel alt mt-3 lopa-note-panel lopa-alt">
                    <h6>History Monitoring (50 data terakhir)</h6>
                    <p class="text-muted small mb-0">Belum ada data monitoring</p>
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
                            <select class="form-select" name="jenis_tindakan" required>
                                <option value="">-- Pilih --</option>
                                <option value="Vaksinasi">Vaksinasi</option>
                                <option value="Pengobatan">Pengobatan</option>
                                <option value="Pemeriksaan">Pemeriksaan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Nama Vaksin/Obat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_vaksin" placeholder="Nama vaksin/obat" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Dosis</label>
                            <input type="text" class="form-control" name="dosis" placeholder="Dosis" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Metode</label>
                            <input type="text" class="form-control" name="metode" placeholder="Oral/Injeksi/dll" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lopa-form-label">Biaya</label>
                            <input type="number" class="form-control" name="biaya" placeholder="0" />
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Gejala/Kondisi</label>
                            <textarea class="form-control" name="gejala" rows="2" placeholder="Deskripsi kondisi atau gejala..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label lopa-form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-notes-medical"></i> Simpan Kesehatan
                        </button>
                    </div>
                </form>

                <div class="note-panel alt mt-3 lopa-note-panel lopa-alt">
                    <h6>Riwayat Kesehatan & Vaksinasi</h6>
                    <p class="text-muted small mb-0">Belum ada riwayat kesehatan</p>
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
