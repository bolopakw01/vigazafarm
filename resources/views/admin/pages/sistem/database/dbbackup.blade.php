@extends('admin.layouts.app')

@section('title', 'Backup Database')

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

    .backup-meta { font-size: 0.9rem; color: #6b7280; }
    .table-backup tbody td { vertical-align: middle; }
    .table-backup .actions { white-space: nowrap; }

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

    .btn-outline-danger {
        background: transparent;
        color: #dc3545;
        border: 1px solid #dc3545;
    }

    .btn-outline-danger:hover {
        background: #dc3545;
        color: white;
    }

    .header-right {
        display: flex;
        align-items: center;
    }
</style>
@endpush

@section('content')
<div class="set-database-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1 class="set-database-title">
                    <i class="fas fa-download"></i>
                    Backup Database
                </h1>
                <p class="set-database-subtitle">Simpan cadangan data sistem secara berkala untuk keamanan dan pemulihan data.</p>
            </div>
            <div class="header-right">
                <button onclick="history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert-box alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-box alert-error">{{ session('error') }}</div>
            @endif

            <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0 0 5px 0; color: #495057;">File Cadangan Tersimpan</h4>
                    <small class="text-muted">Kelola file backup yang telah dibuat sebelumnya</small>
                </div>
                <form method="POST" action="{{ route('admin.sistem.database.backup.run') }}">
                    @csrf
                    <button class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Buat Backup Baru
                    </button>
                </form>
            </div>

            @php
                $formatBytes = function ($bytes) {
                    if ($bytes <= 0) {
                        return '0 B';
                    }
                    $units = ['B','KB','MB','GB','TB'];
                    $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
                    return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
                };
            @endphp

            <div class="table-responsive">
                <table class="table table-backup align-middle">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Ukuran</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $backup['name'] }}</div>
                                    <div class="backup-meta">{{ storage_path('app/db-backups') }}</div>
                                </td>
                                <td>{{ $formatBytes($backup['size']) }}</td>
                                <td>{{ \Carbon\Carbon::createFromTimestamp($backup['created_at'])->format('d M Y H:i') }}</td>
                                <td class="text-end actions">
                                    <a href="{{ route('admin.sistem.database.backup.download', $backup['name']) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-down"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.sistem.database.backup.delete', $backup['name']) }}" class="d-inline" onsubmit="return confirm('Hapus backup ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada file cadangan. Klik "Buat Backup Baru" untuk memulai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

