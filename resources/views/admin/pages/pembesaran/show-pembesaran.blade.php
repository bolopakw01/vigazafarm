@extends('admin.layouts.app')

@section('title', 'Detail Pembesaran - ' . $pembesaran->batch_produksi_id)

@push('styles')
{{-- Custom CSS for this page only (scoped to prevent sidebar conflicts) --}}
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-pembesaran.css') }}">
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-part-pembesaran.css') }}">
{{-- Inter font dari Google Fonts, Patrick Hand sudah local --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
{{-- ApexCharts for graphs --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
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
                <div class="bolopa-kai-value">{{ number_format($mortalitas, 2) }}<small style="font-size:0.45em;">%</small></div>
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
                <div class="bolopa-kai-value">Rp {{ number_format($totalBiayaPakan, 0, ',', '.') }}</div>
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
<script src="{{ asset('bolopa/js/admin-show-part-pembesaran.js') }}"></script>
<script>
// Ensure Bootstrap tabs work properly
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tab triggers
    const tabTriggers = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const tab = new bootstrap.Tab(this);
            tab.show();
        });
        
        // Accessibility update on tab shown
        trigger.addEventListener('shown.bs.tab', function(e) {
            tabTriggers.forEach(t => t.setAttribute('aria-selected', 'false'));
            e.target.setAttribute('aria-selected', 'true');
        });
    });
    
    console.log('Bootstrap tabs initialized:', tabTriggers.length, 'tabs found');
});
</script>
@endpush


