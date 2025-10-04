@extends('admin.layouts.app')

@section('title', 'Tambah Data Pembesaran')

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
                    Tambah Data Pembesaran
                </h1>
                <p class="text-muted mb-0">Formulir untuk menambah data pembesaran DOC/anak puyuh</p>
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
                                @foreach($kandangList as $k)
                                <option value="{{ $k->id }}" {{ old('kandang_id') == $k->id ? 'selected' : '' }}>
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
                                value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
                            <small class="form-text">Tanggal DOC masuk ke kandang pembesaran</small>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_anak_ayam" class="form-label">
                                Jumlah Anak Puyuh <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="jumlah_anak_ayam" id="jumlah_anak_ayam" class="form-control" 
                                value="{{ old('jumlah_anak_ayam') }}" required min="1" placeholder="Contoh: 500">
                            <small class="form-text">Jumlah DOC/anak puyuh yang masuk</small>
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
                                    (DOC: {{ number_format($p->jumlah_doc) }} ekor, 
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
                                <option value="">-- Belum Dipisah / Campur --</option>
                                <option value="betina" {{ old('jenis_kelamin') == 'betina' ? 'selected' : '' }}>
                                    Betina
                                </option>
                                <option value="jantan" {{ old('jenis_kelamin') == 'jantan' ? 'selected' : '' }}>
                                    Jantan
                                </option>
                            </select>
                            <small class="form-text">Jenis kelamin (opsional, jika sudah dipisah)</small>
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
                            <small class="form-text">Umur DOC saat masuk ke pembesaran (default: 1 hari)</small>
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
                                value="{{ old('tanggal_siap') }}" min="{{ date('Y-m-d') }}">
                            <small class="form-text">Estimasi tanggal siap dipindah ke produksi/penjualan</small>
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
                                Kondisi DOC Saat Masuk
                            </label>
                            <select name="kondisi_doc" id="kondisi_doc" class="form-control">
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="Sehat" {{ old('kondisi_doc') == 'Sehat' ? 'selected' : '' }}>✅ Sehat - Kondisi prima</option>
                                <option value="Baik" {{ old('kondisi_doc') == 'Baik' ? 'selected' : '' }}>👍 Baik - Kondisi normal</option>
                                <option value="Lemah" {{ old('kondisi_doc') == 'Lemah' ? 'selected' : '' }}>⚠️ Lemah - Perlu perhatian khusus</option>
                                <option value="Sakit" {{ old('kondisi_doc') == 'Sakit' ? 'selected' : '' }}>🏥 Sakit - Perlu perawatan</option>
                            </select>
                            <small class="form-text">Kondisi fisik DOC saat masuk ke kandang pembesaran</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="catatan" class="form-label">
                                Catatan Operator / Petugas
                            </label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="4" 
                                placeholder="Catatan khusus: asal DOC, kondisi cuaca saat masuk, perlakuan khusus, dll...">{{ old('catatan') }}</textarea>
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
    const form = document.getElementById('formPembesaran');
    const btnSubmit = document.getElementById('btnSubmit');
    const tanggalMasuk = document.getElementById('tanggal_masuk');
    const tanggalSiap = document.getElementById('tanggal_siap');
    const penetasanSelect = document.getElementById('penetasan_id');
    const jumlahInput = document.getElementById('jumlah_anak_ayam');

    // Auto-calculate tanggal siap (35-42 hari untuk puyuh, default: 40 hari)
    tanggalMasuk.addEventListener('change', function() {
        if (this.value && !tanggalSiap.value) {
            const masuk = new Date(this.value);
            masuk.setDate(masuk.getDate() + 40); // 40 hari standar pembesaran
            tanggalSiap.value = masuk.toISOString().split('T')[0];
        }
    });

    // Auto-fill jumlah jika memilih penetasan
    penetasanSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const text = selectedOption.text;
            
            // Extract jumlah DOC dari text (format: "... (DOC: 500 ekor, ...)")
            const match = text.match(/DOC:\s*([\d,]+)/);
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
        }
    });

    // Trigger auto-calculate on page load if tanggal_masuk has value
    if (tanggalMasuk.value && !tanggalSiap.value) {
        const masuk = new Date(tanggalMasuk.value);
        masuk.setDate(masuk.getDate() + 40);
        tanggalSiap.value = masuk.toISOString().split('T')[0];
    }

    // Form submission dengan SweetAlert2 confirmation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const batchCode = document.getElementById('batch_produksi_id').value;
        const kandang = document.getElementById('kandang_id').selectedOptions[0].text;
        const jumlah = parseInt(jumlahInput.value);
        
        Swal.fire({
            title: 'Konfirmasi Simpan',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Batch:</strong> ${batchCode}</p>
                    <p><strong>Kandang:</strong> ${kandang}</p>
                    <p><strong>Jumlah DOC:</strong> ${jumlah.toLocaleString('id-ID')} ekor</p>
                    <hr>
                    <p style="color: #dc2626;">Apakah data sudah benar?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
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
