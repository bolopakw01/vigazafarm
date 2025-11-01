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
      <select id="kandang_id" name="kandang_id" class="form-select @error('kandang_id') is-invalid @enderror" required>
        <option value="">Pilih Kandang</option>
        @foreach($kandangList as $kandang)
    <option value="{{ $kandang->id }}" {{ old('kandang_id') == $kandang->id ? 'selected' : '' }}>
      {{ $kandang->nama_dengan_detail }}
    </option>
        @endforeach
      </select>
      @error('kandang_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text">
        <small class="text-muted">Pilih kandang produksi yang akan digunakan untuk batch ini</small>
      </div>
    </div>

    <div class="col-md-6">
      <label class="form-label">Jenis Input <span class="required">*</span></label>
      @php
        $jenisInput = old('jenis_input', 'manual');
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
          <input class="form-check-input" type="radio" name="jenis_input" id="dari_penetasan" value="dari_penetasan" {{ $jenisInput == 'dari_penetasan' ? 'checked' : '' }}>
          <label class="form-check-label" for="dari_penetasan"><i class="fa-solid fa-egg me-1"></i>Dari Penetasan</label>
        </div>
      </div>
      <div class="form-text">
        <small class="text-muted">
          <strong>Manual:</strong> Input indukan secara manual |
          <strong>Dari Pembesaran:</strong> Transfer indukan dari batch pembesaran |
          <strong>Dari Penetasan:</strong> Transfer telur infertil dari penetasan
        </small>
      </div>
    </div>
  </div>
</div>