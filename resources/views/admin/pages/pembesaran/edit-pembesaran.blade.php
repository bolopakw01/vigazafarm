@extends('admin.layouts.app')

@section('title', 'Edit Pembesaran Puyuh - ' . $pembesaran->batch_produksi_id)

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Pembesaran', 'link' => route('admin.pembesaran')],
        ['label' => 'Detail Batch', 'link' => route('admin.pembesaran.show', $pembesaran->id), 'badge' => $pembesaran->batch_produksi_id],
        ['label' => 'Edit Data'],
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-pembesaran.css') }}">
<style>
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

</style>
@endpush

@section('content')
@php
    $defaultTanggalMasuk = old('tanggal_masuk', $pembesaran->tanggal_masuk ? $pembesaran->tanggal_masuk->format('Y-m-d') : date('Y-m-d'));
    $defaultTanggalSiap = old('tanggal_siap', $pembesaran->tanggal_siap ? $pembesaran->tanggal_siap->format('Y-m-d') : null);
    if (empty($defaultTanggalSiap) && !empty($defaultTanggalMasuk)) {
        try {
            $defaultTanggalSiap = \Carbon\Carbon::parse($defaultTanggalMasuk)->addDays(27)->format('Y-m-d');
        } catch (\Exception $e) {
            $defaultTanggalSiap = null;
        }
    }
@endphp
@php
    $beratAwalValue = $pembesaran->berat_rata_rata;
    if (is_null($beratAwalValue)) {
        $beratAwalFormatted = null;
    } elseif ($beratAwalValue == floor($beratAwalValue)) {
        $beratAwalFormatted = number_format($beratAwalValue, 0);
    } else {
        $beratAwalFormatted = rtrim(rtrim(number_format($beratAwalValue, 2), '0'), '.');
    }
