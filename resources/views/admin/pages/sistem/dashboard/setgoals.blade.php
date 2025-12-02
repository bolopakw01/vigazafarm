@extends('admin.layouts.app')

@section('title', 'Set Goals')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Sistem', 'link' => route('admin.sistem')],
        ['label' => 'Set Goals'],
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

    .set-goals-wrapper {
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

    .set-goals-header {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 25px;
    }

    .set-goals-title {
        font-size: 1.75rem;
        font-weight: 600;
        font-family: 'AlanSans', sans-serif;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .set-goals-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
        margin: 0;
    }

    .alert-box {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    .alert-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .goal-form {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 30px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .goal-form h4 {
        margin-bottom: 20px;
        color: #495057;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .goal-form h4 i {
        color: #4299e1;
    }

    .form-info {
        margin-bottom: 25px;
        padding: 15px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        border-left: 4px solid #4299e1;
    }

    .info-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .info-badge {
        display: flex;
        align-items: center;
        gap: 5px;
        background: #d1ecf1;
        color: #0c5460;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
    }

    .info-badge i {
        color: #17a2b8;
    }

    .info-content {
        font-size: 14px;
        color: #6c757d;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .form-group label i {
        color: #6c757d;
        font-size: 14px;
    }

    .required {
        color: #dc3545;
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-help {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: #6c757d;
        font-style: italic;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
        flex-wrap: wrap;
        gap: 15px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* Make color picker visually match other inputs */
    input[type="color"].form-control {
        width: 100%;
        box-sizing: border-box;
        height: 44px; /* similar to other inputs */
        padding: 6px 10px;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        background-color: transparent;
        -webkit-appearance: none;
        appearance: none;
    }

    /* For Firefox, style the color swatch inside the input */
    input[type="color"].form-control::-moz-color-swatch {
        border-radius: 4px;
        border: none;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .action-info {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 13px;
        font-style: italic;
    }

    .action-info i {
        color: #ffc107;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        margin-right: 10px;
    }

    .btn-primary {
        background: #2563eb;
        color: white;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #1e7e34;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #bd2130;
    }

    .goals-section {
        margin-top: 30px;
    }

    .goals-header-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f8f9fa;
    }

    .goals-header-info h4 {
        margin: 0;
        color: #495057;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .goals-header-info h4 i {
        color: #4299e1;
    }

    .goals-summary {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .goals-count {
        background: #e9ecef;
        color: #495057;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 13px;
        font-weight: 500;
    }

    .goals-help i {
        color: #6c757d;
        cursor: help;
        font-size: 16px;
    }

    .goal-item {
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: white;
        transition: all 0.2s ease;
        gap: 20px;
    }

    .goal-item:hover {
        background: #f8f9fa;
        transform: translateX(2px);
    }

    .goal-item:last-child {
        border-bottom: none;
    }

    /* Grid layout for goals list */
    #goalsContainer {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
        align-items: start;
    }

    /* Make each goal look like a card in grid */
    #goalsContainer .goal-item {
        border-bottom: none;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 18px;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        background: #fff;
    }

    /* Keep action buttons aligned to bottom */
    #goalsContainer .goal-actions {
        margin-top: 12px;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    @media (max-width: 575.98px) {
        #goalsContainer { grid-template-columns: 1fr; }
    }

    .goal-info {
        flex: 1;
        min-width: 0;
    }

    .goal-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .goal-title h5 {
        margin: 0;
        color: #495057;
        font-size: 16px;
        font-weight: 600;
        flex: 1;
        min-width: 0;
    }

    .goal-category-badge {
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .goal-details {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .goal-target {
        font-size: 14px;
        color: #495057;
    }

    .goal-category-desc {
        font-size: 12px;
        color: #6c757d;
    }

    .goal-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .empty-tips {
        margin-top: 15px;
        padding: 12px;
        background: #d1ecf1;
        border-radius: 6px;
        font-size: 13px;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .empty-state {
        text-align: center;
        padding: 50px 30px;
        color: #6c757d;
        background: white;
        border-radius: 8px;
        margin: 0;
        border: 1px solid #e9ecef;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        display: block;
        color: #cbd5e0;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .modal-info {
        font-size: 13px;
        color: #856404;
        line-height: 1.4;
    }

    .header-right {
        display: flex;
        align-items: center;
    }
</style>
@endpush

@section('content')
<div class="set-goals-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1 class="set-goals-title">
                    <i class="fas fa-bullseye"></i>
                    Set Goals Dashboard
                </h1>
                <p class="set-goals-subtitle">Sederhana: tambah goal lewat form di bawah, lalu simpan semua.</p>
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

            <div class="goal-form">
                <div class="form-info">
                    <div class="info-header">
                        <h4><i class="fas fa-plus"></i> Tambah Goal Baru</h4>
                        <div class="info-badge">
                            <i class="fas fa-info-circle"></i>
                            <span>Goals akan ditampilkan di dashboard untuk tracking progress</span>
                        </div>
                    </div>
                    <div class="info-content">
                        <p><strong>Cara Kerja:</strong> Setiap goal akan menampilkan progress pencapaian target di dashboard utama. Goals membantu Anda memantau KPI penting bisnis seperti penjualan, produksi, dan inventori.</p>
                    </div>
                </div>

                <form id="addGoalForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="goal_category">
                                <i class="fas fa-list"></i> Kategori
                                <span class="required">*</span>
                            </label>
                            <select id="goal_category" name="key" class="form-control" required>
                                <option value="">Pilih kategori</option>
                                <option value="produksi">⚙️ Produksi - Total batch/record produksi aktif</option>
                                <option value="penetasan">🐣 Penetasan - Total telur menetas/DOQ</option>
                                <option value="pembesaran">🌱 Pembesaran - Total batch pembesaran</option>
                                <option value="user">👥 User - Total pengguna terdaftar</option>
                            </select>
                            <small class="text-muted">Kategori lain disembunyikan agar sesuai dengan data proyek ini</small>
                        </div>

                        <div class="form-group">
                            <label for="goal_label">
                                <i class="fas fa-tag"></i> Nama Goal
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="goal_label" name="label" class="form-control"
                                   placeholder="Contoh: Produksi, Penetasan, Pembesaran, User"
                                   required maxlength="100">
                            <small class="form-help">Beri nama yang jelas dan deskriptif untuk goal Anda</small>
                        </div>

                        <div class="form-group">
                            <label for="goal_target">
                                <i class="fas fa-bullseye"></i> Target
                                <span class="required">*</span>
                            </label>
                            <input type="number" id="goal_target" name="target" class="form-control"
                                   placeholder="Masukkan angka target (contoh: 1000000 untuk penjualan)"
                                   min="1" required>
                            <small class="form-help">Target harus dalam angka positif. Progress akan dihitung otomatis berdasarkan data real-time.</small>
                        </div>

                        <div class="form-group">
                            <label for="goal_color">
                                <i class="fas fa-palette"></i> Warna (Opsional)
                            </label>
                            <input type="color" id="goal_color" name="color" class="form-control" value="#2563eb">
                            <small class="form-help">Warna untuk membedakan goal di dashboard. Default: Biru</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Goal
                        </button>
                        <div class="action-info">
                            <i class="fas fa-lightbulb"></i>
                            <span>Goal akan langsung muncul di dashboard setelah ditambahkan</span>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Daftar goals -->
            <div class="goals-section">
                <div class="goals-header-info">
                    <h4><i class="fas fa-list"></i> Daftar Goals</h4>
                    <div class="goals-summary">
                        <span class="goals-count" id="goalsCount">{{ count($goals ?? []) }} goals aktif</span>
                        <div class="goals-help">
                            <i class="fas fa-question-circle" title="Goals akan ditampilkan di dashboard dengan progress real-time"></i>
                        </div>
                    </div>
                </div>

                <div class="goals-list">
                    <div id="goalsContainer">
                        @if(isset($goals) && count($goals) > 0)
                            @foreach($goals as $index => $goal)
                                <div class="goal-item" data-index="{{ $index }}">
                                    <div class="goal-info">
                                        <div class="goal-title">
                                            <h5>{{ $goal['title'] ?? 'Unnamed Goal' }}</h5>
                                            <span class="goal-category-badge" style="background: {{ $goal['color'] ?? '#007bff' }};">
                                                {{ ucfirst(str_replace('_', ' ', $goal['title'] ?? 'unknown')) }}
                                            </span>
                                        </div>
                                        <div class="goal-details">
                                            <div class="goal-target">
                                                <strong>Target:</strong> {{ number_format($goal['target'] ?? 0) }}
                                            </div>
                                            <div class="goal-category-desc">
                                                @switch(strtolower($goal['title'] ?? ''))
                                                    @case('produksi')
                                                        <small>⚙️ Total batch produksi aktif</small>
                                                        @break
                                                    @case('user')
                                                        <small>👥 Total pengguna terdaftar</small>
                                                        @break
                                                    @case('penetasan')
                                                        <small>🐔 Total telur menetas bulan ini</small>
                                                        @break
                                                    @case('pembesaran')
                                                        <small>🌱 Total ayam pembesaran</small>
                                                        @break
                                                    @default
                                                        <small>📋 {{ $goal['unit'] ?? 'Unit' }}</small>
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                    <div class="goal-actions">
                                        <button class="btn btn-primary btn-sm edit-goal" data-index="{{ $index }}"
                                                title="Edit goal ini">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-goal" data-index="{{ $index }}"
                                                title="Hapus goal ini">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-bullseye"></i>
                                <h4>Belum ada goals</h4>
                                <p>Gunakan form di atas untuk menambah goal pertama Anda.</p>
                                <div class="empty-tips">
                                    <strong>Tips:</strong> Mulai dengan goal yang paling penting untuk bisnis Anda, seperti target penjualan bulanan atau jumlah produk aktif.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk edit goal -->
<div id="editGoalModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 550px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h4 style="margin: 0; color: #2d3748; font-size: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit" style="color: #4299e1;"></i> Edit Goal
            </h4>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 20px; color: #6c757d; cursor: pointer; padding: 5px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-info" style="margin: 20px 0; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #ffc107;">
            <i class="fas fa-info-circle" style="color: #ffc107; margin-right: 8px;"></i>
            <strong>Edit Goal:</strong> Anda dapat mengubah nama, kategori, target, dan warna goal. Pastikan kategori yang dipilih belum digunakan goal lain.
        </div>

        <form id="editGoalForm">
            <input type="hidden" id="edit_index" name="index">

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_goal_category">
                        <i class="fas fa-list"></i> Kategori
                        <span class="required">*</span>
                    </label>
                    <select id="edit_goal_category" name="key" class="form-control" required>
                        <option value="produk">Produk</option>
                        <option value="penjualan">Penjualan</option>
                        <option value="bahan_baku">Bahan Baku</option>
                        <option value="produksi">Produksi</option>
                        <option value="stok">Stok</option>
                        <option value="user">User</option>
                        <option value="penetasan">Penetasan</option>
                        <option value="pembesaran">Pembesaran</option>
                    </select>
                    <small class="form-help">Kategori yang sudah digunakan goal lain akan dinonaktifkan</small>
                </div>

                <div class="form-group">
                    <label for="edit_goal_label">
                        <i class="fas fa-tag"></i> Nama Goal
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="edit_goal_label" name="label" class="form-control"
                           placeholder="Contoh: Total Penjualan Bulanan, Jumlah Produk Aktif"
                           required maxlength="100">
                    <small class="form-help">Beri nama yang jelas dan deskriptif untuk goal Anda</small>
                </div>

                <div class="form-group">
                    <label for="edit_goal_target">
                        <i class="fas fa-bullseye"></i> Target
                        <span class="required">*</span>
                    </label>
                    <input type="number" id="edit_goal_target" name="target" class="form-control"
                           placeholder="Masukkan angka target"
                           min="1" required>
                    <small class="form-help">Target harus dalam angka positif</small>
                </div>

                <div class="form-group">
                    <label for="edit_goal_color">
                        <i class="fas fa-palette"></i> Warna (Opsional)
                    </label>
                    <input type="color" id="edit_goal_color" name="color" class="form-control">
                    <small class="form-help">Warna untuk membedakan goal di dashboard</small>
                </div>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 1px solid #e9ecef;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('bolopa/plugin/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
const titleToKeyMap = {
    'Produksi': 'produksi',
    'Penetasan': 'penetasan',
    'Pembesaran': 'pembesaran',
    'User': 'user'
};

const toNumber = (value, fallback = 0) => {
    const parsed = parseInt(value, 10);
    return Number.isFinite(parsed) ? parsed : fallback;
};

function normalizeGoalFromServer(goal) {
    const safeGoal = goal || {};
    const title = safeGoal.title || safeGoal.label || 'Goal';
    const derivedKey = safeGoal.key || titleToKeyMap[title] || title.toLowerCase().trim().replace(/\s+/g, '_');

    return {
        label: title,
        key: derivedKey,
        target: toNumber(safeGoal.target),
        current: toNumber(safeGoal.current),
        color: safeGoal.color || '#007bff',
        unit: safeGoal.unit || '',
        icon: safeGoal.icon || 'fas fa-chart-line'
    };
}

let goals = (@json($goals ?? [])).map(normalizeGoalFromServer);

function updateCategoryDropdowns() {
    const usedCategories = goals.map(goal => goal.key);
    const allCategories = [
        { value: 'produksi', label: '⚙️ Produksi - Total batch/record produksi aktif', shortLabel: 'Produksi' },
        { value: 'penetasan', label: '🐣 Penetasan - Total telur menetas/DOQ', shortLabel: 'Penetasan' },
        { value: 'pembesaran', label: '🌱 Pembesaran - Total batch pembesaran', shortLabel: 'Pembesaran' },
        { value: 'user', label: '👥 User - Total pengguna terdaftar', shortLabel: 'User' }
    ];

    // Update add goal form dropdown
    const addSelect = document.getElementById('goal_category');
    addSelect.innerHTML = '<option value="">Pilih kategori</option>';
    allCategories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.value;
        option.textContent = cat.label;
        if (usedCategories.includes(cat.value)) {
            option.disabled = true;
        }
        addSelect.appendChild(option);
    });

    // Update edit modal dropdown
    const editSelect = document.getElementById('edit_goal_category');
    editSelect.innerHTML = '';
    allCategories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.value;
        option.textContent = cat.shortLabel;
        editSelect.appendChild(option);
    });
}

function renderGoals() {
    const container = document.getElementById('goalsContainer');
    const goalsCountElement = document.getElementById('goalsCount');

    // Update goals count
    if (goalsCountElement) {
        goalsCountElement.textContent = `${goals.length} goals aktif`;
    }

    if (goals.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-bullseye"></i>
                <h4>Belum ada goals</h4>
                <p>Gunakan form di atas untuk menambah goal pertama Anda.</p>
                <div class="empty-tips">
                    <strong>Tips:</strong> Mulai dengan goal yang paling penting untuk bisnis Anda, seperti target penjualan bulanan atau jumlah produk aktif.
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = goals.map((goal, index) => {
        const categoryDescriptions = {
            'produksi': '⚙️ Total batch produksi aktif',
            'penetasan': '🐣 Total telur menetas/DOQ',
            'pembesaran': '🌱 Total batch pembesaran',
            'user': '👥 Total pengguna terdaftar'
        };

        const categoryDesc = categoryDescriptions[goal.key] || '📋 Kategori tidak dikenal';

        return `
        <div class="goal-item" data-index="${index}">
            <div class="goal-info">
                <div class="goal-title">
                    <h5>${goal.label || 'Unnamed Goal'}</h5>
                    <span class="goal-category-badge" style="background: ${goal.color || '#007bff'};">${goal.key ? goal.key.charAt(0).toUpperCase() + goal.key.slice(1).replace('_', ' ') : 'Unknown'}</span>
                </div>
                <div class="goal-details">
                    <div class="goal-target">
                        <strong>Target:</strong> ${goal.target ? parseInt(goal.target).toLocaleString('id-ID') : 0}
                    </div>
                    <div class="goal-category-desc">
                        <small>${categoryDesc}</small>
                    </div>
                </div>
            </div>
            <div class="goal-actions">
                <button class="btn btn-primary btn-sm edit-goal" data-index="${index}" title="Edit goal ini">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm delete-goal" data-index="${index}" title="Hapus goal ini">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    `}).join('');

    // Update category dropdowns after rendering goals
    updateCategoryDropdowns();

    // Re-attach event listeners
    attachEventListeners();
}

function attachEventListeners() {
    // Edit buttons
    document.querySelectorAll('.edit-goal').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            openEditModal(index);
        });
    });

    // Delete buttons
    document.querySelectorAll('.delete-goal').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            const goal = goals[index];

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus goal "${goal.label}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteGoal(index);
                }
            });
        });
    });
}

