@extends('admin.layouts.app')

@section('title', 'Produksi - Tambah Data Produksi')

@push('styles')
<style>
    body {
      background: linear-gradient(135deg, #f0f5ff, #ffffff);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }

    .card {
      border: none;
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.06);
    }

    .card-header {
      background: white;
      color: #333;
      border-radius: 0;
      padding: 1rem 1.5rem 16px;
      border-bottom: 1px solid #e9ecef;
      margin-bottom: 24px;
    }

    .card-header h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #333;
      line-height: 1.2;
    }

    .card-header p {
      margin-bottom: 0;
      font-size: 0.9rem;
      opacity: 0.9;
    }

    .form-label {
      font-weight: 600;
      color: #333;
    }

    .form-control, .form-select {
      border-radius: 0.5rem;
      border: 1px solid #ced4da;
      transition: 0.2s;
    }

    .form-control:focus, .form-select:focus {
      border-color: #007bff;
      box-shadow: 0 0 0 0.15rem rgba(0,123,255,0.2);
    }

    .section-box {
      background: #f8fbff;
      border: 1px solid #e0e6f0;
      border-radius: 0.75rem;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .section-title {
      font-weight: 600;
      color: #0077b6;
      border-left: 4px solid #00b4d8;
      padding-left: 0.75rem;
      margin-bottom: 1rem;
    }

    .section-title.manual {
      color: #007bff;
      border-left-color: #007bff;
    }

    .section-title.pembesaran {
      color: #28a745;
      border-left-color: #28a745;
    }

    .section-title.penetasan {
      color: #fd7e14;
      border-left-color: #fd7e14;
    }

    .btn-primary {
      background: linear-gradient(90deg, #007bff, #0096c7);
      border: none;
      border-radius: 0.5rem;
      transition: 0.3s;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      background: linear-gradient(90deg, #0069d9, #0084b4);
    }

    .btn-secondary {
      border-radius: 0.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    @media (max-width: 768px) {
      .card-header h1 {
        font-size: 1.2rem;
      }
      .jenis-input-radios {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
      .btn-secondary {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        white-space: nowrap;
      }
      .btn-secondary i {
        font-size: 0.8rem;
      }
    }

    .field-auto-fill {
      transition: all 0.3s ease;
    }

    .field-auto-fill.auto-filled {
      background-color: #e8f5e8 !important;
      border-color: #28a745 !important;
      color: #155724 !important;
    }

    .detail-subsection {
      margin-bottom: 2rem;
      padding: 1.5rem;
      background: #f8f9fa;
      border-radius: 0.5rem;
      border-left: 4px solid #007bff;
    }

    .detail-subsection h6 {
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .field-hint-manual, .field-hint-pembesaran, .field-hint-penetasan {
      transition: opacity 0.3s ease;
    }

    .required {
      color: #dc3545;
      font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <img src="{{ asset('bolopa/img/icon/streamline-sharp--archive-box-solid.svg') }}" alt="icon" class="me-3" style="width: 36px; height: 36px;">
            <div>
              <h1 class="mb-1">Form Input Produksi</h1>
              <p class="mb-0 text-muted small">Formulir untuk menambah data produksi</p>
            </div>
          </div>
          <a href="{{ route('admin.produksi') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
          </a>
        </div>
        <div class="card-body p-4">
          <form id="produksiForm" action="{{ route('admin.produksi.store') }}" method="POST">
            @csrf

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
                  <select id="kandang_id" name="kandang_id" class="form-select" required>
                    <option value="">Pilih Kandang</option>
                    @foreach($kandangList as $kandang)
                      <option value="{{ $kandang->id }}">{{ $kandang->nama_kandang }} (Kapasitas: {{ $kandang->kapasitas_maksimal }}, Tipe: {{ ucfirst($kandang->tipe_kandang) }})</option>
                    @endforeach
                  </select>
                  <div class="form-text">
                    <small class="text-muted">Pilih kandang produksi yang akan digunakan untuk batch ini</small>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Jenis Input <span class="required">*</span></label>
                  <div class="d-flex flex-wrap gap-3 mt-2 jenis-input-radios">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jenis_input" id="manual" value="manual" checked>
                      <label class="form-check-label" for="manual"><i class="fa-solid fa-keyboard me-1"></i>Manual</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jenis_input" id="dari_pembesaran" value="dari_pembesaran">
                      <label class="form-check-label" for="dari_pembesaran"><i class="fa-solid fa-feather me-1"></i>Dari Pembesaran</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jenis_input" id="dari_penetasan" value="dari_penetasan">
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
                  <div class="d-flex flex-wrap gap-3 mt-2">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="fokus_manual" id="fokus_burung" value="burung" checked>
                      <label class="form-check-label" for="fokus_burung"><i class="fa-solid fa-feather me-1"></i>Puyuh</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="fokus_manual" id="fokus_telur" value="telur">
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
                <select id="pembesaran_id" name="pembesaran_id" class="form-select">
                  <option value="">Pilih Pembesaran</option>
                  @foreach($pembesaranList as $pembesaran)
                    <option value="{{ $pembesaran->id }}"
                            data-tanggal-siap="{{ $pembesaran->tanggal_siap ? $pembesaran->tanggal_siap->format('Y-m-d') : '' }}"
                            data-stok-tersedia="{{ $pembesaran->jumlah_siap ? ($pembesaran->jumlah_siap - ($pembesaran->indukan_ditransfer ?? 0)) : 0 }}"
                            data-umur-hari="{{ $pembesaran->umur_hari }}">
                      {{ $pembesaran->batch_produksi_id }} - Stok: {{ $pembesaran->jumlah_siap ? ($pembesaran->jumlah_siap - ($pembesaran->indukan_ditransfer ?? 0)) : 0 }} ekor
                    </option>
                  @endforeach
                </select>
                <div class="form-text">
                  <small class="text-muted">Pilih batch pembesaran yang memiliki indukan siap produksi</small>
                </div>
              </div>
            </div>

            <!-- Section Penetasan -->
            <div id="penetasanSection" class="section-box" style="display:none;">
              <h6 class="section-title penetasan">Data Dari Penetasan</h6>
              <p class="text-muted mb-3">
                <i class="fa-solid fa-info-circle me-1"></i>
                Transfer telur infertil dari batch penetasan yang sudah selesai.
                Telur infertil akan digunakan untuk keperluan produksi lainnya.
              </p>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="penetasan_id" class="form-label">Pilih Penetasan</label>
                  <select id="penetasan_id" name="penetasan_id" class="form-select">
                    <option value="">Pilih Penetasan</option>
                    @foreach($penetasanList as $penetasan)
                      <option value="{{ $penetasan->id }}"
                              data-tanggal-menetas="{{ $penetasan->tanggal_menetas ? $penetasan->tanggal_menetas->format('Y-m-d') : '' }}"
                              data-stok-tersedia="{{ $penetasan->telur_tidak_fertil - ($penetasan->telur_infertil_ditransfer ?? 0) }}">
                        {{ $penetasan->batch }} - Telur Infertil: {{ $penetasan->telur_tidak_fertil - ($penetasan->telur_infertil_ditransfer ?? 0) }} butir
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">
                    <small class="text-muted">Pilih batch penetasan yang memiliki telur infertil</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <label for="jumlah_telur" class="form-label">Jumlah Telur</label>
                  <input type="number" id="jumlah_telur" name="jumlah_telur" class="form-control" min="0">
                  <div class="form-text">
                    <small class="text-muted">Akan terisi otomatis saat memilih penetasan</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <label for="berat_rata_telur" class="form-label">Berat Rata-rata (gram)</label>
                  <input type="number" step="0.01" id="berat_rata_telur" name="berat_rata_telur" class="form-control" min="0">
                  <div class="form-text">
                    <small class="text-muted">Berat rata-rata telur infertil</small>
                  </div>
                </div>
              </div>
            </div>

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
                  <input type="text" id="batch_produksi_id" name="batch_produksi_id" class="form-control" readonly style="background-color: #f1f5f9; font-weight: 600; color: #1e293b;">
                  <div class="form-text">
                    <small class="text-muted">ID batch produksi (readonly). Akan ter-generate otomatis berdasarkan tanggal dan jenis input.</small>
                  </div>
                </div>
                <div class="col-md-6" id="field_jumlah_burung_container">
                  <label for="jumlah_burung" class="form-label">Jumlah Puyuh <span class="required">*</span></label>
                  <input type="number" id="jumlah_burung" name="jumlah_burung" class="form-control field-auto-fill" required min="1">
                  <div class="form-text">
                    <small class="text-muted field-hint-manual field-hint-pembesaran">Masukkan jumlah puyuh indukan yang akan diproduksi.</small>
                    <small class="text-muted field-hint-pembesaran" style="display:none;">Akan terisi otomatis dari batch pembesaran yang dipilih.</small>
                  </div>
                </div>
                <div class="col-md-6" id="field_jumlah_telur_container" style="display:none;">
                  <label for="jumlah_telur" class="form-label">Jumlah Telur</label>
                  <input type="number" id="jumlah_telur" name="jumlah_telur" class="form-control field-auto-fill" min="1">
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
                  <div class="d-flex flex-wrap gap-3 mt-2">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jenis_kelamin" id="jantan" value="jantan" checked>
                      <label class="form-check-label" for="jantan"><i class="fa-solid fa-mars me-1 text-dark"></i>Jantan</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jenis_kelamin" id="betina" value="betina">
                      <label class="form-check-label" for="betina"><i class="fa-solid fa-venus me-1 text-dark"></i>Betina</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jenis_kelamin" id="campuran" value="campuran">
                      <label class="form-check-label" for="campuran"><i class="fa-solid fa-venus-mars me-1 text-dark"></i>Campuran</label>
                    </div>
                  </div>
                </div>
                <div id="campuranFields" class="col-12" style="display:none;">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="jumlah_jantan" class="form-label">Jumlah Jantan</label>
                      <input type="number" id="jumlah_jantan" name="jumlah_jantan" class="form-control" min="0">
                    </div>
                    <div class="col-md-6">
                      <label for="jumlah_betina" class="form-label">Jumlah Betina</label>
                      <input type="number" id="jumlah_betina" name="jumlah_betina" class="form-control" min="0">
                    </div>
                  </div>
                </div>
              </div>

              <!-- Row 3: Tanggal Mulai & Tanggal Akhir -->
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="required">*</span></label>
                  <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control field-auto-fill" required value="{{ date('Y-m-d') }}">
                  <div class="form-text">
                    <small class="text-muted">Tanggal mulai batch produksi. Default: hari ini.</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="tanggal_akhir" class="form-label">Tanggal Akhir (expired)</label>
                  <input type="date" id="tanggal_akhir" name="tanggal_akhir" class="form-control">
                  <div class="form-text">
                    <small class="text-muted">Tanggal akhir batch produksi (opsional)</small>
                  </div>
                </div>
              </div>

              <!-- Row 4: Umur & Berat Rata-rata -->
              <div class="row g-3 mb-3" id="field_umur_berat_container">
                <div class="col-md-6" id="field_umur_container">
                  <label for="umur_burung" class="form-label">Umur Puyuh (hari) <span class="required">*</span></label>
                  <input type="number" id="umur_burung" name="umur_burung" class="form-control field-auto-fill" required min="1">
                  <div class="form-text">
                    <small class="text-muted field-hint-manual field-hint-pembesaran">Masukkan umur puyuh saat mulai produksi.</small>
                    <small class="text-muted field-hint-pembesaran" style="display:none;">Akan terisi otomatis dari batch pembesaran yang dipilih.</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="berat_rata_burung" class="form-label">Berat Rata-rata Puyuh (gram) <span class="required">*</span></label>
                  <input type="number" step="0.01" id="berat_rata_burung" name="berat_rata_burung" class="form-control" required min="0">
                  <div class="form-text">
                    <small class="text-muted">Berat rata-rata puyuh indukan.</small>
                  </div>
                </div>
              </div>

              <!-- Row 5: Persentase Fertil & Berat Rata-rata Telur (untuk telur) -->
              <div class="row g-3 mb-3" id="field_fertil_telur_container" style="display:none;">
                <div class="col-md-6">
                  <label for="persentase_fertil" class="form-label">Persentase Fertil (%)</label>
                  <input type="number" step="0.01" id="persentase_fertil" name="persentase_fertil" class="form-control" min="0" max="100">
                  <div class="form-text">
                    <small class="text-muted">Persentase kesuburan telur.</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="berat_rata_telur" class="form-label">Berat Rata-rata Telur (gram)</label>
                  <input type="number" step="0.01" id="berat_rata_telur" name="berat_rata_telur" class="form-control" min="0">
                  <div class="form-text">
                    <small class="text-muted">Berat rata-rata telur.</small>
                  </div>
                </div>
              </div>

              <!-- Row 6: Status & Harga -->
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="status" class="form-label">Status <span class="required">*</span></label>
                  <select id="status" name="status" class="form-select" required>
                    <option value="">Pilih Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="selesai">Selesai</option>
                  </select>
                  <div class="form-text">
                    <small class="text-muted">Status batch produksi saat ini</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="harga_per_kg" class="form-label">Harga per KG</label>
                  <input type="number" step="0.01" id="harga_per_kg" name="harga_per_kg" class="form-control" min="0">
                  <div class="form-text">
                    <small class="text-muted field-hint-manual field-hint-pembesaran">Harga jual per kilogram telur (opsional).</small>
                    <small class="text-muted field-hint-penetasan" style="display:none;">Harga jual per butir telur (opsional).</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Catatan -->
            <div class="section-box">
              <h6 class="section-title dynamic">Catatan Tambahan</h6>
              <p class="text-muted mb-3">
                <i class="fa-solid fa-info-circle me-1"></i>
                Tambahkan catatan penting tentang batch produksi ini (opsional).
              </p>
              <textarea id="catatan" name="catatan" class="form-control" rows="3" placeholder="Tulis catatan tambahan..."></textarea>
              <div class="form-text">
                <small class="text-muted">Catatan tambahan tentang batch produksi ini (opsional)</small>
              </div>
            </div>

            <!-- Tombol -->
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-secondary" onclick="resetForm()">
                <i class="fa-solid fa-times me-2"></i>Batal
              </button>
              <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-2"></i>Simpan Data
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // GLOBAL helpers so resetForm can call toggleSections()
  function generateBatchId() {
    const tanggalMulaiEl = document.getElementById('tanggal_mulai');
    const batchIdEl = document.getElementById('batch_produksi_id');

    const tanggalMulai = tanggalMulaiEl.value;
    if (!tanggalMulai) return;

    // Format date as YYYYMMDD
    const date = new Date(tanggalMulai);
    const dateStr = date.getFullYear().toString() +
                    (date.getMonth() + 1).toString().padStart(2, '0') +
                    date.getDate().toString().padStart(2, '0');

    // Determine prefix based on jenis_input and fokus_manual
    const jenisInput = document.querySelector('input[name="jenis_input"]:checked').value;
    let prefix = 'PROD';

    if (jenisInput === 'dari_penetasan') {
      prefix = 'PROD-TEL';
    } else if (jenisInput === 'manual') {
      const fokusManual = document.querySelector('input[name="fokus_manual"]:checked').value;
      prefix = fokusManual === 'telur' ? 'PROD-TEL' : 'PROD-PUY';
    } else if (jenisInput === 'dari_pembesaran') {
      prefix = 'PROD-PUY';
    }

    // Generate a simple sequential number using time
    const timestamp = Date.now().toString().slice(-4); // Last 4 digits of timestamp
    const batchId = `${prefix}-${dateStr}-${timestamp}`;

    batchIdEl.value = batchId;
  }

  function toggleCampuranFields() {
    const campuranFields = document.getElementById('campuranFields');
    const selectedKelamin = document.querySelector('input[name="jenis_kelamin"]:checked');
    if (selectedKelamin && selectedKelamin.value === 'campuran') {
      campuranFields.style.display = 'block';
    } else {
      campuranFields.style.display = 'none';
    }
  }

  function toggleFokusManual() {
    // Call toggleSections to update field visibility and required attributes
    toggleSections();
    // Regenerate batch ID when fokus_manual changes
    generateBatchId();
  }

  function toggleCampuranFieldsManual() {
    const campuranFieldsManual = document.getElementById('campuranFieldsManual');
    const selectedKelamin = document.querySelector('input[name="jenis_kelamin"]:checked');
    if (selectedKelamin && selectedKelamin.value === 'campuran') {
      campuranFieldsManual.style.display = 'block';
    } else {
      campuranFieldsManual.style.display = 'none';
    }
  }

  function autoFillFromPembesaran() {
    const select = document.getElementById('pembesaran_id');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
      // Auto-fill tanggal_mulai from pembesaran tanggal_siap
      const tanggalSiap = selectedOption.getAttribute('data-tanggal-siap');
      if (tanggalSiap) {
        document.getElementById('tanggal_mulai').value = tanggalSiap;
        document.getElementById('tanggal_mulai').classList.add('auto-filled');
        // Regenerate batch ID with new date
        generateBatchId();
      }
    }
  }

  function autoFillFromPenetasan() {
    const select = document.getElementById('penetasan_id');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
      // Auto-fill tanggal_mulai from penetasan tanggal_menetas
      const tanggalMenetas = selectedOption.getAttribute('data-tanggal-menetas');
      if (tanggalMenetas) {
        document.getElementById('tanggal_mulai').value = tanggalMenetas;
        document.getElementById('tanggal_mulai').classList.add('auto-filled');
        // Regenerate batch ID with new date
        generateBatchId();
      }
    }
  }

  function toggleSections() {
    const selected = document.querySelector('input[name="jenis_input"]:checked').value;

    // Show/hide sections based on jenis_input
    const manualSection = document.getElementById('manualSection');
    const pembesaranSection = document.getElementById('pembesaranSection');
    const penetasanSection = document.getElementById('penetasanSection');

    manualSection.style.display = selected === 'manual' ? 'block' : 'none';
    pembesaranSection.style.display = selected === 'dari_pembesaran' ? 'block' : 'none';
    penetasanSection.style.display = selected === 'dari_penetasan' ? 'block' : 'none';

    // Show/hide fields based on jenis_input and fokus_manual
    const fieldJumlahBurung = document.getElementById('field_jumlah_burung_container');
    const fieldJumlahTelur = document.getElementById('field_jumlah_telur_container');
    const fieldJenisKelamin = document.getElementById('field_jenis_kelamin_container');
    const fieldUmurBerat = document.getElementById('field_umur_berat_container');
    const fieldFertilTelur = document.getElementById('field_fertil_telur_container');

    // Get field elements
    const jumlahBurungField = document.getElementById('jumlah_burung');
    const jumlahTelurField = document.getElementById('jumlah_telur');
    const persentaseFertilField = document.getElementById('persentase_fertil');
    const beratRataTelurField = document.getElementById('berat_rata_telur');

    if (selected === 'manual') {
      const fokus = document.querySelector('input[name="fokus_manual"]:checked').value;
      if (fokus === 'burung') {
        fieldJumlahBurung.style.display = 'block';
        fieldJumlahTelur.style.display = 'none';
        fieldJenisKelamin.style.display = 'block';
        fieldUmurBerat.style.display = 'block';
        fieldFertilTelur.style.display = 'none';

        // Set required attributes
        jumlahBurungField.required = true;
        jumlahTelurField.required = false;
        persentaseFertilField.required = false;
        beratRataTelurField.required = false;
      } else {
        fieldJumlahBurung.style.display = 'none';
        fieldJumlahTelur.style.display = 'block';
        fieldJenisKelamin.style.display = 'none';
        fieldUmurBerat.style.display = 'none';
        fieldFertilTelur.style.display = 'block';

        // Set required attributes
        jumlahBurungField.required = false;
        jumlahTelurField.required = true;
        persentaseFertilField.required = true;
        beratRataTelurField.required = true;
      }
    } else if (selected === 'dari_pembesaran') {
      fieldJumlahBurung.style.display = 'block';
      fieldJumlahTelur.style.display = 'none';
      fieldJenisKelamin.style.display = 'block';
      fieldUmurBerat.style.display = 'block';
      fieldFertilTelur.style.display = 'none';

      // Set required attributes
      jumlahBurungField.required = true;
      jumlahTelurField.required = false;
      persentaseFertilField.required = false;
      beratRataTelurField.required = false;
    } else if (selected === 'dari_penetasan') {
      fieldJumlahBurung.style.display = 'none';
      fieldJumlahTelur.style.display = 'block';
      fieldJenisKelamin.style.display = 'none';
      fieldUmurBerat.style.display = 'none';
      fieldFertilTelur.style.display = 'block';

      // Set required attributes
      jumlahBurungField.required = false;
      jumlahTelurField.required = true;
      persentaseFertilField.required = true;
      beratRataTelurField.required = false; // Not required for penetasan transfer
    }

    // Update dynamic titles
    const dynamicTitles = document.querySelectorAll('.dynamic');
    dynamicTitles.forEach(title => {
      title.classList.remove('manual', 'pembesaran', 'penetasan');
      if (selected === 'manual') title.classList.add('manual');
      else if (selected === 'dari_pembesaran') title.classList.add('pembesaran');
      else if (selected === 'dari_penetasan') title.classList.add('penetasan');
    });

    // Update field hints and visibility based on jenis_input
    updateFieldHints(selected);

    // Update required asterisks in labels
    updateRequiredLabels();

    // regenerate batch id when jenis_input changes
    generateBatchId();
  }

  function updateRequiredLabels() {
    // Get field elements
    const jumlahBurungField = document.getElementById('jumlah_burung');
    const jumlahTelurField = document.getElementById('jumlah_telur');
    const persentaseFertilField = document.getElementById('persentase_fertil');
    const beratRataTelurField = document.getElementById('berat_rata_telur');

    // Get label elements
    const jumlahBurungLabel = document.querySelector('label[for="jumlah_burung"]');
    const jumlahTelurLabel = document.querySelector('label[for="jumlah_telur"]');
    const persentaseFertilLabel = document.querySelector('label[for="persentase_fertil"]');
    const beratRataTelurLabel = document.querySelector('label[for="berat_rata_telur"]');

    // Update labels with required asterisks
    if (jumlahBurungLabel) {
      const baseText = 'Jumlah Puyuh';
      jumlahBurungLabel.innerHTML = baseText + (jumlahBurungField.required ? ' <span class="required">*</span>' : '');
    }

    if (jumlahTelurLabel) {
      const baseText = 'Jumlah Telur';
      jumlahTelurLabel.innerHTML = baseText + (jumlahTelurField.required ? ' <span class="required">*</span>' : '');
    }

    if (persentaseFertilLabel) {
      const baseText = 'Persentase Fertil (%)';
      persentaseFertilLabel.innerHTML = baseText + (persentaseFertilField.required ? ' <span class="required">*</span>' : '');
    }

    if (beratRataTelurLabel) {
      const baseText = 'Berat Rata-rata Telur (gram)';
      beratRataTelurLabel.innerHTML = baseText + (beratRataTelurField.required ? ' <span class="required">*</span>' : '');
    }
  }

  function updateFieldHints(jenisInput) {
    // Hide all hints first
    document.querySelectorAll('.field-hint-manual, .field-hint-pembesaran, .field-hint-penetasan').forEach(hint => {
      hint.style.display = 'none';
    });

    // Show relevant hints
    const manualHints = document.querySelectorAll('.field-hint-manual');
    const pembesaranHints = document.querySelectorAll('.field-hint-pembesaran');
    const penetasanHints = document.querySelectorAll('.field-hint-penetasan');

    if (jenisInput === 'manual') {
      manualHints.forEach(hint => hint.style.display = 'block');
    } else if (jenisInput === 'dari_pembesaran') {
      pembesaranHints.forEach(hint => hint.style.display = 'block');
    } else if (jenisInput === 'dari_penetasan') {
      penetasanHints.forEach(hint => hint.style.display = 'block');
    }

    // Reset auto-filled styling
    document.querySelectorAll('.field-auto-fill').forEach(field => {
      field.classList.remove('auto-filled');
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today for tanggal_mulai
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_mulai').value = today;

    // Generate initial batch ID
    generateBatchId();

    // Attach change listeners
    const jenisInputRadios = document.querySelectorAll('input[name="jenis_input"]');
    jenisInputRadios.forEach(r => r.addEventListener('change', toggleSections));

    // Attach change listeners for fokus_manual
    const fokusManualRadios = document.querySelectorAll('input[name="fokus_manual"]');
    fokusManualRadios.forEach(r => r.addEventListener('change', toggleFokusManual));

    // Attach change listeners for jenis_kelamin
    const jenisKelaminRadios = document.querySelectorAll('input[name="jenis_kelamin"]');
    jenisKelaminRadios.forEach(r => r.addEventListener('change', toggleCampuranFields));

    // Update batch ID when tanggal_mulai changes
    document.getElementById('tanggal_mulai').addEventListener('change', generateBatchId);

    // Store pembesaran and penetasan data for auto-fill
    const pembesaranData = {!! json_encode($pembesaranList ? $pembesaranList->map(function($p) {
      return [
        'id' => $p->id,
        'stok_tersedia' => $p->jumlah_siap ? ($p->jumlah_siap - ($p->indukan_ditransfer ?? 0)) : 0,
        'tanggal_siap' => $p->tanggal_siap ? $p->tanggal_siap->format('Y-m-d') : null,
        'umur_hari' => $p->umur_hari
      ];
    })->toArray() : []) !!};

    const penetasanData = {!! json_encode($penetasanList ? $penetasanList->map(function($p) {
      return [
        'id' => $p->id,
        'stok_tersedia' => $p->telur_tidak_fertil - ($p->telur_infertil_ditransfer ?? 0),
        'tanggal_menetas' => $p->tanggal_menetas ? $p->tanggal_menetas->format('Y-m-d') : null
      ];
    })->toArray() : []) !!};

    // Auto-fill jumlah_burung when pembesaran is selected
    document.getElementById('pembesaran_id').addEventListener('change', function() {
      const selectedId = this.value;
      const jumlahBurungField = document.getElementById('jumlah_burung');
      const umurBurungField = document.getElementById('umur_burung');

      if (selectedId) {
        const pembesaran = pembesaranData.find(p => p.id == selectedId);
        if (pembesaran) {
          jumlahBurungField.value = pembesaran.stok_tersedia;
          umurBurungField.value = pembesaran.umur_hari || '';
          jumlahBurungField.classList.add('auto-filled');
          umurBurungField.classList.add('auto-filled');
        }
      } else {
        jumlahBurungField.value = '';
        umurBurungField.value = '';
        jumlahBurungField.classList.remove('auto-filled');
        umurBurungField.classList.remove('auto-filled');
      }

      // Call the new auto-fill function for production fields
      autoFillFromPembesaran();
    });

    // Auto-fill jumlah_telur when penetasan is selected
    document.getElementById('penetasan_id').addEventListener('change', function() {
      const selectedId = this.value;
      const jumlahTelurField = document.getElementById('jumlah_telur');

      if (selectedId) {
        const penetasan = penetasanData.find(p => p.id == selectedId);
        if (penetasan) {
          jumlahTelurField.value = penetasan.stok_tersedia;
        }
      } else {
        jumlahTelurField.value = '';
      }

      // Call the new auto-fill function for production fields
      autoFillFromPenetasan();
    });

    // initial UI state
    toggleSections();
  });

  function resetForm() {
    document.getElementById('produksiForm').reset();
    // Reset tanggal_mulai to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_mulai').value = today;
    // Reset auto-filled styling
    document.querySelectorAll('.auto-filled').forEach(field => {
      field.classList.remove('auto-filled');
    });
    // regenerate values and UI state
    toggleSections();
    toggleFokusManual();
    toggleCampuranFields();
    toggleCampuranFieldsManual();
    generateBatchId();
  }
</script>
@endpush

@endsection