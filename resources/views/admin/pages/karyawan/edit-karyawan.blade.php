@extends('admin.layouts.app')

@section('title', 'Edit Karyawan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css">
<style>
  .employee-photo-card {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 16px 18px;
    border: 1px dashed #cbd5f5;
    border-radius: 14px;
    background: #f8fafc;
  }

  .employee-avatar {
    width: 112px;
    height: 112px;
    border-radius: 50%;
    border: 4px solid #ffffff;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.16);
    background: #e2e8f0;
  }

  img.employee-avatar {
    object-fit: cover;
  }

  .employee-avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #6478ff, #00c6a7);
  }

  .employee-avatar-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .employee-photo-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .employee-photo-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .employee-helper-text {
    font-size: 0.85rem;
    color: #64748b;
  }

  .employee-crop-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2050;
  }

  .employee-crop-modal.is-active {
    display: flex;
  }

  .employee-crop-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(2px);
  }

  .employee-crop-dialog {
    position: relative;
    background: #ffffff;
    border-radius: 14px;
    width: min(520px, 94vw);
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.28);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .employee-crop-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.24);
  }

  .employee-crop-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.05rem;
    color: #0f172a;
  }

  .employee-crop-close {
    border: none;
    background: transparent;
    color: #64748b;
    font-size: 1.3rem;
    line-height: 1;
    cursor: pointer;
    padding: 4px 6px;
  }

  .employee-crop-close:hover {
    color: #0f172a;
  }

  .employee-crop-body {
    padding: 16px;
    background: #f8fafc;
  }

  .employee-crop-canvas {
    width: 100%;
    height: clamp(360px, 60vh, 520px);
    background: #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .employee-crop-canvas img,
  .employee-crop-canvas .cropper-container {
    max-width: 100%;
    max-height: 100%;
  }

  .employee-crop-canvas img {
    object-fit: contain;
  }

  .employee-crop-canvas .cropper-container,
  .employee-crop-canvas .cropper-wrap-box,
  .employee-crop-canvas .cropper-canvas,
  .employee-crop-canvas .cropper-drag-box {
    width: 100% !important;
    height: 100% !important;
  }

  .employee-crop-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-top: 1px solid rgba(148, 163, 184, 0.24);
    background: #ffffff;
  }

  .employee-crop-footer .btn {
    min-width: 110px;
  }

  body.employee-crop-open {
    overflow: hidden;
  }

  @media (max-width: 576px) {
    .employee-photo-card {
      flex-direction: column;
      align-items: flex-start;
    }

    .employee-avatar {
      width: 96px;
      height: 96px;
      font-size: 32px;
    }

    .employee-crop-canvas {
      height: clamp(260px, 65vh, 380px);
    }

    .employee-photo-actions {
      width: 100%;
      flex-direction: column;
    }

    .employee-photo-actions .btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0 mx-auto" style="max-width: 1200px; border-radius: 1rem;">
    <div class="card-body p-4 text-start">

      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
            <i class="fa-solid fa-user-edit text-warning fa-lg"></i>
          </div>
          <div>
            <h5 class="fw-semibold mb-0">Form Edit Karyawan</h5>
            <small class="text-muted">Ubah detail data karyawan</small>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form action="{{ route('admin.karyawan.update', $karyawan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row g-3">

          <!-- Nama Lengkap -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-user text-primary me-1"></i>Nama Lengkap
            </label>
            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Contoh: John Doe" value="{{ old('nama', $karyawan->nama) }}" required>
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Username -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-at text-primary me-1"></i>Username
            </label>
            <input type="text" name="nama_pengguna" class="form-control @error('nama_pengguna') is-invalid @enderror" placeholder="Contoh: johndoe" value="{{ old('nama_pengguna', $karyawan->nama_pengguna) }}" required>
            @error('nama_pengguna')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Email -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-envelope text-info me-1"></i>Email
            </label>
            <input type="email" name="surel" class="form-control @error('surel') is-invalid @enderror" placeholder="Contoh: john@example.com" value="{{ old('surel', $karyawan->surel) }}" required>
            @error('surel')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Password -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-lock text-success me-1"></i>Password Baru
            </label>
            <input type="password" name="kata_sandi" class="form-control @error('kata_sandi') is-invalid @enderror" placeholder="Kosongkan jika tidak diubah">
            <small class="text-muted">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password</small>
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
              <option value="" disabled>Pilih peran</option>
              <option value="operator" {{ old('peran', $karyawan->peran) == 'operator' ? 'selected' : '' }}>Operator</option>
              <option value="owner" {{ old('peran', $karyawan->peran) == 'owner' ? 'selected' : '' }}>Owner</option>
            </select>
            @error('peran')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Foto Profil -->
          @php
            $employeeInitialName = old('nama', $karyawan->nama);
            $employeeInitialLetter = $employeeInitialName !== '' ? mb_strtoupper(mb_substr($employeeInitialName, 0, 1)) : 'A';
            $employeePhotoUrl = $karyawan->foto_profil ? asset('foto_profil/' . $karyawan->foto_profil) : '';
          @endphp
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-camera text-secondary me-1"></i>Foto Profil
            </label>
            <div class="employee-photo-card" data-employee-photo data-initial-photo="{{ $employeePhotoUrl }}">
              <div class="employee-avatar-wrapper">
                <img src="{{ $employeePhotoUrl }}" alt="Pratinjau Foto Karyawan" class="employee-avatar {{ $employeePhotoUrl ? '' : 'd-none' }}" data-avatar-preview data-initial-src="{{ $employeePhotoUrl }}">
                <div class="employee-avatar employee-avatar-initial {{ $employeePhotoUrl ? 'd-none' : '' }}" data-avatar-initial>{{ $employeeInitialLetter }}</div>
              </div>
              <div class="d-flex flex-column gap-2 flex-grow-1">
                <div class="employee-photo-actions">
                  <label for="employeePhotoInput" class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-camera me-1"></i> Ganti Foto
                  </label>
                  <button type="button" class="btn btn-sm btn-outline-danger" data-remove-button {{ $employeePhotoUrl ? '' : 'disabled' }}>
                    <i class="fa-solid fa-trash-can me-1"></i> Hapus Foto
                  </button>
                </div>
                <input type="hidden" name="remove_profile_picture" value="0" data-remove-flag>
                <input type="file" id="employeePhotoInput" name="foto_profil" class="form-control d-none @error('foto_profil') is-invalid @enderror" accept="image/*" data-photo-input>
                <div class="employee-helper-text">PNG, JPG atau GIF. Maks 2MB.</div>
                @error('foto_profil')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <!-- Alamat -->
          <div class="col-12">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-map-marker-alt text-danger me-1"></i>Alamat
            </label>
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat', $karyawan->alamat) }}</textarea>
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
          <button type="submit" class="btn btn-warning px-4">
            <i class="fa-solid fa-save me-1"></i>Update
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<div class="employee-crop-modal" id="employeeCropperModal" aria-hidden="true">
    <div class="employee-crop-backdrop" id="employeeCropperBackdrop"></div>
    <div class="employee-crop-dialog" role="dialog" aria-modal="true" aria-labelledby="employeeCropperTitle">
      <div class="employee-crop-header">
        <h5 id="employeeCropperTitle"><i class="fa-solid fa-image me-2 text-primary"></i>Sesuaikan Foto Karyawan</h5>
        <button type="button" class="employee-crop-close" id="employeeCropperClose" aria-label="Tutup">&times;</button>
      </div>
      <div class="employee-crop-body">
        <div class="employee-crop-canvas">
          <img id="employeeCropperImage" alt="Pratinjau Foto Karyawan">
        </div>
      </div>
      <div class="employee-crop-footer">
        <button type="button" class="btn btn-outline-secondary" id="employeeCropperCancel">Batal</button>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-info" id="employeeCropperRotate">
            <i class="fa-solid fa-rotate-right me-2"></i>Putar 90°
          </button>
          <button type="button" class="btn btn-primary" id="employeeCropperConfirm">
            <i class="fa-solid fa-crop me-2"></i>Simpan
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>
  <script src="{{ asset('bolopa/plugin/sweetalert2/sweetalert2.all.min.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const photoSection = document.querySelector('[data-employee-photo]');
      if (!photoSection) {
        return;
      }

      const fileInput = photoSection.querySelector('[data-photo-input]');
      const avatarPreview = photoSection.querySelector('[data-avatar-preview]');
      const avatarInitial = photoSection.querySelector('[data-avatar-initial]');
      const removeButton = photoSection.querySelector('[data-remove-button]');
      const removeField = photoSection.querySelector('[data-remove-flag]');
      const nameInput = document.querySelector('input[name="nama"]');
      const form = photoSection.closest('form');
      const submitBtn = form ? form.querySelector('button[type="submit"]') : null;

      const cropperModal = document.getElementById('employeeCropperModal');
      const cropperBackdrop = document.getElementById('employeeCropperBackdrop');
      const cropperClose = document.getElementById('employeeCropperClose');
      const cropperCancel = document.getElementById('employeeCropperCancel');
      const cropperConfirm = document.getElementById('employeeCropperConfirm');
      const cropperRotate = document.getElementById('employeeCropperRotate');
      const cropperImage = document.getElementById('employeeCropperImage');

      let cropperInstance = null;
      let pendingFile = null;
      let objectUrl = null;

      const initialPhotoSrc = photoSection.dataset.initialPhoto || (avatarPreview ? (avatarPreview.dataset.initialSrc || avatarPreview.getAttribute('src') || '') : '');
      const defaultInitial = avatarInitial ? (avatarInitial.textContent || 'A').trim() || 'A' : 'A';

      if (removeField) {
        removeField.value = '0';
      }

      function computeInitial(name) {
        if (!name || !name.trim()) {
          return defaultInitial || 'A';
        }
        const parts = name.trim().split(/\s+/);
        return (parts[0] ? parts[0][0] : defaultInitial || 'A').toUpperCase();
      }

      function refreshInitial() {
        if (!avatarInitial) {
          return;
        }
        const value = nameInput ? nameInput.value : '';
        avatarInitial.textContent = computeInitial(value);
      }

      function toggleRemoveButton(enable) {
        if (!removeButton) {
          return;
        }
        removeButton.disabled = !enable;
      }

      function showImage(src) {
        if (!avatarPreview || !avatarInitial) {
          return;
        }
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

      function resetFileInput() {
        if (fileInput) {
          fileInput.value = '';
        }
      }

      function openCropper(file) {
        if (!cropperModal || !cropperImage) {
          return;
        }
        pendingFile = file;
        if (objectUrl) {
          URL.revokeObjectURL(objectUrl);
        }
        objectUrl = URL.createObjectURL(file);
        cropperImage.src = objectUrl;
        cropperModal.classList.add('is-active');
        document.body.classList.add('employee-crop-open');
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
        document.body.classList.remove('employee-crop-open');
        if (!options.keepFileInput) {
          resetFileInput();
        }
      }

      function handleFileChange(event) {
        const file = event.target.files[0];
        if (!file) {
          return;
        }
        if (!file.type || !file.type.startsWith('image/')) {
          Swal.fire({
            icon: 'error',
            title: 'File tidak valid',
            text: 'Silakan pilih file gambar yang sesuai.'
          });
          resetFileInput();
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
            dragMode: 'move',
            background: false,
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
            const baseName = pendingFile.name ? pendingFile.name.replace(/\.[^/.]+$/, '') : 'employee-photo';
            const extension = targetType.split('/')[1] || 'png';
            const updatedFile = new File([blob], `${baseName}.${extension}`, { type: targetType });
            const transfer = new DataTransfer();
            transfer.items.add(updatedFile);
            if (fileInput) {
              fileInput.files = transfer.files;
            }
            if (removeField) {
              removeField.value = '0';
            }
            if (avatarPreview) {
              avatarPreview.dataset.initialSrc = '';
            }
            const reader = new FileReader();
            reader.onload = function (event) {
              showImage(event.target.result);
            };
            reader.readAsDataURL(updatedFile);
            closeCropper({ keepFileInput: true });
          }, targetType, 0.95);
        });
      }

      const cancelCropper = function () {
        closeCropper();
      };

      [cropperCancel, cropperClose, cropperBackdrop].forEach(function (trigger) {
        if (trigger) {
          trigger.addEventListener('click', cancelCropper);
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

      if (removeButton && removeField) {
        removeButton.addEventListener('click', function () {
          if (removeButton.disabled) {
            return;
          }
          Swal.fire({
            title: 'Hapus Foto?',
            text: 'Foto profil karyawan akan dihapus dan diganti inisial akun.',
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
            resetFileInput();
            Swal.fire({
              title: 'Berhasil',
              text: 'Foto profil telah dihapus.',
              icon: 'success',
              timer: 1500,
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

      if (form && submitBtn) {
        form.addEventListener('submit', function () {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
          Swal.fire({
            title: 'Menyimpan Data',
            text: 'Mohon tunggu, data karyawan sedang diproses.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: function () {
              Swal.showLoading();
            }
          });
        });
      }

      if (nameInput) {
        nameInput.addEventListener('input', refreshInitial);
      }

      refreshInitial();

      if (avatarPreview) {
        avatarPreview.dataset.initialSrc = initialPhotoSrc;
      }

      if (initialPhotoSrc) {
        showImage(initialPhotoSrc);
      } else {
        showImage('');
      }
    });
  </script>
@endpush