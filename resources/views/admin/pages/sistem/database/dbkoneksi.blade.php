@extends('admin.layouts.app')

@section('title', 'Koneksi Database')

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

    .form-text { color: #6b7280; }
</style>
@endpush

@section('content')
<div class="set-database-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1 class="set-database-title">
                    <i class="fas fa-database"></i>
                    Koneksi Database
                </h1>
                <p class="set-database-subtitle">Perbarui kredensial koneksi MySQL yang digunakan aplikasi.</p>
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

            <form method="POST" action="{{ route('admin.sistem.database.connection.update') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Host</label>
                    <input type="text" class="form-control @error('host') is-invalid @enderror" name="host" value="{{ old('host', $connection['host']) }}" placeholder="127.0.0.1">
                    @error('host')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Port</label>
                    <input type="number" class="form-control @error('port') is-invalid @enderror" name="port" value="{{ old('port', $connection['port']) }}" placeholder="3306">
                    @error('port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Database</label>
                    <input type="text" class="form-control @error('database') is-invalid @enderror" name="database" value="{{ old('database', $connection['database']) }}" placeholder="vigazafarm">
                    @error('database')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $connection['username']) }}" placeholder="root">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password', $connection['password']) }}" placeholder="********">
                    <div class="form-text">Biarkan kosong jika tidak ingin mengubah password.</div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 d-flex justify-content-between flex-wrap gap-2">
                    <div class="text-muted small">Perubahan ini akan menuliskan ulang nilai pada file <code>.env</code> dan me-reload konfigurasi.</div>
                    <button class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

