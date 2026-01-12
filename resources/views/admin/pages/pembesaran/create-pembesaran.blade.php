@extends('admin.layouts.app')

@section('title', 'Tambah Pembesaran Puyuh')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Pembesaran', 'link' => route('admin.pembesaran')],
        ['label' => 'Tambah Data'],
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-pembesaran.css') }}">
<style>
    .capacity-info-card {
        margin-top: 10px;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        gap: 12px;
        align-items: center;
        font-size: 13px;
    }

    .capacity-info-card.capacity-warning {
        border-color: #f97316;
        background: #fff7ed;
        color: #9a3412;
    }

    .capacity-info-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #475569;
    }

    .capacity-info-title {
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .capacity-info-text {
        color: #475569;
        line-height: 1.4;
    }

</style>
@endpush

@section('content')
@php
    $defaultTanggalMasuk = old('tanggal_masuk', date('Y-m-d'));
    $defaultTanggalSiap = old('tanggal_siap');
    if (empty($defaultTanggalSiap) && !empty($defaultTanggalMasuk)) {
        try {
            $defaultTanggalSiap = \Carbon\Carbon::parse($defaultTanggalMasuk)->addDays(27)->format('Y-m-d');
        } catch (\Exception $e) {
            $defaultTanggalSiap = null;
        }
    }
@endphp
<div class="bolopa-form-wrapper">
    <div class="bolopa-form-container">
        <!-- Header -->
        <div class="bolopa-form-header">
            <div>
                <h1>
                    <i class="fa-solid fa-dove"></i>
                    Tambah Data Pembesaran
                </h1>
                <p class="text-muted mb-0">Formulir untuk menambah data pembesaran DOQ/anak puyuh</p>
            </div>
            <a href="{{ route('admin.pembesaran') }}" class="bolopa-form-btn bolopa-form-btn-secondary">
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
            <form action="{{ route('admin.pembesaran.store') }}" method="POST" id="formPembesaran">
                @csrf

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
                                value="{{ old('batch_produksi_id', $generatedBatch) }}" required readonly 
                                style="background-color: #f1f5f9; font-weight: 600; color: #1e293b;">
                            <small class="form-text">Kode batch pembesaran (otomatis)</small>
                        </div>

                        <div class="form-group">
                            <label for="kandang_id" class="form-label">
                                Kandang Pembesaran <span class="text-danger">*</span>
                            </label>
                            <select name="kandang_id" id="kandang_id" class="form-control" required>
                                <option value="">-- Pilih Kandang --</option>
                                @php
                                    if (isset($kandangList) && $kandangList instanceof \Illuminate\Support\Collection && $kandangList->isNotEmpty()) {
                                        $availableKandangs = $kandangList;
                                    } elseif (isset($kandangList) && is_array($kandangList) && !empty($kandangList)) {
                                        $availableKandangs = collect($kandangList);
                                    } elseif (isset($kandang) && $kandang instanceof \Illuminate\Support\Collection && $kandang->isNotEmpty()) {
                                        $availableKandangs = $kandang;
                                    } elseif (isset($kandang) && is_array($kandang) && !empty($kandang)) {
                                        $availableKandangs = collect($kandang);
                                    } elseif (isset($kandangs) && $kandangs instanceof \Illuminate\Support\Collection && $kandangs->isNotEmpty()) {
                                        $availableKandangs = $kandangs;
                                    } elseif (isset($kandangs) && is_array($kandangs) && !empty($kandangs)) {
                                        $availableKandangs = collect($kandangs);
                                    } else {
                                        $availableKandangs = collect();
                                    }
                                @endphp
                                @php $selectedKandangId = old('kandang_id'); @endphp
                                @foreach($availableKandangs as $k)
                                @php
                                    $typeLabel = ucwords(strtolower($k->tipe_kandang ?? $k->tipe ?? '-'));
                                    $remainingLabel = number_format((int) $k->kapasitas_tersisa);
                                    $statusLabel = strtolower($k->status_computed ?? ($k->status ?? 'aktif'));
                                    $isMaintenance = $statusLabel === 'maintenance';
                                    $isFull = $statusLabel === 'full';
                                    $isSelected = (string) $selectedKandangId === (string) $k->id;
                                @endphp
                                <option
                                    value="{{ $k->id }}"
                                    data-status="{{ $statusLabel }}"
                                    data-kapasitas="{{ $k->kapasitas_total }}"
                                    data-terpakai="{{ $k->kapasitas_terpakai }}"
                                    data-sisa="{{ $k->kapasitas_tersisa }}"
                                    {{ $isSelected ? 'selected' : '' }}
                                    @disabled(($isMaintenance || $isFull) && !$isSelected)
                                >
                                    {{ $k->nama_kandang }} ({{ $typeLabel }}, {{ $remainingLabel }})
                                    @if($isMaintenance)
                                        &ndash; Maintenance
                                    @elseif($isFull)
                                        &ndash; Full
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text">Pilih kandang pembesaran yang akan digunakan</small>
                            <div id="kandangCapacityInfo" class="capacity-info-card">
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
                                value="{{ $defaultTanggalMasuk }}" required max="{{ date('Y-m-d') }}">
                            <small class="form-text">Tanggal DOQ masuk ke kandang pembesaran</small>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_anak_ayam" class="form-label">
                                Jumlah Anak Puyuh <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="jumlah_anak_ayam" id="jumlah_anak_ayam" class="form-control" 
                                value="{{ old('jumlah_anak_ayam') }}" required min="1" placeholder="Contoh: 500">
                            <small class="form-text">Jumlah DOQ/anak puyuh yang masuk</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="penetasan_id" class="form-label">
                                Asal Penetasan
                            </label>
                            <select name="penetasan_id" id="penetasan_id" class="form-control">
                                <option value="">-- Pilih Batch Penetasan (Opsional) --</option>
                                @foreach($penetasanList as $p)
                                <option value="{{ $p->id }}" {{ old('penetasan_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->batch }} - {{ $p->kandang->nama_kandang ?? '-' }} 
                                    (DOQ: {{ number_format($p->jumlah_doc) }} ekor, 
                                    Menetas: {{ $p->tanggal_menetas ? $p->tanggal_menetas->format('d/m/Y') : '-' }})
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text">Pilih dari batch penetasan yang sudah selesai (opsional)</small>
                        </div>

                        <div class="form-group">
                            <label for="jenis_kelamin" class="form-label">
                                Jenis Kelamin
                            </label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                                <option value="campuran" {{ old('jenis_kelamin', 'campuran') == 'campuran' ? 'selected' : '' }}>
                                    Campuran
                                </option>
                                <option value="betina" {{ old('jenis_kelamin') == 'betina' ? 'selected' : '' }}>
                                    Betina
                                </option>
                                <option value="jantan" {{ old('jenis_kelamin') == 'jantan' ? 'selected' : '' }}>
                                    Jantan
                                </option>
                            </select>
                            <small class="form-text">Default: Campuran (jika belum dipisah berdasarkan jenis kelamin)</small>
                        </div>
                    </div>
                </div>

                <!-- Section: Target Awal / Parameter -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-bullseye"></i>
                        Target Awal / Parameter
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="umur_hari" class="form-label">
                                Umur Awal (Hari)
                            </label>
                            <input type="number" name="umur_hari" id="umur_hari" class="form-control" 
                                value="{{ old('umur_hari', 1) }}" min="0" placeholder="Default: 1">
                            <small class="form-text">Umur DOQ saat masuk ke pembesaran (default: 1 hari)</small>
                        </div>

                        <div class="form-group">
                            <label for="berat_rata_rata" class="form-label">
                                Berat Rata-rata Awal (gram)
                            </label>
                            <input type="number" name="berat_rata_rata" id="berat_rata_rata" class="form-control" 
                                value="{{ old('berat_rata_rata') }}" min="0" step="0.01" placeholder="Contoh: 8.5">
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
                            <small class="form-text">Gunakan estimasi manual (disarankan 27-28 hari dari tanggal masuk untuk puyuh) dan sesuaikan dengan kondisi lapangan.</small>
                            <small class="form-text">Penyelesaian batch akan mengikuti Perkiraan Tanggal Siap ini.</small>
                        </div>

                        <div class="form-group">
                            <label for="target_berat_akhir" class="form-label">
                                Target Berat Akhir (gram)
                            </label>
                            <input type="number" name="target_berat_akhir" id="target_berat_akhir" class="form-control" 
                                value="{{ old('target_berat_akhir', 150) }}" min="0" step="0.01" placeholder="Contoh: 150">
                            <small class="form-text">Target berat rata-rata saat siap (opsional, standar: 150g)</small>
                        </div>
                    </div>
                </div>

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
                            <select name="kondisi_doc" id="kondisi_doc" class="form-control">
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="Sehat" {{ old('kondisi_doc') == 'Sehat' ? 'selected' : '' }}>✅ Sehat - Kondisi prima</option>
                                <option value="Baik" {{ old('kondisi_doc') == 'Baik' ? 'selected' : '' }}>👍 Baik - Kondisi normal</option>
                                <option value="Lemah" {{ old('kondisi_doc') == 'Lemah' ? 'selected' : '' }}>⚠️ Lemah - Perlu perhatian khusus</option>
                                <option value="Sakit" {{ old('kondisi_doc') == 'Sakit' ? 'selected' : '' }}>🏥 Sakit - Perlu perawatan</option>
                            </select>
                            <small class="form-text">Kondisi fisik DOQ saat masuk ke kandang pembesaran</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="catatan" class="form-label">
                                Catatan Operator / Petugas
                            </label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="4" 
                                placeholder="Catatan khusus: asal DOQ, kondisi cuaca saat masuk, perlakuan khusus, dll...">{{ old('catatan') }}</textarea>
                            <small class="form-text">Informasi tambahan dari operator/petugas kandang</small>
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
                        Simpan Data
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
    const penetasanSelect = document.getElementById('penetasan_id');
    const jumlahInput = document.getElementById('jumlah_anak_ayam');
    const kandangSelect = document.getElementById('kandang_id');
    const capacityInfo = document.getElementById('kandangCapacityInfo');
    const capacityInfoText = document.getElementById('kandangCapacityInfoText');
    let kapasitasSisaSaatIni = 0;
    let lastCapacityAlertValue = null;
    const siapOffsetHari = 27;
    let lastAutoTanggalSiap = tanggalSiap?.value || '';

    const formatCapacityNumber = (value) => {
        const numeric = Number.isFinite(value) ? value : parseInt(value ?? 0, 10) || 0;
        return new Intl.NumberFormat('id-ID').format(Math.max(numeric, 0));
    };

    function updateCapacityInfo() {
        if (!capacityInfo || !capacityInfoText) {
            return;
        }

        if (!kandangSelect || !kandangSelect.value) {
            kapasitasSisaSaatIni = 0;
            capacityInfo.classList.remove('capacity-warning');
            capacityInfoText.textContent = 'Pilih kandang untuk melihat stok tersisa.';
            return;
        }

        const option = kandangSelect.options[kandangSelect.selectedIndex];
        const total = parseInt(option?.dataset?.kapasitas ?? '0', 10) || 0;
        const used = parseInt(option?.dataset?.terpakai ?? '0', 10) || 0;
        const remaining = parseInt(option?.dataset?.sisa ?? '0', 10);
        kapasitasSisaSaatIni = Math.max(remaining, 0);

        capacityInfoText.innerHTML = `Sisa <strong>${formatCapacityNumber(remaining)}</strong> dari ${formatCapacityNumber(total)} slot (terpakai ${formatCapacityNumber(used)})`;
        capacityInfo.classList.toggle('capacity-warning', remaining <= 0);
    }

    function enforceCapacityLimit(showAlert = false) {
        if (!jumlahInput || !kandangSelect || !kandangSelect.value) {
            lastCapacityAlertValue = null;
            return true;
        }

        const value = parseInt(jumlahInput.value || '0', 10);
        if (!Number.isFinite(value) || value <= 0) {
            lastCapacityAlertValue = null;
            return true;
        }

        if (kapasitasSisaSaatIni <= 0) {
            if (showAlert && lastCapacityAlertValue !== 'full') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kapasitas penuh',
                    text: 'Kandang yang dipilih sudah penuh. Pilih kandang lain atau akhiri batch yang berjalan.',
                });
                lastCapacityAlertValue = 'full';
            }
            jumlahInput.value = '';
            return false;
        }

        if (value > kapasitasSisaSaatIni) {
            if (showAlert && lastCapacityAlertValue !== value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Melebihi kapasitas',
                    text: `Jumlah anak puyuh melebihi sisa kapasitas (${formatCapacityNumber(kapasitasSisaSaatIni)}). Nilai akan disesuaikan otomatis.`,
                });
                lastCapacityAlertValue = value;
            }
            jumlahInput.value = kapasitasSisaSaatIni;
            return false;
        }

        lastCapacityAlertValue = null;
        return true;
    }

    function validateCapacityBeforeSubmit() {
        if (!kandangSelect || !kandangSelect.value) {
            return true;
        }

        const value = parseInt(jumlahInput?.value || '0', 10);
        const selectedLabel = kandangSelect.options[kandangSelect.selectedIndex]?.text?.trim() || 'kandang terpilih';

        if (kapasitasSisaSaatIni <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Kandang penuh',
                text: `${selectedLabel} sudah tidak memiliki slot tersisa.`,
            });
            return false;
        }

        if (value > kapasitasSisaSaatIni) {
            Swal.fire({
                icon: 'error',
                title: 'Melebihi kapasitas',
                text: `Sisa kapasitas hanya ${formatCapacityNumber(kapasitasSisaSaatIni)} ekor di ${selectedLabel}.`,
            });
            return false;
        }

        return true;
    }

    // Auto-fill jumlah jika memilih penetasan
    penetasanSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const text = selectedOption.text;
            
            // Extract jumlah DOQ dari text (format: "... (DOQ: 500 ekor, ...)")
            const match = text.match(/DOQ:\s*([\d,]+)/);
            if (match && !jumlahInput.value) {
                const jumlahDoc = parseInt(match[1].replace(/,/g, ''));
                jumlahInput.value = jumlahDoc;
                
                Swal.fire({
                    icon: 'info',
                    title: 'Auto-fill',
                    text: `Jumlah anak puyuh diisi otomatis: ${jumlahDoc.toLocaleString('id-ID')} ekor dari batch penetasan`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            enforceCapacityLimit(true);
        }
    });

    // Form submission dengan SweetAlert2 confirmation
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateCapacityBeforeSubmit()) {
            return;
        }

        const batchCode = document.getElementById('batch_produksi_id').value;
        const kandang = document.getElementById('kandang_id').selectedOptions[0].text;
        const jumlah = parseInt(jumlahInput.value);
        
        Swal.fire({
            title: 'Konfirmasi Simpan',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Batch:</strong> ${batchCode}</p>
                    <p><strong>Kandang:</strong> ${kandang}</p>
                    <p><strong>Jumlah DOQ:</strong> ${jumlah.toLocaleString('id-ID')} ekor</p>
                    <hr>
                    <p style="color: #dc2626;">Apakah data sudah benar?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            focusCancel: true,
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Periksa Lagi'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable button dan show loading
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
                
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
            text: 'Semua data yang sudah diisi akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.reset();
                setTimeout(() => {
                    updateCapacityInfo();
                }, 50);
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

    if (kandangSelect) {
        kandangSelect.addEventListener('change', () => {
            updateCapacityInfo();
            enforceCapacityLimit(false);
        });
        updateCapacityInfo();
    }

    if (jumlahInput) {
        jumlahInput.addEventListener('input', () => enforceCapacityLimit(true));
    }

    function hitungTanggalSiapAwal(baseTanggal, offsetHari = siapOffsetHari) {
        if (!baseTanggal) return '';
        const baseDate = new Date(baseTanggal);
        if (Number.isNaN(baseDate.getTime())) return '';
        baseDate.setDate(baseDate.getDate() + offsetHari);
        return baseDate.toISOString().slice(0, 10);
    }

    function autoIsiTanggalSiap(options = {}) {
        if (!tanggalMasuk || !tanggalSiap) return;
        const { respectUserEdit = true } = options;
        if (respectUserEdit && tanggalSiap.dataset.userEdited === 'true') {
            return;
        }
        const kandidat = hitungTanggalSiapAwal(tanggalMasuk.value, siapOffsetHari);
        if (!kandidat) return;
        tanggalSiap.value = kandidat;
        delete tanggalSiap.dataset.userEdited;
        lastAutoTanggalSiap = kandidat;
    }

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
});
</script>

@endsection
