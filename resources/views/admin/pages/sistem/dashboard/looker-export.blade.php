@extends('admin.layouts.app')

@section('title', 'Export Master Dashboard')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Sistem', 'link' => route('admin.sistem')],
        ['label' => 'Export Looker Studio'],
    ];
@endphp

@push('styles')
<style>
    @font-face {
        font-family: 'AlanSans';
        src: url('{{ asset("bolopa/font/AlanSans-VariableFont_wght.ttf") }}') format('truetype');
        font-weight: 100 900;
        font-display: swap;
    }

    .export-wrapper {
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

    .export-header {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 25px;
    }

    .export-title {
        font-size: 1.75rem;
        font-weight: 600;
        font-family: 'AlanSans', sans-serif;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .export-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
        margin: 0;
    }

    .download-section {
        background: linear-gradient(135deg, #1e40af 0%, #3730a3 50%, #581c87 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .download-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .download-section > * {
        position: relative;
        z-index: 1;
    }

    .download-section h3 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .download-section p {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 24px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .download-info {
        display: flex;
        justify-content: center;
        gap: 32px;
        margin-bottom: 32px;
        flex-wrap: wrap;
    }

    .download-info-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .download-info-item i {
        font-size: 2rem;
        opacity: 0.8;
    }

    .download-info-item span {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .download-buttons {
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .btn-download {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: white;
        color: #1e40af;
        padding: 16px 32px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.15);
    }

    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.2);
        background: #f8fafc;
    }

    .btn-download i {
        font-size: 1.2rem;
    }

    .btn-download-outline {
        background: transparent;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.6);
    }

    .btn-download-outline:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #e0e7ff;
    }

    .download-hint {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .file-list {
        text-align: left;
        margin: 24px auto 0;
        max-width: 520px;
        background: rgba(15, 23, 42, 0.35);
        padding: 18px 22px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .file-list h5 {
        margin: 0 0 12px;
        font-size: 1rem;
        font-weight: 600;
    }

    .file-list ul {
        margin: 0;
        padding-left: 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 0.9rem;
    }

    .file-list li strong {
        display: block;
        font-weight: 600;
    }

    .tips-section {
        background: #f8fafc;
        border-radius: 16px;
        padding: 24px;
        margin-top: 24px;
    }

    .tips-section h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tips-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .tip-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .tip-item i {
        color: #10b981;
        font-size: 1.2rem;
        margin-top: 2px;
    }

    .tip-item p {
        margin: 0;
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .export-wrapper {
            padding: 16px;
        }

        .download-section {
            padding: 24px;
        }

        .download-info {
            gap: 16px;
        }

        .download-buttons {
            flex-direction: column;
        }

        .file-list {
            max-width: 100%;
        }

        .tips-list {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="export-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1 class="export-title">
                    <i class="fa-solid fa-chart-line"></i>
                    Export Master Dashboard
                </h1>
                <p class="export-subtitle">Siapkan data lengkap untuk analisis Looker Studio dengan sekali klik.</p>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.sistem') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="download-section">
                <h3><i class="fa-solid fa-download"></i> Unduh Paket Data</h3>
                <p>Pilih antara paket standar (semua dataset mentah per tabel) atau paket profesional berisi tiga CSV kurasi (D1-D3) dalam satu file ZIP siap pakai untuk Looker Studio.</p>

                <div class="download-info">
                    <div class="download-info-item">
                        <i class="fa-solid fa-shield-alt"></i>
                        <span>Data Aman</span>
                    </div>
                    <div class="download-info-item">
                        <i class="fa-solid fa-file-zipper"></i>
                        <span>Paket Multi CSV Standar (ZIP)</span>
                    </div>
                    <div class="download-info-item">
                        <i class="fa-solid fa-file-csv"></i>
                        <span>3 CSV Profesional (D1-D3)</span>
                    </div>
                    <div class="download-info-item">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>{{ count($datasetStats) }} Dataset Terintegrasi</span>
                    </div>
                </div>

                <div class="download-buttons">
                    <a href="{{ route('admin.sistem.looker.export.download') }}" class="btn-download">
                        <i class="fa-solid fa-file-zipper"></i>
                        Unduh Paket Standar (ZIP)
                    </a>
                    <a href="{{ route('admin.sistem.looker.export.download.csv') }}" class="btn-download btn-download-outline">
                        <i class="fa-solid fa-file-csv"></i>
                        Unduh Paket CSV Profesional
                    </a>
                </div>
                <p class="download-hint">Paket profesional menghasilkan file ZIP berisi tiga CSV utama untuk analisis harian, snapshot populasi, dan stok inventaris.</p>

                <div class="tips-section">
                    <h4><i class="fa-solid fa-lightbulb"></i> Tips Penggunaan</h4>
                    <div class="tips-list">
                        <div class="tip-item">
                            <i class="fa-solid fa-upload"></i>
                            <p><strong>Upload ke Drive:</strong> Unggah file CSV atau ZIP ke Google Drive untuk akses mudah.</p>
                        </div>
                        <div class="tip-item">
                            <i class="fa-solid fa-link"></i>
                            <p><strong>Connect ke Looker:</strong> Gunakan "Google Sheets" sebagai sumber data di Looker Studio.</p>
                        </div>
                        <div class="tip-item">
                            <i class="fa-solid fa-chart-bar"></i>
                            <p><strong>Buat Dashboard:</strong> Dataset meta_summary berisi target dan goals untuk visualisasi utama.</p>
                        </div>
                        <div class="tip-item">
                            <i class="fa-solid fa-sync"></i>
                            <p><strong>Update Berkala:</strong> Export ulang secara berkala untuk data terbaru.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
