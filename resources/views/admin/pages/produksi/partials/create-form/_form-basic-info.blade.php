<!-- Kandang & Jenis Input -->
<div class="section-box">
  <h6 class="section-title dynamic">Informasi Dasar</h6>
  <p class="text-muted mb-3">
    <i class="fa-solid fa-info-circle me-1"></i>
    Pilih kandang produksi dan tentukan sumber input untuk batch produksi baru.
  </p>
  <div class="row g-3">
    <div class="col-md-6">
      <label for="kandang_id" class="form-label">Kandang <span class="required">*</span></label>
      @php $selectedKandangId = old('kandang_id'); @endphp
      <select id="kandang_id" name="kandang_id" class="form-select @error('kandang_id') is-invalid @enderror" required>
        <option value="">Pilih Kandang</option>
        @foreach($kandangList as $kandang)
        @php
          $typeLabel = ucwords(strtolower($kandang->tipe_kandang ?? $kandang->tipe ?? '-'));
          $remainingLabel = number_format((int) $kandang->kapasitas_tersisa);
            $statusLabel = strtolower($kandang->status_computed ?? ($kandang->status ?? 'aktif'));
            $isMaintenance = $statusLabel === 'maintenance';
            $isFull = $statusLabel === 'full';
          $isSelected = (string) $selectedKandangId === (string) $kandang->id;
        @endphp
        <option
          value="{{ $kandang->id }}"
          data-status="{{ $statusLabel }}"
          data-kapasitas="{{ $kandang->kapasitas_total }}"
          data-terpakai="{{ $kandang->kapasitas_terpakai }}"
          data-sisa="{{ $kandang->kapasitas_tersisa }}"
          {{ $isSelected ? 'selected' : '' }}
            @disabled(($isMaintenance || $isFull) && !$isSelected)
        >
          {{ $kandang->nama_kandang }} ({{ $typeLabel }}, {{ $remainingLabel }})
          @if($isMaintenance)
            &ndash; Maintenance
            @elseif($isFull)
              &ndash; Full
          @endif
        </option>
        @endforeach
      </select>
      @error('kandang_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text">
        <small class="text-muted">Pilih kandang produksi yang akan digunakan untuk batch ini</small>
      </div>
      <div id="kandangCapacityInfo" class="capacity-info-card mt-2">
        <div class="capacity-info-icon">
          <i class="fa-solid fa-battery-half"></i>
        </div>
        <div>
          <div class="capacity-info-title">Sisa Kapasitas</div>
          <div id="kandangCapacityInfoText" class="capacity-info-text">
            Pilih kandang untuk melihat stok tersisa.
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <label class="form-label">Jenis Input <span class="required">*</span></label>
      @php
        $jenisInput = old('jenis_input', $defaultJenisInput ?? 'manual');
      @endphp
      <div class="d-flex flex-wrap gap-3 mt-2 jenis-input-radios">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="jenis_input" id="manual" value="manual" {{ $jenisInput == 'manual' ? 'checked' : '' }}>
          <label class="form-check-label" for="manual"><i class="fa-solid fa-keyboard me-1"></i>Manual</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="jenis_input" id="dari_pembesaran" value="dari_pembesaran" {{ $jenisInput == 'dari_pembesaran' ? 'checked' : '' }}>
          <label class="form-check-label" for="dari_pembesaran"><i class="fa-solid fa-feather me-1"></i>Dari Pembesaran</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="jenis_input" id="dari_produksi" value="dari_produksi" {{ $jenisInput == 'dari_produksi' ? 'checked' : '' }}>
          <label class="form-check-label" for="dari_produksi"><i class="fa-solid fa-egg me-1"></i>Dari Produksi</label>
        </div>
      </div>
      <div class="form-text">
        <small class="text-muted">
          <strong>Manual:</strong> Input indukan secara manual |
          <strong>Dari Pembesaran:</strong> Transfer indukan dari batch pembesaran |
          <strong>Dari Produksi:</strong> Gunakan stok telur dari produksi puyuh aktif
        </small>
      </div>
    </div>
  </div>
</div>