{{-- Kesehatan & Vaksinasi --}}
<div class="card lopa-card mb-0">
    <h5 class="section-title lopa-section-title">
        <i class="fa-solid fa-syringe" style="color:var(--accent)"></i> 
        Kesehatan & Vaksinasi
    </h5>

    @php
        $vitaminOptions = collect($vitaminOptions ?? []);
        $kandangKarantinaOptions = collect($kandangKarantinaOptions ?? []);
    @endphp
    <form class="form-card p-3 lopa-form-card" aria-label="Form kesehatan & vaksinasi">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" @if($batchStartDate) min="{{ $batchStartDate }}" @endif {{ $disabledAttr }} required />
                <small class="form-text text-muted">Tanggal tindakan kesehatan dilakukan.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Jenis Tindakan <span class="text-danger">*</span></label>
                <select class="form-select" name="tipe_kegiatan" {{ $disabledAttr }} required>
                    <option value="">-- Pilih --</option>
                    <option value="vaksinasi">Vaksinasi</option>
                    <option value="pengobatan">Pengobatan</option>
                    <option value="pemeriksaan_rutin">Pemeriksaan Rutin</option>
                    <option value="vitamin">Vitamin</option>
                    <option value="karantina">Karantina</option>
                </select>
                <small class="form-text text-muted">Tentukan jenis kegiatan agar laporan seragam.</small>
            </div>
            <div class="col-md-4" data-kesehatan-name-field>
                <label class="form-label lopa-form-label">Nama Vaksin/Obat <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_vaksin_obat" placeholder="Nama vaksin/obat" data-default-placeholder="Nama vaksin/obat" data-vitamin-placeholder="Terisi otomatis dari pilihan vitamin" {{ $disabledAttr }} required />
                <small class="form-text text-muted">Isi nama produk yang diberikan ke ternak.</small>
            </div>
            <div class="col-md-4 d-none" data-kesehatan-vitamin-field>
                <label class="form-label lopa-form-label">Pilih Vitamin dari Set <span class="text-danger">*</span></label>
                <select class="form-select" name="feed_vitamin_item_id" disabled {{ $disabledAttr }} @if($disabledAttr) data-form-locked="true" @endif>
                    <option value="">-- Pilih dari Set Vitamin --</option>
                    @forelse($vitaminOptions as $item)
                        <option value="{{ $item->id }}" data-price="{{ (float) $item->price }}" data-label="{{ $item->name }}">
                            {{ $item->name }} — {{ strtoupper($item->unit) }} @ Rp {{ number_format((float) $item->price, 0, ',', '.') }}
                        </option>
                    @empty
                        <option value="" disabled>Belum ada data aktif. Tambahkan via Set Pakan &amp; Vitamin.</option>
                    @endforelse
                </select>
                <small class="form-text text-muted">Daftar ini tersinkron dari menu <strong>Set Pakan &amp; Vitamin</strong>.</small>
            </div>
            @php $maxJumlahBurung = (int) ($populasiAwal ?? 0); @endphp
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Jumlah Burung <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="jumlah_burung" placeholder="0" min="1" @if($maxJumlahBurung > 0) max="{{ $maxJumlahBurung }}" data-max-populasi="{{ $maxJumlahBurung }}" @endif {{ $disabledAttr }} required />
                <small class="form-text text-muted">
                    Jumlah burung yang menerima tindakan ini.
                </small>
                {{-- <small class="form-text text-muted">
                    @if($maxJumlahBurung > 0) (maks {{ number_format($maxJumlahBurung) }} ekor)@endif.
                </small> --}}
            </div>
            <div class="col-md-4 d-none" data-kesehatan-karantina-field>
                <label class="form-label lopa-form-label">Masuk Kandang Karantina <span class="text-danger">*</span></label>
                <select class="form-select" name="kandang_tujuan_id" disabled {{ $disabledAttr }} @if($disabledAttr) data-form-locked="true" @endif>
                    <option value="">-- Pilih Kandang --</option>
                    @forelse($kandangKarantinaOptions as $kandang)
                        <option value="{{ $kandang->id }}">
                            {{ $kandang->nama_kandang }}
                            @if($kandang->tipe_kandang)
                                ({{ ucwords($kandang->tipe_kandang) }})
                            @endif
                        </option>
                    @empty
                        <option value="" disabled>Belum ada kandang aktif.</option>
                    @endforelse
                </select>
                <small class="form-text text-muted">Opsional jika belum ada kandang khusus karantina.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Biaya</label>
                <input type="number" class="form-control" name="biaya" placeholder="0" {{ $disabledAttr }} />
                <small class="form-text text-muted">Opsional, isi total biaya untuk tindakan ini.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label lopa-form-label">Petugas</label>
                <input type="text" class="form-control" name="petugas" placeholder="Nama petugas" {{ $disabledAttr }} />
                <small class="form-text text-muted">Catat siapa yang melakukan tindakan.</small>
            </div>
            <div class="col-12">
                <label class="form-label lopa-form-label">Catatan</label>
                <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan kesehatan..." {{ $disabledAttr }}></textarea>
                <small class="form-text text-muted">Opsional, tuliskan catatan kesehatan atau vaksinasi.</small>
            </div>
        </div>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary" {{ $disabledAttr }}>
                <i class="fa-solid fa-notes-medical"></i> Simpan Kesehatan
            </button>
        </div>
    </form>

    <div class="note-panel alt mt-3 lopa-note-panel lopa-alt" id="kesehatan-history-container">
        <div class="d-flex justify-content-between align-items-center mb-2" style="cursor: pointer;" onclick="toggleHistory('kesehatan')">
            <h6 class="mb-0">
                <i class="fa-solid fa-clock-rotate-left me-1" style="color:#8b5cf6;"></i>
                Riwayat Kesehatan &amp; Vaksinasi (50 data terakhir)
            </h6>
            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="toggle-kesehatan">
                <i class="fa-solid fa-chevron-down"></i>
            </button>
        </div>
        <div id="kesehatan-history-content" class="history-scroll-container" style="display: block;">
            <p class="text-muted small mb-0">Loading...</p>
        </div>
    </div>
</div>
