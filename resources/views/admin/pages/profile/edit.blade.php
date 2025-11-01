@extends('admin.layouts.app')

@section('title', 'Edit Profile')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css">
<style>
  :root {
    --profile-card-bg: #ffffff;
    --profile-accent: #0d6efd;
    --profile-muted: #6c757d;
  }

  .profile-edit-wrapper {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 16px 16px;
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

  .profile-photo-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    flex-wrap: wrap;
  }

  .profile-photo-actions label {
    margin-bottom: 0;
  }

  .profile-photo-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .profile-photo-actions .btn:disabled {
    opacity: 0.5;
    pointer-events: none;
  }

  .profile-edit-wrapper .disabled-field {
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    background-color: #e9ecef !important;
  }

  .profile-crop-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
  }

  .profile-crop-modal.is-active {
    display: flex;
  }

  .profile-crop-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(2px);
  }

  .profile-crop-dialog {
    position: relative;
    background: #fff;
    border-radius: 14px;
    width: min(520px, 94vw);
    box-shadow: 0 20px 48px rgba(15, 23, 42, 0.22);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .profile-crop-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.25);
  }

  .profile-crop-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.05rem;
    color: #0f172a;
  }

  .profile-crop-close {
    border: none;
    background: transparent;
    color: #64748b;
    font-size: 1.4rem;
    line-height: 1;
    cursor: pointer;
    padding: 4px 6px;
  }

  .profile-crop-close:hover {
    color: #0f172a;
  }

  .profile-crop-body {
    padding: 16px;
    background: #f8fafc;
  }

  .profile-crop-canvas {
    width: 100%;
    height: clamp(360px, 60vh, 520px);
    background: #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .profile-crop-canvas img,
  .profile-crop-canvas .cropper-container {
    max-width: 100%;
    max-height: 100%;
  }

  .profile-crop-canvas img {
    object-fit: contain;
  }

  .profile-crop-canvas .cropper-container,
  .profile-crop-canvas .cropper-wrap-box,
  .profile-crop-canvas .cropper-canvas,
  .profile-crop-canvas .cropper-drag-box {
    width: 100% !important;
    height: 100% !important;
  }

  @media (max-width: 576px) {
    .profile-crop-canvas {
      height: clamp(260px, 65vh, 380px);
      border-radius: 10px;
    }
  }

  .profile-crop-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-top: 1px solid rgba(148, 163, 184, 0.25);
    background: #ffffff;
  }

  .profile-crop-footer .btn {
    min-width: 110px;
  }

  .profile-crop-rotate-icon {
    font-size: 1rem;
  }

  body.profile-crop-open {
    overflow: hidden;
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
  $profilePhotoUrl = $user->foto_profil ? asset('foto_profil/' . $user->foto_profil) : '';
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
        <div class="profile-photo-actions">
          <label for="profile_picture" class="btn btn-sm btn-outline-primary profile-change-photo">
            <i class="fa-solid fa-camera me-1"></i> Ganti Foto
          </label>
          <button type="button" class="btn btn-sm btn-outline-danger" id="removePhotoBtn" {{ $profilePhotoUrl ? '' : 'disabled' }}>
            <i class="fa-solid fa-trash-can me-1"></i> Hapus Foto
          </button>
        </div>
        <input type="hidden" name="remove_profile_picture" id="removeProfilePicture" value="0">
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

<div class="profile-crop-modal" id="profileCropperModal" aria-hidden="true">
  <div class="profile-crop-backdrop" id="profileCropperBackdrop"></div>
  <div class="profile-crop-dialog" role="dialog" aria-modal="true" aria-labelledby="profileCropperTitle">
    <div class="profile-crop-header">
      <h5 id="profileCropperTitle"><i class="fa-solid fa-image me-2 text-primary"></i>Sesuaikan Foto Profil</h5>
      <button type="button" class="profile-crop-close" id="cropperClose" aria-label="Tutup">&times;</button>
    </div>
    <div class="profile-crop-body">
      <div class="profile-crop-canvas">
        <img id="cropperImage" alt="Pratinjau Foto Profil" />
      </div>
    </div>
    <div class="profile-crop-footer">
      <button type="button" class="btn btn-outline-secondary" id="cropperCancel">Batal</button>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-info" id="cropperRotate">
          <i class="fa-solid fa-rotate-right profile-crop-rotate-icon me-2"></i>Putar 90°
        </button>
        <button type="button" class="btn btn-primary" id="cropperConfirm">
          <i class="fa-solid fa-crop me-2"></i>Simpan
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    const removeBtn = document.getElementById('removePhotoBtn');
    const removeField = document.getElementById('removeProfilePicture');
    const cropperModal = document.getElementById('profileCropperModal');
    const cropperBackdrop = document.getElementById('profileCropperBackdrop');
    const cropperClose = document.getElementById('cropperClose');
    const cropperImage = document.getElementById('cropperImage');
    const cropperConfirm = document.getElementById('cropperConfirm');
    const cropperCancel = document.getElementById('cropperCancel');
    const cropperRotate = document.getElementById('cropperRotate');
    const initialPhotoSrc = avatarPreview ? (avatarPreview.dataset.initialSrc || avatarPreview.getAttribute('src') || '') : '';
    let cropperInstance = null;
    let pendingFile = null;
    let objectUrl = null;

    if (removeField) {
      removeField.value = '0';
    }

    function computeInitial(name) {
      if (!name) { return '?'; }
      const parts = name.trim().split(/\s+/);
      return (parts[0] ? parts[0][0] : '?').toUpperCase();
    }

    function refreshInitial() {
      if (!avatarInitial) { return; }
      const targetName = nameInput ? nameInput.value : '';
      avatarInitial.textContent = computeInitial(targetName);
    }

    function toggleRemoveButton(enable) {
      if (!removeBtn) { return; }
      removeBtn.disabled = !enable;
    }

    function showImage(src) {
      if (!avatarPreview || !avatarInitial) { return; }
      if (!src) {
        avatarPreview.classList.add('d-none');
        avatarPreview.removeAttribute('src');
        avatarInitial.classList.remove('d-none');
        toggleRemoveButton(false);
        return;
      }
      avatarPreview.src = src;
      avatarPreview.classList.remove('d-none');
      avatarInitial.classList.add('d-none');
      toggleRemoveButton(true);
    }

    function openCropper(file) {
      if (!cropperModal || !cropperImage) { return; }
      pendingFile = file;
      if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
      }
      objectUrl = URL.createObjectURL(file);
      cropperImage.src = objectUrl;
      cropperModal.classList.add('is-active');
      document.body.classList.add('profile-crop-open');
    }

    function closeCropper(options = {}) {
      if (cropperInstance) {
        cropperInstance.destroy();
        cropperInstance = null;
      }
      if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        objectUrl = null;
      }
      pendingFile = null;
      if (cropperModal) {
        cropperModal.classList.remove('is-active');
      }
      document.body.classList.remove('profile-crop-open');
      if (!options.keepFileInput && fileInput) {
        fileInput.value = '';
      }
    }

    function handleFileChange(event) {
      const file = event.target.files[0];
      if (!file) { return; }
      if (!file.type || !file.type.startsWith('image/')) {
        alert('Silakan pilih file gambar yang valid.');
        fileInput.value = '';
        return;
      }
      if (removeField) {
        removeField.value = '0';
      }
      openCropper(file);
    }

    if (cropperImage) {
      cropperImage.addEventListener('load', function () {
        if (cropperInstance) {
          cropperInstance.destroy();
        }
        cropperInstance = new Cropper(cropperImage, {
          aspectRatio: 1,
          viewMode: 1,
          autoCropArea: 0.85,
          responsive: true,
          background: false,
          dragMode: 'move',
          ready: function () {
            if (this && this.cropper) {
              this.cropper.reset();
            }
          }
        });
      });
    }

    if (cropperConfirm) {
      cropperConfirm.addEventListener('click', function () {
        if (!cropperInstance || !pendingFile) {
          closeCropper();
          return;
        }
        const canvas = cropperInstance.getCroppedCanvas({
          width: 512,
          height: 512,
          imageSmoothingQuality: 'high'
        });
        if (!canvas) {
          closeCropper();
          return;
        }
        const targetType = pendingFile.type && pendingFile.type.startsWith('image/') ? pendingFile.type : 'image/png';
        canvas.toBlob(function (blob) {
          if (!blob) {
            closeCropper();
            return;
          }
          const baseName = pendingFile.name ? pendingFile.name.replace(/\.[^/.]+$/, '') : 'profile-picture';
          const extension = targetType.split('/')[1] || 'png';
          const newFile = new File([blob], `${baseName}.${extension}`, { type: targetType });
          const transfer = new DataTransfer();
          transfer.items.add(newFile);
          fileInput.files = transfer.files;
          if (removeField) {
            removeField.value = '0';
          }
          if (avatarPreview) {
            avatarPreview.dataset.initialSrc = '';
          }
          const reader = new FileReader();
          reader.onload = function (readerEvent) {
            showImage(readerEvent.target.result);
          };
          reader.readAsDataURL(newFile);
          closeCropper({ keepFileInput: true });
        }, targetType, 0.95);
      });
    }

    const cancelCropper = function () {
      closeCropper();
    };

    [cropperCancel, cropperClose, cropperBackdrop].forEach(function (el) {
      if (el) {
        el.addEventListener('click', cancelCropper);
      }
    });

    if (cropperRotate) {
      cropperRotate.addEventListener('click', function () {
        if (cropperInstance) {
          cropperInstance.rotate(90);
        }
      });
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && cropperModal && cropperModal.classList.contains('is-active')) {
        closeCropper();
      }
    });

    if (removeBtn && removeField) {
      removeBtn.addEventListener('click', function () {
        if (removeBtn.disabled) { return; }

        Swal.fire({
          title: 'Hapus Foto?',
          text: 'Foto profil akan dihapus dan diganti inisial akun.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#d33',
          reverseButtons: true
        }).then(function (result) {
          if (!result.isConfirmed) {
            return;
          }
          removeField.value = '1';
          if (avatarPreview) {
            avatarPreview.dataset.initialSrc = '';
          }
          showImage('');
          if (fileInput) {
            fileInput.value = '';
          }
          Swal.fire({
            title: 'Berhasil',
            text: 'Foto profil telah dihapus.',
            icon: 'success',
            timer: 1600,
            showConfirmButton: false
          });
        });
      });
    }

    if (fileInput) {
      fileInput.addEventListener('change', handleFileChange);
    }

    if (avatarPreview) {
      avatarPreview.addEventListener('error', function () {
        avatarPreview.classList.add('d-none');
        avatarPreview.removeAttribute('src');
        if (avatarInitial) {
          avatarInitial.classList.remove('d-none');
        }
        toggleRemoveButton(Boolean(avatarPreview.dataset.initialSrc));
      });
    }

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
        Swal.fire({
          title: 'Menyimpan Perubahan',
          text: 'Mohon tunggu, perubahan Anda sedang disimpan.',
          icon: 'info',
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
      });
    }

    if (nameInput) {
      nameInput.addEventListener('input', function () {
        refreshInitial();
      });
    }

    refreshInitial();

    if (initialPhotoSrc) {
      showImage(initialPhotoSrc);
    } else if (avatarPreview && !avatarPreview.getAttribute('src')) {
      showImage('');
    } else {
      const hasPhoto = avatarPreview && !avatarPreview.classList.contains('d-none');
      toggleRemoveButton(Boolean(hasPhoto));
    }

    @if(session('success'))
      Swal.fire({
        title: 'Berhasil',
        text: @json(session('success')),
        icon: 'success',
        confirmButtonText: 'OK'
      });
    @endif

    @if(session('error'))
      Swal.fire({
        title: 'Gagal',
        text: @json(session('error')),
        icon: 'error',
        confirmButtonText: 'OK'
      });
    @endif
  });
</script>
@endpush