@endphp
<div class="bolopa-form-wrapper">
    <div class="bolopa-form-container">
        <!-- Header -->
        <div class="bolopa-form-header">
            <div>
                <h1>
                    <i class="fa-solid fa-dove"></i>
                    Edit Data Pembesaran
                </h1>
                <p class="text-muted mb-0">Formulir untuk mengedit data pembesaran DOQ/anak puyuh</p>
            </div>
            <a href="{{ route('admin.pembesaran') }}" class="bolopa-form-btn bolopa-form-btn-secondary">
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
            <form action="{{ route('admin.pembesaran.update', $pembesaran->id) }}" method="POST" id="formPembesaran">
                @csrf
                @method('PATCH')

                <!-- Section: Batch & Kandang -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-warehouse"></i>
                        Informasi Batch & Kandang
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="batch_produksi_id" class="form-label">
                                Kode Batch <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="batch_produksi_id" id="batch_produksi_id" class="form-control" 
                                value="{{ $pembesaran->batch_produksi_id }}" readonly 
                                style="background-color: #f1f5f9; font-weight: 600; color: #1e293b;">
                            <small class="form-text">Kode batch pembesaran (tidak dapat diubah)</small>
                        </div>

                        <div class="form-group">
                            <label for="kandang_id" class="form-label">
                                Kandang Pembesaran <span class="text-danger">*</span>
                            </label>
                            <select name="kandang_id" id="kandang_id" class="form-control" required>
                                <option value="">-- Pilih Kandang --</option>
                                @foreach($kandangList as $k)
                                @php
                                    $statusLabel = strtolower($k->status ?? 'aktif');
                                    $isMaintenance = $statusLabel === 'maintenance';
                                    $isSelected = (string) old('kandang_id', $pembesaran->kandang_id) === (string) $k->id;
                                @endphp
                                <option
                                    value="{{ $k->id }}"
                                    data-status="{{ $statusLabel }}"
                                    {{ $isSelected ? 'selected' : '' }}
                                    @disabled($isMaintenance && !$isSelected)
                                >
                                    {{ $k->nama_dengan_detail }}
                                    @if($isMaintenance)
                                        &ndash; Maintenance
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text">Pilih kandang pembesaran yang akan digunakan</small>
                        </div>
                    </div>
                </div>

                <!-- Section: Data Masuk DOQ / Anak Puyuh -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-dove"></i>
                        Data Masuk DOQ / Anak Puyuh
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_masuk" class="form-label">
                                Tanggal Masuk <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" 
                                value="{{ $defaultTanggalMasuk }}" 
                                required max="{{ date('Y-m-d') }}">
                            <small class="form-text">Tanggal DOQ masuk ke kandang pembesaran</small>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_anak_ayam" class="form-label">
                                Jumlah Anak Puyuh <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="jumlah_anak_ayam" id="jumlah_anak_ayam" class="form-control" 
                                value="{{ old('jumlah_anak_ayam', $pembesaran->jumlah_anak_ayam) }}" 
                                required min="1" placeholder="Contoh: 500">
                            <small class="form-text">Jumlah DOQ/anak puyuh yang masuk</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="penetasan_id" class="form-label">
                                Asal Penetasan
                            </label>
                            <input type="text" class="form-control" 
                                value="{{ $pembesaran->penetasan ? $pembesaran->penetasan->batch . ' - ' . ($pembesaran->penetasan->kandang->nama_kandang ?? '-') : 'Tidak ada data penetasan' }}" 
                                readonly style="background-color: #f1f5f9;">
                            <small class="form-text">Data asal penetasan (tidak dapat diubah)</small>
                        </div>

                        <div class="form-group">
                            <label for="jenis_kelamin" class="form-label">
                                Jenis Kelamin
                            </label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                                <option value="campuran" {{ old('jenis_kelamin', $pembesaran->jenis_kelamin) == 'campuran' ? 'selected' : '' }}>
                                    Campuran
                                </option>
                                <option value="betina" {{ old('jenis_kelamin', $pembesaran->jenis_kelamin) == 'betina' ? 'selected' : '' }}>
                                    Betina
                                </option>
                                <option value="jantan" {{ old('jenis_kelamin', $pembesaran->jenis_kelamin) == 'jantan' ? 'selected' : '' }}>
                                    Jantan
                                </option>
                            </select>
                            <small class="form-text">Default: Campuran (jika belum dipisah berdasarkan jenis kelamin)</small>
                        </div>
                    </div>
                </div>

                <!-- Section: Target / Parameter -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-bullseye"></i>
                        Target / Parameter Pembesaran
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="umur_hari" class="form-label">
                                Umur Awal (Hari)
                            </label>
                            <input type="number" name="umur_hari" id="umur_hari" class="form-control" 
                                value="{{ old('umur_hari', $pembesaran->umur_hari ?? 1) }}" 
                                min="0" placeholder="Default: 1">
                            <small class="form-text">Umur DOQ saat masuk ke pembesaran (default: 1 hari)</small>
                        </div>

                        <div class="form-group">
                            <label for="berat_rata_rata" class="form-label">
                                Berat Rata-rata Awal (gram)
                            </label>
                            <input type="number" name="berat_rata_rata" id="berat_rata_rata" class="form-control" 
                                value="{{ old('berat_rata_rata', $beratAwalFormatted) }}" 
                                min="0" step="0.01" placeholder="Contoh: 8.5">
                            <small class="form-text">Berat rata-rata per ekor saat masuk (opsional)</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_siap" class="form-label">
                                Perkiraan Tanggal Siap
                            </label>
                            <input type="date" name="tanggal_siap" id="tanggal_siap" class="form-control" 
                                value="{{ $defaultTanggalSiap }}">
                            <small class="form-text">Isi dengan estimasi Anda (disarankan 27-28 hari dari tanggal masuk untuk puyuh) dan sesuaikan sesuai kondisi lapangan.</small>
                            <small class="form-text">Penyelesaian batch akan mengikuti Perkiraan Tanggal Siap ini.</small>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_siap" class="form-label">
                                Jumlah Siap (ekor)
                            </label>
                            <input type="number" name="jumlah_siap" id="jumlah_siap" class="form-control" 
                                value="{{ old('jumlah_siap', $pembesaran->jumlah_siap) }}" 
                                min="0" placeholder="Jumlah yang siap">
                            <small class="form-text">Jumlah puyuh yang siap untuk produksi/penjualan</small>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->peran === 'owner' || auth()->user()->peran === 'super_admin')
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
                                <input type="hidden" name="owner_override_active" id="ownerOverrideFlag" value="0">
                                <span id="ownerOverrideLabel" style="font-size: 14px; color: #64748b;">
                                    Aktifkan untuk override status pembesaran
                                </span>
                            </div>
                            <small class="form-text">
                                <i class="fa-solid fa-info-circle text-warning"></i>
                                Status saat ini: 
                                @php
                                    $statusBadge = [
                                        'aktif' => ['text' => 'Aktif', 'class' => 'primary'],
                                        'selesai' => ['text' => 'Selesai', 'class' => 'success'],
                                    ];
                                    $statusKey = strtolower($pembesaran->status_batch ?? 'aktif');
                                    $current = $statusBadge[$statusKey] ?? $statusBadge['aktif'];
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
                        <strong>Perhatian:</strong> Fitur ini memungkinkan Owner untuk mengubah status batch secara manual tanpa validasi target.
                    </div>

                    <!-- Override Status -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="status_batch" class="form-label">
                                <i class="fa-solid fa-tag"></i> Override Status Batch
                            </label>
                            <select name="status_batch" id="status_batch" class="form-control">
                                <option value="Aktif" {{ old('status_batch', $pembesaran->status_batch ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>
                                    🟢 Aktif - Batch Sedang Berjalan
                                </option>
                                <option value="Selesai" {{ old('status_batch', $pembesaran->status_batch) == 'Selesai' ? 'selected' : '' }}>
                                    ✅ Selesai - Batch Telah Diselesaikan
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_selesai" class="form-label">
                                <i class="fa-solid fa-calendar-check"></i> Tanggal Selesai
                            </label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" 
                                value="{{ old('tanggal_selesai', $pembesaran->tanggal_selesai ? $pembesaran->tanggal_selesai->format('Y-m-d') : '') }}">
                            <small class="form-text">Tanggal batch diselesaikan (otomatis terisi saat klik tombol selesaikan)</small>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Section: Catatan Tambahan -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-clipboard"></i>
                        Catatan Tambahan
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="kondisi_doc" class="form-label">
                                Kondisi DOQ Saat Masuk
                            </label>
                            <input type="text" class="form-control" 
                                value="{{ $pembesaran->kondisi_doc ?? 'Tidak ada data' }}" 
                                readonly style="background-color: #f1f5f9;">
                            <small class="form-text">Kondisi fisik DOQ saat masuk (tidak dapat diubah)</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="catatan" class="form-label">
                                Catatan Operator / Petugas
                            </label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="4" 
                                placeholder="Catatan khusus: perubahan kondisi, perlakuan khusus, dll...">{{ old('catatan', $pembesaran->catatan) }}</textarea>
                            <small class="form-text">Informasi tambahan dari operator/petugas kandang</small>
                        </div>
                    </div>
                </div>

                <!-- Section: Informasi Status -->
                <div class="form-section" style="background-color: #f8fafc; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.9rem 1.2rem;">
                    <h6 style="font-size: 0.95rem; font-weight: 600; color: #0d6efd; margin-bottom: 0.75rem;">
                        <i class="fa-solid fa-circle-info me-2"></i>Informasi Status
                    </h6>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <!-- Status -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: #6c757d; font-size: 0.85rem;">
                                <i class="fa-solid fa-seedling text-success"></i>
                                <span>Status</span>
                            </div>
                            <span style="background-color: #198754; color: white; font-size: 0.75rem; padding: 0.3em 0.6em; border-radius: 0.25rem; font-weight: 500;">
                                <i class="fa-solid fa-check me-1"></i>{{ $pembesaran->status ?? 'Aktif' }}
                            </span>
                        </div>

                        <!-- Tanggal Dibuat -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: #6c757d; font-size: 0.85rem;">
                                <i class="fa-regular fa-calendar"></i>
                                <span>Dibuat</span>
                            </div>
                            <span class="text-muted" style="font-size: 0.85rem;">
                                @if(isset($pembesaran->dibuat_pada) && $pembesaran->dibuat_pada)
                                    {{ \Carbon\Carbon::parse($pembesaran->dibuat_pada)->format('d/m/Y') }}, {{ \Carbon\Carbon::parse($pembesaran->dibuat_pada)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>

                        <!-- Terakhir Update -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: #6c757d; font-size: 0.85rem;">
                                <i class="fa-regular fa-clock"></i>
                                <span>Update</span>
                            </div>
                            <span class="text-muted" style="font-size: 0.85rem;">
                                @if(isset($pembesaran->diperbarui_pada) && $pembesaran->diperbarui_pada)
                                    {{ \Carbon\Carbon::parse($pembesaran->diperbarui_pada)->format('d/m/Y') }}, {{ \Carbon\Carbon::parse($pembesaran->diperbarui_pada)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>

                        <!-- Diinput Oleh -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: #6c757d; font-size: 0.85rem;">
                                <i class="fa-solid fa-user-pen"></i>
                                <span>Input</span>
                            </div>
                            <span class="text-muted" style="font-size: 0.85rem;">
                                @php
                                    // Cek apakah ada user_id di pembesaran
                                    if (isset($pembesaran->user_id) && $pembesaran->user_id) {
                                        $user = $pembesaran->user;
                                    } else {
                                        // Jika tidak ada user_id, ambil dari auth user yang sedang login
                                        $user = auth()->user();
                                    }
                                    
                                    if ($user) {
                                        $namaDepan = explode(' ', $user->nama)[0];
                                        $email = $user->email ?? '';
                                        $displayText = $namaDepan . ' (' . $email . ')';
                                    } else {
                                        $displayText = 'Sistem';
                                    }
                                @endphp
                                {{ $displayText }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="bolopa-form-btn bolopa-form-btn-secondary" onclick="window.history.back()">
                        <i class="fa-solid fa-times"></i>
                        Batal
                    </button>
                    <button type="reset" class="bolopa-form-btn bolopa-form-btn-warning">
                        <i class="fa-solid fa-redo"></i>
                        Reset
                    </button>
                    <button type="submit" class="bolopa-form-btn bolopa-form-btn-primary" id="btnSubmit">
                        <i class="fa-solid fa-save"></i>
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            didOpen: (toast) => {
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

    const form = document.getElementById('formPembesaran');
    const btnSubmit = document.getElementById('btnSubmit');
    const tanggalMasuk = document.getElementById('tanggal_masuk');
    const tanggalSiap = document.getElementById('tanggal_siap');
    const jumlahInput = document.getElementById('jumlah_anak_ayam');
    const siapOffsetHari = 27;
    let lastAutoTanggalSiap = tanggalSiap ? tanggalSiap.value : '';

    const hitungTanggalSiapAwal = (baseTanggal, offsetHari = siapOffsetHari) => {
        if (!baseTanggal) {
            return '';
        }
        const baseDate = new Date(baseTanggal);
        if (Number.isNaN(baseDate.getTime())) {
            return '';
        }
        baseDate.setDate(baseDate.getDate() + offsetHari);
        return baseDate.toISOString().slice(0, 10);
    };

    const autoIsiTanggalSiap = (options = {}) => {
        if (!tanggalMasuk || !tanggalSiap) {
            return;
        }
        const { respectUserEdit = true } = options;
        if (respectUserEdit && tanggalSiap.dataset.userEdited === 'true') {
            return;
        }
        const kandidat = hitungTanggalSiapAwal(tanggalMasuk.value, siapOffsetHari);
        if (!kandidat) {
            return;
        }
        tanggalSiap.value = kandidat;
        delete tanggalSiap.dataset.userEdited;
        lastAutoTanggalSiap = kandidat;
    };

    // Form submission dengan SweetAlert2 confirmation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const batchCode = document.getElementById('batch_produksi_id').value;
        const kandang = document.getElementById('kandang_id').selectedOptions[0].text;
        const jumlah = parseInt(jumlahInput.value);
        
        Swal.fire({
            title: 'Konfirmasi Update',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Batch:</strong> ${batchCode}</p>
                    <p><strong>Kandang:</strong> ${kandang}</p>
                    <p><strong>Jumlah DOQ:</strong> ${jumlah.toLocaleString('id-ID')} ekor</p>
                    <hr>
                    <p style="color: #dc2626;">Apakah Anda yakin ingin mengupdate data ini?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Periksa Lagi'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable button dan show loading
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengupdate...';
                
                // Submit form
                form.submit();
            }
        });
    });

    // Reset form confirmation
    form.addEventListener('reset', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua perubahan akan dikembalikan ke data awal',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.reset();
                if (tanggalSiap) {
                    delete tanggalSiap.dataset.userEdited;
                }
                setTimeout(() => autoIsiTanggalSiap({ respectUserEdit: false }), 0);
                triggerFlashToast('success', 'Berhasil!', 'Form pembesaran berhasil direset.', 2200);
            }
        });
    });

    // Numeric input validation
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });


    if (tanggalSiap) {
        if (!tanggalSiap.value) {
            autoIsiTanggalSiap({ respectUserEdit: false });
        } else {
            lastAutoTanggalSiap = tanggalSiap.value;
        }

        tanggalSiap.addEventListener('input', () => {
            if (!tanggalSiap.value || tanggalSiap.value === lastAutoTanggalSiap) {
                delete tanggalSiap.dataset.userEdited;
            } else {
                tanggalSiap.dataset.userEdited = 'true';
            }
        });
    }

    if (tanggalMasuk) {
        tanggalMasuk.addEventListener('change', () => autoIsiTanggalSiap({ respectUserEdit: true }));
    }
    // Toggle Owner Override Section
    const ownerOverrideToggle = document.getElementById('ownerOverrideToggle');
    const ownerOverrideSection = document.getElementById('ownerOverrideSection');
    const ownerOverrideLabel = document.getElementById('ownerOverrideLabel');
    const ownerOverrideFlag = document.getElementById('ownerOverrideFlag');
    const statusBatchInput = document.getElementById('status_batch');
    const tanggalSelesaiInput = document.getElementById('tanggal_selesai');
    const ownerOverrideDefaultActive = @json(
        (auth()->user()->peran === 'owner' || auth()->user()->peran === 'super_admin') && (
            (isset($pembesaran->status_batch) && strtolower($pembesaran->status_batch) !== 'aktif') ||
            !empty($pembesaran->tanggal_selesai)
        )
    );

    function setPembesaranOverrideState(isActive, { skipScroll = false } = {}) {
        if (!ownerOverrideSection || !ownerOverrideLabel) {
            return;
        }

        ownerOverrideSection.style.display = isActive ? 'block' : 'none';
        ownerOverrideLabel.innerHTML = isActive
            ? '<i class="fa-solid fa-check-circle"></i> Override aktif - Status pembesaran tersedia'
            : 'Aktifkan untuk override status pembesaran';
        ownerOverrideLabel.style.color = isActive ? '#dc3545' : '#64748b';
        ownerOverrideLabel.style.fontWeight = isActive ? '600' : '400';

        if (ownerOverrideFlag) {
            ownerOverrideFlag.value = isActive ? '1' : '0';
        }

        if (statusBatchInput) {
            statusBatchInput.disabled = !isActive;
        }

        if (tanggalSelesaiInput) {
            tanggalSelesaiInput.disabled = !isActive;
        }

        if (isActive && !skipScroll) {
            setTimeout(() => {
                ownerOverrideSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }
    }

    if (ownerOverrideToggle && ownerOverrideSection) {
        ownerOverrideToggle.addEventListener('change', function() {
            setPembesaranOverrideState(this.checked);
        });

        if (ownerOverrideDefaultActive) {
            ownerOverrideToggle.checked = true;
            setPembesaranOverrideState(true, { skipScroll: true });
        } else {
            setPembesaranOverrideState(false, { skipScroll: true });
        }
    }
    
    // Pastikan field aktif sesuai toggle saat submit
    form.addEventListener('submit', function() {
        if (ownerOverrideToggle && ownerOverrideToggle.checked) {
            if (statusBatchInput) statusBatchInput.disabled = false;
            if (tanggalSelesaiInput) tanggalSelesaiInput.disabled = false;
        }
    });
});
</script>

@endsection
