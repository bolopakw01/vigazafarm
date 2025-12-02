@extends('admin.layouts.app')

@section('title', 'Pengaturan IoT')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Sistem', 'link' => route('admin.sistem')],
        ['label' => 'Pengaturan IoT'],
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

    .iot-wrapper {
        padding: 20px;
    }

    .card {
        background: white;
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

    .header-left {
        flex: 1;
    }

    .iot-title {
        font-size: 24px;
        font-weight: 700;
        color: #2d3748;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .iot-subtitle {
        color: #718096;
        font-size: 14px;
        margin: 0;
        line-height: 1.5;
    }

    .header-right {
        flex-shrink: 0;
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
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
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
        background: #d1ecf1;
        border: 1px solid #bee5eb;
        color: #0c5460;
    }

    .alert-error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .mode-selector h4 {
        margin-bottom: 20px;
        color: #495057;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mode-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .mode-option {
        padding: 20px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }

    .mode-option:hover {
        border-color: #dee2e6;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .mode-option.selected {
        border-color: #007bff;
        background: #f8f9ff;
        box-shadow: 0 2px 12px rgba(0, 123, 255, 0.15);
    }

    .mode-icon {
        font-size: 24px;
        margin-bottom: 12px;
    }

    .mode-title {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .mode-description {
        font-size: 14px;
        color: #718096;
        line-height: 1.5;
    }

    .simple-mode-info {
        background: #d1ecf1;
        border: 1px solid #bee5eb;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .simple-mode-info h5 {
        margin: 0 0 10px 0;
        color: #0c5460;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.4;
    }

    .api-mode-settings {
        display: none;
    }

    .api-mode-settings.show {
        display: block;
    }

    .settings-section {
        margin-bottom: 32px;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 24px;
    }

    .settings-section h4 {
        margin-bottom: 20px;
        color: #495057;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .required {
        color: #dc3545;
    }

    .form-control {
        padding: 12px 16px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .form-help {
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .action-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #6c757d;
    }

    .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .status-card {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .status-card i {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .status-card h5 {
        margin: 0 0 5px 0;
        color: #2d3748;
        font-size: 16px;
    }

    .status-card p {
        margin: 0 0 5px 0;
        color: #718096;
        font-size: 14px;
    }

    .status-card small {
        color: #a0aec0;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            gap: 16px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .mode-options {
            grid-template-columns: 1fr;
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
@php
    $mode = old('mode', $settings['mode'] ?? 'simple');
    $interval = (int) old('update_interval', $settings['update_interval'] ?? 60);
@endphp

<div class="iot-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1 class="iot-title">
                    <i class="fas fa-satellite-dish"></i>
                    Pengaturan IoT - Suhu & Kelembapan
                </h1>
                <p class="iot-subtitle">Kelola integrasi perangkat IoT untuk monitoring suhu dan kelembapan kandang secara real-time.</p>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.sistem') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert-box alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert-box alert-error">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin:8px 0 0 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.sistem.iot.update') }}" method="POST" id="iotSettingsForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="mode" id="iotModeInput" value="{{ $mode }}">

                <!-- Mode Selection -->
                <div class="mode-selector settings-section">
                    <h4><i class="fas fa-toggle-on"></i> Pilih Mode Operasi</h4>
                    <div class="mode-options">
                        <div class="mode-option {{ $mode === 'simple' ? 'selected' : '' }}" data-mode="simple">
                            <i class="fas fa-magic mode-icon" style="color: #4299e1;"></i>
                            <div class="mode-title">Mode Sederhana</div>
                            <div class="mode-description">
                                Gunakan simulasi data untuk testing dan development. Data suhu dan kelembapan akan di-generate secara otomatis.
                            </div>
                        </div>
                        <div class="mode-option {{ $mode === 'iot' ? 'selected' : '' }}" data-mode="iot">
                            <i class="fas fa-plug mode-icon" style="color: #48bb78;"></i>
                            <div class="mode-title">Mode API IoT</div>
                            <div class="mode-description">
                                Integrasi dengan perangkat IoT real melalui API. Konfigurasi endpoint dan autentikasi diperlukan.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Simple Mode Info -->
                <div id="simpleModeInfo" class="simple-mode-info settings-section" style="display: {{ $mode === 'simple' ? 'block' : 'none' }};">
                    <h5><i class="fas fa-info-circle"></i> Mode Sederhana Aktif</h5>
                    <p>Sistem akan menggunakan data simulasi untuk suhu (25-35°C) dan kelembapan (40-80%) yang di-generate secara acak. Data akan diperbarui setiap 30 detik untuk testing dashboard.</p>
                </div>

                <!-- API Mode Settings -->
                <div id="apiModeSettings" class="api-mode-settings settings-section {{ $mode === 'iot' ? 'show' : '' }}">
                    <h4><i class="fas fa-cogs"></i> Konfigurasi API IoT</h4>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="api_endpoint">
                                <i class="fas fa-link"></i> API Endpoint
                                <span class="required">*</span>
                            </label>
                            <input type="url" id="api_endpoint" name="api_endpoint" class="form-control"
                                   placeholder="https://api.iot-provider.com/v1/sensors"
                                   value="{{ old('api_endpoint', $settings['api_endpoint'] ?? '') }}">
                            <small class="form-help">URL endpoint API untuk mengambil data sensor IoT</small>
                        </div>

                        <div class="form-group">
                            <label for="api_key">
                                <i class="fas fa-key"></i> API Key
                                <span class="required">*</span>
                            </label>
                            <input type="password" id="api_key" name="api_key" class="form-control"
                                   placeholder="Masukkan API key untuk autentikasi"
                                   value="{{ old('api_key', $settings['api_key'] ?? '') }}">
                            <small class="form-help">Token autentikasi untuk mengakses API IoT</small>
                        </div>

                        <div class="form-group">
                            <label for="device_id">
                                <i class="fas fa-microchip"></i> Device ID
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="device_id" name="device_id" class="form-control"
                                   placeholder="sensor-kandang-001"
                                   value="{{ old('device_id', $settings['device_id'] ?? '') }}">
                            <small class="form-help">ID unik perangkat sensor IoT</small>
                        </div>

                        <div class="form-group">
                            <label for="update_interval">
                                <i class="fas fa-clock"></i> Interval Update
                                <span class="required">*</span>
                            </label>
                            <select id="update_interval" name="update_interval" class="form-control">
                                <option value="30" {{ $interval === 30 ? 'selected' : '' }}>30 detik</option>
                                <option value="60" {{ $interval === 60 ? 'selected' : '' }}>1 menit</option>
                                <option value="300" {{ $interval === 300 ? 'selected' : '' }}>5 menit</option>
                                <option value="600" {{ $interval === 600 ? 'selected' : '' }}>10 menit</option>
                            </select>
                            <small class="form-help">Frekuensi pengambilan data dari sensor</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="action-info">
                        <i class="fas fa-lightbulb"></i>
                        <span>Konfigurasi akan langsung aktif setelah disimpan</span>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>

            <!-- Current Status -->
            <div class="settings-section">
                <h4><i class="fas fa-chart-line"></i> Status Monitoring</h4>

                <div class="status-grid">
                    <div class="status-card">
                        <i class="fas fa-thermometer-half" style="color: #e53e3e;"></i>
                        <h5>Suhu</h5>
                        <p>-- °C</p>
                        <small>Belum terhubung</small>
                    </div>

                    <div class="status-card">
                        <i class="fas fa-tint" style="color: #3182ce;"></i>
                        <h5>Kelembapan</h5>
                        <p>-- %</p>
                        <small>Belum terhubung</small>
                    </div>

                    <div class="status-card">
                        <i class="fas fa-clock" style="color: #38a169;"></i>
                        <h5>Terakhir Update</h5>
                        <p>--</p>
                        <small>Tidak ada data</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeOptions = document.querySelectorAll('.mode-option');
    const simpleModeInfo = document.getElementById('simpleModeInfo');
    const apiModeSettings = document.getElementById('apiModeSettings');
    const modeInput = document.getElementById('iotModeInput');

    const applyModeUI = (mode) => {
        if (!simpleModeInfo || !apiModeSettings) {
            return;
        }

        if (mode === 'simple') {
            simpleModeInfo.style.display = 'block';
            apiModeSettings.classList.remove('show');
        } else {
            simpleModeInfo.style.display = 'none';
            apiModeSettings.classList.add('show');
        }
    };

    modeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            modeOptions.forEach(opt => opt.classList.remove('selected'));

            // Add selected class to clicked option
            this.classList.add('selected');

            const mode = this.dataset.mode;

            if (modeInput) {
                modeInput.value = mode;
            }

            applyModeUI(mode);
        });
    });

    // Initialize UI based on current mode
    applyModeUI(modeInput ? modeInput.value : 'simple');
});

const triggerFlashToast = (icon, title, message, timer = 3500) => {
    if (!message) {
        return;
    }

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon,
        title,
        text: message,
        showConfirmButton: false,
        timer,
        timerProgressBar: true,
        didOpen: toast => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
};

@if(session('success'))
triggerFlashToast('success', 'Berhasil!', @json(session('success')));
@endif

@if(session('error'))
triggerFlashToast('error', 'Gagal!', @json(session('error')), 4500);
@endif
</script>
@endpush

@endsection

