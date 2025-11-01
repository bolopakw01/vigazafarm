@extends('admin.layouts.app')

@section('title', 'Detail Produksi Puyuh - ' . ($produksi->batch_produksi_id ?? 'Tanpa Kode Batch'))

@section('content')
<div class="container my-4">
    @php
        $status = $produksi->status ?? 'aktif';
        $statusBadgeClass = $status === 'aktif' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary';
        $tanggalMulai = $produksi->tanggal_mulai ? \Carbon\Carbon::parse($produksi->tanggal_mulai)->translatedFormat('d F Y') : '-';
        $tanggalAkhir = $produksi->tanggal_akhir ? \Carbon\Carbon::parse($produksi->tanggal_akhir)->translatedFormat('d F Y') : '-';
        $defaultTanggalForm = old('tanggal', optional($todayLaporan?->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d'));
        $defaultPopulasi = old('jumlah_burung', optional($todayLaporan)->jumlah_burung ?? ($produksi->jumlah_indukan ?? 0));
    @endphp

    <div class="row g-4">
        @include('admin.pages.produksi.partials.show-form._puyuh-header')
        @include('admin.pages.produksi.partials.show-form._alerts')
        @include('admin.pages.produksi.partials.show-form._puyuh-summary-cards')
        @include('admin.pages.produksi.partials.show-form._puyuh-tabbed-form')
        @include('admin.pages.produksi.partials.show-form._puyuh-laporan-table')
    </div>
</div>
@endsection
