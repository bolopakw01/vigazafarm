@extends('admin.layouts.app')

@section('title', 'Tambah Data Penetasan')

@section('content')
<div class="bolopa-form-wrapper">
    <div class="bolopa-form-container">
        <!-- Header -->
        <div class="bolopa-form-header">
            <div>
                <h1>
                    <i class="fa-solid fa-egg"></i>
                    Tambah Data Penetasan
                </h1>
                <p class="text-muted mb-0">Formulir untuk menambah data penetasan baru</p>
            </div>
            <a href="{{ route('admin.penetasan') }}" class="bolopa-form-btn bolopa-form-btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form Card -->
        <div class="bolopa-form-card">
            <form action="{{ route('admin.penetasan.store') }}" method="POST" id="formPenetasan">
                @csrf

                <!-- Section: Informasi Kandang -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-warehouse"></i>
                        Informasi Kandang
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kandang_id" class="form-label">
                                Kandang Penetasan <span class="text-danger">*</span>
                            </label>
                            <select name="kandang_id" id="kandang_id" class="form-control" required>
                                <option value="">-- Pilih Kandang --</option>
                                @foreach($kandang as $k)
                                <option value="{{ $k->id }}" {{ old('kandang_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kandang }} (Kapasitas: {{ number_format($k->kapasitas) }})
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text">Pilih kandang yang akan digunakan untuk penetasan</small>
                        </div>
                    </div>
                </div>

                <!-- Section: Data Telur -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-egg"></i>
                        Data Telur & Penetasan
                    </h3>
                    <div class="alert alert-success" style="margin-bottom: 20px; border-left: 4px solid #198754;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-barcode" style="font-size: 24px;"></i>
                            <div style="flex: 1;">
                                <strong>Preview Kode Batch:</strong>
                                <div id="batchPreview" style="font-size: 18px; font-weight: bold; color: #198754; margin-top: 5px; font-family: 'Courier New', monospace; transition: transform 0.2s ease;">
                                    PTN-{{ date('Ymd') }}-<span id="randomPart">???</span>
                                </div>
                                <small style="color: #6c757d;">
                                    <i class="fa-solid fa-info-circle"></i> Kode akan di-generate otomatis saat data disimpan
                                </small>
                            </div>
                            <button type="button" id="refreshBatch" class="btn btn-sm btn-outline-success" style="padding: 5px 15px;">
                                <i class="fa-solid fa-refresh"></i> Preview Baru
                            </button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_simpan_telur" class="form-label">
                                Tanggal Mulai Penetasan <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_simpan_telur" id="tanggal_simpan_telur" 
                                   class="form-control" value="{{ old('tanggal_simpan_telur', date('Y-m-d')) }}" required>
                            <small class="form-text">Tanggal saat telur mulai disimpan di mesin tetas</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="estimasi_menetas" class="form-label">
                                Estimasi Tanggal Menetas
                            </label>
                            <input type="date" id="estimasi_menetas" 
                                   class="form-control" readonly style="background-color: #f8fafc; cursor: not-allowed;">
                            <small class="form-text">
                                <i class="fa-solid fa-clock text-primary"></i> 
                                Otomatis dihitung: 17-18 hari dari tanggal mulai
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_telur" class="form-label">
                                Jumlah Telur <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="jumlah_telur" id="jumlah_telur" 
                                   class="form-control" value="{{ old('jumlah_telur') }}" 
                                   min="1" placeholder="Contoh: 1000" required>
                            <small class="form-text">Total telur yang dimasukkan (dalam butir)</small>
                        </div>
                        <div class="form-group">
                            <label for="telur_tidak_fertil" class="form-label">
                                Telur Tidak Fertil
                            </label>
                            <input type="number" name="telur_tidak_fertil" id="telur_tidak_fertil" 
                                   class="form-control" value="{{ old('telur_tidak_fertil') }}" 
                                   min="0" placeholder="Contoh: 50">
                            <small class="form-text">Jumlah telur yang tidak fertil (jika sudah diketahui)</small>
                        </div>
                    </div>
                    
                    <!-- Info Card Estimasi -->
                    <div class="info-card" id="estimasiInfo" style="display: none;">
                        <div class="info-card-icon">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="info-card-content">
                            <h4>Informasi Estimasi Penetasan</h4>
                            <p class="mb-1">
                                <strong>Durasi Penetasan Burung Puyuh:</strong> 17-18 hari
                            </p>
                            <p class="mb-0">
                                <strong>Perkiraan Tanggal Menetas:</strong> <span id="estimasiText" class="text-primary fw-semibold">-</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section: Hasil Penetasan -->
                <div class="form-section" id="hasilPenetasanSection" style="display: none;">
                    <h3 class="section-title">
                        <i class="fa-solid fa-dove"></i>
                        Hasil Penetasan
                    </h3>
                    <div class="alert alert-success" id="alertPenetasanSelesai" style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; border-radius: 8px; padding: 12px 16px; font-size: 13px; display: none;">
                        <i class="fa-solid fa-check-circle"></i>
                        <strong>Penetasan Selesai!</strong> Anda dapat mengisi hasil penetasan sekarang.
                    </div>
                    <div class="alert alert-warning" id="alertMenungguPenetasan" style="background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; border-radius: 8px; padding: 12px 16px; font-size: 13px;">
                        <i class="fa-solid fa-clock"></i>
                        <strong>Proses Penetasan Berlangsung...</strong>
                        <p class="mb-0 mt-1">Estimasi penetasan selesai pada <strong id="tanggalEstimasiInfo">-</strong>. Bagian ini akan otomatis aktif saat penetasan sudah mencapai estimasi waktunya.</p>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_menetas" class="form-label">
                                Tanggal Menetas Aktual
                            </label>
                            <input type="date" name="tanggal_menetas" id="tanggal_menetas" 
                                   class="form-control" value="{{ old('tanggal_menetas') }}">
                            <small class="form-text">Tanggal aktual saat telur mulai menetas</small>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_menetas" class="form-label">
                                Jumlah Menetas
                            </label>
                            <input type="number" name="jumlah_menetas" id="jumlah_menetas" 
                                   class="form-control" value="{{ old('jumlah_menetas') }}" 
                                   min="0" placeholder="Contoh: 850">
                            <small class="form-text">Total telur yang berhasil menetas</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="jumlah_doc" class="form-label">
                                Jumlah DOC (Day Old Chick)
                            </label>
                            <input type="number" name="jumlah_doc" id="jumlah_doc" 
                                   class="form-control" value="{{ old('jumlah_doc') }}" 
                                   min="0" placeholder="Contoh: 840">
                            <small class="form-text">Jumlah anak ayam yang sehat dan layak pindah ke kandang pembesaran</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                Persentase Tetas (Estimasi)
                            </label>
                            <div class="form-control" id="persentase_display" style="background-color: #f8fafc; color: #64748b; font-weight: 500;">
                                -
                            </div>
                            <small class="form-text">
                                <i class="fa-solid fa-calculator text-primary"></i>
                                Otomatis dihitung dari: (Jumlah Menetas / Jumlah Telur) × 100%
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Section: Kondisi Lingkungan -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-temperature-half"></i>
                        Kondisi Lingkungan
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="suhu_penetasan" class="form-label">
                                Suhu Penetasan (°C)
                            </label>
                            <input type="number" name="suhu_penetasan" id="suhu_penetasan" 
                                   class="form-control" value="{{ old('suhu_penetasan') }}" 
                                   step="0.1" min="0" max="50" placeholder="Contoh: 37.5">
                            <small class="form-text">Suhu optimal: 37.0°C - 38.0°C</small>
                        </div>
                        <div class="form-group">
                            <label for="kelembaban_penetasan" class="form-label">
                                Kelembaban Penetasan (%)
                            </label>
                            <input type="number" name="kelembaban_penetasan" id="kelembaban_penetasan" 
                                   class="form-control" value="{{ old('kelembaban_penetasan') }}" 
                                   step="0.1" min="0" max="100" placeholder="Contoh: 65">
                            <small class="form-text">Kelembaban optimal: 55% - 65%</small>
                        </div>
                    </div>
                </div>

                <!-- Section: Catatan -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-regular fa-note-sticky"></i>
                        Catatan Tambahan
                    </h3>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="catatan" class="form-label">
                                Catatan
                            </label>
                            <textarea name="catatan" id="catatan" class="form-control" 
                                      rows="4" placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                            <small class="form-text">Catatan penting terkait proses penetasan</small>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.penetasan') }}" class="bolopa-form-btn bolopa-form-btn-secondary">
                        <i class="fa-solid fa-times"></i>
                        Batal
                    </a>
                    <button type="submit" class="bolopa-form-btn bolopa-form-btn-primary">
                        <i class="fa-solid fa-save"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Form Wrapper */
.bolopa-form-wrapper {
    width: 100%;
    max-width: 100%;
    padding: 0;
}

.bolopa-form-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Form Header */
.bolopa-form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2px;
    flex-wrap: wrap;
    gap: 16px;
    background: white;
    padding: 24px 32px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.bolopa-form-header h1 {
    font-size: 28px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.bolopa-form-header h1 i {
    color: #1e293b;
    font-size: 28px;
}

.bolopa-form-header p {
    font-size: 14px;
    color: #64748b;
}

/* Alert Styles */
.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background: #d1fae5;
    border: 1px solid #6ee7b7;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #991b1b;
}

.alert ul {
    padding-left: 20px;
}

/* Form Card */
.bolopa-form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 32px;
    margin-bottom: 24px;
}

/* Form Section */
.form-section {
    margin-bottom: 32px;
    padding-bottom: 32px;
    border-bottom: 1px solid #e2e8f0;
}

.form-section:last-of-type {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #3b82f6;
    font-size: 20px;
}

/* Form Row & Group */
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-row:last-child {
    margin-bottom: 0;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-size: 14px;
    font-weight: 500;
    color: #334155;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: white;
    transition: all 0.2s ease;
    font-family: 'Poppins', sans-serif;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control:disabled {
    background: #f1f5f9;
    cursor: not-allowed;
}

select.form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23334155' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 40px;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.form-text {
    font-size: 12px;
    color: #64748b;
    margin-top: 6px;
    display: block;
}

.text-danger {
    color: #dc2626;
}

.text-muted {
    color: #64748b;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
    justify-content: flex-end;
}

/* Form Buttons */
.bolopa-form-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
    font-family: 'Poppins', sans-serif;
}

.bolopa-form-btn-primary {
    background: #3b82f6;
    color: white;
}

.bolopa-form-btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);
}

