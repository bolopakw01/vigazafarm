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
                    <p class="form-help mb-3">Semua nilai di bawah ini akan menimpa konfigurasi default untuk seluruh pengguna.</p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Feed • Max Insights</label>
                            <input type="number" name="config[feed][max_insights]" class="form-control" min="1" max="20" value="{{ old('config.feed.max_insights', data_get($config, 'feed.max_insights')) }}">
                            <div class="form-help">Jumlah maksimal insight pakan yang ditampilkan pada dashboard DSS.</div>
                        </div>
                        <div class="form-group">
                            <label>Feed • History Days</label>
                            <input type="number" name="config[feed][history_days]" class="form-control" min="1" max="30" value="{{ old('config.feed.history_days', data_get($config, 'feed.history_days')) }}">
                            <div class="form-help">Rentang hari data historis yang digunakan untuk analisis konsumsi pakan.</div>
                        </div>
                        <div class="form-group">
                            <label>Feed • Warning Ratio</label>
                            <input type="number" step="0.01" name="config[feed][warning_ratio]" class="form-control" min="0" max="1" value="{{ old('config.feed.warning_ratio', data_get($config, 'feed.warning_ratio')) }}">
                            <div class="form-help">Persentase rasio konsumsi yang memicu status peringatan.</div>
                        </div>
                        <div class="form-group">
                            <label>Feed • Critical Ratio</label>
                            <input type="number" step="0.01" name="config[feed][critical_ratio]" class="form-control" min="0" max="1" value="{{ old('config.feed.critical_ratio', data_get($config, 'feed.critical_ratio')) }}">
                            <div class="form-help">Persentase rasio konsumsi yang memicu status kritis.</div>
                        </div>
                        <div class="form-group">
                            <label>Stock • Max Items</label>
                            <input type="number" name="config[stock][max_items]" class="form-control" min="1" max="10" value="{{ old('config.stock.max_items', data_get($config, 'stock.max_items')) }}">
                            <div class="form-help">Jumlah maksimum komoditas stok yang ingin ditampilkan.</div>
                        </div>
                        <div class="form-group">
                            <label>Stock • Warning Cover (Hari)</label>
                            <input type="number" step="0.1" name="config[stock][cover_warning_days]" class="form-control" min="0" max="60" value="{{ old('config.stock.cover_warning_days', data_get($config, 'stock.cover_warning_days')) }}">
                            <div class="form-help">Batas hari cakupan stok ketika status berubah menjadi peringatan.</div>
                        </div>
                        <div class="form-group">
                            <label>Stock • Critical Cover (Hari)</label>
                            <input type="number" step="0.1" name="config[stock][cover_critical_days]" class="form-control" min="0" max="60" value="{{ old('config.stock.cover_critical_days', data_get($config, 'stock.cover_critical_days')) }}">
                            <div class="form-help">Batas hari cakupan stok ketika status berubah menjadi kritis.</div>
                        </div>
                        <div class="form-group">
                            <label>Environment • Max Items</label>
                            <input type="number" name="config[environment][max_items]" class="form-control" min="1" max="10" value="{{ old('config.environment.max_items', data_get($config, 'environment.max_items')) }}">
                            <div class="form-help">Jumlah parameter lingkungan (suhu, kelembapan, dsb) yang disorot.</div>
                        </div>
                        <div class="form-group">
                            <label>Health • Window Days</label>
                            <input type="number" name="config[health][window_days]" class="form-control" min="1" max="14" value="{{ old('config.health.window_days', data_get($config, 'health.window_days')) }}">
                            <div class="form-help">Jendela hari yang dipakai menghitung tren kesehatan flok.</div>
                        </div>
                        <div class="form-group">
                            <label>Health • Max Items</label>
                            <input type="number" name="config[health][max_items]" class="form-control" min="1" max="10" value="{{ old('config.health.max_items', data_get($config, 'health.max_items')) }}">
                            <div class="form-help">Jumlah insight kesehatan yang ditampilkan pada DSS.</div>
                        </div>
                        <div class="form-group">
                            <label>Health • Warning %</label>
                            <input type="number" step="0.1" name="config[health][warning_pct]" class="form-control" min="0" max="100" value="{{ old('config.health.warning_pct', data_get($config, 'health.warning_pct')) }}">
                            <div class="form-help">Persentase mortalitas yang memicu status warning.</div>
                        </div>
                        <div class="form-group">
                            <label>Health • Critical %</label>
                            <input type="number" step="0.1" name="config[health][critical_pct]" class="form-control" min="0" max="100" value="{{ old('config.health.critical_pct', data_get($config, 'health.critical_pct')) }}">
                            <div class="form-help">Persentase mortalitas yang memicu status critical.</div>
                        </div>
                    </div>
                </div>

                <div class="settings-section mode-panel mode-ml {{ $currentMode === 'ml' ? 'active' : '' }}" data-panel="ml">
                    <h4><i class="fas fa-robot"></i> Parameter Machine Learning</h4>
                    <p class="section-hint">
                        <strong>Tampilkan rekomendasi model</strong><br>
                        Gunakan pengenal artefak, catatan, serta contoh metrik agar admin memahami konteks model yang sedang aktif.
                    </p>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Default Phase</label>
                            <input type="text" name="ml[default_phase]" class="form-control" value="{{ old('ml.default_phase', $ml['default_phase'] ?? 'grower') }}">
                            <div class="form-help">Fase siklus ternak yang dipakai ketika payload ML tidak menyertakan fase.</div>
                        </div>
                        <div class="form-group">
                            <label>Label Artefak</label>
                            <input type="text" name="ml[artifact_label]" class="form-control" value="{{ old('ml.artifact_label', $ml['artifact_label'] ?? '') }}">
                            <div class="form-help">Nama model atau artefak agar admin mudah mengenalinya.</div>
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <input type="text" name="ml[notes]" class="form-control" value="{{ old('ml.notes', $ml['notes'] ?? '') }}">
                            <div class="form-help">Catatan singkat mengenai perilaku atau asumsi model.</div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label>Metrics JSON</label>
                        <textarea name="ml[metrics_json]" rows="6" class="form-control" placeholder='{"fcr":1.8,"mortality_pct":0.4}'>{{ old('ml.metrics_json', $metricsJson ?? '{}') }}</textarea>
                        <div class="form-help">Gunakan JSON valid untuk menyimpan metrik evaluasi (misal FCR, akurasi) yang ditampilkan pada halaman DSS saat mode ML aktif.</div>
                    </div>
                </div>

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
    });
</script>
@endpush