function openEditModal(index) {
    const goal = goals[index];
    if (!goal) return;

    // Store original category for this goal
    document.getElementById('edit_index').value = index;
    document.getElementById('edit_goal_label').value = goal.label || '';
    document.getElementById('edit_goal_target').value = goal.target || 0;
    document.getElementById('edit_goal_color').value = goal.color || '#007bff';

    // Update edit modal dropdown, allowing current goal's category
    const usedCategories = goals.map((g, i) => i !== index ? g.key : null).filter(key => key !== null);
    const allCategories = [
        { value: 'produksi', label: 'Produksi' },
        { value: 'penetasan', label: 'Penetasan' },
        { value: 'pembesaran', label: 'Pembesaran' },
        { value: 'user', label: 'User' }
    ];

    const editSelect = document.getElementById('edit_goal_category');
    editSelect.innerHTML = '';
    allCategories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.value;
        option.textContent = cat.label;
        // Disable only if category is used by other goals (not this one)
        if (usedCategories.includes(cat.value)) {
            option.disabled = true;
        }
        editSelect.appendChild(option);
    });

    // Set current value
    document.getElementById('edit_goal_category').value = goal.key || '';

    document.getElementById('editGoalModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editGoalModal').style.display = 'none';
}

function deleteGoal(index) {
    goals.splice(index, 1);
    renderGoals();
    saveGoals('Goal berhasil dihapus.');
}

