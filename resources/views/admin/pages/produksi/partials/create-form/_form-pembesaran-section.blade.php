<!-- Section Pembesaran -->
<div id="pembesaranSection" class="section-box" style="display:none;">
  <h6 class="section-title pembesaran">Data Dari Pembesaran</h6>
  <p class="text-muted mb-3">
    <i class="fa-solid fa-info-circle me-1"></i>
    Transfer indukan dari batch pembesaran yang sudah siap produksi.
    Pilih batch yang memiliki stok indukan tersedia.
  </p>
  <div class="col-12">
    <label for="pembesaran_id" class="form-label">Pilih Pembesaran</label>
    <select id="pembesaran_id" name="pembesaran_id" class="form-select @error('pembesaran_id') is-invalid @enderror">
      <option value="">Pilih Pembesaran</option>
      @foreach($pembesaranList as $pembesaran)
  <option value="{{ $pembesaran->id }}"
    data-tanggal-siap="{{ $pembesaran->tanggal_siap ? $pembesaran->tanggal_siap->format('Y-m-d') : '' }}"
    data-stok-tersedia="{{ $pembesaran->jumlah_siap ? ($pembesaran->jumlah_siap - ($pembesaran->indukan_ditransfer ?? 0)) : 0 }}"
    data-umur-hari="{{ $pembesaran->umur_hari }}"
    data-berat-rata="{{ $pembesaran->berat_rata_rata ?? 0 }}"
    data-jenis-kelamin="{{ $pembesaran->jenis_kelamin ?? '' }}"
    data-jumlah-siap="{{ $pembesaran->jumlah_siap ?? 0 }}"
    data-jumlah-jantan="{{ $pembesaran->jumlah_jantan ?? '' }}"
    data-jumlah-betina="{{ $pembesaran->jumlah_betina ?? '' }}"
                {{ old('pembesaran_id') == $pembesaran->id ? 'selected' : '' }}>
          {{ $pembesaran->batch_label }} - {{ $pembesaran->tanggal_siap ? $pembesaran->tanggal_siap->format('d/m/Y') : 'TBA' }} - Stok: {{ $pembesaran->jumlah_siap ? ($pembesaran->jumlah_siap - ($pembesaran->indukan_ditransfer ?? 0)) : 0 }} ekor (Selesai)
        </option>
      @endforeach
    </select>
    @error('pembesaran_id')
      <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <div class="form-text">
      <small class="text-muted">Pilih batch pembesaran yang memiliki indukan siap produksi</small>
    </div>
  </div>
</div>