@extends('admin.layouts.app')

@section('title', 'Transfer Penetasan - ' . $penetasan->batch)

@section('content')
<div class="bolopa-page-container">
    <div class="bolopa-page-header">
        <div class="bolopa-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="bolopa-breadcrumb-separator">/</span>
            <a href="{{ route('admin.penetasan') }}">Penetasan</a>
            <span class="bolopa-breadcrumb-separator">/</span>
            <span>Transfer: {{ $penetasan->batch }}</span>
        </div>
        <h1 class="bolopa-page-title">
            <img src="{{ asset('bolopa/img/icon/streamline-ultimate--animal-products-egg-bold.svg') }}" alt="Transfer" class="bolopa-icon-svg" style="width: 32px; height: 32px;">
            Transfer Hasil Penetasan
        </h1>
    </div>

    <div class="bolopa-card">
        <div class="bolopa-card-header">
            <h2>Informasi Batch: {{ $penetasan->batch }}</h2>
        </div>
        <div class="bolopa-card-body">
            <div class="bolopa-info-grid">
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Kandang:</span>
                    <span class="bolopa-info-value">{{ $penetasan->kandang->nama_kandang ?? '-' }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Tanggal Mulai:</span>
                    <span class="bolopa-info-value">{{ \Carbon\Carbon::parse($penetasan->tanggal_mulai)->format('d/m/Y') }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Jumlah Telur:</span>
                    <span class="bolopa-info-value">{{ number_format($penetasan->jumlah_telur) }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">DOC Menetas:</span>
                    <span class="bolopa-info-value">{{ number_format($penetasan->jumlah_doc) }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">DOC Tersedia:</span>
                    <span class="bolopa-info-value" style="color: #10b981; font-weight: 600;">{{ number_format($penetasan->doc_tersedia) }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Telur Infertil:</span>
                    <span class="bolopa-info-value">{{ number_format($penetasan->telur_tidak_fertil) }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Telur Infertil Tersedia:</span>
                    <span class="bolopa-info-value" style="color: #10b981; font-weight: 600;">{{ number_format($penetasan->telur_infertil_tersedia) }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Status:</span>
                    <span class="bolopa-badge bolopa-badge-{{ $penetasan->status_batch === 'aktif' ? 'success' : 'secondary' }}">
                        {{ ucfirst($penetasan->status_batch) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bolopa-alert bolopa-alert-success" style="margin-top: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bolopa-alert bolopa-alert-error" style="margin-top: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="bolopa-transfer-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
        <!-- Transfer DOC ke Pembesaran -->
        <div class="bolopa-card">
            <div class="bolopa-card-header">
                <h3 style="margin: 0; font-size: 18px;">Transfer DOC ke Pembesaran</h3>
            </div>
            <div class="bolopa-card-body">
                @if($penetasan->doc_tersedia > 0)
                    <form action="{{ route('admin.penetasan.transfer.doc', $penetasan->id) }}" method="POST" class="bolopa-form">
                        @csrf
                        
                        <div class="bolopa-form-group">
                            <label for="kandang_pembesaran" class="bolopa-form-label">
                                Kandang Pembesaran <span style="color: #ef4444;">*</span>
                            </label>
                            <select name="kandang_id" id="kandang_pembesaran" class="bolopa-form-input" required>
                                <option value="">Pilih Kandang</option>
                                @foreach($kandangList as $kandang)
                                    <option value="{{ $kandang->id }}" {{ old('kandang_id') == $kandang->id ? 'selected' : '' }}>
                                        {{ $kandang->nama_kandang }} (Kapasitas: {{ number_format($kandang->kapasitas_maksimal) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('kandang_id')
                                <span class="bolopa-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bolopa-form-group">
                            <label for="jumlah_doc" class="bolopa-form-label">
                                Jumlah DOC yang Ditransfer <span style="color: #ef4444;">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="jumlah_doc" 
                                id="jumlah_doc" 
                                class="bolopa-form-input" 
                                min="1" 
                                max="{{ $penetasan->doc_tersedia }}"
                                value="{{ old('jumlah_doc', $penetasan->doc_tersedia) }}"
                                required
                            >
                            <small style="color: #6b7280; font-size: 13px;">
                                Maksimal: {{ number_format($penetasan->doc_tersedia) }} DOC
                            </small>
                            @error('jumlah_doc')
                                <span class="bolopa-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bolopa-form-group">
                            <label for="tanggal_transfer_doc" class="bolopa-form-label">
                                Tanggal Transfer <span style="color: #ef4444;">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_mulai" 
                                id="tanggal_transfer_doc" 
                                class="bolopa-form-input" 
                                value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                                required
                            >
                            @error('tanggal_mulai')
                                <span class="bolopa-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bolopa-form-actions">
                            <button type="submit" class="bolopa-btn bolopa-btn-primary">
                                <img src="{{ asset('bolopa/img/icon/line-md--upload-outline.svg') }}" alt="Transfer" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
                                Transfer DOC
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bolopa-alert bolopa-alert-warning">
                        <strong>Tidak ada DOC tersedia untuk ditransfer.</strong><br>
                        Semua DOC dari batch ini sudah ditransfer ke Pembesaran.
                    </div>
                @endif
            </div>
        </div>

        <!-- Transfer Telur Infertil ke Produksi -->
        <div class="bolopa-card">
            <div class="bolopa-card-header">
                <h3 style="margin: 0; font-size: 18px;">Catat Penjualan Telur Infertil</h3>
            </div>
            <div class="bolopa-card-body">
                @if($penetasan->telur_infertil_tersedia > 0)
                    <form action="{{ route('admin.penetasan.transfer.telur', $penetasan->id) }}" method="POST" class="bolopa-form">
                        @csrf
                        
                        <div class="bolopa-form-group">
                            <label for="jumlah_telur_infertil" class="bolopa-form-label">
                                Jumlah Telur yang Dijual <span style="color: #ef4444;">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="jumlah_telur" 
                                id="jumlah_telur_infertil" 
                                class="bolopa-form-input" 
                                min="1" 
                                max="{{ $penetasan->telur_infertil_tersedia }}"
                                value="{{ old('jumlah_telur', $penetasan->telur_infertil_tersedia) }}"
                                required
                            >
                            <small style="color: #6b7280; font-size: 13px;">
                                Maksimal: {{ number_format($penetasan->telur_infertil_tersedia) }} telur
                            </small>
                            @error('jumlah_telur')
                                <span class="bolopa-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bolopa-form-group">
                            <label for="berat_rata_telur" class="bolopa-form-label">
                                Berat Rata-rata per Telur (gram) <span style="color: #ef4444;">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="berat_rata_telur" 
                                id="berat_rata_telur" 
                                class="bolopa-form-input" 
                                min="1" 
                                step="0.1"
                                value="{{ old('berat_rata_telur', 60) }}"
                                required
                            >
                            @error('berat_rata_telur')
                                <span class="bolopa-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bolopa-form-group">
                            <label for="harga_per_kg" class="bolopa-form-label">
                                Harga per Kg (Rp) <span style="color: #ef4444;">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="harga_per_kg" 
                                id="harga_per_kg" 
                                class="bolopa-form-input" 
                                min="1" 
                                step="1"
                                value="{{ old('harga_per_kg', 25000) }}"
                                required
                            >
                            @error('harga_per_kg')
                                <span class="bolopa-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bolopa-form-group">
                            <label for="tanggal_jual" class="bolopa-form-label">
                                Tanggal Penjualan <span style="color: #ef4444;">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="tanggal" 
                                id="tanggal_jual" 
                                class="bolopa-form-input" 
                                value="{{ old('tanggal', date('Y-m-d')) }}"
                                required
                            >
                            @error('tanggal')
                                <span class="bolopa-form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bolopa-form-actions">
                            <button type="submit" class="bolopa-btn bolopa-btn-success">
                                <img src="{{ asset('bolopa/img/icon/line-md--confirm.svg') }}" alt="Catat" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
                                Catat Penjualan
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bolopa-alert bolopa-alert-warning">
                        <strong>Tidak ada telur infertil tersedia.</strong><br>
                        Semua telur infertil dari batch ini sudah dicatat.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div style="margin-top: 24px;">
        <a href="{{ route('admin.penetasan') }}" class="bolopa-btn bolopa-btn-secondary">
            <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Kembali" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
            Kembali ke Daftar Penetasan
        </a>
    </div>
</div>

<style>
.bolopa-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.bolopa-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.bolopa-info-label {
    font-size: 13px;
    color: #6b7280;
    font-weight: 500;
}

.bolopa-info-value {
    font-size: 15px;
    color: #111827;
    font-weight: 500;
}

.bolopa-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.bolopa-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.bolopa-form-label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

.bolopa-form-input {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.2s;
}

.bolopa-form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.bolopa-form-error {
    font-size: 13px;
    color: #ef4444;
    margin-top: 4px;
}

.bolopa-form-actions {
    margin-top: 8px;
}

.bolopa-alert {
    padding: 16px;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.5;
}

.bolopa-alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.bolopa-alert-error {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.bolopa-alert-warning {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
}

.bolopa-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
}

.bolopa-badge-success {
    background-color: #d1fae5;
    color: #065f46;
}

.bolopa-badge-secondary {
    background-color: #e5e7eb;
    color: #374151;
}

@media (max-width: 768px) {
    .bolopa-transfer-container {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
