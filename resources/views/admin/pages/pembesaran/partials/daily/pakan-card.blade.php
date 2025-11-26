{{-- Konsumsi Pakan Harian --}}
<div class="card lopa-card mb-0">
    <h5 class="section-title lopa-section-title">
        <i class="fa-solid fa-bowl-food" style="color:var(--accent)"></i> 
        Konsumsi Pakan Harian
    </h5>
    <form class="form-card p-3 mb-3 lopa-form-card" aria-label="Form pencatatan pakan harian">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" {{ $disabledAttr }} required />
                <small class="form-text text-muted">Catat tanggal konsumsi pakan yang ingin disimpan.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Jenis Pakan <span class="text-danger">*</span></label>
                <select class="form-select" name="feed_item_id" {{ $disabledAttr }} required>
                    <option value="">-- Pilih dari Set Pakan &amp; Vitamin --</option>
                    @forelse($feedOptions as $item)
                        <option value="{{ $item->id }}" data-price="{{ (float) $item->price }}" data-unit="{{ $item->unit }}">
                            {{ $item->name }} &mdash; {{ $item->unit }} @ Rp {{ number_format((float) $item->price, 0, ',', '.') }}
                        </option>
                    @empty
                        <option value="" disabled>Belum ada data aktif. Tambahkan via menu Set Pakan &amp; Vitamin.</option>
                    @endforelse
                </select>
                <div class="form-text text-muted" style="font-size: 0.8rem;">
                    Daftar ini tersinkron dari menu <strong>Set Pakan &amp; Vitamin</strong>.
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Jumlah (kg) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="jumlah_kg" placeholder="0.00" {{ $disabledAttr }} required />
                <small class="form-text text-muted">Masukkan total pakan yang diberikan dalam kilogram.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Jumlah Karung</label>
                <input type="number" class="form-control" name="jumlah_karung" placeholder="0" {{ $disabledAttr }} />
                <small class="form-text text-muted">Opsional, isi jika Anda juga melacak jumlah karung fisik.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Harga per Satuan</label>
                <input type="number" class="form-control" name="harga_per_kg" placeholder="Rp 0" {{ $disabledAttr }} />
                <div class="form-text text-muted" style="font-size: 0.8rem;">
                    Satuan mengikuti pilihan pakan: <span id="feed-unit-label" data-default-unit="kg">kg</span>.
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Total Biaya</label>
                <input type="text" class="form-control" name="total_biaya" placeholder="Rp 0" readonly />
                <small class="form-text text-muted">Nilai ini dihitung otomatis dari jumlah dan harga per satuan.</small>
            </div>
        </div>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary" {{ $disabledAttr }}>
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
