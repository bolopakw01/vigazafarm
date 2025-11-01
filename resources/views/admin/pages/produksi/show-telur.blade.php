@extends('admin.layouts.app')

@section('title', 'Detail Produksi Telur - ' . ($produksi->batch_produksi_id ?? 'Tanpa Kode Batch'))

@section('content')
<div class="container my-4">
    <!-- Placeholder: Fokus pengembangan di produksi puyuh terlebih dahulu -->
    <div class="alert alert-info">
        <i class="fa-solid fa-circle-info me-2"></i>Halaman detail produksi telur masih dalam pengembangan.
    </div>
</div>
@endsection
