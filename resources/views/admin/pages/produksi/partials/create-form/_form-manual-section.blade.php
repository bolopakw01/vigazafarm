<!-- Section Manual -->
<div id="manualSection" class="section-box">
  <h6 class="section-title manual">Data Dari Manual</h6>
  <p class="text-muted mb-3">
    <i class="fa-solid fa-info-circle me-1"></i>
    Isi data indukan secara manual jika tidak berasal dari pembesaran atau penetasan.
  </p>
  <div class="row g-3">
    <div class="col-12">
      <label class="form-label">Fokus Input Manual <span class="required">*</span></label>
      @php
        $fokusManual = old('fokus_manual', 'burung');
      @endphp
      <div class="d-flex flex-wrap gap-3 mt-2">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="fokus_manual" id="fokus_burung" value="burung" {{ $fokusManual == 'burung' ? 'checked' : '' }}>
          <label class="form-check-label" for="fokus_burung"><i class="fa-solid fa-feather me-1"></i>Puyuh</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="fokus_manual" id="fokus_telur" value="telur" {{ $fokusManual == 'telur' ? 'checked' : '' }}>
          <label class="form-check-label" for="fokus_telur"><i class="fa-solid fa-egg me-1"></i>Telur</label>
        </div>
      </div>
      <div class="form-text">
        <small class="text-muted">
          <strong>Puyuh:</strong> Input data puyuh indukan untuk produksi |
          <strong>Telur:</strong> Input data telur untuk penetasan
        </small>
      </div>
    </div>
  </div>
</div>