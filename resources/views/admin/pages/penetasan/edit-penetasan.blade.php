@extends('admin.layouts.app')

@section('title', 'Edit Data Penetasan')

@section('content')
<div class="bolopa-form-wrapper">
    <div class="bolopa-form-container">
        <!-- Header -->
        <div class="bolopa-form-header">
            <div>
                <h1>
                    <i class="fa-solid fa-pen-to-square"></i>
                    Edit Data Penetasan
                </h1>
                <p class="text-muted mb-0">Formulir untuk mengubah data penetasan</p>
            </div>
            <a href="{{ route('admin.penetasan') }}" class="bolopa-form-btn bolopa-form-btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>

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
            <form action="{{ route('admin.penetasan.update', $penetasan->id) }}" method="POST" id="formPenetasan">
                @csrf
                @method('PATCH')

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
                                @php
                                    if (isset($kandang) && $kandang instanceof \Illuminate\Support\Collection && $kandang->isNotEmpty()) {
                                        $availableKandangs = $kandang;
                                    } elseif (isset($kandang) && is_array($kandang) && !empty($kandang)) {
                                        $availableKandangs = collect($kandang);
                                    } elseif (isset($kandangList) && $kandangList instanceof \Illuminate\Support\Collection && $kandangList->isNotEmpty()) {
                                        $availableKandangs = $kandangList;
                                    } elseif (isset($kandangList) && is_array($kandangList) && !empty($kandangList)) {
                                        $availableKandangs = collect($kandangList);
                                    } elseif (isset($kandangs) && $kandangs instanceof \Illuminate\Support\Collection && $kandangs->isNotEmpty()) {
                                        $availableKandangs = $kandangs;
                                    } elseif (isset($kandangs) && is_array($kandangs) && !empty($kandangs)) {
                                        $availableKandangs = collect($kandangs);
                                    } else {
                                        $availableKandangs = collect();
                                    }
                                @endphp
                                @foreach($availableKandangs as $k)
                                <option value="{{ $k->id }}" {{ old('kandang_id', $penetasan->kandang_id) == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_dengan_detail }}
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text">Pilih kandang yang digunakan untuk penetasan</small>
                        </div>
                    </div>
                </div>

                <!-- Section: Data Telur -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-egg"></i>
                        Data Telur & Penetasan
                    </h3>
                    <div class="alert alert-secondary" style="margin-bottom: 20px;">
                        <i class="fa-solid fa-barcode"></i>
                        <strong>Kode Batch:</strong> {{ $penetasan->batch ?? 'Belum ada batch' }}
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_simpan_telur" class="form-label">
                                Tanggal Mulai Penetasan <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_simpan_telur" id="tanggal_simpan_telur" 
                                   class="form-control" value="{{ old('tanggal_simpan_telur', $penetasan->tanggal_simpan_telur?->format('Y-m-d')) }}" required>
                            <small class="form-text">Tanggal saat telur mulai disimpan di mesin tetas</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="estimasi_menetas" class="form-label">
                                Estimasi Tanggal Menetas
                            </label>
                            <input type="date" name="estimasi_tanggal_menetas" id="estimasi_menetas" 
                                   class="form-control" value="{{ old('estimasi_tanggal_menetas', $penetasan->estimasi_tanggal_menetas?->format('Y-m-d')) }}">
                            <small class="form-text">
                                <i class="fa-solid fa-clock text-primary"></i> 
                                Otomatis dihitung 17 hari dari tanggal mulai, namun bisa disesuaikan manual jika dibutuhkan.
                            </small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="jumlah_telur" class="form-label">
                                Jumlah Telur <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="jumlah_telur" id="jumlah_telur" 
                                   class="form-control" value="{{ old('jumlah_telur', $penetasan->jumlah_telur) }}" 
                                   min="1" placeholder="Contoh: 1000" required>
                            <small class="form-text">Total telur yang dimasukkan (dalam butir)</small>
                        </div>
                        <div class="form-group">
                            <label for="telur_tidak_fertil" class="form-label">
                                Telur Tidak Fertil
                            </label>
                            <input type="number" name="telur_tidak_fertil" id="telur_tidak_fertil" 
                                   class="form-control" value="{{ old('telur_tidak_fertil', $penetasan->telur_tidak_fertil) }}" 
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
                                   class="form-control" value="{{ old('suhu_penetasan', $penetasan->suhu_penetasan) }}" 
                                   step="0.1" min="0" max="50" placeholder="Contoh: 37.5">
                            <small class="form-text">Suhu optimal: 37.0°C - 38.0°C</small>
                        </div>
                        <div class="form-group">
                            <label for="kelembaban_penetasan" class="form-label">
                                Kelembaban Penetasan (%)
                            </label>
                            <input type="number" name="kelembaban_penetasan" id="kelembaban_penetasan" 
                                   class="form-control" value="{{ old('kelembaban_penetasan', $penetasan->kelembaban_penetasan) }}" 
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
                                      rows="4" placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan', $penetasan->catatan) }}</textarea>
                            <small class="form-text">Catatan penting terkait proses penetasan</small>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->peran === 'owner')
                <!-- Divider -->
                <hr style="margin: 30px 0; border: 0; border-top: 2px dashed #e2e8f0;">

                <!-- Section: Owner Control Toggle -->
                <div class="form-section" style="background: #fef3c7; border: 2px solid #fbbf24; border-radius: 10px; padding: 20px;">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-user-shield text-danger"></i> 
                                <strong>Owner Override Control</strong>
                            </label>
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px;">
                                <label class="custom-switch">
                                    <input type="checkbox" id="ownerOverrideToggle">
                                    <span class="slider"></span>
                                </label>
                                <span id="ownerOverrideLabel" style="font-size: 14px; color: #64748b;">
                                    Aktifkan untuk override hasil penetasan & status
                                </span>
                            </div>
                            <small class="form-text">
                                <i class="fa-solid fa-info-circle text-warning"></i>
                                Status saat ini: 
                                @php
                                    $statusBadge = [
                                        'proses' => ['text' => 'Proses', 'class' => 'secondary'],
                                        'selesai' => ['text' => 'Selesai', 'class' => 'success'],
                                        'gagal' => ['text' => 'Gagal', 'class' => 'danger'],
                                    ];
                                    $current = $statusBadge[$penetasan->status ?? 'proses'];
                                @endphp
                                <span class="badge bg-{{ $current['class'] }}">{{ $current['text'] }}</span>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Section: Owner Override (Hidden by default) -->
                <div class="form-section" id="ownerOverrideSection" style="display: none; border-left: 4px solid #dc3545; background: #fee2e2; border-radius: 10px; padding: 20px; margin-top: 15px;">
                    <h3 class="section-title">
                        <i class="fa-solid fa-user-shield" style="color: #dc3545;"></i>
                        Kontrol Owner
                        <span class="badge bg-danger ms-2" style="font-size: 11px; vertical-align: middle;">Owner Only</span>
                    </h3>
                    <div class="alert alert-warning" style="margin-bottom: 20px;">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Fitur ini memungkinkan Owner untuk mengubah hasil penetasan dan status secara manual.
                    </div>

                    <!-- Override Status -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="status" class="form-label">
                                <i class="fa-solid fa-tag"></i> Override Status
                            </label>
                            <select name="status" id="status" class="form-control">
                                <option value="">-- Gunakan status otomatis --</option>
                                <option value="proses" {{ old('status', $penetasan->status) === 'proses' ? 'selected' : '' }}>
                                    Proses
                                </option>
                                <option value="selesai" {{ old('status', $penetasan->status) === 'selesai' ? 'selected' : '' }}>
                                    Selesai
                                </option>
                                <option value="gagal" {{ old('status', $penetasan->status) === 'gagal' ? 'selected' : '' }}>
                                    Gagal
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Hasil Penetasan -->
                    <h4 style="margin-top: 25px; margin-bottom: 15px; color: #dc3545; font-size: 16px;">
                        <i class="fa-solid fa-dove"></i> Hasil Penetasan
                    </h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_menetas" class="form-label">
                                Tanggal Menetas Aktual
                            </label>
                            <input type="date" name="tanggal_menetas" id="tanggal_menetas" 
                                   class="form-control" value="{{ old('tanggal_menetas', $penetasan->tanggal_menetas?->format('Y-m-d')) }}">
                            <small class="form-text">Tanggal aktual saat telur mulai menetas</small>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_menetas" class="form-label">
                                Jumlah Menetas
                            </label>
                            <input type="number" name="jumlah_menetas" id="jumlah_menetas" 
                                   class="form-control" value="{{ old('jumlah_menetas', $penetasan->jumlah_menetas) }}" 
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
                                   class="form-control" value="{{ old('jumlah_doc', $penetasan->jumlah_doc) }}" 
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
                @endif

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.penetasan') }}" class="bolopa-form-btn bolopa-form-btn-secondary">
                        <i class="fa-solid fa-times"></i>
                        Batal
                    </a>
                    <button type="submit" class="bolopa-form-btn bolopa-form-btn-primary">
                        <i class="fa-solid fa-save"></i>
                        Update Data
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
    margin-bottom: 24px;
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

