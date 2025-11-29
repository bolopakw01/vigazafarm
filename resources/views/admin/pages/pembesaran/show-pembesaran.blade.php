@extends('admin.layouts.app')

@section('title', 'Detail Pembesaran Puyuh - ' . $pembesaran->batch_produksi_id)

@push('styles')
{{-- Custom CSS for this page only (scoped to prevent sidebar conflicts) --}}
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-pembesaran.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-part-pembesaran.css') }}?v={{ time() }}">
{{-- ApexCharts for graphs --}}
<script src="{{ asset('bolopa/plugin/apexcharts/apexcharts.min.js') }}"></script>

<style>
/* Mobile Responsiveness Improvements */
@media (max-width: 768px) {
    .pembesaran-detail-wrapper {
        padding: 1rem 0.5rem;
    }

    .bolopa-page-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
    }

    .bolopa-header-content {
        width: 100%;
        text-align: left;
    }

    .bolopa-header-action {
        width: 100%;
        text-align: center;
    }

    .bolopa-header-action .btn {
        width: 100%;
        max-width: 200px;
    }

    /* KAI Cards - Stack vertically on mobile */
    .bolopa-kai-cards {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .bolopa-card-kai {
        width: 100% !important;
        min-height: 120px !important; /* Updated to match new compact size */
        padding: 1.25rem 1rem;
    }

    .bolopa-kai-content {
        margin-bottom: 0.75rem;
    }

    .bolopa-kai-value {
        font-size: 1.75rem !important;
        margin-bottom: 0.25rem;
    }

    .bolopa-kai-more {
        position: static !important;
        margin-top: 0.5rem;
        width: 100%;
        text-align: center;
        padding: 0.5rem;
        border-radius: 0.375rem;
    }
}

@media (max-width: 576px) {
    .pembesaran-detail-wrapper {
        padding: 0.75rem 0.25rem;
    }

    .bolopa-page-header {
        padding: 1rem;
        margin-bottom: 1rem !important;
    }

    .bolopa-logo-icon {
        margin-bottom: 0.5rem;
    }

    .bolopa-page-title {
        font-size: 1.25rem !important;
    }

    .bolopa-page-subtitle {
        font-size: 0.875rem !important;
    }

    .bolopa-kai-cards {
        gap: 0.75rem;
    }

    .bolopa-card-kai {
        padding: 1rem 0.75rem;
    }

    .bolopa-kai-value {
        font-size: 1.5rem !important;
    }

    .bolopa-kai-label {
        font-size: 0.875rem !important;
    }
}

/* Hide KAI menu on mobile devices */
@media (max-width: 768px) {
    .bolopa-kai-cards {
        display: none !important;
    }
}

/* Force KAI card compact size - highest specificity */
.pembesaran-detail-wrapper .bolopa-kai-cards .bolopa-card-kai {
    min-height: 120px !important;
    height: 120px !important;
}

@media (max-width: 576px) {
    .pembesaran-detail-wrapper .bolopa-kai-cards .bolopa-card-kai {
        min-height: 80px !important;
        height: 80px !important;
    }
}

/* Toast notification - reuse generic styling to stay consistent */
.bolopa-tabel-toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 12px 20px;
    background: #1f2937;
    color: #fff;
    border-radius: 0.75rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.25s ease;
    z-index: 1200;
    display: none;
}

.bolopa-tabel-toast.bolopa-tabel-show {
    opacity: 1;
    transform: translateY(0);
}
</style>
@endpush

@section('content')
{{-- Helper untuk format mortalitas --}}
@php
    $mortalitasFormatted = $mortalitas == floor($mortalitas) 
        ? number_format($mortalitas, 0) 
        : rtrim(rtrim(number_format($mortalitas, 2), '0'), '.');
    $batchStartDate = optional($pembesaran->tanggal_masuk)->format('Y-m-d');
@endphp

