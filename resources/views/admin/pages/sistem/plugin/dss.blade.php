@extends('admin.layouts.app')

@section('title', 'Pengaturan DSS')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Sistem VF', 'link' => route('admin.sistem')],
        ['label' => 'Pengaturan DSS'],
    ];

    $config = $configSettings ?? [];
    $ml = $mlSettings ?? [];
    $currentMode = old('mode', $mode ?? 'config');
    $dssEnabled = filter_var(old('enabled', data_get($settings ?? [], 'enabled', true)), FILTER_VALIDATE_BOOLEAN);
@endphp

@push('styles')
<style>
    @font-face {
        font-family: 'AlanSans';
        src: url('{{ asset("bolopa/font/AlanSans-VariableFont_wght.ttf") }}') format('truetype');
        font-weight: 100 900;
        font-display: swap;
    }

    .dss-wrapper {
        padding: 20px;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 24px;
        border-bottom: 1px solid #e9ecef;
    }

    .header-left h1 {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-left p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    .ml-note {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        padding: 18px 20px;
        margin-bottom: 18px;
    }

    .ml-note h5 {
        margin-bottom: 8px;
        font-size: 15px;
        color: #0f172a;
    }

    .ml-note ul {
        margin-bottom: 0;
        padding-left: 20px;
        color: #475569;
        font-size: 13px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-secondary {
        background: #334155;
        color: white;
    }

    .btn-secondary:hover {
        background: #1e293b;
    }

    .card-body {
        padding: 24px;
    }

    .alert-box {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    .settings-section {
        margin-bottom: 32px;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 24px;
    }

    .settings-section:last-of-type {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    .settings-section h4 {
        margin-bottom: 20px;
        color: #0f172a;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mode-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .mode-option {
        padding: 20px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .mode-option.selected {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.1);
    }

    .mode-option-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .mode-option-icon.config {
        background: rgba(16, 185, 129, 0.15);
        color: #059669;
    }

    .mode-option-icon.ml {
        background: rgba(59, 130, 246, 0.15);
        color: #2563eb;
    }

    .mode-option-content {
        flex: 1;
    }

    .mode-title {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .mode-description {
        font-size: 14px;
        color: #64748b;
        line-height: 1.5;
    }

    .mode-panel {
        display: none;
    }

    .mode-panel.active {
        display: block;
    }

    .section-hint {
        margin-top: -12px;
        margin-bottom: 20px;
        color: #475569;
        font-size: 13px;
        line-height: 1.5;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .form-help {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .config-focus-grid {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .config-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 70%);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .config-card-head {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .config-card-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .config-card-icon.eggs {
        background: rgba(251, 146, 60, 0.18);
        color: #ea580c;
    }

    .config-card-icon.feed {
        background: rgba(74, 222, 128, 0.18);
        color: #15803d;
    }

    .config-card-icon.mortality {
        background: rgba(248, 113, 113, 0.18);
        color: #b91c1c;
    }

    .config-card-body {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
    }

    .form-group.compact label {
        font-size: 13px;
        text-transform: none;
    }

    .config-card-foot {
        margin-top: 14px;
        font-size: 12px;
        color: #64748b;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
        margin-top: 10px;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
        padding: 12px 28px;
        border-radius: 10px;
        border: none;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .action-info {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-size: 14px;
    }

    .dss-enable-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 80%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .dss-enable-card .label {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .dss-enable-card .label strong {
        color: #0f172a;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
    }

    .dss-enable-card .label strong .icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-size: 14px;
    }

    .dss-enable-card .label span {
        color: #64748b;
        font-size: 13px;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: #e0f2fe;
        color: #075985;
    }

    .status-chip.off {
        background: #f1f5f9;
        color: #475569;
    }

    .form-switch-lg {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
    }

    .switch-track {
        position: relative;
        width: 50px;
        height: 26px;
        background: #cbd5e1;
        border-radius: 999px;
        transition: background 0.2s ease;
    }

    .switch-thumb {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.18);
        transition: transform 0.2s ease;
    }

    .form-switch-lg input {
        display: none;
    }

    .form-switch-lg input:checked + .switch-track {
        background: #2563eb;
    }

    .form-switch-lg input:checked + .switch-track .switch-thumb {
        transform: translateX(24px);
    }

    .ml-features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .ml-feature-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 70%);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        display: flex;
        gap: 16px;
        align-items: flex-start;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .ml-feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
    }

    .ml-feature-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .ml-feature-icon.eggs {
        background: rgba(251, 146, 60, 0.15);
        color: #ea580c;
    }

    .ml-feature-icon.feed {
        background: rgba(74, 222, 128, 0.15);
        color: #15803d;
    }

    .ml-feature-icon.mortality {
        background: rgba(248, 113, 113, 0.15);
        color: #b91c1c;
    }

    .ml-feature-icon.price {
        background: rgba(59, 130, 246, 0.15);
        color: #2563eb;
    }

    .ml-feature-icon.alert {
        background: rgba(168, 85, 247, 0.15);
        color: #7c3aed;
    }

    .ml-feature-content h5 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
    }

    .ml-feature-content p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.5;
    }

    .ml-status-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        background: #f8fafc;
    }

    .ml-status-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    .ml-status-header i {
        color: #2563eb;
        font-size: 18px;
    }

    .ml-status-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
    }

    .ml-status-card p {
        margin: 0 0 16px 0;
        color: #475569;
        font-size: 14px;
        line-height: 1.5;
    }

    .ml-status-note {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13px;
        color: #1e40af;
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            gap: 16px;
        }

        .form-actions {
            flex-direction: column;
            gap: 16px;
            align-items: stretch;
        }

        .action-info {
            justify-content: center;
        }

        .ml-features-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="dss-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1>
                    <i class="fas fa-brain"></i>
                    Pengaturan Decision Support System
                </h1>
                <p>Kelola parameter rule-based dan Machine Learning agar halaman DSS tetap selaras dengan kebutuhan operasional.</p>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.sistem') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert-box alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-error">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.sistem.dss.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="mode" id="dssModeInput" value="{{ $currentMode }}">
                <input type="hidden" name="enabled" value="0">

                <div class="dss-enable-card">
                    <div class="label">
                        <strong><span class="icon"><i class="fas fa-power-off"></i></span> Halaman DSS</strong>
                        <span>Aktifkan atau sembunyikan halaman DSS dari sidebar.</span>
                    </div>
                    <label class="form-switch-lg">
                        <input id="dssEnabledToggle" type="checkbox" name="enabled" value="1" {{ $dssEnabled ? 'checked' : '' }} aria-label="Aktifkan halaman DSS">
                        <span class="switch-track"><span class="switch-thumb"></span></span>
                        <span id="dssEnabledLabel" class="status-chip {{ $dssEnabled ? '' : 'off' }}">{{ $dssEnabled ? 'Aktif' : 'Nonaktif' }}</span>
                    </label>
                </div>

                <div id="dss-mode-block" class="{{ $dssEnabled ? '' : 'd-none' }}">
                <div class="settings-section">
                    <h4><i class="fas fa-toggle-on"></i> Pilih Mode Operasi</h4>
                    <div class="mode-options">
                        <div class="mode-option {{ $currentMode === 'config' ? 'selected' : '' }}" data-mode="config">
                            <div class="mode-option-icon config">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <div class="mode-option-content">
                                <div class="mode-title">Config-driven (Rule Based)</div>
                                <div class="mode-description">
                                    Gunakan parameter yang sama dengan file <code>dss.php</code> serta kalkulasi langsung dari database untuk setiap insight.
                                </div>
                            </div>
                        </div>
                        <div class="mode-option {{ $currentMode === 'ml' ? 'selected' : '' }}" data-mode="ml">
                            <div class="mode-option-icon ml">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="mode-option-content">
                                <div class="mode-title">Machine Learning</div>
                                <div class="mode-description">
                                    Halaman DSS akan mengonsumsi rekomendasi dari artefak ML di <code>ml/artifacts/dss_model.onnx</code> lengkap dengan metadata model.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="settings-section mode-panel mode-config {{ $currentMode === 'config' ? 'active' : '' }}" data-panel="config">
                    <h4><i class="fas fa-sliders-h"></i> Parameter Config-driven</h4>
                    <p class="section-hint">
                        <strong>Atur manual sesuai SOP</strong><br>
                        Parameter ini langsung membentuk insight DSS berdasarkan data yang ada di database tanpa model ML.
                    </p>
                    <p class="form-help mb-2">Semua nilai di bawah ini akan menimpa konfigurasi default untuk seluruh pengguna.</p>
                    <p class="form-help mb-3">Label ke pengguna: Aman / Perlu Perhatian / Darurat (logika sistem tetap Normal / Warning / Critical).</p>
                    <div class="ml-note">
                        <h5 class="mb-2"><i class="fas fa-lightbulb"></i> Tips pengisian cepat</h5>
                        <ul class="mb-0">
                            <li>Isi nilai wajar sesuai SOP harian; gunakan batas <strong>warning</strong> (tampil: Perlu Perhatian) dan <strong>critical</strong> (tampil: Darurat).</li>
                            <li>Rasio pakan gunakan desimal (contoh 0.10 = toleransi ±10%).</li>
                            <li>Perbanyak batas hari atau turunkan rasio bila tim kandang masih sering false alarm.</li>
                        </ul>
                    </div>

                    <div class="config-focus-grid">
                        <div class="config-card">
                            <div class="config-card-head">
                                <div class="config-card-icon eggs"><i class="fas fa-egg"></i></div>
                                <div>
                                    <h5 class="mb-1">Penetasan Telur</h5>
                                    <p class="form-help mb-0">Mengendalikan widget "Status Penetasan Telur" pada halaman DSS.</p>
                                </div>
                            </div>
                            <div class="config-card-body">
                                <div class="form-group compact">
                                    <label>Batch yang dipantau</label>
                                    <input type="number" name="config[eggs][max_batches]" class="form-control" min="1" max="10" value="{{ old('config.eggs.max_batches', data_get($config, 'eggs.max_batches')) }}">
                                    <div class="form-help">Jumlah baris telur yang muncul sekaligus.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Warning (Perlu Perhatian) pindah hatcher (hari)</label>
                                    <input type="number" name="config[eggs][hatcher_warning_days]" class="form-control" min="0" max="14" value="{{ old('config.eggs.hatcher_warning_days', data_get($config, 'eggs.hatcher_warning_days')) }}">
                                    <div class="form-help">Berapa hari sebelum due date status menjadi kuning. Saran: 2-3 hari.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Critical (Darurat) pindah hatcher (hari)</label>
                                    <input type="number" name="config[eggs][hatcher_critical_days]" class="form-control" min="0" max="14" value="{{ old('config.eggs.hatcher_critical_days', data_get($config, 'eggs.hatcher_critical_days')) }}">
                                    <div class="form-help">Lewat angka ini status merah. Saran: 0-1 hari.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Warning (Perlu Perhatian) hatch rate (%)</label>
                                    <input type="number" step="0.1" name="config[eggs][hatch_rate_warning]" class="form-control" min="0" max="100" value="{{ old('config.eggs.hatch_rate_warning', data_get($config, 'eggs.hatch_rate_warning')) }}">
                                    <div class="form-help">Persentase tetas minimum sebelum muncul peringatan. Saran: 85-88%.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Critical (Darurat) hatch rate (%)</label>
                                    <input type="number" step="0.1" name="config[eggs][hatch_rate_critical]" class="form-control" min="0" max="100" value="{{ old('config.eggs.hatch_rate_critical', data_get($config, 'eggs.hatch_rate_critical')) }}">
                                    <div class="form-help">Di bawah angka ini dianggap gawat. Saran: 80-82%.</div>
                                </div>
                            </div>
                            <div class="config-card-foot">Ideal untuk menyelaraskan SOP pindah setter → hatcher dengan indikator di DSS.</div>
                        </div>

                        <div class="config-card">
                            <div class="config-card-head">
                                <div class="config-card-icon feed"><i class="fas fa-wheat-awn"></i></div>
                                <div>
                                    <h5 class="mb-1">Konsumsi Pakan</h5>
                                    <p class="form-help mb-0">Mengatur tabel "Insight Konsumsi Pakan" agar mudah dibaca tim kandang.</p>
                                </div>
                            </div>
                            <div class="config-card-body">
                                <div class="form-group compact">
                                    <label>Baris insight yang tampil</label>
                                    <input type="number" name="config[feed][max_insights]" class="form-control" min="1" max="20" value="{{ old('config.feed.max_insights', data_get($config, 'feed.max_insights')) }}">
                                    <div class="form-help">Berapa batch (pembesaran + produksi) diprioritaskan.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Data historis (hari)</label>
                                    <input type="number" name="config[feed][history_days]" class="form-control" min="1" max="30" value="{{ old('config.feed.history_days', data_get($config, 'feed.history_days')) }}">
                                    <div class="form-help">Dipakai untuk rata-rata konsumsi harian. Saran: 7 hari.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Batas Warning (Perlu Perhatian) — rasio</label>
                                    <input type="number" step="0.01" name="config[feed][warning_ratio]" class="form-control" min="0" max="1" value="{{ old('config.feed.warning_ratio', data_get($config, 'feed.warning_ratio')) }}">
                                    <div class="form-help">Masukkan 0.10 untuk toleransi ±10%. Saran: 0.10.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Batas Critical (Darurat) — rasio</label>
                                    <input type="number" step="0.01" name="config[feed][critical_ratio]" class="form-control" min="0" max="1" value="{{ old('config.feed.critical_ratio', data_get($config, 'feed.critical_ratio')) }}">
                                    <div class="form-help">Di atas angka ini dianggap sangat janggal. Saran: 0.20.</div>
                                </div>
                            </div>
                            <div class="config-card-foot">Parameter ini memengaruhi target vs aktual pada widget pakan.</div>
                        </div>

                        <div class="config-card">
                            <div class="config-card-head">
                                <div class="config-card-icon mortality"><i class="fas fa-skull-crossbones"></i></div>
                                <div>
                                    <h5 class="mb-1">Alert Mortalitas</h5>
                                    <p class="form-help mb-0">Langsung terkait bagian "Alert Mortalitas" yang memantau deviasi 3 hari terakhir.</p>
                                </div>
                            </div>
                            <div class="config-card-body">
                                <div class="form-group compact">
                                    <label>Rentang hari pemantauan</label>
                                    <input type="number" name="config[mortality][window_days]" class="form-control" min="1" max="14" value="{{ old('config.mortality.window_days', data_get($config, 'mortality.window_days')) }}">
                                    <div class="form-help">Default 3 hari mengikuti tampilan DSS. Saran: 3-5 hari.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Maksimum alert yang tampil</label>
                                    <input type="number" name="config[mortality][max_items]" class="form-control" min="1" max="10" value="{{ old('config.mortality.max_items', data_get($config, 'mortality.max_items')) }}">
                                    <div class="form-help">Batasi jumlah kartu agar mudah dipantau.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Batas Warning (Perlu Perhatian) — % populasi</label>
                                    <input type="number" step="0.1" name="config[mortality][warning_pct]" class="form-control" min="0" max="100" value="{{ old('config.mortality.warning_pct', data_get($config, 'mortality.warning_pct')) }}">
                                    <div class="form-help">Persentase mortalitas yang menyalakan warna kuning. Saran: 0.3-0.5%.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Batas Critical (Darurat) — % populasi</label>
                                    <input type="number" step="0.1" name="config[mortality][critical_pct]" class="form-control" min="0" max="100" value="{{ old('config.mortality.critical_pct', data_get($config, 'mortality.critical_pct')) }}">
                                    <div class="form-help">Persentase mortalitas yang dianggap gawat. Saran: 0.8-1%.</div>
                                </div>
                            </div>
                            <div class="config-card-foot">Gunakan angka ini untuk menyelaraskan SOP penanganan kematian produksi & pembesaran.</div>
                        </div>
                    </div>
                </div>

                <div class="settings-section mode-panel mode-ml {{ $currentMode === 'ml' ? 'active' : '' }}" data-panel="ml">
                    <h4><i class="fas fa-robot"></i> Parameter Machine Learning</h4>
                    <p class="section-hint">
                        <strong>Semua prediksi memakai data yang sudah dicatat di sistem.</strong><br>
                        Tidak ada parameter tambahan yang perlu diisi ketika mode ML aktif.
                    </p>

                    <div class="ml-features-grid">
                        <div class="ml-feature-card">
                            <div class="ml-feature-icon eggs">
                                <i class="fas fa-egg"></i>
                            </div>
                            <div class="ml-feature-content">
                                <h5>Produksi Telur</h5>
                                <p>Forecasting harian dari histori pencatatan produksi untuk memprediksi output telur per hari.</p>
                            </div>
                        </div>
                        <div class="ml-feature-card">
                            <div class="ml-feature-icon feed">
                                <i class="fas fa-wheat-awn"></i>
                            </div>
                            <div class="ml-feature-content">
                                <h5>Kebutuhan Pakan</h5>
                                <p>Estimasi konsumsi per batch berdasarkan data pakan 7 hari terakhir dan pola historis.</p>
                            </div>
                        </div>
                        <div class="ml-feature-card">
                            <div class="ml-feature-icon mortality">
                                <i class="fas fa-skull-crossbones"></i>
                            </div>
                            <div class="ml-feature-content">
                                <h5>Mortalitas / Outbreak</h5>
                                <p>Deteksi anomali menggunakan log kematian untuk mengidentifikasi pola outbreak dini.</p>
                            </div>
                        </div>
                        <div class="ml-feature-card">
                            <div class="ml-feature-icon price">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="ml-feature-content">
                                <h5>Optimasi Harga</h5>
                                <p>Rekomendasi harga jual berdasarkan forecast produksi dan analisis biaya pakan.</p>
                            </div>
                        </div>
                        <div class="ml-feature-card">
                            <div class="ml-feature-icon alert">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="ml-feature-content">
                                <h5>Alert & Explainability</h5>
                                <p>Rangkuman otomatis dari semua analitik dengan penjelasan yang mudah dipahami.</p>
                            </div>
                        </div>
                    </div>

                    <div class="ml-status-card">
                        <div class="ml-status-header">
                            <i class="fas fa-info-circle"></i>
                            <h5>Status Model</h5>
                        </div>
                        <p>Model ML akan otomatis menggunakan data terbaru dari sistem. Pastikan data historis pencatatan produksi, pakan, dan kematian sudah tercatat dengan baik untuk akurasi prediksi yang optimal.</p>
                        <div class="ml-status-note">
                            <strong>Catatan:</strong> Aktifkan mode ML, simpan pengaturan, dan halaman DSS akan langsung menampilkan insight prediktif tersebut.
                        </div>
                    </div>
                </div>

                </div><!-- /dss-mode-block -->

                <div class="form-actions">
                    <div class="action-info">
                        <i class="fas fa-shield-alt"></i>
                        Perubahan langsung diterapkan ke halaman DSS setelah disimpan.
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const options = document.querySelectorAll('.mode-option');
        const input = document.getElementById('dssModeInput');
        const panels = document.querySelectorAll('.mode-panel');
        const enableToggle = document.getElementById('dssEnabledToggle');
        const enableLabel = document.getElementById('dssEnabledLabel');
        const modeBlock = document.getElementById('dss-mode-block');

        const togglePanels = (mode) => {
            panels.forEach(panel => {
                panel.classList.toggle('active', panel.dataset.panel === mode);
            });
        };

        togglePanels(input.value || 'config');

        options.forEach(option => {
            option.addEventListener('click', () => {
                options.forEach(item => item.classList.remove('selected'));
                option.classList.add('selected');
                input.value = option.dataset.mode;
                togglePanels(option.dataset.mode);
            });
        });

        // Toggle visibility of mode section based on enabled switch
        if (enableToggle && modeBlock && enableLabel) {
            const syncEnableState = () => {
                const isOn = enableToggle.checked;
                modeBlock.classList.toggle('d-none', !isOn);
                enableLabel.textContent = isOn ? 'Aktif' : 'Nonaktif';
                enableLabel.classList.toggle('off', !isOn);
            };

            enableToggle.addEventListener('change', syncEnableState);
            syncEnableState();
        }

    });
</script>
@endpush