function saveGoals(successMessage = 'Goals berhasil diperbarui.') {
    return fetch('{{ route("admin.sistem.dashboard.update") }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            goals: goals.map(goal => ({
                key: goal.key,
                title: goal.label,
                current: parseInt(goal.current ?? 0),
                target: Math.max(parseInt(goal.target ?? 1), 1),
                unit: goal.unit || '',
                color: goal.color || '#2563eb',
                icon: goal.icon || 'fas fa-chart-line'
            }))
        })
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 422) {
                return response.json().then(data => {
                    console.log('Validation error response:', data);
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join('\n');
                        Swal.fire('Validation Errors', errorMessages, 'error');
                    } else {
                        Swal.fire('Validation Failed', data.message || 'Unknown error', 'error');
                    }
                    throw new Error('Validation failed');
                });
            }
            throw new Error('Gagal menyimpan goals.');
        }
        return response.json();
    })
    .then(data => {
        if (data?.success) {
            if (Array.isArray(data.goals)) {
                goals = data.goals.map(normalizeGoalFromServer);
            }
            renderGoals();
            if (successMessage) {
                Swal.fire('Success', successMessage, 'success');
            }
            return true;
        }
        throw new Error('Respons tidak valid dari server.');
    })
    .catch(error => {
        console.error(error);
        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan goals.', 'error');
        return false;
    });
}