<div class="pembesaran-detail-wrapper">
<div class="container-fluid py-4">
    
    {{-- Header (tanpa card background) --}}
    <div class="bolopa-page-header mb-3">
        <div class="bolopa-logo-icon">
            <i class="fa-solid fa-dove"></i>
        </div>
        <div class="bolopa-header-content">
            <h5 class="bolopa-page-title">Detail Pembesaran Puyuh</h5>
            <div class="bolopa-page-subtitle">
                <span class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em;">DOQ Puyuh → Puyuh dewasa siap produksi</span><br>
                Batch: <a href="#">{{ $pembesaran->batch_produksi_id }}</a> &nbsp;|&nbsp;
                Kandang: <strong>{{ $pembesaran->kandang->nama_kandang ?? '-' }}</strong> &nbsp;|&nbsp;
                Umur: <strong>{{ (int)$umurHari }} hari</strong>
            </div>
        </div>
        <div class="bolopa-header-action">
            <a href="{{ route('admin.pembesaran') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    {{-- Menu KAI: empat kartu untuk manajemen pembesaran puyuh --}}
    <div class="bolopa-kai-cards mb-4">
        {{-- Populasi --}}
        <div class="bolopa-card-kai bolopa-kai-teal">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value">{{ number_format($populasiSaatIni) }}</div>
                <div class="bolopa-kai-label">Populasi (awal {{ number_format($populasiAwal) }})</div>
            </div>
            <i class="fa-solid fa-egg bolopa-icon-faint"></i>
        </div>

        {{-- Mortalitas --}}
        <div class="bolopa-card-kai bolopa-kai-red">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value">{{ $mortalitasFormatted }}<small style="font-size:0.45em;">%</small></div>
                <div class="bolopa-kai-label">Mortalitas ({{ number_format($totalMati) }} ekor)</div>
            </div>
            <i class="fa-solid fa-skull-crossbones bolopa-icon-faint"></i>
        </div>

        {{-- Berat Rata-rata --}}
        <div class="bolopa-card-kai bolopa-kai-green">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value">{{ $pembesaran->berat_rata_rata ? number_format($pembesaran->berat_rata_rata, 0) : 0 }}g</div>
                <div class="bolopa-kai-label">Berat rata-rata</div>
            </div>
            <i class="fa-solid fa-scale-balanced bolopa-icon-faint"></i>
        </div>

        {{-- Total Biaya --}}
        <div class="bolopa-card-kai bolopa-kai-indigo">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <span style="font-size: 0.35em; color: rgba(255, 255, 255, 0.7); margin-top: 0.2em;">Rp</span>
                    <span id="kai-total-biaya-pakan" data-value="{{ $totalBiayaPakan }}">{{ number_format($totalBiayaPakan, 0, ',', '.') }}</span>
                </div>
                <div class="bolopa-kai-label">Total Biaya Pakan</div>
            </div>
            <i class="fa-solid fa-coins bolopa-icon-faint"></i>
        </div>
    </div>

    {{-- Notebook Container with Tabs --}}
    @include('admin.pages.pembesaran.partials._tab-show-pembesaran')

    <div class="bolopa-tabel-toast" id="pembesaran-toast" role="status" aria-live="polite"></div>

</div>
</div>
@endsection

@push('scripts')
{{-- Pass data to JavaScript --}}
<script>
    // Global config for AJAX endpoints
    window.vigazaConfig = {
        baseUrl: '{{ url('/') }}',
        pembesaranId: {{ $pembesaran->id }},
        csrfToken: '{{ csrf_token() }}',
        batchStartDate: '{{ $batchStartDate ?? '' }}',
        populasi_awal: {{ (int) $populasiAwal }},
        populasi_saat_ini: {{ (int) $populasiSaatIni }}
    };
</script>
<script src="{{ asset('bolopa/js/admin-show-part-pembesaran.js') }}?v={{ time() }}"></script>
<script>
// Tab persistence is now handled by the partial view (_tab-show-pembesaran.blade.php)
// This script is kept minimal to avoid conflicts
console.log('✅ Pembesaran detail page loaded');

@endpush


