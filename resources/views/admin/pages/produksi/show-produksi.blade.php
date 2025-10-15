@extends('admin.layouts.app')

@section('title', 'Detail Produksi - ' . $produksi->batch_produksi_id)

@section('content')
<div class="bolopa-page-container">
    <div class="bolopa-page-header">
        <div class="bolopa-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="bolopa-breadcrumb-separator">/</span>
            <a href="{{ route('admin.produksi') }}">Produksi</a>
            <span class="bolopa-breadcrumb-separator">/</span>
            <span>{{ $produksi->batch_produksi_id }}</span>
        </div>
        <h1 class="bolopa-page-title">
            <img src="{{ asset('bolopa/img/icon/el--eye-open.svg') }}" alt="Detail" class="bolopa-icon-svg" style="width: 32px; height: 32px;">
            Detail Produksi
        </h1>
    </div>

    @if(session('success'))
        <div class="bolopa-alert bolopa-alert-success" style="margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="bolopa-card">
        <div class="bolopa-card-header">
            <h2 style="margin: 0; font-size: 18px;">Informasi Batch: {{ $produksi->batch_produksi_id }}</h2>
            <span class="bolopa-badge bolopa-badge-{{ $produksi->status === 'aktif' ? 'success' : 'secondary' }}">
                {{ ucfirst($produksi->status) }}
            </span>
        </div>
        <div class="bolopa-card-body">
            <div class="bolopa-info-grid">
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Batch ID:</span>
                    <span class="bolopa-info-value">{{ $produksi->batch_produksi_id }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Kandang:</span>
                    <span class="bolopa-info-value">{{ $produksi->kandang->nama_kandang ?? '-' }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Jumlah Indukan:</span>
                    <span class="bolopa-info-value">{{ number_format($produksi->jumlah_indukan) }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Umur Mulai Produksi:</span>
                    <span class="bolopa-info-value">{{ $produksi->umur_mulai_produksi ? $produksi->umur_mulai_produksi . ' hari' : '-' }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Tanggal Mulai:</span>
                    <span class="bolopa-info-value">{{ \Carbon\Carbon::parse($produksi->tanggal_mulai)->format('d/m/Y') }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Tanggal Akhir:</span>
                    <span class="bolopa-info-value">{{ $produksi->tanggal_akhir ? \Carbon\Carbon::parse($produksi->tanggal_akhir)->format('d/m/Y') : 'Belum ditentukan' }}</span>
                </div>
                
                @if($produksi->tanggal_akhir)
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Durasi Produksi:</span>
                    <span class="bolopa-info-value">
                        {{ \Carbon\Carbon::parse($produksi->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($produksi->tanggal_akhir)) }} hari
                    </span>
                </div>
                @endif
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Status:</span>
                    <span class="bolopa-badge bolopa-badge-{{ $produksi->status === 'aktif' ? 'success' : 'secondary' }}">
                        {{ ucfirst($produksi->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Origin Information (if transferred) -->
    @if($produksi->pembesaran_id || $produksi->penetasan_id)
    <div class="bolopa-card">
        <div class="bolopa-card-header">
            <h2 style="margin: 0; font-size: 18px;">
                <img src="{{ asset('bolopa/img/icon/line-md--upload-outline.svg') }}" alt="Transfer" class="bolopa-icon-svg" style="width: 20px; height: 20px;">
                Informasi Transfer
            </h2>
        </div>
        <div class="bolopa-card-body">
            @if($produksi->pembesaran_id)
                <div class="bolopa-info-box">
                    <h3 style="margin: 0 0 12px 0; font-size: 16px; color: #374151;">Ditransfer dari Pembesaran</h3>
                    <div class="bolopa-info-grid">
                        <div class="bolopa-info-item">
                            <span class="bolopa-info-label">Batch Pembesaran:</span>
                            <span class="bolopa-info-value">{{ $produksi->pembesaran->batch_produksi_id ?? '-' }}</span>
                        </div>
                        <div class="bolopa-info-item">
                            <span class="bolopa-info-label">Kandang Asal:</span>
                            <span class="bolopa-info-value">{{ $produksi->pembesaran->kandang->nama_kandang ?? '-' }}</span>
                        </div>
                        <div class="bolopa-info-item">
                            <span class="bolopa-info-label">Tanggal Mulai Pembesaran:</span>
                            <span class="bolopa-info-value">
                                {{ $produksi->pembesaran ? \Carbon\Carbon::parse($produksi->pembesaran->tanggal_masuk)->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            @if($produksi->penetasan_id)
                <div class="bolopa-info-box" style="margin-top: {{ $produksi->pembesaran_id ? '16px' : '0' }};">
                    <h3 style="margin: 0 0 12px 0; font-size: 16px; color: #374151;">Telur Infertil dari Penetasan</h3>
                    <div class="bolopa-info-grid">
                        <div class="bolopa-info-item">
                            <span class="bolopa-info-label">Batch Penetasan:</span>
                            <span class="bolopa-info-value">{{ $produksi->penetasan->batch ?? '-' }}</span>
                        </div>
                        <div class="bolopa-info-item">
                            <span class="bolopa-info-label">Kandang Asal:</span>
                            <span class="bolopa-info-value">{{ $produksi->penetasan->kandang->nama_kandang ?? '-' }}</span>
                        </div>
                        <div class="bolopa-info-item">
                            <span class="bolopa-info-label">Tanggal Penetasan:</span>
                            <span class="bolopa-info-value">
                                {{ $produksi->penetasan ? \Carbon\Carbon::parse($produksi->penetasan->tanggal_menetas)->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Egg Sales Information (if from penetasan) -->
    @if($produksi->penetasan_id && $produksi->jumlah_telur)
    <div class="bolopa-card">
        <div class="bolopa-card-header">
            <h2 style="margin: 0; font-size: 18px;">
                <img src="{{ asset('bolopa/img/icon/streamline-ultimate--animal-products-egg-bold.svg') }}" alt="Telur" class="bolopa-icon-svg" style="width: 20px; height: 20px;">
                Informasi Penjualan Telur
            </h2>
        </div>
        <div class="bolopa-card-body">
            <div class="bolopa-info-grid">
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Jumlah Telur:</span>
                    <span class="bolopa-info-value">{{ number_format($produksi->jumlah_telur) }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Berat Rata-rata per Telur:</span>
                    <span class="bolopa-info-value">{{ $produksi->berat_rata_telur ? number_format($produksi->berat_rata_telur, 1) . ' gram' : '-' }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Total Berat:</span>
                    <span class="bolopa-info-value">
                        {{ $produksi->berat_rata_telur ? number_format(($produksi->jumlah_telur * $produksi->berat_rata_telur) / 1000, 2) . ' kg' : '-' }}
                    </span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Harga per Kg:</span>
                    <span class="bolopa-info-value">{{ $produksi->harga_per_kg ? 'Rp ' . number_format($produksi->harga_per_kg) : '-' }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Total Pendapatan:</span>
                    <span class="bolopa-info-value" style="color: #10b981; font-weight: 600;">
                        @if($produksi->harga_per_kg && $produksi->berat_rata_telur)
                            Rp {{ number_format((($produksi->jumlah_telur * $produksi->berat_rata_telur) / 1000) * $produksi->harga_per_kg) }}
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($produksi->catatan)
    <div class="bolopa-card">
        <div class="bolopa-card-header">
            <h2 style="margin: 0; font-size: 18px;">Catatan</h2>
        </div>
        <div class="bolopa-card-body">
            <p style="margin: 0; color: #374151; line-height: 1.6;">{{ $produksi->catatan }}</p>
        </div>
    </div>
    @endif

    <!-- Metadata -->
    <div class="bolopa-card">
        <div class="bolopa-card-header">
            <h2 style="margin: 0; font-size: 18px;">Metadata</h2>
        </div>
        <div class="bolopa-card-body">
            <div class="bolopa-info-grid">
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Dibuat Pada:</span>
                    <span class="bolopa-info-value">{{ \Carbon\Carbon::parse($produksi->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="bolopa-info-item">
                    <span class="bolopa-info-label">Terakhir Diperbarui:</span>
                    <span class="bolopa-info-value">{{ \Carbon\Carbon::parse($produksi->updated_at)->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bolopa-actions-container">
        <a href="{{ route('admin.produksi.edit', $produksi->id) }}" class="bolopa-btn bolopa-btn-primary">
            <img src="{{ asset('bolopa/img/icon/line-md--edit-twotone.svg') }}" alt="Edit" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
            Edit Data
        </a>
        
        <form action="{{ route('admin.produksi.destroy', $produksi->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data produksi ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bolopa-btn bolopa-btn-danger">
                <img src="{{ asset('bolopa/img/icon/line-md--trash.svg') }}" alt="Hapus" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
                Hapus Data
            </button>
        </form>
        
        <a href="{{ route('admin.produksi') }}" class="bolopa-btn bolopa-btn-secondary">
            <img src="{{ asset('bolopa/img/icon/line-md--chevron-small-left.svg') }}" alt="Kembali" class="bolopa-icon-svg" style="width: 16px; height: 16px;">
            Kembali ke Daftar
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

.bolopa-info-box {
    padding: 16px;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.bolopa-actions-container {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    padding-top: 8px;
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

.bolopa-alert {
    padding: 16px;
    border-radius: 8px;
    font-size: 14px;
}

.bolopa-alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.bolopa-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bolopa-btn-danger {
    background-color: #ef4444;
    color: white;
    padding: 10px 16px;
    border-radius: 6px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.2s;
}

.bolopa-btn-danger:hover {
    background-color: #dc2626;
}
</style>
@endsection
