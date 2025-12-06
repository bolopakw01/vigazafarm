{{-- Pencatatan Kematian --}}
<div class="card lopa-card mb-0">
    <h5 class="section-title lopa-section-title">
        <i class="fa-solid fa-skull-crossbones" style="color:#ef4444"></i>
        Pencatatan Kematian
    </h5>
    <form class="form-card p-3 mb-3 lopa-form-card" aria-label="Form pencatatan kematian harian">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" @if($batchStartDate) min="{{ $batchStartDate }}" @endif {{ $disabledAttr }} required />
                <small class="form-text text-muted">Tanggal terjadinya kematian dicatat di sini.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Jumlah Ekor <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="jumlah_ekor" placeholder="0" {{ $disabledAttr }} required />
                <small class="form-text text-muted">Isi jumlah burung yang mati pada tanggal tersebut.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Penyebab <span class="text-danger">*</span></label>
                <select class="form-select" name="penyebab" {{ $disabledAttr }} required>
                    <option value="">-- Pilih Penyebab --</option>
                    <option value="penyakit">Penyakit</option>
                    <option value="stress">Stress</option>
                    <option value="kecelakaan">Kecelakaan</option>
                    <option value="tidak_diketahui">Tidak Diketahui</option>
                </select>
                <small class="form-text text-muted">Pilih penyebab dominan untuk memudahkan analisis.</small>
            </div>
            <div class="col-12">
                <label class="form-label lopa-form-label">Catatan</label>
                <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan tambahan..." {{ $disabledAttr }}></textarea>
                <small class="form-text text-muted">Opsional, tuliskan kronologi singkat atau tindakan yang diambil.</small>
            </div>
        </div>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-danger" {{ $disabledAttr }}>
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
        <div id="kematian-history-content" class="history-scroll-container" style="display: block;">
            <p class="text-muted small mb-0">Loading...</p>
        </div>
    </div>
</div>

<style>
/* Mobile responsiveness for kematian form */
@media (max-width: 768px) {
    .lopa-form-card .row.g-3 .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 1rem;
    }

    .lopa-form-card .text-end.mt-3 {
        text-align: center !important;
    }

    .lopa-form-card .btn {
        width: 100%;
    }

    .d-flex.flex-wrap.gap-3.mb-3 {
        flex-direction: column;
        gap: 0.75rem !important;
    }

    .status-compact {
        width: 100% !important;
        justify-content: center;
        padding: 0.75rem;
    }
}

@media (max-width: 576px) {
    .lopa-form-card {
        padding: 1rem !important;
    }

    .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select, .form-textarea {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    .form-text {
        font-size: 0.8rem;
    }
}
</style>
