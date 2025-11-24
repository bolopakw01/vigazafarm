<!-- Detail Produksi -->
<div class="section-box">
  <h6 class="section-title dynamic">Detail Produksi</h6>
  <p class="text-muted mb-3">
    <i class="fa-solid fa-info-circle me-1"></i>
    Lengkapi detail batch produksi. Field yang bertanda <span class="required">*</span> wajib diisi.
  </p>

  <!-- Row 1: Batch ID & Jumlah -->
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <label for="batch_produksi_id" class="form-label">Batch Produksi ID</label>
      <input type="text" id="batch_produksi_id" name="batch_produksi_id" class="form-control" readonly style="background-color: #f1f5f9; font-weight: 600; color: #1e293b;" value="{{ old('batch_produksi_id') }}">
      <div class="form-text">
        <small class="text-muted">ID batch produksi (readonly). Akan ter-generate otomatis berdasarkan tanggal dan jenis input.</small>
      </div>
    </div>
    <div class="col-md-6" id="field_jumlah_burung_container">
      <label for="jumlah_burung" class="form-label">Jumlah Puyuh <span class="required">*</span></label>
      <input type="number" id="jumlah_burung" name="jumlah_burung" class="form-control field-auto-fill @error('jumlah_indukan') is-invalid @enderror" min="1" value="{{ old('jumlah_burung') }}">
      @error('jumlah_indukan')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text">
        <small class="text-muted field-hint-manual field-hint-pembesaran">Masukkan jumlah puyuh indukan yang akan diproduksi.</small>
        <small class="text-muted field-hint-pembesaran" style="display:none;">Akan terisi otomatis dari batch pembesaran yang dipilih.</small>
      </div>
    </div>
    <div class="col-md-6" id="field_jumlah_telur_container" style="display:none;">
      <label for="jumlah_telur" class="form-label">Jumlah Telur</label>
      <input type="number" id="jumlah_telur" name="jumlah_telur" class="form-control field-auto-fill" min="1" value="{{ old('jumlah_telur') }}">
      <div class="form-text">
        <small class="text-muted field-hint-manual field-hint-penetasan">Masukkan jumlah telur yang akan diproduksi.</small>
        <small class="text-muted field-hint-penetasan" style="display:none;">Akan terisi otomatis dari batch penetasan yang dipilih.</small>
      </div>
    </div>
  </div>

  <!-- Row 2: Jenis Kelamin (untuk burung) -->
  <div class="row g-3 mb-3" id="field_jenis_kelamin_container">
    <div class="col-md-6">
      <label class="form-label">Jenis Kelamin Puyuh <span class="required">*</span></label>
      @php
        $jenisKelamin = old('jenis_kelamin', 'jantan');
      @endphp
      <div class="d-flex flex-wrap gap-3 mt-2">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="jenis_kelamin" id="jantan" value="jantan" {{ $jenisKelamin == 'jantan' ? 'checked' : '' }}>
          <label class="form-check-label" for="jantan"><i class="fa-solid fa-mars me-1 text-dark"></i>Jantan</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="jenis_kelamin" id="betina" value="betina" {{ $jenisKelamin == 'betina' ? 'checked' : '' }}>
          <label class="form-check-label" for="betina"><i class="fa-solid fa-venus me-1 text-dark"></i>Betina</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="jenis_kelamin" id="campuran" value="campuran" {{ $jenisKelamin == 'campuran' ? 'checked' : '' }}>
          <label class="form-check-label" for="campuran"><i class="fa-solid fa-venus-mars me-1 text-dark"></i>Campuran</label>
        </div>
      </div>
    </div>
    <div id="campuranFields" class="col-12" style="display:none;">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="jumlah_jantan" class="form-label">Jumlah Jantan</label>
          <input type="number" id="jumlah_jantan" class="form-control" min="0" value="{{ old('jumlah_jantan') }}">
        </div>
        <div class="col-md-6">
          <label for="jumlah_betina" class="form-label">Jumlah Betina</label>
          <input type="number" id="jumlah_betina" class="form-control" min="0" value="{{ old('jumlah_betina') }}">
        </div>
        <div class="col-12">
          <div id="campuranValidationAlert" class="alert alert-info" style="display: none;">
            <i class="fa-solid fa-info-circle me-2"></i>
            Jumlah Jantan & Betina harus sama dengan Jumlah Puyuh
          </div>
          <div id="campuranSuccessAlert" class="alert alert-success" style="display: none;">
            <i class="fa-solid fa-check-circle me-2"></i>
            <strong>Sesuai:</strong> Jumlah Puyuh sudah sesuai
          </div>
          <div id="campuranErrorAlert" class="alert alert-danger" style="display: none;">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> Jumlah Puyuh tidak sesuai (current_sum) / (total_puyuh)
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 3: Tanggal Mulai & Tanggal Akhir -->
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="required">*</span></label>
      <div class="date-input-wrapper">
        <input type="date" id="tanggal_mulai" name="tanggal_mulai"
               class="form-control field-auto-fill date-input @error('tanggal_mulai') is-invalid @enderror"
               data-placeholder="Pilih tanggal mulai" required
               value="{{ old('tanggal_mulai', date('Y-m-d')) }}">
        <span class="placeholder-label">Pilih tanggal mulai</span>
      </div>
      @error('tanggal_mulai')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text">
        <small class="text-muted">Tanggal mulai batch produksi. Default: hari ini.</small>
      </div>
    </div>
    <div class="col-md-6">
      <label for="tanggal_akhir" class="form-label">Tanggal Akhir (expired)</label>
      <div class="date-input-wrapper">
         <input type="date" id="tanggal_akhir" name="tanggal_akhir"
           class="form-control date-input" data-placeholder="Pilih tanggal akhir" value="{{ old('tanggal_akhir') }}">
        <span class="placeholder-label">Pilih tanggal akhir</span>
      </div>
      <div class="form-text">
        <small class="text-muted">Tanggal akhir batch produksi (opsional)</small>
      </div>
    </div>
  </div>

  <!-- Row 4: Umur & Berat Rata-rata -->
  <div class="row g-3 mb-3" id="field_umur_berat_container">
    <div class="col-12">
      <div class="d-flex gap-3" style="width: 100%;">
        <div style="flex: 1; min-width: 0;" id="field_umur_container">
          <label for="umur_burung" class="form-label">Umur Puyuh (hari) <span class="required">*</span></label>
          <input type="number" id="umur_burung" name="umur_burung" class="form-control field-auto-fill" min="1" value="{{ old('umur_burung') }}">
          <div class="form-text">
            <small class="text-muted field-hint-manual field-hint-pembesaran">Masukkan umur puyuh saat mulai produksi.</small>
            <small class="text-muted field-hint-pembesaran" style="display:none;">Akan terisi otomatis dari batch pembesaran yang dipilih.</small>
          </div>
        </div>
        <div style="flex: 1; min-width: 0;">
          <label for="berat_rata_burung" class="form-label">Berat Rata-rata Puyuh (gram) <span class="required">*</span></label>
          <input type="number" step="0.01" id="berat_rata_burung" name="berat_rata_burung" class="form-control field-auto-fill" min="0" value="{{ old('berat_rata_burung') }}">
          <div class="form-text">
            <small class="text-muted">Berat rata-rata puyuh indukan.</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 4.5: Info fields from pembesaran (shown only when dari_pembesaran is selected) -->
  <div class="row g-3 mb-3" id="field_info_pembesaran_container" style="display:none;">
    <div class="col-12">
      <div class="d-flex gap-3" style="width: 100%;">
        <div style="flex: 1; min-width: 0;">
          <label for="umur_burung_pembesaran" class="form-label">Umur Puyuh (hari)</label>
          <input type="number" id="umur_burung_pembesaran" class="form-control field-auto-fill">
          <div class="form-text">
            <small class="text-muted">Umur puyuh dari batch pembesaran yang dipilih</small>
          </div>
        </div>
        <div style="flex: 1; min-width: 0;">
          <label for="berat_rata_burung_pembesaran" class="form-label">Berat Rata-rata Puyuh (gram)</label>
          <input type="number" step="0.01" id="berat_rata_burung_pembesaran" class="form-control field-auto-fill">
          <div class="form-text">
            <small class="text-muted">Berat rata-rata puyuh dari batch pembesaran yang dipilih</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 5: Persentase Fertil & Berat Rata-rata Telur (untuk telur) -->
  <div class="row g-3 mb-3" id="field_fertil_telur_container" style="display:none;">
    <div class="col-12">
      <div class="d-flex gap-3" style="width: 100%;">
        <div style="flex: 1; min-width: 0;">
          <label for="persentase_fertil" class="form-label">Persentase Fertil (%)</label>
          <input type="number" step="0.01" id="persentase_fertil" name="persentase_fertil" class="form-control field-auto-fill" min="0" max="100" value="{{ old('persentase_fertil') }}">
          <div class="form-text">
            <small class="text-muted">Persentase kesuburan telur.</small>
          </div>
        </div>
        <div style="flex: 1; min-width: 0;" id="field_berat_rata_telur_container" style="display:none;">
          <label for="berat_rata_telur" class="form-label">Berat Rata-rata Telur (gram)</label>
          <input type="number" step="0.01" id="berat_rata_telur" name="berat_rata_telur" class="form-control field-auto-fill" min="0" value="{{ old('berat_rata_telur') }}">
          <div class="form-text">
            <small class="text-muted">Berat rata-rata telur infertil</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 6: Status & Harga -->
  <div class="row g-3">
    <div class="col-md-6">
      <label for="status" class="form-label">Status <span class="required">*</span></label>
      @php
        $selectedStatus = old('status', 'aktif');
        if (in_array($selectedStatus, ['selesai', 'dibatalkan'])) {
            $selectedStatus = 'tidak_aktif';
        }
      @endphp
      <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
        <option value="">Pilih Status</option>
        <option value="aktif" {{ $selectedStatus === 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="tidak_aktif" {{ $selectedStatus === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
      </select>
      @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text">
        <small class="text-muted">Status batch produksi saat ini</small>
      </div>
    </div>
    <div class="col-md-6">
      <label for="harga_per_pcs" class="form-label" id="harga_label">Harga per Butir</label>
      <input type="number" step="0.01" id="harga_per_pcs" name="harga_per_pcs" class="form-control" min="0" value="{{ number_format(old('harga_per_pcs'), 0) }}">
      <div class="form-text">
        <small class="text-muted field-hint-manual field-hint-pembesaran" id="harga_hint_manual">Harga jual per ekor puyuh (opsional).</small>
        <small class="text-muted field-hint-penetasan" style="display:none;" id="harga_hint_penetasan">Harga jual per butir telur (opsional).</small>
      </div>
    </div>
  </div>
</div>