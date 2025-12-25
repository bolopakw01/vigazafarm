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

    .embed-card {
        margin-top: 28px;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #c7d2fe;
        box-shadow: 0 10px 30px rgba(79, 70, 229, 0.12);
        color: #1f2937;
    }

    .embed-card h5 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #111827;
    }

    .embed-card .embed-lead {
        color: #374151;
        margin-bottom: 14px;
        font-size: 0.95rem;
    }

    /* Ensure this single description line is left aligned only (overrides parent center alignment) */
    .embed-card .embed-lead {
        text-align: left;
        width: 100%;
        margin: 0 0 14px 0;
    }

    .embed-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid #c7d2fe;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 14px;
    }

    /* Left-align the status text block only */
    .embed-row > div:first-child {
        text-align: left;
        flex: 1 1 auto;
        min-width: 0;
    }

    .toggle-switch { position: relative; display: inline-block; width: 52px; height: 28px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #cbd5e1; transition: .3s; border-radius: 28px; }
    .toggle-slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background: #fff; transition: .3s; border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
    input:checked + .toggle-slider { background: #4338ca; }
    input:checked + .toggle-slider:before { transform: translateX(24px); }
    .toggle-label { font-size: 0.95rem; font-weight: 600; color: #111827; margin-left: 12px; }

    .embed-form-label { font-weight: 600; color: #111827; display: block; margin-bottom: 6px; }

    .embed-input {
        width: 100%;
        border: 2px solid #c7d2fe;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.95rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        background: rgba(255, 255, 255, 0.9);
    }

    .embed-input:focus {
        outline: none;
        border-color: #4338ca;
        box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.2);
    }

    .btn-embed {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #4338ca;
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 12px 20px;
        font-weight: 700;
        box-shadow: 0 10px 25px rgba(67, 56, 202, 0.25);
        transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
    }

    .btn-embed:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 30px rgba(67, 56, 202, 0.3);
        opacity: 0.95;
    }

    .embed-hint {
        margin-top: 8px;
        color: #4b5563;
        font-size: 0.9rem;
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

        .embed-row { align-items: flex-start; }
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
                <p>Unduh satu CSV terkurasi siap pakai di Looker Studio. Tidak perlu ekstrak ZIP atau menggabungkan dataset secara manual.</p>

                <div class="download-info">
                    <div class="download-info-item">
                        <i class="fa-solid fa-shield-alt"></i>
                        <span>Data Aman</span>
                    </div>
                    <div class="download-info-item">
                        <i class="fa-solid fa-file-csv"></i>
                        <span>CSV Tunggal Siap Upload</span>
                    </div>
                    <div class="download-info-item">
                        <i class="fa-solid fa-diagram-project"></i>
                        <span>Schema & header konsisten</span>
                    </div>
                    <div class="download-info-item">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>{{ count($datasetStats) }} Dataset Terintegrasi</span>
                    </div>
                </div>

                <div class="download-buttons">
                    <a href="{{ route('admin.sistem.looker.export.download.flat') }}" class="btn-download">
                        <i class="fa-solid fa-file-csv"></i>
                        Unduh CSV (Looker Ready)
                    </a>
                </div>
                <p class="download-hint">CSV ini sudah mencakup kolom meta dan nilai terhitung untuk laporan operasional harian. Langsung sambungkan ke Looker Studio via Google Sheets atau upload file.</p>

            @php $embedEnabled = data_get($embedConfig, 'enabled'); @endphp
            <form action="{{ route('admin.sistem.looker.export.embed') }}" method="POST" class="mt-4">
                @csrf
                <div class="embed-card">
                    <h5><i class="fa-solid fa-window-maximize"></i> Embed Looker Studio di Dashboard</h5>
                    <p class="embed-lead">Aktifkan untuk menampilkan iframe Looker Studio menggantikan dashboard bawaan.</p>

                    <div class="embed-row">
                        <div>
                            <h6 class="mb-1 fw-semibold">Status Embed</h6>
                            <small class="text-muted">Klik toggle untuk mengaktifkan/menonaktifkan.</small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <input type="hidden" name="enabled" value="0">
                            <label class="toggle-switch mb-0" aria-label="Toggle Looker embed">
                                <input type="checkbox" name="enabled" value="1" id="embedToggle" {{ $embedEnabled ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label" id="embedToggleLabel">{{ $embedEnabled ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="embed-form-label" for="embedUrl">URL Looker Studio</label>
                        <input id="embedUrl" type="url" name="url" class="embed-input" value="{{ data_get($embedConfig, 'url') }}" placeholder="https://lookerstudio.google.com/embed/..." required>
                        <div class="embed-hint">Gunakan URL embed (bukan editor). Contoh: link yang berisi /embed/reporting/.</div>
                    </div>

                    <button class="btn-embed" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Embed</button>
                </div>
            </form>

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

            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function(){
                const toggle = document.getElementById('embedToggle');
                const label = document.getElementById('embedToggleLabel');
                if (!toggle || !label) return;
                toggle.addEventListener('change', function(){
                    label.textContent = toggle.checked ? 'Aktif' : 'Nonaktif';
                });
            });
            </script>
            @endpush