// Form submit handlers
document.getElementById('addGoalForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const selectedCategory = formData.get('key');

    // Check if category is already used
    const usedCategories = goals.map(goal => goal.key);
    if (usedCategories.includes(selectedCategory)) {
        Swal.fire('Error', 'Kategori ini sudah digunakan untuk goal lain. Pilih kategori yang berbeda.', 'error');
        return;
    }

    const newGoal = {
        label: formData.get('label'),
        key: formData.get('key'),
        target: parseInt(formData.get('target')),
        color: formData.get('color') || '#007bff',
        current: 0,
        unit: ''
    };

    goals.push(newGoal);
    renderGoals();
    saveGoals('Goal baru telah berhasil ditambahkan.').then(success => {
        if (success) {
            this.reset();
        }
    });
});

document.getElementById('editGoalForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const index = parseInt(formData.get('index'));
    const selectedCategory = formData.get('key');

    // Check if category is already used by other goals (excluding current goal)
    const usedCategories = goals.map((goal, i) => i !== index ? goal.key : null).filter(key => key !== null);
    if (usedCategories.includes(selectedCategory)) {
        Swal.fire('Error', 'Kategori ini sudah digunakan untuk goal lain. Pilih kategori yang berbeda.', 'error');
        return;
    }

    goals[index] = {
        ...goals[index],
        label: formData.get('label'),
        key: formData.get('key'),
        target: parseInt(formData.get('target')),
        color: formData.get('color') || '#007bff'
    };

    renderGoals();
    saveGoals('Goal telah berhasil diperbarui.').then(success => {
        if (success) {
            closeEditModal();
            Swal.fire({
                title: 'Berhasil!',
                text: 'Goal telah berhasil diperbarui.',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    renderGoals();

    // Show success message from session if exists
    @if(session('success'))
        Swal.fire('Success', '{{ session('success') }}', 'success');
    @endif
});
</script>
@endpush

@endsection
