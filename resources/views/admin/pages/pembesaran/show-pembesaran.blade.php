@extends('admin.layouts.app')

@section('title', 'Detail Pembesaran - ' . $pembesaran->batch_produksi_id)

@push('styles')
{{-- Custom CSS for this page only (scoped to prevent sidebar conflicts) --}}
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-pembesaran.css') }}">
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-part-pembesaran.css') }}">
{{-- ApexCharts for graphs --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
{{-- Helper untuk format mortalitas --}}
@php
    $mortalitasFormatted = $mortalitas == floor($mortalitas) 
        ? number_format($mortalitas, 0) 
        : rtrim(rtrim(number_format($mortalitas, 2), '0'), '.');
@endphp

<div class="pembesaran-detail-wrapper">
<div class="container-fluid py-4">
    
    {{-- Header (tanpa card background) --}}
    <div class="bolopa-page-header mb-3">
        <div class="bolopa-logo-icon">
            <i class="fa-solid fa-dove"></i>
        </div>
        <div class="bolopa-header-content">
            <h5 class="bolopa-page-title">Detail Pembesaran</h5>
            <div class="bolopa-page-subtitle">
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
            <a href="#infoBatch" class="bolopa-kai-more" data-bs-toggle="tab" data-bs-target="#infoBatch">
                Detail <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Mortalitas --}}
        <div class="bolopa-card-kai bolopa-kai-red">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value">{{ $mortalitasFormatted }}<small style="font-size:0.45em;">%</small></div>
                <div class="bolopa-kai-label">Mortalitas ({{ number_format($totalMati) }} ekor)</div>
            </div>
            <i class="fa-solid fa-skull-crossbones bolopa-icon-faint"></i>
            <a href="#grafikAnalisis" class="bolopa-kai-more" data-bs-toggle="tab" data-bs-target="#grafikAnalisis">
                Detail <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Berat Rata-rata --}}
        <div class="bolopa-card-kai bolopa-kai-green">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value">{{ $pembesaran->berat_rata_rata ? number_format($pembesaran->berat_rata_rata, 0) : 0 }}g</div>
                <div class="bolopa-kai-label">Berat rata-rata</div>
            </div>
            <i class="fa-solid fa-scale-balanced bolopa-icon-faint"></i>
            <a href="#recordMingguan" class="bolopa-kai-more" data-bs-toggle="tab" data-bs-target="#recordMingguan">
                Update <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
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
            <a href="#infoBatch" class="bolopa-kai-more" data-bs-toggle="tab" data-bs-target="#infoBatch">
                Rincian <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    {{-- Notebook Container with Tabs --}}
    @include('admin.pages.pembesaran.partials._tab-show-pembesaran')

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
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script src="{{ asset('bolopa/js/admin-show-part-pembesaran.js') }}?v={{ time() }}"></script>
<script>
// Tab persistence is now handled by the partial view (_tab-show-pembesaran.blade.php)
// This script is kept minimal to avoid conflicts
console.log('✅ Pembesaran detail page loaded');

@endpush


