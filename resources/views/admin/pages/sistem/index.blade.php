@extends('admin.layouts.app')

@section('title', 'Manajemen Sistem')

@push('styles')
<style>
    @font-face {
        font-family: 'AlanSans';
        src: url('{{ asset("bolopa/font/AlanSans-VariableFont_wght.ttf") }}') format('truetype');
        font-weight: 100 900;
        font-display: swap;
    }

    .sistem-container {
        padding: 20px;
    }

    .sistem-section {
        margin-bottom: 40px;
    }

    .sistem-section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'AlanSans', sans-serif;
    }

    .sistem-section-title::before {
        content: '';
        width: 4px;
        height: 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    .dashboard-settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .goals-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border: 1px solid #e0e0e0;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        aspect-ratio: 1 / 1; /* Make it square */
        max-width: 250px;
        margin: 0 auto;
    }

    .goals-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .goals-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin: 15px 0 10px 0;
        font-family: 'AlanSans', sans-serif;
    }

    .goals-card-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        margin: 0 auto;
    }

    .goals-card-icon.production { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .goals-card-icon.revenue { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .goals-card-icon.efficiency { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .goals-card-icon.quality { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

    .goals-description {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 20px;
        line-height: 1.4;
        flex: 1;
        font-family: 'AlanSans', sans-serif;
    }

    .goals-card-actions {
        width: 100%;
        margin-top: auto;
    }

    .goals-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        font-family: 'AlanSans', sans-serif;
    }

    .goals-btn-primary {
        background: #2563eb;
        color: white;
    }

    .goals-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-settings-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .goals-card {
            max-width: 100%;
            padding: 20px;
        }

        .sistem-container {
            padding: 15px;
        }

        .sistem-section-title {
            font-size: 1.3rem;
        }

        .goals-card-icon {
            width: 45px;
            height: 45px;
            font-size: 18px;
        }

        .goals-card-title {
            font-size: 1rem;
        }

        .goals-description {
            font-size: 0.8rem;
        }

        .goals-btn {
            padding: 10px 20px;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')
<div class="sistem-container">
    <!-- Setting Dashboard Section -->
    <div class="sistem-section">
        <h2 class="sistem-section-title">
            <i class="fas fa-chart-line"></i>
            Pengaturan Dashboard
        </h2>

        <div class="dashboard-settings-grid">
            <!-- Set Goals Card -->
            <div class="goals-card">
                <div class="goals-card-icon production">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3 class="goals-card-title">Goals</h3>
                <div class="goals-description">
                    Atur target dan goals untuk dashboard utama. Tetapkan target penjualan, produksi, dan pesanan bulanan.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.dashboard') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-cog"></i>
                        Set Goals
                    </a>
                </div>
            </div>
            <!-- Feed/Vitamin Settings Card -->
            <div class="goals-card">
                <div class="goals-card-icon efficiency">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3 class="goals-card-title">Pakan & Vitamin</h3>
                <div class="goals-description">
                    Buat daftar pakan dan vitamin lengkap dengan harga serta satuan untuk dipakai di form produksi.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.pakanvitamin') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-sliders-h"></i>
                        Set Pakan/Vitamin
                    </a>
                </div>
            </div>
            <!-- Matrix Settings Card -->
            <div class="goals-card">
                <div class="goals-card-icon revenue">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="goals-card-title">Set Matriks</h3>
                <div class="goals-description">
                    Tetapkan target pendapatan, pengeluaran, dan laba agar KPI dashboard keuangan terupdate otomatis.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.matriks') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-sliders-h"></i>
                        Set Matriks
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Remove old JavaScript functions that are no longer needed
</script>
@endpush

