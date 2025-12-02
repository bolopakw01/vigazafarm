@extends('admin.layouts.app')

@section('title', 'Informasi Database')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Sistem', 'link' => route('admin.sistem')],
        ['label' => 'Database'],
        ['label' => 'Informasi Database'],
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

    .header-right {
        display: flex;
        align-items: center;
    }

    .info-item {
        margin-bottom: 20px;
    }

    .info-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .info-value {
        padding: 10px 15px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 0.95rem;
        color: #1f2937;
        word-break: break-all;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .form-text { color: #6b7280; }
</style>
@endpush

@section('content')
<div class="set-database-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1 class="set-database-title">
                    <i class="fas fa-info-circle"></i>
                    Informasi Database
                </h1>
                <p class="set-database-subtitle">Lihat informasi lengkap tentang konfigurasi dan status database saat ini.</p>
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

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Host</label>
                        <div class="info-value">{{ $connection['host'] }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Port</label>
                        <div class="info-value">{{ $connection['port'] }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Nama Database</label>
                        <div class="info-value">{{ $connection['database'] }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Username</label>
                        <div class="info-value">{{ $connection['username'] }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Status Koneksi</label>
                        <div class="info-value">
                            <span class="status-badge status-success">
                                <i class="fas fa-check-circle"></i> Terhubung
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Versi MySQL</label>
                        <div class="info-value">{{ $mysql_version ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="mb-2"><i class="fas fa-info-circle text-info"></i> Catatan</h6>
                <p class="text-muted small mb-0">
                    Informasi koneksi database ini bersifat read-only. Untuk mengubah konfigurasi database,
                    silakan edit file <code>.env</code> secara manual atau hubungi administrator sistem.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

