@extends('admin.layouts.app')

@section('title', 'Tambah Kandang')

@php
  $breadcrumbs = [
    ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
    ['label' => 'Kandang', 'link' => route('admin.kandang')],
    ['label' => 'Tambah Data'],
  ];
@endphp

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0 mx-auto" style="max-width: 1200px; border-radius: 1rem;">
    <div class="card-body p-4 text-start">

      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
            <i class="fa-solid fa-warehouse text-primary fa-lg"></i>
          </div>
          <div>
            <h5 class="fw-semibold mb-0">Form Input Kandang</h5>
            <small class="text-muted">Masukkan detail data kandang dengan lengkap</small>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form action="{{ route('admin.kandang.store') }}" method="POST">
        @csrf
        <div class="row g-3">

          <!-- Nama Kandang -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-tag text-primary me-1"></i>Nama Kandang
            </label>
            <input type="text" name="nama_kandang" class="form-control @error('nama_kandang') is-invalid @enderror" placeholder="Contoh: Kandang Pembesaran A" value="{{ old('nama_kandang') }}" required>
            @error('nama_kandang')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Tipe -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-layer-group text-primary me-1"></i>Tipe
            </label>
            <select name="tipe_kandang" class="form-select @error('tipe_kandang') is-invalid @enderror" required>
              <option value="" disabled {{ old('tipe_kandang') ? '' : 'selected' }}>Pilih tipe kandang</option>
              <option value="Pembesaran" {{ old('tipe_kandang') == 'Pembesaran' ? 'selected' : '' }}>Pembesaran</option>
              <option value="Produksi" {{ old('tipe_kandang') == 'Produksi' ? 'selected' : '' }}>Produksi</option>
              <option value="Penetasan" {{ old('tipe_kandang') == 'Penetasan' ? 'selected' : '' }}>Penetasan</option>
              <option value="Karantina" {{ old('tipe_kandang') == 'Karantina' ? 'selected' : '' }}>Karantina</option>
            </select>
            @error('tipe_kandang')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Kapasitas Maks -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-cubes-stacked text-info me-1"></i>Kapasitas Maksimal
            </label>
            <input type="number" name="kapasitas_maksimal" class="form-control @error('kapasitas_maksimal') is-invalid @enderror" placeholder="Masukkan kapasitas maksimal (ekor)" value="{{ old('kapasitas_maksimal') }}" min="0">
            @error('kapasitas_maksimal')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Status -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-toggle-on text-success me-1"></i>Status
            </label>
            <select name="status" class="form-select @error('status') is-invalid @enderror">
              <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
              <option value="Maintenance" {{ old('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
              <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Keterangan -->
          <div class="col-12">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-note-sticky text-secondary me-1"></i>Keterangan
            </label>
            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Tuliskan keterangan singkat kandang...">{{ old('keterangan') }}</textarea>
            @error('keterangan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

        </div>

        <!-- Tombol Aksi -->
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('admin.kandang') }}" class="btn btn-secondary px-4">
            <i class="fa-solid fa-arrow-left me-1"></i>Batal
          </a>
          <button type="reset" class="btn btn-outline-secondary px-4">
            <i class="fa-solid fa-rotate-left me-1"></i>Reset
          </button>
          <button type="submit" class="btn btn-primary px-4">
            <i class="fa-solid fa-save me-1"></i>Simpan
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection
