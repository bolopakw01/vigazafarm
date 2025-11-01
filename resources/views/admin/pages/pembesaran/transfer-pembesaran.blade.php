@extends('admin.layouts.app')

@section('title', 'Transfer Pembesaran - ' . $pembesaran->batch_produksi_id)

@section('content')
<div class="bolopa-page-container">
    <div class="bolopa-page-header">
        <div class="bolopa-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="bolopa-breadcrumb-separator">/</span>
            <a href="{{ route('admin.pembesaran') }}">Pembesaran</a>
            <span class="bolopa-breadcrumb-separator">/</span>
            <span>Transfer: {{ $pembesaran->batch_produksi_id }}</span>
        </div>
        <h1 class="bolopa-page-title">
            <img src="{{ asset('bolopa/img/icon/streamline-ultimate--animal-products-egg-bold.svg') }}" alt="Transfer" class="bolopa-icon-svg" style="width: 32px; height: 32px;">
            Transfer Indukan ke Produksi
        </h1>
    </div>

    <div class="bolopa-card">
        <div class="bolopa-card-header">
            <h2>Informasi Batch: {{ $pembesaran->batch_produksi_id }}</h2>
        </div>
        <div class="bolopa-card-body">
            <div class="bolopa-info-grid">
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Kandang:</span>
                    <span class="bolopa-info-value">{{ $pembesaran->kandang->nama_kandang ?? '-' }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Tanggal Mulai:</span>
                    <span class="bolopa-info-value">{{ \Carbon\Carbon::parse($pembesaran->tanggal_mulai)->format('d/m/Y') }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Jumlah Awal:</span>
                    <span class="bolopa-info-value">{{ number_format($pembesaran->jumlah_awal) }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Jumlah Siap Produksi:</span>
                    <span class="bolopa-info-value">{{ number_format($pembesaran->jumlah_siap) }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Indukan Tersedia:</span>
                    <span class="bolopa-info-value" style="color: #10b981; font-weight: 600;">{{ number_format($pembesaran->indukan_tersedia) }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Umur (hari):</span>
                    <span class="bolopa-info-value">{{ $pembesaran->umur_hari ?? '-' }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Berat Rata-rata (gram):</span>
                    <span class="bolopa-info-value">{{ $pembesaran->berat_rata ? number_format($pembesaran->berat_rata, 2) : '-' }}</span>
                </div>
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Status:</span>
                    <span class="bolopa-badge bolopa-badge-{{ $pembesaran->status_batch === 'aktif' ? 'success' : 'secondary' }}">
                        {{ ucfirst($pembesaran->status_batch) }}
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

    <div class="bolopa-card" style="margin-top: 24px;">
        <div class="bolopa-card-header">
            <h3 style="margin: 0; font-size: 18px;">Transfer Indukan ke Produksi</h3>
            <p style="margin: 8px 0 0 0; font-size: 14px; color: #6b7280;">
                Burung yang sudah siap dipindahkan ke kandang produksi untuk menghasilkan telur
            </p>
        </div>
        <div class="bolopa-card-body">
            @if($pembesaran->indukan_tersedia > 0)
                <form action="{{ route('admin.pembesaran.transfer.indukan', $pembesaran->id) }}" method="POST" class="bolopa-form" style="max-width: 600px;">
                    @csrf
                    
                    <div class="bolopa-form-group">
                        <label for="kandang_produksi" class="bolopa-form-label">
                            Kandang Produksi <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="kandang_id" id="kandang_produksi" class="bolopa-form-input" required>
                            <option value="">Pilih Kandang</option>
                            @foreach($kandangList as $kandang)
                                <option value="{{ $kandang->id }}" {{ old('kandang_id') == $kandang->id ? 'selected' : '' }}>
                                    {{ $kandang->nama_dengan_detail }}
                                </option>
                            @endforeach
                        </select>
                        @error('kandang_id')
                            <span class="bolopa-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bolopa-form-group">
                        <label for="jumlah_indukan" class="bolopa-form-label">
                            Jumlah Indukan yang Ditransfer <span style="color: #ef4444;">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="jumlah_indukan" 
                            id="jumlah_indukan" 
                            class="bolopa-form-input" 
                            min="1" 
                            max="{{ $pembesaran->indukan_tersedia }}"
                            value="{{ old('jumlah_indukan', $pembesaran->indukan_tersedia) }}"
                            required
                        >
                        <small style="color: #6b7280; font-size: 13px;">
                            Maksimal: {{ number_format($pembesaran->indukan_tersedia) }} indukan
                        </small>
                        @error('jumlah_indukan')
                            <span class="bolopa-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bolopa-form-group">
                        <label for="umur_mulai_produksi" class="bolopa-form-label">
                            Umur Mulai Produksi (hari) <span style="color: #ef4444;">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="umur_mulai_produksi" 
                            id="umur_mulai_produksi" 
                            class="bolopa-form-input" 
                            min="1"
                            value="{{ old('umur_mulai_produksi', $pembesaran->umur_hari ?? 120) }}"
                            required
                        >
                        <small style="color: #6b7280; font-size: 13px;">
                            Umur burung saat mulai berproduksi (biasanya 120-150 hari)
                        </small>
                        @error('umur_mulai_produksi')
                            <span class="bolopa-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bolopa-form-group">
                        <label for="tanggal_mulai" class="bolopa-form-label">
                            Tanggal Mulai Produksi <span style="color: #ef4444;">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_mulai" 
                            id="tanggal_mulai" 
                            class="bolopa-form-input" 
                            value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                            required
                        >
                        @error('tanggal_mulai')
                            <span class="bolopa-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bolopa-form-group">
                        <label for="tanggal_akhir" class="bolopa-form-label">
                            Estimasi Tanggal Akhir Produksi
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_akhir" 
                            id="tanggal_akhir" 
                            class="bolopa-form-input" 
                            value="{{ old('tanggal_akhir') }}"
                        >
                        <small style="color: #6b7280; font-size: 13px;">
                            Opsional. Estimasi berapa lama batch ini akan berproduksi.
                        </small>
                        @error('tanggal_akhir')
                            <span class="bolopa-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bolopa-form-actions" style="display: flex; gap: 12px;">
                        <button type="submit" class="bolopa-btn bolopa-btn-primary">
                            <img src="{{ asset('bolopa/img/icon/line-md--upload-outline.svg') }}" alt="Transfer" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
                            Transfer ke Produksi
                        </button>
                        <a href="{{ route('admin.pembesaran') }}" class="bolopa-btn bolopa-btn-secondary">
                            <img src="{{ asset('bolopa/img/icon/line-md--close.svg') }}" alt="Batal" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
                            Batal
                        </a>
                    </div>
                </form>
            @else
                <div class="bolopa-alert bolopa-alert-warning">
                    <strong>Tidak ada indukan tersedia untuk ditransfer.</strong><br>
                    Semua burung dari batch ini sudah ditransfer ke Produksi atau belum mencapai jumlah siap produksi.
                </div>
                <div style="margin-top: 16px;">
                    <a href="{{ route('admin.pembesaran') }}" class="bolopa-btn bolopa-btn-secondary">
                        <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Kembali" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
                        Kembali ke Daftar Pembesaran
                    </a>
                </div>
            @endif
        </div>
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
</style>
@endsection
