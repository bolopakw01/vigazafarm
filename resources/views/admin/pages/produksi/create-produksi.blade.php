@extends('admin.layouts.app')

@section('title', 'Tambah Data Produksi')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Data Produksi
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.produksi.store') }}" method="POST" id="produksiForm">
                        @csrf

                        <!-- Kandang -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kandang_id" class="form-label">Kandang <span class="text-danger">*</span></label>
                                <select name="kandang_id" id="kandang_id" class="form-select @error('kandang_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kandang</option>
                                    @foreach($kandangList as $kandang)
                                        <option value="{{ $kandang->id }}" {{ old('kandang_id') == $kandang->id ? 'selected' : '' }}>
                                            {{ $kandang->nama_kandang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kandang_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jenis Input -->
                            <div class="col-md-6">
                                <label class="form-label">Jenis Input <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_input" id="manual" value="manual" {{ old('jenis_input', 'manual') == 'manual' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="manual">
                                        Manual
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_input" id="dari_pembesaran" value="dari_pembesaran" {{ old('jenis_input') == 'dari_pembesaran' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dari_pembesaran">
                                        Dari Pembesaran
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_input" id="dari_penetasan" value="dari_penetasan" {{ old('jenis_input') == 'dari_penetasan' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dari_penetasan">
                                        Dari Penetasan
                                    </label>
                                </div>
                                @error('jenis_input')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Dari Pembesaran Section -->
                        <div id="pembesaranSection" class="row mb-3" style="display: none;">
                            <div class="col-12">
                                <label for="pembesaran_id" class="form-label">Pilih Pembesaran</label>
                                <select name="pembesaran_id" id="pembesaran_id" class="form-select @error('pembesaran_id') is-invalid @enderror">
                                    <option value="">Pilih Pembesaran</option>
                                    @foreach($pembesaranList as $pembesaran)
                                        <option value="{{ $pembesaran->id }}" {{ old('pembesaran_id') == $pembesaran->id ? 'selected' : '' }}>
                                            Batch {{ $pembesaran->batch_pembesaran_id }} - {{ $pembesaran->kandang->nama_kandang ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pembesaran_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Dari Penetasan Section -->
                        <div id="penetasanSection" class="row mb-3" style="display: none;">
                            <div class="col-md-6">
                                <label for="penetasan_id" class="form-label">Pilih Penetasan</label>
                                <select name="penetasan_id" id="penetasan_id" class="form-select @error('penetasan_id') is-invalid @enderror">
                                    <option value="">Pilih Penetasan</option>
                                    @foreach($penetasanList as $penetasan)
                                        <option value="{{ $penetasan->id }}" {{ old('penetasan_id') == $penetasan->id ? 'selected' : '' }}>
                                            Batch {{ $penetasan->batch_penetasan_id }} - {{ $penetasan->kandang->nama_kandang ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('penetasan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="jumlah_telur" class="form-label">Jumlah Telur</label>
                                <input type="number" name="jumlah_telur" id="jumlah_telur" class="form-control @error('jumlah_telur') is-invalid @enderror" value="{{ old('jumlah_telur') }}" min="0">
                                @error('jumlah_telur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="berat_rata_telur" class="form-label">Berat Rata-rata Telur (gram)</label>
                                <input type="number" step="0.01" name="berat_rata_telur" id="berat_rata_telur" class="form-control @error('berat_rata_telur') is-invalid @enderror" value="{{ old('berat_rata_telur') }}" min="0">
                                @error('berat_rata_telur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Batch Produksi ID -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="batch_produksi_id" class="form-label">Batch Produksi ID</label>
                                <input type="text" name="batch_produksi_id" id="batch_produksi_id" class="form-control @error('batch_produksi_id') is-invalid @enderror" value="{{ old('batch_produksi_id') }}" maxlength="50">
                                @error('batch_produksi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jumlah Indukan -->
                            <div class="col-md-6">
                                <label for="jumlah_indukan" class="form-label">Jumlah Indukan <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah_indukan" id="jumlah_indukan" class="form-control @error('jumlah_indukan') is-invalid @enderror" value="{{ old('jumlah_indukan') }}" min="1" required>
                                @error('jumlah_indukan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tanggal Mulai dan Akhir -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control @error('tanggal_akhir') is-invalid @enderror" value="{{ old('tanggal_akhir') }}">
                                @error('tanggal_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Umur Mulai Produksi dan Status -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="umur_mulai_produksi" class="form-label">Umur Mulai Produksi (hari)</label>
                                <input type="number" name="umur_mulai_produksi" id="umur_mulai_produksi" class="form-control @error('umur_mulai_produksi') is-invalid @enderror" value="{{ old('umur_mulai_produksi') }}" min="1">
                                @error('umur_mulai_produksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="">Pilih Status</option>
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Harga per KG -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="harga_per_kg" class="form-label">Harga per KG</label>
                                <input type="number" step="0.01" name="harga_per_kg" id="harga_per_kg" class="form-control @error('harga_per_kg') is-invalid @enderror" value="{{ old('harga_per_kg') }}" min="0">
                                @error('harga_per_kg')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan
                                </button>
                                <a href="{{ route('admin.produksi') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisInputRadios = document.querySelectorAll('input[name="jenis_input"]');
    const pembesaranSection = document.getElementById('pembesaranSection');
    const penetasanSection = document.getElementById('penetasanSection');

    function toggleSections() {
        const selectedValue = document.querySelector('input[name="jenis_input"]:checked').value;
        
        if (selectedValue === 'dari_pembesaran') {
            pembesaranSection.style.display = 'block';
            penetasanSection.style.display = 'none';
            // Make pembesaran_id required
            document.getElementById('pembesaran_id').setAttribute('required', 'required');
            document.getElementById('penetasan_id').removeAttribute('required');
        } else if (selectedValue === 'dari_penetasan') {
            pembesaranSection.style.display = 'none';
            penetasanSection.style.display = 'block';
            // Make penetasan_id required
            document.getElementById('penetasan_id').setAttribute('required', 'required');
            document.getElementById('pembesaran_id').removeAttribute('required');
        } else {
            pembesaranSection.style.display = 'none';
            penetasanSection.style.display = 'none';
            document.getElementById('pembesaran_id').removeAttribute('required');
            document.getElementById('penetasan_id').removeAttribute('required');
        }
    }

    jenisInputRadios.forEach(radio => {
        radio.addEventListener('change', toggleSections);
    });

    // Initial check
    toggleSections();
});
</script>
@endsection