/* Custom Toggle Switch */
.custom-switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
}

.custom-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .4s;
    border-radius: 28px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.custom-switch input:checked + .slider {
    background-color: #f59e0b;
}

.custom-switch input:checked + .slider:before {
    transform: translateX(24px);
}

.custom-switch input:focus + .slider {
    box-shadow: 0 0 1px #f59e0b;
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
    const triggerFlashToast = (icon, title, message, timer = 3500) => {
        if (!message) {
            return;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title,
            text: message,
            showConfirmButton: false,
            timer,
            timerProgressBar: true,
            didOpen: toast => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    };

    @if(session('success'))
    triggerFlashToast('success', 'Berhasil!', @json(session('success')));
    @endif

    @if(session('error'))
    triggerFlashToast('error', 'Gagal!', @json(session('error')));
    @endif

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
    const alertOwnerOverride = document.getElementById('alertOwnerOverride');
    let estimasiManualEdit = !!(estimasiMenetasInput && estimasiMenetasInput.value);
    
    const isOwner = {{ auth()->user()->peran === 'owner' ? 'true' : 'false' }};
    
    // Format tanggal ke Indonesia
    function formatTanggalIndonesia(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }
    
    function updateEstimasiDisplay(dateValue) {
        if (!estimasiMenetasInput) {
            return;
        }

        if (dateValue) {
            if (estimasiInfo) {
                estimasiInfo.style.display = 'flex';
            }
            if (estimasiText) {
                estimasiText.textContent = formatTanggalIndonesia(dateValue) + ' (± 1 hari)';
            }
        } else {
            if (estimasiInfo) {
                estimasiInfo.style.display = 'none';
            }
        }
    }
    
    // Hitung estimasi tanggal menetas (default 17 hari)
    function hitungEstimasiMenetas(force = false) {
        const tanggalSimpan = tanggalSimpanInput.value;
        if (!tanggalSimpan || !estimasiMenetasInput) {
            if (estimasiMenetasInput) {
                estimasiMenetasInput.value = '';
            }
            updateEstimasiDisplay('');
            return;
        }

        const date = new Date(tanggalSimpan);
        date.setDate(date.getDate() + 17);
        const estimasiDate = date.toISOString().split('T')[0];

        estimasiMenetasInput.min = tanggalSimpan;

        if (!estimasiManualEdit || force || !estimasiMenetasInput.value) {
            estimasiMenetasInput.value = estimasiDate;
        }

        updateEstimasiDisplay(estimasiMenetasInput.value || estimasiDate);
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
        hitungEstimasiMenetas(true);
        
        tanggalSimpanInput.addEventListener('change', () => {
            estimasiManualEdit = false;
            hitungEstimasiMenetas(true);
        });
    }

    if (estimasiMenetasInput) {
        updateEstimasiDisplay(estimasiMenetasInput.value);
        estimasiMenetasInput.addEventListener('input', () => {
            estimasiManualEdit = !!estimasiMenetasInput.value;
            updateEstimasiDisplay(estimasiMenetasInput.value);
        });
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
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal tidak valid',
                    text: 'Tanggal menetas tidak boleh lebih awal dari tanggal mulai penetasan.'
                });
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Data belum lengkap',
                    text: 'Harap lengkapi semua field yang wajib diisi (bertanda *).'
                });
                return false;
            }
        });
    }

    // Toggle Owner Override Section (Status + Hasil Penetasan)
    const ownerOverrideToggle = document.getElementById('ownerOverrideToggle');
    const ownerOverrideSection = document.getElementById('ownerOverrideSection');
    const ownerOverrideLabel = document.getElementById('ownerOverrideLabel');

    if (ownerOverrideToggle && ownerOverrideSection) {
        ownerOverrideToggle.addEventListener('change', function() {
            if (this.checked) {
                ownerOverrideSection.style.display = 'block';
                ownerOverrideLabel.innerHTML = '<i class="fa-solid fa-check-circle"></i> Override aktif - Hasil penetasan & status tersedia';
                ownerOverrideLabel.style.color = '#dc3545';
                ownerOverrideLabel.style.fontWeight = '600';
                
                // Scroll ke section
                setTimeout(() => {
                    ownerOverrideSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            } else {
                ownerOverrideSection.style.display = 'none';
                ownerOverrideLabel.textContent = 'Aktifkan untuk override hasil penetasan & status';
                ownerOverrideLabel.style.color = '#64748b';
                ownerOverrideLabel.style.fontWeight = '400';
                
                // Reset semua input
                const statusSelect = document.getElementById('status');
                const tanggalMenetas = document.getElementById('tanggal_menetas');
                const jumlahMenetas = document.getElementById('jumlah_menetas');
                const jumlahDoc = document.getElementById('jumlah_doc');
                
                if (statusSelect) statusSelect.value = '';
                if (tanggalMenetas) tanggalMenetas.value = '';
                if (jumlahMenetas) jumlahMenetas.value = '';
                if (jumlahDoc) jumlahDoc.value = '';
            }
        });
    }
});
</script>
@endsection
