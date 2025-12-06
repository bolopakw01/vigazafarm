{{-- Sampling Berat --}}
<div class="card lopa-card mb-0">
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
                    $tanggalMasuk = $pembesaran->tanggal_masuk ? \Carbon\Carbon::parse($pembesaran->tanggal_masuk) : null;
                    $umurAwal = max(0, (int) ($pembesaran->umur_hari ?? 0));
                    $hariBerlalu = $tanggalMasuk ? max(0, $tanggalMasuk->diffInDays(now())) : 0;
                    $umurHari = $umurAwal + $hariBerlalu;
                @endphp
                <input type="number" class="form-control" name="umur_hari" value="{{ intval($umurHari) }}" min="0" required readonly />
                <small class="text-muted">
                    Umur awal {{ $umurAwal }} hari @if($tanggalMasuk) • dihitung sejak {{ $tanggalMasuk->format('d/m/Y') }} @endif
                </small>
            </div>
            <div class="col-md-6">
                <label class="form-label lopa-form-label">Berat Rata-rata (gram) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="berat_rata_rata" placeholder="0.00" min="0" {{ $disabledAttr }} required />
                <small class="form-text text-muted">Masukkan hasil sampling berat terbaru dalam gram.</small>
            </div>
        </div>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary" {{ $disabledAttr }}>
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
        <div id="berat-history-content" class="history-scroll-container" style="display: block;">
            <p class="text-muted small mb-0">Loading...</p>
        </div>
    </div>
</div>
