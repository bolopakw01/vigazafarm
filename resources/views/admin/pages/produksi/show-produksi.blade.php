@extends('admin.layouts.app')

@section('title', 'Detail Produksi')

@section('content')
<div class="container-fluid py-4">
    <div class="card p-4">
        <h5>Detail Produksi (Placeholder)</h5>
        <p>ID: {{ $id }}</p>
        <p>Halaman detail produksi belum diimplementasikan. Kembali ke <a href="{{ route('admin.produksi') }}">Daftar Produksi</a>.</p>
    </div>
</div>
@endsection
