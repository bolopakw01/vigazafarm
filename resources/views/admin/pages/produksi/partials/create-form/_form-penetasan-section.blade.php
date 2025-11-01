<!-- Section Penetasan -->
<div id="penetasanSection" class="section-box" style="display:none;">
  <h6 class="section-title penetasan">Data Dari Penetasan</h6>
  <p class="text-muted mb-3">
    <i class="fa-solid fa-info-circle me-1"></i>
    Transfer telur infertil dari batch penetasan yang sudah selesai.
    Telur infertil akan digunakan untuk keperluan produksi lainnya.
  </p>
  <div class="row g-3">
    <div class="col-md-12">
      <label for="penetasan_id" class="form-label">Pilih Penetasan</label>
      <select id="penetasan_id" name="penetasan_id" class="form-select">
        <option value="">Pilih Penetasan</option>
        @foreach($penetasanList as $penetasan)
          <option value="{{ $penetasan->id }}"
                  data-tanggal-menetas="{{ $penetasan->tanggal_menetas ? $penetasan->tanggal_menetas->format('Y-m-d') : '' }}"
                  data-stok-tersedia="{{ $penetasan->telur_tidak_fertil - ($penetasan->telur_infertil_ditransfer ?? 0) }}">
            {{ $penetasan->batch }} - {{ $penetasan->tanggal_menetas ? $penetasan->tanggal_menetas->format('d/m/Y') : 'TBA' }} - Telur Infertil: {{ $penetasan->telur_tidak_fertil - ($penetasan->telur_infertil_ditransfer ?? 0) }} butir (Selesai)
          </option>
        @endforeach
      </select>
      <div class="form-text">
        <small class="text-muted">Pilih batch penetasan yang memiliki telur infertil</small>
      </div>
    </div>
  </div>
</div>