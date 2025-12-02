@extends('admin.layouts.app')

@section('title', 'Restore Database')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Sistem', 'link' => route('admin.sistem')],
        ['label' => 'Database'],
        ['label' => 'Restore Database'],
    ];
@endphp

@push('styles')
<style>
    @font-face {
        font-family: 'AlanSans';
        src: url('{{ asset("bolopa/font/AlanSans-VariableFont_wght.ttf") }}') format('truetype');
        font-weight: 100 900;
        font-display: swap;
    }

    .set-database-wrapper {
        padding: 20px;
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 25px 30px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .card-body {
        padding: 30px;
    }

    .set-database-title {
        font-size: 1.75rem;
        font-weight: 600;
        font-family: 'AlanSans', sans-serif;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .set-database-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
        margin: 0;
    }

    .alert-box {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    .alert-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .alert-warning {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fed7aa;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        margin-right: 10px;
    }

    .btn-primary {
        background: #2563eb;
        color: white;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-outline-primary {
        background: transparent;
        color: #2563eb;
        border: 1px solid #2563eb;
    }

    .btn-outline-primary:hover {
        background: #2563eb;
        color: white;
    }

    .header-right {
        display: flex;
        align-items: center;
    }

    .card-subtitle { font-size: 0.95rem; color: #6b7280; }
</style>
@endpush

@section('content')
<div class="set-database-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1 class="set-database-title">
                    <i class="fas fa-cloud-upload-alt"></i>
                    Restore Database
                </h1>
                <p class="set-database-subtitle">Kembalikan data sistem dari file cadangan yang tersimpan atau file eksternal.</p>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.sistem') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert-box alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->has('restore'))
                <div class="alert-box alert-error">{{ $errors->first('restore') }}</div>
            @endif

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card h-100" style="box-shadow: none; border: 1px solid #e9ecef;">
                        <div class="card-header" style="background: #f8f9fa; padding: 20px 25px;">
                            <h5 class="card-title mb-1" style="font-size: 1.25rem; font-weight: 600;">Restore dari Backup</h5>
                            <div class="card-subtitle">Pilih file yang pernah dibuat untuk mengembalikan data</div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.sistem.database.restore.run') }}" class="restore-confirm-form" data-confirm-message="Proses restore akan menimpa seluruh data dari backup yang dipilih. Lanjutkan?" data-confirm-icon="warning">
                                @csrf
                                <input type="hidden" name="source" value="existing">
                                <div class="mb-3">
                                    <label class="form-label">Pilih File Backup</label>
                                    <select name="filename" class="form-select" @if(empty($backups)) disabled @endif>
                                        <option value="">-- pilih backup --</option>
                                        @foreach($backups as $backup)
                                            <option value="{{ $backup['name'] }}" @selected(old('filename') === $backup['name'])>
                                                {{ $backup['name'] }} ({{ \Carbon\Carbon::createFromTimestamp($backup['created_at'])->format('d M Y H:i') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('filename')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button class="btn btn-primary" @if(empty($backups)) disabled @endif>
                                    <i class="fas fa-cloud-upload-alt me-2"></i>Restore dari Backup
                                </button>
                                @if(empty($backups))
                                    <div class="text-muted small mt-2">Belum ada file backup yang tersedia.</div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card h-100" style="box-shadow: none; border: 1px solid #e9ecef;">
                        <div class="card-header" style="background: #f8f9fa; padding: 20px 25px;">
                            <h5 class="card-title mb-1" style="font-size: 1.25rem; font-weight: 600;">Restore dari File</h5>
                            <div class="card-subtitle">Unggah file backup eksternal untuk direstore</div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.sistem.database.restore.run') }}" enctype="multipart/form-data" class="restore-confirm-form" data-confirm-message="Pastikan file backup valid. Proses restore akan menimpa data. Lanjutkan?" data-confirm-icon="info">
                                @csrf
                                <input type="hidden" name="source" value="upload">
                                <div class="mb-3">
                                    <label class="form-label">File Backup (.json)</label>
                                    <input type="file" name="backup_file" accept=".json,application/json" class="form-control">
                                    @error('backup_file')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-file-import me-2"></i>Upload & Restore
                                </button>
                            </form>
                            <div class="alert alert-warning mt-3 mb-0">
                                <strong>Peringatan:</strong> Proses restore akan menimpa seluruh isi database. Pastikan Anda sudah melakukan backup terbaru sebelum melanjutkan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

    @push('scripts')
    <script src="{{ asset('bolopa/plugin/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.restore-confirm-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton && submitButton.disabled) {
                    return;
                }

                event.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi Restore',
                    text: form.dataset.confirmMessage || 'Proses restore akan menimpa seluruh data. Lanjutkan?',
                    icon: form.dataset.confirmIcon || 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
    </script>
    @endpush

