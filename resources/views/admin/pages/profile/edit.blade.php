@extends('admin.layouts.app')

@section('title', 'Edit Profile')

@push('styles')
<style>
  :root {
    --profile-card-bg: #ffffff;
    --profile-accent: #0d6efd;
    --profile-muted: #6c757d;
  }

  .profile-edit-wrapper {
    min-height: calc(100vh - 160px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 16px;
    background: linear-gradient(180deg, #f1f5f9 0%, #f8fafc 100%);
  }

  .profile-edit-card {
    width: 100%;
    max-width: 960px;
    border-radius: 14px;
    background: var(--profile-card-bg);
    box-shadow: 0 10px 30px rgba(16, 24, 40, 0.08);
    display: flex;
    overflow: hidden;
    gap: 0;
  }

  .profile-card-left {
    width: 300px;
    padding: 28px 22px;
    background: linear-gradient(180deg, rgba(13, 110, 253, 0.06), rgba(13, 110, 253, 0.02));
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    text-align: center;
  }

  .profile-card-right {
    flex: 1 1 auto;
    padding: 32px 32px 36px;
  }

  .profile-edit-wrapper .profile-edit-card .profile-avatar {
    width: 128px;
    height: 128px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 6px 18px rgba(13, 110, 253, 0.12);
    background: #e9eefc;
  }

  .profile-edit-wrapper .profile-edit-card .profile-avatar-initial {
    font-size: 44px;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, #6478ff, #00c6a7);
  }

  .profile-change-photo {
    font-size: 0.9rem;
  }

  .profile-edit-wrapper .profile-form .form-label {
    font-weight: 600 !important;
    color: #0f172a !important;
  }

  .profile-edit-wrapper .profile-form .form-control:focus,
  .profile-edit-wrapper .profile-form .form-select:focus {
    border-color: var(--profile-accent) !important;
    box-shadow: none !important;
  }

  .profile-edit-wrapper .profile-helper-text {
    color: var(--profile-muted) !important;
    font-size: 0.9rem !important;
  }

  .profile-edit-wrapper .profile-section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
  }

  .profile-edit-wrapper .profile-section-title h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0;
  }

  .profile-edit-wrapper .disabled-field {
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    background-color: #e9ecef !important;
  }

  .profile-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 32px;
  }

  @media (max-width: 992px) {
    .profile-edit-card {
      flex-direction: column;
    }

    .profile-card-left {
      width: 100%;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      text-align: left;
      padding: 20px 22px;
    }

    .profile-card-right {
      padding: 24px;
    }

    .profile-edit-wrapper .profile-avatar,
    .profile-edit-wrapper .profile-avatar-initial {
      width: 96px;
      height: 96px;
      font-size: 36px;
    }
  }

  @media (max-width: 576px) {
    .profile-edit-wrapper {
      padding: 18px 12px;
    }

    .profile-card-left {
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .profile-actions {
      flex-direction: column;
    }

    .profile-actions button,
    .profile-actions a {
      width: 100%;
    }
  }
</style>
@endpush

@section('content')
@php
  $profilePhotoUrl = $user->foto_profil ? asset('storage/foto_profil/' . $user->foto_profil) : '';
  $lastUpdatedHuman = $user->diperbarui_pada ? \Carbon\Carbon::parse($user->diperbarui_pada)->diffForHumans() : 'Belum pernah diperbarui';
  $peranLabel = match ($user->peran) {
    'owner' => 'Owner',
    'super_admin' => 'Super Admin',
    'admin' => 'Admin',
    default => 'Operator',
  };
@endphp

<div class="profile-edit-wrapper">
  <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-edit-card profile-form" id="profileForm">
    @csrf
    @method('PATCH')

    <div class="profile-card-left">
      <div class="position-relative">
        <img src="{{ $profilePhotoUrl }}" alt="Foto Profil" id="avatarPreview" class="profile-avatar mb-2 {{ $profilePhotoUrl ? '' : 'd-none' }}" data-initial-src="{{ $profilePhotoUrl }}">
        <div id="avatarInitial" class="profile-avatar profile-avatar-initial d-flex align-items-center justify-content-center mb-2 {{ $profilePhotoUrl ? 'd-none' : '' }}">
          {{ mb_strtoupper(mb_substr($user->nama ?? $user->nama_pengguna, 0, 1)) }}
        </div>
      </div>
      <div class="w-100">
        <label for="profile_picture" class="btn btn-sm btn-outline-primary profile-change-photo mb-2">
          <i class="fa-solid fa-camera me-1"></i> Ganti Foto
        </label>
        <input type="file" name="profile_picture" id="profile_picture" class="form-control d-none @error('profile_picture') is-invalid @enderror" accept="image/*">
        <div class="profile-helper-text">PNG, JPG atau GIF. Maks 2MB.</div>
        @error('profile_picture')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
      <div class="mt-auto w-100">
        <div class="profile-helper-text">Terakhir diperbarui: <strong>{{ $lastUpdatedHuman }}</strong></div>
      </div>
    </div>

    <div class="profile-card-right">
      <div class="profile-section-title">
        <h3><i class="fa-solid fa-user-pen text-primary me-2"></i>Edit Profil</h3>
        <small class="profile-helper-text">Perbarui informasi akun Anda</small>
      </div>

      <div class="row g-3">
        <div class="col-12 col-lg-6">
          <label class="form-label" for="nama">
            Nama Lengkap
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
            <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama lengkap" value="{{ old('nama', $user->nama) }}" required>
          </div>
          @error('nama')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label" for="username">
            Username
          </label>
          <div class="input-group">
            <span class="input-group-text">@</span>
            <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror {{ $user->peran !== 'owner' ? 'disabled-field' : '' }}" placeholder="Masukkan username" value="{{ old('username', $user->nama_pengguna) }}" {{ $user->peran !== 'owner' ? 'readonly' : 'required' }}>
          </div>
          @error('username')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label" for="email">
            Email
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email" value="{{ old('email', $user->surel) }}" required>
          </div>
          @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label" for="peran">
            Role / Peran</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-shield"></i></span>
            <input type="text" id="peran" class="form-control" value="{{ $peranLabel }}" readonly style="cursor: not-allowed; opacity: 0.6; background-color: #e9ecef;">
          </div>
          <div class="profile-helper-text">Peran tidak dapat diubah melalui halaman ini.</div>
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label" for="password">
            Kata Sandi Baru <small class="text-muted">(opsional)</small>
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Isi jika ingin mengganti password" aria-describedby="togglePassword">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Tampilkan kata sandi"><i class="fa-regular fa-eye"></i></button>
          </div>
          <div class="profile-helper-text">Gunakan kombinasi huruf dan angka minimal 8 karakter.</div>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12">
          <label class="form-label" for="alamat">
            Alamat
          </label>
          <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Masukkan alamat lengkap" rows="3">{{ old('alamat', $user->alamat ?? '') }}</textarea>
          @error('alamat')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="profile-actions">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Batal</a>
        <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('profile_picture');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarInitial = document.getElementById('avatarInitial');
    const nameInput = document.getElementById('nama');
    const form = document.getElementById('profileForm');
    const submitBtn = document.getElementById('btnSubmit');
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    function showImage(src) {
      if (!src) {
        avatarPreview.classList.add('d-none');
        avatarPreview.removeAttribute('src');
        avatarInitial.classList.remove('d-none');
        return;
      }
      avatarPreview.src = src;
      avatarPreview.classList.remove('d-none');
      avatarInitial.classList.add('d-none');
    }

    function computeInitial(name) {
      if (!name) { return '?'; }
      const parts = name.trim().split(/\s+/);
      return (parts[0] ? parts[0][0] : '?').toUpperCase();
    }

    function refreshInitial() {
      avatarInitial.textContent = computeInitial(nameInput.value);
    }

    if (nameInput) {
      nameInput.addEventListener('input', function () {
        refreshInitial();
      });
    }

    if (avatarPreview && !avatarPreview.getAttribute('src')) {
      const initialSrc = avatarPreview.dataset.initialSrc;
      if (initialSrc) {
        showImage(initialSrc);
      } else {
        showImage('');
      }
    }

    if (fileInput) {
      fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) {
          showImage('');
          return;
        }
        if (!file.type.startsWith('image/')) {
          alert('Silakan pilih file gambar yang valid.');
          fileInput.value = '';
          showImage('');
          return;
        }
        const reader = new FileReader();
        reader.onload = function () {
          showImage(reader.result);
        };
        reader.readAsDataURL(file);
      });
    }

    if (avatarPreview) {
      avatarPreview.addEventListener('error', function () {
        showImage('');
      });
    }

    refreshInitial();

    if (togglePassword && passwordField) {
      togglePassword.addEventListener('click', function () {
        const isText = passwordField.type === 'text';
        passwordField.type = isText ? 'password' : 'text';
        togglePassword.innerHTML = isText
          ? '<i class="fa-regular fa-eye"></i>'
          : '<i class="fa-regular fa-eye-slash"></i>';
      });
    }

    if (form && submitBtn) {
      form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
      });
    }
  });
</script>
@endpush
