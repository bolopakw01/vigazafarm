<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa-solid fa-book me-2"></i>Catatan Harian Telur</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.produksi.laporan.store', $produksi->id) }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ $defaultTanggalForm }}" max="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="jumlah_burung" class="form-label">Populasi Aktif <span class="text-danger">*</span></label>
                        <input type="number" min="0" id="jumlah_burung" name="jumlah_burung" class="form-control" value="{{ $defaultPopulasi }}" required>
                    </div>
                </div>

                <ul class="nav nav-pills mb-3" id="telurDailyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-produksi-tab" data-bs-toggle="tab" data-bs-target="#tab-produksi" type="button" role="tab" aria-controls="tab-produksi" aria-selected="true">
                            <i class="fa-solid fa-egg me-1"></i> Produksi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-penjualan-tab" data-bs-toggle="tab" data-bs-target="#tab-penjualan" type="button" role="tab" aria-controls="tab-penjualan" aria-selected="false">
                            <i class="fa-solid fa-cash-register me-1"></i> Penjualan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-persediaan-tab" data-bs-toggle="tab" data-bs-target="#tab-persediaan" type="button" role="tab" aria-controls="tab-persediaan" aria-selected="false">
                            <i class="fa-solid fa-boxes-stacked me-1"></i> Persediaan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-catatan-tab" data-bs-toggle="tab" data-bs-target="#tab-catatan" type="button" role="tab" aria-controls="tab-catatan" aria-selected="false">
                            <i class="fa-solid fa-note-sticky me-1"></i> Kematian & Catatan
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="telurDailyTabsContent">
                    <div class="tab-pane fade show active" id="tab-produksi" role="tabpanel" aria-labelledby="tab-produksi-tab">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="produksi_telur" class="form-label">Produksi Telur (butir)</label>
                                <input type="number" min="0" id="produksi_telur" name="produksi_telur" class="form-control" value="{{ old('produksi_telur', optional($todayLaporan)->produksi_telur) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="jumlah_kematian" class="form-label">Jumlah Kematian</label>
                                <input type="number" min="0" id="jumlah_kematian" name="jumlah_kematian" class="form-control" value="{{ old('jumlah_kematian', optional($todayLaporan)->jumlah_kematian) }}">
                                <div class="form-text">Isi 0 bila tidak ada kematian hari ini.</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-penjualan" role="tabpanel" aria-labelledby="tab-penjualan-tab">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="penjualan_telur_butir" class="form-label">Telur Terjual (butir)</label>
                                <input type="number" min="0" id="penjualan_telur_butir" name="penjualan_telur_butir" class="form-control" value="{{ old('penjualan_telur_butir', optional($todayLaporan)->penjualan_telur_butir) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="pendapatan_harian" class="form-label">Pendapatan (Rp)</label>
                                <input type="number" min="0" step="0.01" id="pendapatan_harian" name="pendapatan_harian" class="form-control" value="{{ old('pendapatan_harian', optional($todayLaporan)->pendapatan_harian) }}">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-persediaan" role="tabpanel" aria-labelledby="tab-persediaan-tab">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="konsumsi_pakan_kg" class="form-label">Konsumsi Pakan (kg)</label>
                                <input type="number" min="0" step="0.01" id="konsumsi_pakan_kg" name="konsumsi_pakan_kg" class="form-control" value="{{ old('konsumsi_pakan_kg', optional($todayLaporan)->konsumsi_pakan_kg) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="sisa_pakan_kg" class="form-label">Sisa Pakan (kg)</label>
                                <input type="number" min="0" step="0.01" id="sisa_pakan_kg" name="sisa_pakan_kg" class="form-control" value="{{ old('sisa_pakan_kg', optional($todayLaporan)->sisa_pakan_kg) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="sisa_telur" class="form-label">Sisa Telur (butir)</label>
                                <input type="number" min="0" id="sisa_telur" name="sisa_telur" class="form-control" value="{{ old('sisa_telur', optional($todayLaporan)->sisa_telur) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="sisa_tray_bal" class="form-label">Sisa Tray (bal)</label>
                                <input type="number" min="0" step="0.01" id="sisa_tray_bal" name="sisa_tray_bal" class="form-control" value="{{ old('sisa_tray_bal', optional($todayLaporan)->sisa_tray_bal) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="sisa_tray_lembar" class="form-label">Sisa Tray (lembar)</label>
                                <input type="number" min="0" id="sisa_tray_lembar" name="sisa_tray_lembar" class="form-control" value="{{ old('sisa_tray_lembar', optional($todayLaporan)->sisa_tray_lembar) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="sisa_vitamin_liter" class="form-label">Sisa Vitamin (liter)</label>
                                <input type="number" min="0" step="0.01" id="sisa_vitamin_liter" name="sisa_vitamin_liter" class="form-control" value="{{ old('sisa_vitamin_liter', optional($todayLaporan)->sisa_vitamin_liter) }}">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-catatan" role="tabpanel" aria-labelledby="tab-catatan-tab">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="catatan_kejadian" class="form-label">Catatan Harian</label>
                                <textarea id="catatan_kejadian" name="catatan_kejadian" rows="4" class="form-control" placeholder="Contoh: telur retak, kendala penjualan, penggunaan vitamin">{{ old('catatan_kejadian', optional($todayLaporan)->catatan_kejadian) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
