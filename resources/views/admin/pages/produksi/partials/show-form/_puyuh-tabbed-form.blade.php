<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa-solid fa-book me-2"></i>Catatan Harian Puyuh</h5>
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

                <ul class="nav nav-pills mb-3" id="puyuhDailyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-penjualan-tab" data-bs-toggle="tab" data-bs-target="#tab-penjualan" type="button" role="tab" aria-controls="tab-penjualan" aria-selected="true">
                            <i class="fa-solid fa-basket-shopping me-1"></i> Penjualan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-kematian-tab" data-bs-toggle="tab" data-bs-target="#tab-kematian" type="button" role="tab" aria-controls="tab-kematian" aria-selected="false">
                            <i class="fa-solid fa-skull-crossbones me-1"></i> Kematian
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-pakan-tab" data-bs-toggle="tab" data-bs-target="#tab-pakan" type="button" role="tab" aria-controls="tab-pakan" aria-selected="false">
                            <i class="fa-solid fa-wheat-awn me-1"></i> Pakan
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="puyuhDailyTabsContent">
                    <div class="tab-pane fade show active" id="tab-penjualan" role="tabpanel" aria-labelledby="tab-penjualan-tab">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="penjualan_puyuh_ekor" class="form-label">Puyuh Terjual (ekor)</label>
                                <input type="number" min="0" id="penjualan_puyuh_ekor" name="penjualan_puyuh_ekor" class="form-control" value="{{ old('penjualan_puyuh_ekor', optional($todayLaporan)->penjualan_puyuh_ekor) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="pendapatan_harian" class="form-label">Pendapatan (Rp)</label>
                                <input type="number" min="0" step="0.01" id="pendapatan_harian" name="pendapatan_harian" class="form-control" value="{{ old('pendapatan_harian', optional($todayLaporan)->pendapatan_harian) }}">
                            </div>
                            <div class="col-12">
                                <label for="catatan_kejadian" class="form-label">Catatan Penjualan</label>
                                <textarea id="catatan_kejadian" name="catatan_kejadian" rows="3" class="form-control" placeholder="Contoh: penambahan vitamin, kendala kesehatan, permintaan pelanggan">{{ old('catatan_kejadian', optional($todayLaporan)->catatan_kejadian) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-kematian" role="tabpanel" aria-labelledby="tab-kematian-tab">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="jumlah_kematian" class="form-label">Jumlah Kematian</label>
                                <input type="number" min="0" id="jumlah_kematian" name="jumlah_kematian" class="form-control" value="{{ old('jumlah_kematian', optional($todayLaporan)->jumlah_kematian) }}">
                            </div>
                            <div class="col-12">
                                <div class="form-text">Isi 0 jika tidak ada kematian hari ini.</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-pakan" role="tabpanel" aria-labelledby="tab-pakan-tab">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="konsumsi_pakan_kg" class="form-label">Konsumsi Pakan (kg)</label>
                                <input type="number" min="0" step="0.01" id="konsumsi_pakan_kg" name="konsumsi_pakan_kg" class="form-control" value="{{ old('konsumsi_pakan_kg', optional($todayLaporan)->konsumsi_pakan_kg) }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="sisa_pakan_kg" class="form-label">Sisa Pakan (kg)</label>
                                <input type="number" min="0" step="0.01" id="sisa_pakan_kg" name="sisa_pakan_kg" class="form-control" value="{{ old('sisa_pakan_kg', optional($todayLaporan)->sisa_pakan_kg) }}">
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