.bolopa-form-btn-secondary {
    background: #f1f5f9;
    color: #475569;
}

.bolopa-form-btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-1px);
}

/* Info Card */
.info-card {
    display: flex;
    gap: 16px;
    padding: 16px;
    background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);
    border: 1px solid #93c5fd;
    border-radius: 10px;
    margin-top: 16px;
    align-items: flex-start;
}

.info-card-icon {
    width: 48px;
    height: 48px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
}

.info-card-icon i {
    font-size: 24px;
    color: #3b82f6;
}

.info-card-content {
    flex: 1;
}

.info-card-content h4 {
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 8px 0;
}

.info-card-content p {
    font-size: 13px;
    color: #475569;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .bolopa-form-container {
        padding: 0 16px;
    }

    .bolopa-form-card {
        padding: 20px;
    }

    .bolopa-form-header {
        flex-direction: column;
    }

    .bolopa-form-header h1 {
        font-size: 24px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .bolopa-form-btn {
        width: 100%;
        justify-content: center;
    }

    .section-title {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .bolopa-form-header h1 {
        font-size: 20px;
    }

    .bolopa-form-card {
        padding: 16px;
    }

    .form-section {
        margin-bottom: 24px;
        padding-bottom: 24px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalSimpanInput = document.getElementById('tanggal_simpan_telur');
    const estimasiMenetasInput = document.getElementById('estimasi_menetas');
    const estimasiInfo = document.getElementById('estimasiInfo');
    const estimasiText = document.getElementById('estimasiText');
    const jumlahTelurInput = document.getElementById('jumlah_telur');
    const jumlahMenetasInput = document.getElementById('jumlah_menetas');
    const persentaseDisplay = document.getElementById('persentase_display');
    const tanggalMenetasInput = document.getElementById('tanggal_menetas');
    const hasilPenetasanSection = document.getElementById('hasilPenetasanSection');
    const alertPenetasanSelesai = document.getElementById('alertPenetasanSelesai');
    const alertMenungguPenetasan = document.getElementById('alertMenungguPenetasan');
    const tanggalEstimasiInfo = document.getElementById('tanggalEstimasiInfo');
    
    // Format tanggal ke Indonesia
    function formatTanggalIndonesia(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }
    
    // Cek apakah sudah waktunya hasil penetasan (estimasi sudah tercapai atau lewat)
    function cekStatusPenetasan() {
        const estimasiDate = estimasiMenetasInput.value;
        if (!estimasiDate) {
            hasilPenetasanSection.style.display = 'none';
            return;
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const estimasi = new Date(estimasiDate);
        estimasi.setHours(0, 0, 0, 0);
        
        if (today >= estimasi) {
            // Sudah waktunya atau sudah lewat - tampilkan dan aktifkan section
            hasilPenetasanSection.style.display = 'block';
            alertPenetasanSelesai.style.display = 'block';
            
            // Enable semua input di section hasil
            const inputs = hasilPenetasanSection.querySelectorAll('input');
            inputs.forEach(input => {
                input.disabled = false;
                input.style.backgroundColor = 'white';
                input.style.cursor = 'text';
            });
        } else {
            // Belum waktunya - sembunyikan section sepenuhnya
            hasilPenetasanSection.style.display = 'none';
        }
    }
    
    // Hitung estimasi tanggal menetas (17-18 hari, kita pakai 17 hari sebagai estimasi)
    function hitungEstimasiMenetas() {
        const tanggalSimpan = tanggalSimpanInput.value;
        if (tanggalSimpan) {
            const date = new Date(tanggalSimpan);
            // Tambah 17 hari untuk estimasi
            date.setDate(date.getDate() + 17);
            
            // Format untuk input date (YYYY-MM-DD)
            const estimasiDate = date.toISOString().split('T')[0];
            estimasiMenetasInput.value = estimasiDate;
            
            // Format untuk tampilan info
            estimasiText.textContent = formatTanggalIndonesia(estimasiDate) + ' (± 1 hari)';
            estimasiInfo.style.display = 'flex';
            
            // Cek status penetasan setelah hitung estimasi
            cekStatusPenetasan();
        } else {
            estimasiMenetasInput.value = '';
            estimasiInfo.style.display = 'none';
            hasilPenetasanSection.style.display = 'none';
        }
    }
    
    // Hitung persentase tetas otomatis
    function hitungPersentase() {
        const jumlahTelur = parseFloat(jumlahTelurInput.value) || 0;
        const jumlahMenetas = parseFloat(jumlahMenetasInput.value) || 0;
        
        if (jumlahTelur > 0 && jumlahMenetas > 0) {
            const percentage = ((jumlahMenetas / jumlahTelur) * 100).toFixed(2);
            persentaseDisplay.textContent = percentage + '%';
            persentaseDisplay.style.color = '#3b82f6';
            persentaseDisplay.style.fontWeight = '600';
            
            // Tambah badge indikator
            let badge = '';
            if (percentage >= 85) {
                badge = ' 🟢 Optimal';
                persentaseDisplay.style.color = '#16a34a';
            } else if (percentage >= 70) {
                badge = ' 🟡 Cukup Baik';
                persentaseDisplay.style.color = '#f59e0b';
            } else if (percentage >= 60) {
                badge = ' 🟠 Perlu Pemantauan';
                persentaseDisplay.style.color = '#f97316';
            } else {
                badge = ' 🔴 Perlu Perbaikan';
                persentaseDisplay.style.color = '#dc2626';
            }
            persentaseDisplay.textContent = percentage + '%' + badge;
        } else {
            persentaseDisplay.textContent = '-';
            persentaseDisplay.style.color = '#64748b';
            persentaseDisplay.style.fontWeight = '500';
        }
    }
    
    // Event listeners
    if (tanggalSimpanInput) {
        // Hitung estimasi saat halaman load jika sudah ada nilai
        hitungEstimasiMenetas();
        
        tanggalSimpanInput.addEventListener('change', hitungEstimasiMenetas);
    }
    
    if (jumlahTelurInput && jumlahMenetasInput) {
        jumlahTelurInput.addEventListener('input', hitungPersentase);
        jumlahMenetasInput.addEventListener('input', hitungPersentase);
        
        // Hitung saat load jika ada old values
        hitungPersentase();
    }

    // Validasi tanggal menetas tidak boleh lebih awal dari tanggal simpan
    if (tanggalSimpanInput && tanggalMenetasInput) {
        tanggalMenetasInput.addEventListener('change', function() {
            const tanggalSimpan = new Date(tanggalSimpanInput.value);
            const tanggalMenetas = new Date(tanggalMenetasInput.value);
            
            if (tanggalMenetas < tanggalSimpan) {
                alert('⚠️ Tanggal menetas tidak boleh lebih awal dari tanggal mulai penetasan!');
                tanggalMenetasInput.value = '';
            }
        });
    }

    // Form validation before submit
    const form = document.getElementById('formPenetasan');
    if (form) {
        form.addEventListener('submit', function(e) {
            const kandangId = document.getElementById('kandang_id').value;
            const tanggalSimpan = document.getElementById('tanggal_simpan_telur').value;
            const jumlahTelur = document.getElementById('jumlah_telur').value;
            
            if (!kandangId || !tanggalSimpan || !jumlahTelur) {
                e.preventDefault();
                alert('⚠️ Harap lengkapi semua field yang wajib diisi (bertanda *)');
                return false;
            }
        });
    }

    // Batch Preview Generator
    function generateRandomBatch() {
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        document.getElementById('randomPart').textContent = random;
    }

    // Generate initial preview
    generateRandomBatch();

    // Refresh batch preview on button click
    const refreshBatchBtn = document.getElementById('refreshBatch');
    if (refreshBatchBtn) {
        refreshBatchBtn.addEventListener('click', function() {
            generateRandomBatch();
            // Add animation effect
            const preview = document.getElementById('batchPreview');
            preview.style.transform = 'scale(1.05)';
            setTimeout(() => {
                preview.style.transform = 'scale(1)';
            }, 200);
        });
    }
});
</script>
@endsection
