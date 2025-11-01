@extends('admin.layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="container-fluid py-2">
  <div class="card shadow-sm border-0 mx-auto" style="max-width: 1200px; border-radius: 1rem;">
    <div class="card-body p-4 text-start">

      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
            <i class="fa-solid fa-user-plus text-primary fa-lg"></i>
          </div>
          <div>
            <h5 class="fw-semibold mb-0">Form Tambah Karyawan</h5>
            <small class="text-muted">Masukkan detail data karyawan dengan lengkap</small>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">

          <!-- Nama Lengkap -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-user text-primary me-1"></i>Nama Lengkap
            </label>
            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Contoh: John Doe" value="{{ old('nama') }}" required>
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Username -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-at text-primary me-1"></i>Username
            </label>
            <input type="text" name="nama_pengguna" class="form-control @error('nama_pengguna') is-invalid @enderror" placeholder="Contoh: johndoe" value="{{ old('nama_pengguna') }}" required>
            @error('nama_pengguna')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Email -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-envelope text-info me-1"></i>Email
            </label>
            <input type="email" name="surel" class="form-control @error('surel') is-invalid @enderror" placeholder="Contoh: john@example.com" value="{{ old('surel') }}" required>
            @error('surel')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Password -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-lock text-success me-1"></i>Password
            </label>
            <input type="password" name="kata_sandi" class="form-control @error('kata_sandi') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
            @error('kata_sandi')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Peran -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-user-shield text-warning me-1"></i>Peran
            </label>
            <select name="peran" class="form-select @error('peran') is-invalid @enderror" required>
              <option value="" selected disabled>Pilih peran</option>
              <option value="operator" {{ old('peran') == 'operator' ? 'selected' : '' }}>Operator</option>
              <option value="owner" {{ old('peran') == 'owner' ? 'selected' : '' }}>Owner</option>
            </select>
            @error('peran')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Foto Profil -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-camera text-secondary me-1"></i>Foto Profil
            </label>
            <input type="file" name="foto_profil" class="form-control @error('foto_profil') is-invalid @enderror" accept="image/*">
            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
            @error('foto_profil')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Alamat -->
          <div class="col-12">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-map-marker-alt text-danger me-1"></i>Alamat
            </label>
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
            @error('alamat')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

        </div>

        <!-- Tombol Aksi -->
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('admin.karyawan') }}" class="btn btn-secondary px-4">
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