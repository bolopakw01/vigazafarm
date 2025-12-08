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
                                    <label>Warning pindah hatcher (hari)</label>
                                    <input type="number" name="config[eggs][hatcher_warning_days]" class="form-control" min="0" max="14" value="{{ old('config.eggs.hatcher_warning_days', data_get($config, 'eggs.hatcher_warning_days')) }}">
                                    <div class="form-help">Berapa hari sebelum due date status menjadi kuning.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Critical pindah hatcher (hari)</label>
                                    <input type="number" name="config[eggs][hatcher_critical_days]" class="form-control" min="0" max="14" value="{{ old('config.eggs.hatcher_critical_days', data_get($config, 'eggs.hatcher_critical_days')) }}">
                                    <div class="form-help">Lewat angka ini status merah.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Warning hatch rate (%)</label>
                                    <input type="number" step="0.1" name="config[eggs][hatch_rate_warning]" class="form-control" min="0" max="100" value="{{ old('config.eggs.hatch_rate_warning', data_get($config, 'eggs.hatch_rate_warning')) }}">
                                    <div class="form-help">Persentase tetas minimum sebelum muncul peringatan.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Critical hatch rate (%)</label>
                                    <input type="number" step="0.1" name="config[eggs][hatch_rate_critical]" class="form-control" min="0" max="100" value="{{ old('config.eggs.hatch_rate_critical', data_get($config, 'eggs.hatch_rate_critical')) }}">
                                    <div class="form-help">Di bawah angka ini dianggap gawat.</div>
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
                                    <div class="form-help">Dipakai untuk rata-rata konsumsi harian.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Batas warning (rasio)</label>
                                    <input type="number" step="0.01" name="config[feed][warning_ratio]" class="form-control" min="0" max="1" value="{{ old('config.feed.warning_ratio', data_get($config, 'feed.warning_ratio')) }}">
                                    <div class="form-help">Masukkan 0.10 untuk toleransi ±10%.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Batas critical (rasio)</label>
                                    <input type="number" step="0.01" name="config[feed][critical_ratio]" class="form-control" min="0" max="1" value="{{ old('config.feed.critical_ratio', data_get($config, 'feed.critical_ratio')) }}">
                                    <div class="form-help">Di atas angka ini dianggap sangat janggal.</div>
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
                                    <div class="form-help">Default 3 hari mengikuti tampilan DSS.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Maksimum alert yang tampil</label>
                                    <input type="number" name="config[mortality][max_items]" class="form-control" min="1" max="10" value="{{ old('config.mortality.max_items', data_get($config, 'mortality.max_items')) }}">
                                    <div class="form-help">Batasi jumlah kartu agar mudah dipantau.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Batas warning (% populasi)</label>
                                    <input type="number" step="0.1" name="config[mortality][warning_pct]" class="form-control" min="0" max="100" value="{{ old('config.mortality.warning_pct', data_get($config, 'mortality.warning_pct')) }}">
                                    <div class="form-help">Persentase mortalitas yang menyalakan warna kuning.</div>
                                </div>
                                <div class="form-group compact">
                                    <label>Batas critical (% populasi)</label>
                                    <input type="number" step="0.1" name="config[mortality][critical_pct]" class="form-control" min="0" max="100" value="{{ old('config.mortality.critical_pct', data_get($config, 'mortality.critical_pct')) }}">
                                    <div class="form-help">Persentase mortalitas yang dianggap gawat.</div>
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
                    <div class="ml-note">
                        <h5>Apa saja yang otomatis dihitung?</h5>
                        <ul>
                            <li><strong>Produksi telur</strong> &ndash; forecasting harian dari histori pencatatan produksi.</li>
                            <li><strong>Kebutuhan pakan</strong> &ndash; estimasi konsumsi per batch dari data pakan 7 hari terakhir.</li>
                            <li><strong>Mortalitas / outbreak</strong> &ndash; deteksi anomali menggunakan log kematian.</li>
                            <li><strong>Optimasi harga</strong> &ndash; rekomendasi harga jual berdasarkan forecast produksi dan biaya pakan.</li>
                            <li><strong>Alert & explainability</strong> &ndash; rangkuman otomatis dari keempat analitik di atas.</li>
                        </ul>
                    </div>
                    <p class="form-help">Aktifkan mode ML, simpan, dan halaman DSS akan langsung menampilkan insight prediktif tersebut.</p>
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
