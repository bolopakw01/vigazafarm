{{-- Monitoring Lingkungan --}}
<div class="card lopa-card mb-0">
    <h5 class="section-title lopa-section-title">
        <i class="fa-solid fa-cloud-sun" style="color:var(--accent)"></i> 
        Monitoring Lingkungan
    </h5>
    <form class="form-card p-3 lopa-form-card" aria-label="Form monitoring lingkungan">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label lopa-form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" @if($batchStartDate) min="{{ $batchStartDate }}" @endif {{ $disabledAttr }} required />
                <small class="form-text text-muted">Tanggal pengukuran kondisi lingkungan dilakukan.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label lopa-form-label">Waktu <span class="text-danger">*</span></label>
                <input type="time" class="form-control" name="waktu" value="{{ now('Asia/Jakarta')->format('H:i') }}" data-fill-current-time="true" {{ $disabledAttr }} required />
                <small class="form-text text-muted">Catat waktu pengambilan data untuk konsistensi.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label lopa-form-label">Suhu (°C) <span class="text-danger">*</span></label>
                <input type="number" step="0.1" class="form-control" name="suhu" placeholder="23.0" {{ $disabledAttr }} required />
                <small class="form-text text-muted">Catat suhu kandang (ideal 20°C - 25°C untuk puyuh).</small>
            </div>
            <div class="col-md-6">
                <label class="form-label lopa-form-label">Kelembaban (%) <span class="text-danger">*</span></label>
                <input type="number" step="0.1" class="form-control" name="kelembaban" placeholder="55.0" {{ $disabledAttr }} required />
                <small class="form-text text-muted">Masukkan kelembaban udara (ideal 30% - 80%).</small>
            </div>
            <div class="col-md-6">
                <label class="form-label lopa-form-label">Intensitas Cahaya (Lux)</label>
                <input type="number" step="0.1" class="form-control" name="intensitas_cahaya" placeholder="50" {{ $disabledAttr }} />
                <small class="form-text text-muted">Opsional, isi jika menggunakan alat ukur intensitas cahaya.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label lopa-form-label">Kondisi Ventilasi</label>
                <select class="form-select" name="kondisi_ventilasi" {{ $disabledAttr }}>
                    <option value="">-- Pilih --</option>
                    <option value="Baik">Baik</option>
                    <option value="Cukup">Cukup</option>
                    <option value="Kurang">Kurang</option>
                </select>
                <small class="form-text text-muted">Pilih kondisi ventilasi umum saat inspeksi.</small>
            </div>
            <div class="col-12">
                <label class="form-label lopa-form-label">Catatan</label>
                <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan kondisi lingkungan..." {{ $disabledAttr }}></textarea>
                <small class="form-text text-muted">Opsional, tuliskan temuan penting atau anomali.</small>
            </div>
        </div>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-success" {{ $disabledAttr }}>
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
        <div id="monitoring-history-content" class="history-scroll-container" style="display: block;">
            <p class="text-muted small mb-0">Loading...</p>
        </div>
    </div>
</div>
