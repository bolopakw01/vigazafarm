@extends('admin.layouts.app')

@section('title', 'Edit Data Pembesaran - ' . $pembesaran->batch_produksi_id)

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-pembesaran.css') }}">
@endpush

@section('content')
<div class="bolopa-form-wrapper">
    <div class="bolopa-form-container">
        <!-- Header -->
        <div class="bolopa-form-header">
            <div>
                <h1>
                    <i class="fa-solid fa-dove"></i>
                    Edit Data Pembesaran
                </h1>
                <p class="text-muted mb-0">Formulir untuk mengedit data pembesaran DOC/anak puyuh</p>
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
                                <option value="{{ $k->id }}" {{ old('kandang_id', $pembesaran->kandang_id) == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kandang }} (Kapasitas: {{ number_format($k->kapasitas) }} ekor)
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text">Pilih kandang pembesaran yang akan digunakan</small>
                        </div>
                    </div>
                </div>

                <!-- Section: Data Masuk DOC / Anak Puyuh -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-dove"></i>
                        Data Masuk DOC / Anak Puyuh
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_masuk" class="form-label">
                                Tanggal Masuk <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" 
                                value="{{ old('tanggal_masuk', $pembesaran->tanggal_masuk ? $pembesaran->tanggal_masuk->format('Y-m-d') : '') }}" 
                                required max="{{ date('Y-m-d') }}">
                            <small class="form-text">Tanggal DOC masuk ke kandang pembesaran</small>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_anak_ayam" class="form-label">
                                Jumlah Anak Puyuh <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="jumlah_anak_ayam" id="jumlah_anak_ayam" class="form-control" 
                                value="{{ old('jumlah_anak_ayam', $pembesaran->jumlah_anak_ayam) }}" 
                                required min="1" placeholder="Contoh: 500">
                            <small class="form-text">Jumlah DOC/anak puyuh yang masuk</small>
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
                                <option value="" {{ old('jenis_kelamin', $pembesaran->jenis_kelamin) == '' ? 'selected' : '' }}>
                                    -- Belum Dipisah / Campur --
                                </option>
                                <option value="betina" {{ old('jenis_kelamin', $pembesaran->jenis_kelamin) == 'betina' ? 'selected' : '' }}>
                                    Betina
                                </option>
                                <option value="jantan" {{ old('jenis_kelamin', $pembesaran->jenis_kelamin) == 'jantan' ? 'selected' : '' }}>
                                    Jantan
                                </option>
                            </select>
                            <small class="form-text">Jenis kelamin (opsional, jika sudah dipisah)</small>
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
                            <small class="form-text">Umur DOC saat masuk ke pembesaran (default: 1 hari)</small>
                        </div>

                        <div class="form-group">
                            <label for="berat_rata_rata" class="form-label">
                                Berat Rata-rata Awal (gram)
                            </label>
                            <input type="number" name="berat_rata_rata" id="berat_rata_rata" class="form-control" 
                                value="{{ old('berat_rata_rata', $pembesaran->berat_rata_rata) }}" 
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
                                value="{{ old('tanggal_siap', $pembesaran->tanggal_siap ? $pembesaran->tanggal_siap->format('Y-m-d') : '') }}" 
                                min="{{ date('Y-m-d') }}">
                            <small class="form-text">Estimasi tanggal siap dipindah ke produksi/penjualan</small>
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

                <!-- Section: Catatan Tambahan -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-clipboard"></i>
                        Catatan Tambahan
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="kondisi_doc" class="form-label">
                                Kondisi DOC Saat Masuk
                            </label>
                            <input type="text" class="form-control" 
                                value="{{ $pembesaran->kondisi_doc ?? 'Tidak ada data' }}" 
                                readonly style="background-color: #f1f5f9;">
                            <small class="form-text">Kondisi fisik DOC saat masuk (tidak dapat diubah)</small>
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
    const form = document.getElementById('formPembesaran');
    const btnSubmit = document.getElementById('btnSubmit');
    const tanggalMasuk = document.getElementById('tanggal_masuk');
    const tanggalSiap = document.getElementById('tanggal_siap');
    const jumlahInput = document.getElementById('jumlah_anak_ayam');

    // Auto-calculate tanggal siap (35-42 hari untuk puyuh, default: 40 hari)
    tanggalMasuk.addEventListener('change', function() {
        if (this.value && !tanggalSiap.value) {
            const masuk = new Date(this.value);
            masuk.setDate(masuk.getDate() + 40); // 40 hari standar pembesaran
            tanggalSiap.value = masuk.toISOString().split('T')[0];
        }
    });

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
                    <p><strong>Jumlah DOC:</strong> ${jumlah.toLocaleString('id-ID')} ekor</p>
                    <hr>
                    <p style="color: #dc2626;">Apakah Anda yakin ingin mengupdate data ini?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
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
                Swal.fire({
                    icon: 'success',
                    title: 'Form direset',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
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
});
</script>

@endsection
