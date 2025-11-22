@php
    $tabVariant = $tabVariant ?? 'puyuh';
    $isTelurVariant = $tabVariant === 'telur';
    $tabs = $isTelurVariant
        ? [
            ['id' => 'telur', 'label' => 'Telur'],
            ['id' => 'tray', 'label' => 'Tray'],
            ['id' => 'penjualan', 'label' => 'Penjualan'],
            ['id' => 'laporan', 'label' => 'Laporan'],
        ]
        : [
            ['id' => 'telur', 'label' => 'Telur'],
            ['id' => 'pakan', 'label' => 'Pakan'],
            ['id' => 'vitamin', 'label' => 'Vitamin'],
            ['id' => 'kematian', 'label' => 'Kematian'],
            ['id' => 'laporan', 'label' => 'Laporan'],
        ];

    $trayEntries = collect($trayEntries ?? []);
    $trayEggsPerTray = $eggsPerTray ?? 30;
    $formatTrayNumber = fn ($value, $decimals = 0) => number_format((float) ($value ?? 0), $decimals, ',', '.');
@endphp

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.produksi.laporan.store', $produksi) }}" method="POST" id="pencatatanForm" data-generate-url="{{ route('admin.produksi.laporan.generate-summary', $produksi) }}">
            @csrf
            <input type="hidden" name="active_tab" id="activeTabInput" value="telur">

            <ul class="nav nav-tabs mb-3" id="pencatatanTabs" role="tablist">
                @foreach ($tabs as $index => $tab)
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $index === 0 ? 'active' : '' }}"
                            id="{{ $tab['id'] }}-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#{{ $tab['id'] }}"
                            type="button"
                            role="tab"
                            aria-controls="{{ $tab['id'] }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        >
                            {{ $tab['label'] }}
                        </button>
                    </li>
                @endforeach
            </ul>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-soft mb-3">
                    <strong>Periksa kembali isian berikut:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="tab-content" id="pencatatanTabsContent">
                <div class="tab-pane fade show active" id="telur" role="tabpanel" aria-labelledby="telur-tab">
                    <div class="record-section">
                        <h6><i class="fa-solid fa-egg"></i> Catat Hasil Telur</h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $defaultTanggal }}" required>
                                </div>
                                <div class="form-hint">Pilih tanggal pencatatan (format YYYY-MM-DD).</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="produksi_telur" class="form-label">Jumlah Telur (butir)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-egg"></i></span>
                                    <input type="number" name="produksi_telur" id="produksi_telur" class="form-control" min="0" value="{{ $defaultProduksiTelur }}">
                                </div>
                                <div class="form-hint">Masukkan total telur yang dipanen hari ini.</div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($isTelurVariant)
                    <div class="tab-pane fade" id="tray" role="tabpanel" aria-labelledby="tray-tab">
                        <div class="record-section">
                            <h6><i class="fa-solid fa-layer-group"></i> Daftar Tray Otomatis</h6>
                            <p class="text-muted small mb-3 mb-md-2">
                                Daftar ini terbentuk otomatis dari setiap pencatatan Telur. Konversi menggunakan rasio
                                <strong>{{ $trayEggsPerTray }}</strong> butir per tray.
                            </p>

                            @if ($trayEntries->isNotEmpty())
                                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
                                    <div class="small text-muted">Pilih tampilan untuk meninjau data tray.</div>
                                    <div class="btn-group btn-group-sm tray-view-toggle" role="group" aria-label="Mode Tampilan Tray">
                                        <button type="button" class="btn btn-outline-primary active" data-tray-view="list">
                                            <i class="fa-solid fa-list me-1"></i>List
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-tray-view="grid">
                                            <i class="fa-solid fa-table-cells-large me-1"></i>Grid
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
                                    <div class="input-group input-group-sm" style="max-width: 250px;">
                                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                                        <input type="text" class="form-control" id="traySearch" placeholder="Cari...">
                                    </div>
                                    <div class="input-group input-group-sm" style="max-width: 200px;">
                                        <span class="input-group-text"><i class="fa-solid fa-sort"></i></span>
                                        <select class="form-select" id="traySort">
                                            <option value="date-desc">Tanggal Terbaru</option>
                                            <option value="date-asc">Tanggal Terlama</option>
                                            <option value="name-asc">Nama A-Z</option>
                                            <option value="name-desc">Nama Z-A</option>
                                            <option value="jumlah-desc">Jumlah Terbanyak</option>
                                            <option value="jumlah-asc">Jumlah Tersedikit</option>
                                        </select>
                                    </div>
                                    <span class="text-muted small">Total: <span id="trayCount">{{ $trayEntries->count() }}</span> tray</span>
                                </div>

                                <div id="trayListView" class="tray-view">
                                    <div class="tray-list-cards">
                                        @foreach ($trayEntries as $entry)
                                            <div class="tray-card" data-tray-id="{{ $entry['id'] }}">
                                                <div class="tray-card-header">
                                                    <div class="tray-card-title-section">
                                                        <div class="tray-card-title">
                                                            <i class="fa-solid fa-layer-group me-2"></i>
                                                            Tray - {{ $entry['tanggal'] }}
                                                        </div>
                                                        <div class="tray-card-updated">
                                                            <small class="text-light">Diupdate: {{ $entry['dibuat_pada'] }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="tray-card-actions">
                                                        <button type="button" class="btn btn-sm btn-outline-light me-1 tray-edit-btn" data-id="{{ $entry['id'] }}">
                                                            <i class="fa-solid fa-edit"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary me-1 tray-save-btn d-none" data-id="{{ $entry['id'] }}">
                                                            <i class="fa-solid fa-save"></i> Save
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary me-1 tray-cancel-btn d-none" data-id="{{ $entry['id'] }}">
                                                            <i class="fa-solid fa-times"></i> Cancel
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger tray-delete-btn" data-id="{{ $entry['id'] }}">
                                                            <i class="fa-solid fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="tray-card-body">
                                                    <div class="tray-card-content d-flex gap-2">
                                                        <div class="tray-field flex-fill">
                                                            <label class="tray-label">Nama Tray</label>
                                                            <input type="text" class="form-control form-control-sm tray-name-input" value="Tray {{ $entry['tanggal'] }}" data-original="Tray {{ $entry['tanggal'] }}" disabled>
                                                        </div>
                                                        <div class="tray-field flex-fill">
                                                            <label class="tray-label">Jumlah Telur</label>
                                                            <input type="number" class="form-control form-control-sm tray-telur-input" value="{{ $entry['jumlah_telur'] }}" data-original="{{ $entry['jumlah_telur'] }}" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="tray-keterangan-field d-none mt-2">
                                                        <label class="tray-label">Keterangan</label>
                                                        <textarea class="form-control form-control-sm tray-keterangan-input" rows="2" placeholder="Opsional: tambahkan catatan..." data-original=""></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="trayGridView" class="tray-view d-none">
                                    <div class="tray-grid">
                                        @foreach ($trayEntries as $entry)
                                            <div class="tray-card-grid" data-tray-id="{{ $entry['id'] }}">
                                                <div class="egg-background">
                                                    <i class="fa-solid fa-egg egg-icon"></i>
                                                    <div class="egg-number">{{ $entry['jumlah_telur'] }}</div>
                                                </div>
                                                <div class="tray-date">{{ $entry['tanggal'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light border d-flex align-items-start gap-2" role="alert">
                                    <i class="fa-solid fa-circle-info mt-1 text-info"></i>
                                    <div>
                                        Belum ada pencatatan Telur sehingga daftar tray masih kosong.
                                        Tambahkan produksi telur terlebih dahulu untuk melihat estimasi tray di sini.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="tab-pane fade" id="penjualan" role="tabpanel" aria-labelledby="penjualan-tab">
                        <div class="record-section">
                            <h6><i class="fa-solid fa-cash-register"></i> Catat Penjualan Telur</h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="tanggal_penjualan" class="form-label">Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        <input type="date" name="tanggal" id="tanggal_penjualan" class="form-control" value="{{ $defaultTanggal }}" required>
                                    </div>
                                    <div class="form-hint">Tanggal transaksi penjualan.</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="penjualan_telur_butir" class="form-label">Telur Terjual (butir)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-egg"></i></span>
                                        <input type="number" name="penjualan_telur_butir" id="penjualan_telur_butir" class="form-control" min="0" value="{{ $defaultPenjualanTelur }}">
                                    </div>
                                    <div class="form-hint">Masukkan total telur yang keluar hari ini.</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="pendapatan_harian" class="form-label">Pendapatan (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-coins"></i></span>
                                        <input type="number" step="0.01" name="pendapatan_harian" id="pendapatan_harian" class="form-control" min="0" value="{{ $defaultPendapatan }}">
                                    </div>
                                    <div class="form-hint">Nilai pemasukan tunai/non-tunai.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="tab-pane fade" id="pakan" role="tabpanel" aria-labelledby="pakan-tab">
                        <div class="record-section">
                            <h6><i class="fa-solid fa-bowl-food"></i> Catat Pemakaian Pakan</h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="tanggal_pakan" class="form-label">Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        <input type="date" name="tanggal" id="tanggal_pakan" class="form-control" value="{{ $defaultTanggal }}" required>
                                    </div>
                                    <div class="form-hint">Pilih tanggal pencatatan pakan (format YYYY-MM-DD).</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="konsumsi_pakan_kg" class="form-label">Pakan Terpakai (kg)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-wheat-awn"></i></span>
                                        <input type="number" step="0.01" name="konsumsi_pakan_kg" id="konsumsi_pakan_kg" class="form-control" min="0" value="{{ $defaultKonsumsiPakan }}">
                                    </div>
                                    <div class="form-hint">Total pakan yang diberikan untuk seluruh kandang.</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="sisa_pakan_kg" class="form-label">Sisa Pakan (kg)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-box"></i></span>
                                        <input type="number" step="0.01" name="sisa_pakan_kg" id="sisa_pakan_kg" class="form-control" min="0" value="{{ $defaultSisaPakan }}">
                                    </div>
                                    <div class="form-hint">Perkirakan stok pakan setelah distribusi hari ini.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="vitamin" role="tabpanel" aria-labelledby="vitamin-tab">
                        <div class="record-section">
                            <h6><i class="fa-solid fa-capsules"></i> Catat Pemakaian Vitamin</h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="tanggal_vitamin" class="form-label">Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        <input type="date" name="tanggal" id="tanggal_vitamin" class="form-control" value="{{ $defaultTanggal }}" required>
                                    </div>
                                    <div class="form-hint">Pilih tanggal pencatatan vitamin (format YYYY-MM-DD).</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="vitamin_terpakai" class="form-label">Vitamin Terpakai (L)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-flask"></i></span>
                                        <input type="number" step="0.001" name="vitamin_terpakai" id="vitamin_terpakai" class="form-control" min="0" value="{{ $defaultVitaminTerpakai }}">
                                    </div>
                                    <div class="form-hint">Jumlah vitamin yang digunakan hari ini.</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="sisa_vitamin_liter" class="form-label">Sisa Vitamin (L)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-box-archive"></i></span>
                                        <input type="number" step="0.001" name="sisa_vitamin_liter" id="sisa_vitamin_liter" class="form-control" min="0" value="{{ $defaultSisaVitamin }}">
                                    </div>
                                    <div class="form-hint">Catat stok vitamin cair yang tersisa.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="kematian" role="tabpanel" aria-labelledby="kematian-tab">
                        <div class="record-section">
                            <h6><i class="fa-solid fa-skull-crossbones"></i> Catat Kematian</h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="tanggal_kematian" class="form-label">Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        <input type="date" name="tanggal" id="tanggal_kematian" class="form-control" value="{{ $defaultTanggal }}" required>
                                    </div>
                                    <div class="form-hint">Pilih tanggal kejadian.</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="jumlah_kematian" class="form-label">Jumlah (ekor)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-dove"></i></span>
                                        <input type="number" name="jumlah_kematian" id="jumlah_kematian" class="form-control" min="1" placeholder="Masukkan jumlah" value="{{ old('jumlah_kematian') }}">
                                    </div>
                                    <div class="form-hint">Masukkan jumlah kematian pada hari ini (ekor).</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="jenis_kelamin_kematian" class="form-label">Jenis Kelamin</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-venus-mars"></i></span>
                                        <select name="jenis_kelamin_kematian" id="jenis_kelamin_kematian" class="form-select">
                                            <option value="" disabled {{ old('jenis_kelamin_kematian') ? '' : 'selected' }}>Pilih jenis kelamin</option>
                                            <option value="jantan" {{ old('jenis_kelamin_kematian') === 'jantan' ? 'selected' : '' }}>Jantan</option>
                                            <option value="betina" {{ old('jenis_kelamin_kematian') === 'betina' ? 'selected' : '' }}>Betina</option>
                                        </select>
                                    </div>
                                    <div class="form-hint">Pilih jenis kelamin untuk pencatatan kematian.</div>
                                </div>
                                <div class="col-12">
                                    <label for="keterangan_kematian" class="form-label">Keterangan</label>
                                    <textarea name="keterangan_kematian" id="keterangan_kematian" rows="3" class="form-control" placeholder="opsional"></textarea>
                                    <div class="form-hint">Catatan singkat (opsional): mis. gejala, dugaan penyebab.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="tab-pane fade" id="laporan" role="tabpanel" aria-labelledby="laporan-tab">
                    <div class="record-section">
                        <h6><i class="fa-solid fa-file-lines"></i> Catat Laporan</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="tanggal_laporan" class="form-label">Tanggal</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                    <input type="date" name="tanggal" id="tanggal_laporan" class="form-control" value="{{ $defaultTanggal }}" required>
                                </div>
                                <div class="form-hint">Pilih tanggal laporan.</div>
                            </div>
                            <div class="col-12">
                                <label for="catatan_kejadian" class="form-label">Catatan Laporan</label>
                                <textarea name="catatan_kejadian" id="catatan_kejadian" rows="4" class="form-control" placeholder="Tulis catatan laporan di sini...">{{ $defaultCatatan }}</textarea>
                                <div class="form-hint">Catatan singkat atau detail kejadian.</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="alert alert-info" role="alert">
                                <i class="fa-solid fa-lightbulb me-2"></i>
                                <strong>Tip:</strong> Klik tombol Generate Laporan untuk membuat laporan otomatis berdasarkan data pakan dan kematian hari ini, lalu sesuaikan jika perlu sebelum menyimpan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary d-none" id="generateLaporanBtn">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                </button>
                <button type="submit" class="btn btn-primary btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </form>

        <link rel="stylesheet" href="/bolopa/css/admin-show-telur-produksi.css">

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabs = document.querySelectorAll('#pencatatanTabs .nav-link');
                const saveButton = document.querySelector('.btn-save');
                const generateButton = document.getElementById('generateLaporanBtn');
                const catatanField = document.getElementById('catatan_kejadian');
                const form = document.getElementById('pencatatanForm');
                const activeTabInput = document.getElementById('activeTabInput');
                const generateUrl = form?.dataset?.generateUrl;
                const existingEntriesByTab = @json($existingEntriesByTab);
                const trayListView = document.getElementById('trayListView');
                const trayGridView = document.getElementById('trayGridView');
                const trayViewToggleButtons = document.querySelectorAll('[data-tray-view]');
                const originalTrayEntries = @json($trayEntries);
                let currentFilteredEntries = [...originalTrayEntries];

                function filterTrays(searchTerm) {
                    return originalTrayEntries.filter(entry => {
                        const name = entry.nama_tray || `Tray ${entry.tanggal}`;
                        return entry.tanggal.includes(searchTerm) || name.toLowerCase().includes(searchTerm.toLowerCase()) || entry.jumlah_telur.toString().includes(searchTerm);
                    });
                }

                function sortTrays(entries, sortBy) {
                    return [...entries].sort((a, b) => {
                        switch(sortBy) {
                            case 'date-asc':
                                return new Date(a.tanggal) - new Date(b.tanggal);
                            case 'date-desc':
                                return new Date(b.tanggal) - new Date(a.tanggal);
                            case 'name-asc':
                                const nameA_asc = a.nama_tray || `Tray ${a.tanggal}`;
                                const nameB_asc = b.nama_tray || `Tray ${b.tanggal}`;
                                return nameA_asc.localeCompare(nameB_asc);
                            case 'name-desc':
                                const nameA_desc = a.nama_tray || `Tray ${a.tanggal}`;
                                const nameB_desc = b.nama_tray || `Tray ${b.tanggal}`;
                                return nameB_desc.localeCompare(nameA_desc);
                            case 'jumlah-asc':
                                return a.jumlah_telur - b.jumlah_telur;
                            case 'jumlah-desc':
                                return b.jumlah_telur - a.jumlah_telur;
                            default:
                                return 0;
                        }
                    });
                }

                function renderTrays(filteredEntries) {
                    // Generate list HTML
                    let listHtml = '<div class="tray-list-cards">';
                    filteredEntries.forEach(entry => {
                        const name = entry.nama_tray || `Tray ${entry.tanggal}`;
                        listHtml += `
                            <div class="tray-card" data-tray-id="${entry.id}">
                                <div class="tray-card-header">
                                    <div class="tray-card-title-section">
                                        <div class="tray-card-title">
                                            <i class="fa-solid fa-layer-group me-2"></i>
                                            ${name}
                                        </div>
                                        <div class="tray-card-updated">
                                            <small class="text-light">Diupdate: ${entry.dibuat_pada}</small>
                                        </div>
                                    </div>
                                    <div class="tray-card-actions">
                                        <button type="button" class="btn btn-sm btn-outline-light me-1 tray-edit-btn" data-id="${entry.id}">
                                            <i class="fa-solid fa-edit"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary me-1 tray-save-btn d-none" data-id="${entry.id}">
                                            <i class="fa-solid fa-save"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary me-1 tray-cancel-btn d-none" data-id="${entry.id}">
                                            <i class="fa-solid fa-times"></i> Cancel
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger tray-delete-btn" data-id="${entry.id}">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                                <div class="tray-card-body">
                                    <div class="tray-card-content d-flex gap-2">
                                        <div class="tray-field flex-fill">
                                            <label class="tray-label">Nama Tray</label>
                                            <input type="text" class="form-control form-control-sm tray-name-input" value="${name}" data-original="${name}" disabled>
                                        </div>
                                        <div class="tray-field flex-fill">
                                            <label class="tray-label">Jumlah Telur</label>
                                            <input type="number" class="form-control form-control-sm tray-telur-input" value="${entry.jumlah_telur}" data-original="${entry.jumlah_telur}" disabled>
                                        </div>
                                    </div>
                                    <div class="tray-keterangan-field d-none mt-2">
                                        <label class="tray-label">Keterangan</label>
                                        <textarea class="form-control form-control-sm tray-keterangan-input" rows="2" placeholder="Opsional: tambahkan catatan..." data-original=""></textarea>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    listHtml += '</div>';
                    trayListView.innerHTML = listHtml;

                    // Generate grid HTML
                    let gridHtml = '<div class="tray-grid">';
                    filteredEntries.forEach(entry => {
                        gridHtml += `
                            <div class="tray-card-grid" data-tray-id="${entry.id}">
                                <div class="egg-background">
                                    <i class="fa-solid fa-egg egg-icon"></i>
                                    <div class="egg-number">${entry.jumlah_telur}</div>
                                </div>
                                <div class="tray-date">${entry.tanggal}</div>
                            </div>
                        `;
                    });
                    gridHtml += '</div>';
                    trayGridView.innerHTML = gridHtml;

                    // Update count
                    document.getElementById('trayCount').textContent = filteredEntries.length;
                }

                // Initial render
                renderTrays(sortTrays(originalTrayEntries, 'date-desc'));

                // Search and sort listeners
                document.getElementById('traySearch').addEventListener('input', function() {
                    const searchTerm = this.value;
                    const filtered = filterTrays(searchTerm);
                    const sorted = sortTrays(filtered, document.getElementById('traySort').value);
                    renderTrays(sorted);
                });

                document.getElementById('traySort').addEventListener('change', function() {
                    const sortBy = this.value;
                    const searchTerm = document.getElementById('traySearch').value;
                    const filtered = filterTrays(searchTerm);
                    const sorted = sortTrays(filtered, sortBy);
                    renderTrays(sorted);
                });

                const tabLabels = {
                    telur: 'Telur',
                    tray: 'Tray',
                    penjualan: 'Penjualan',
                    pakan: 'Pakan',
                    vitamin: 'Vitamin',
                    kematian: 'Kematian',
                    laporan: 'Laporan'
                };

                const dateFieldByTab = {
                    telur: 'tanggal',
                    tray: null,
                    penjualan: 'tanggal_penjualan',
                    pakan: 'tanggal_pakan',
                    vitamin: 'tanggal_vitamin',
                    kematian: 'tanggal_kematian',
                    laporan: 'tanggal_laporan'
                };

                let bypassDuplicateCheck = false;

                // Date input elements
                const dateInputs = [
                    document.getElementById('tanggal'),
                    document.getElementById('tanggal_pakan'),
                    document.getElementById('tanggal_vitamin'),
                    document.getElementById('tanggal_kematian'),
                    document.getElementById('tanggal_laporan'),
                    document.getElementById('tanggal_penjualan')
                ].filter(Boolean);

                function updateButtonColor(activeTabId) {
                    // Remove all color classes
                    saveButton.classList.remove('btn-telur', 'btn-pakan', 'btn-vitamin', 'btn-kematian', 'btn-laporan', 'btn-tray', 'btn-penjualan');

                    // Add the appropriate color class based on active tab
                    switch(activeTabId) {
                        case 'telur':
                            saveButton.classList.add('btn-telur');
                            break;
                        case 'tray':
                            saveButton.classList.add('btn-tray');
                            break;
                        case 'penjualan':
                            saveButton.classList.add('btn-penjualan');
                            break;
                        case 'pakan':
                            saveButton.classList.add('btn-pakan');
                            break;
                        case 'vitamin':
                            saveButton.classList.add('btn-vitamin');
                            break;
                        case 'kematian':
                            saveButton.classList.add('btn-kematian');
                            break;
                        case 'laporan':
                            saveButton.classList.add('btn-laporan');
                            break;
                    }
                }

                function toggleGenerateButton(activeTabId) {
                    if (!generateButton) return;
                    if (activeTabId === 'laporan') {
                        generateButton.classList.remove('d-none');
                    } else {
                        generateButton.classList.add('d-none');
                    }
                }

                function toggleSaveAvailability(activeTabId) {
                    if (!saveButton) return;

                    if (activeTabId === 'tray') {
                        saveButton.disabled = true;
                        saveButton.setAttribute('title', 'Input tray dibuat otomatis dari pencatatan telur.');
                        return;
                    }

                    saveButton.removeAttribute('title');

                    if (activeTabId === 'laporan') {
                        const hasCatatan = catatanField && catatanField.value.trim().length > 0;
                        saveButton.disabled = !hasCatatan;
                    } else {
                        saveButton.disabled = false;
                    }
                }

                function setTrayView(view) {
                    if (!trayListView || !trayGridView) {
                        return;
                    }

                    trayListView.classList.toggle('d-none', view !== 'list');
                    trayGridView.classList.toggle('d-none', view !== 'grid');

                    trayViewToggleButtons.forEach(button => {
                        button.classList.toggle('active', button.dataset.trayView === view);
                    });
                }

                // Sync date across all date inputs
                function syncDateInputs(sourceInput) {
                    const selectedDate = sourceInput.value;
                    dateInputs.forEach(input => {
                        if (input && input !== sourceInput) {
                            input.value = selectedDate;
                        }
                    });
                }

                function syncAllDateInputs(value) {
                    if (!value) return;
                    dateInputs.forEach(input => {
                        if (input) {
                            input.value = value;
                        }
                    });
                }

                // Add event listeners to date inputs
                dateInputs.forEach(input => {
                    if (input) {
                        input.addEventListener('change', function() {
                            syncDateInputs(this);
                        });
                    }
                });

                // Set initial color based on active tab
                const activeTab = document.querySelector('#pencatatanTabs .nav-link.active');
                let currentTabId = 'telur';
                if (activeTab) {
                    currentTabId = activeTab.getAttribute('data-bs-target').substring(1);
                }
                updateButtonColor(currentTabId);
                toggleGenerateButton(currentTabId);
                toggleSaveAvailability(currentTabId);

                // Listen for tab changes
                tabs.forEach(tab => {
                    tab.addEventListener('shown.bs.tab', function(event) {
                        const targetId = event.target.getAttribute('data-bs-target').substring(1);
                        currentTabId = targetId;
                        updateButtonColor(targetId);
                        toggleGenerateButton(targetId);
                        toggleSaveAvailability(targetId);
                        
                        // Update hidden input with active tab
                        if (activeTabInput) {
                            activeTabInput.value = targetId;
                        }
                    });
                });

                trayViewToggleButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const view = this.dataset.trayView || 'list';
                        setTrayView(view);
                    });
                });

                if (trayListView && trayGridView) {
                    setTrayView('list');
                }

                // Handle grid card click to expand with list view below
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.tray-card-grid')) {
                        const card = e.target.closest('.tray-card-grid');
                        const id = card.dataset.trayId;
                        const nextSibling = card.nextElementSibling;
                        const isExpanded = nextSibling && nextSibling.classList.contains('expanded-list-card');

                        // Close any existing expanded
                        const existingExpanded = document.querySelector('.expanded-list-card');
                        if (existingExpanded) {
                            existingExpanded.remove();
                        }

                        if (!isExpanded) {
                            const listCard = document.querySelector(`.tray-card[data-tray-id="${id}"]`);
                            if (listCard) {
                                const clone = listCard.cloneNode(true);
                                clone.classList.add('expanded-list-card');
                                clone.style.marginTop = '1rem';
                                card.parentNode.insertBefore(clone, card.nextSibling);
                            }
                        }
                    }
                });

                // Handle tray edit and delete buttons
                document.addEventListener('click', function(e) {
                    const card = e.target.closest('.tray-card');
                    if (!card) return;

                    const id = card.dataset.trayId;
                    const nameInput = card.querySelector('.tray-name-input');
                    const telurInput = card.querySelector('.tray-telur-input');
                    const keteranganField = card.querySelector('.tray-keterangan-field');
                    const keteranganInput = card.querySelector('.tray-keterangan-input');
                    const editBtn = card.querySelector('.tray-edit-btn');
                    const saveBtn = card.querySelector('.tray-save-btn');
                    const cancelBtn = card.querySelector('.tray-cancel-btn');
                    const deleteBtn = card.querySelector('.tray-delete-btn');

                    if (e.target.closest('.tray-edit-btn')) {
                        // Enter edit mode
                        nameInput.disabled = false;
                        telurInput.disabled = false;
                        keteranganField.classList.remove('d-none');
                        editBtn.classList.add('d-none');
                        deleteBtn.classList.add('d-none');
                        saveBtn.classList.remove('d-none');
                        cancelBtn.classList.remove('d-none');
                        nameInput.focus();
                    }

                    if (e.target.closest('.tray-save-btn')) {
                        // Save changes
                        const newName = nameInput.value.trim();
                        const newTelur = parseInt(telurInput.value) || 0;
                        const newKeterangan = keteranganInput.value.trim();

                        if (newTelur <= 0) {
                            alert('Jumlah telur harus lebih dari 0');
                            return;
                        }

                        // Here you would submit to update the laporan
                        alert(`Saving tray ${id}: Name=${newName}, Telur=${newTelur}, Keterangan=${newKeterangan}`);
                        // For now, just exit edit mode
                        nameInput.disabled = true;
                        telurInput.disabled = true;
                        keteranganField.classList.add('d-none');
                        editBtn.classList.remove('d-none');
                        deleteBtn.classList.remove('d-none');
                        saveBtn.classList.add('d-none');
                        cancelBtn.classList.add('d-none');
                    }

                    if (e.target.closest('.tray-cancel-btn')) {
                        // Cancel changes - reset to original values
                        nameInput.value = nameInput.getAttribute('data-original');
                        telurInput.value = telurInput.getAttribute('data-original');
                        keteranganInput.value = keteranganInput.getAttribute('data-original');
                        nameInput.disabled = true;
                        telurInput.disabled = true;
                        keteranganField.classList.add('d-none');
                        editBtn.classList.remove('d-none');
                        deleteBtn.classList.remove('d-none');
                        saveBtn.classList.add('d-none');
                        cancelBtn.classList.add('d-none');
                    }

                    if (e.target.closest('.tray-delete-btn')) {
                        const produksiId = @json($produksi->id);

                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            text: 'Yakin ingin menghapus entry tray ini? Data telur terkait akan dihapus.',
                            icon: 'error',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = `/admin/produksi/${produksiId}/laporan/${id}`;
                                form.innerHTML = `
                                    @csrf
                                    @method('DELETE')
                                `;
                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    }
                });

                if (catatanField) {
                    catatanField.addEventListener('input', function() {
                        if (currentTabId === 'laporan') {
                            toggleSaveAvailability('laporan');
                        }
                    });
                }

                if (generateButton && generateUrl) {
                    generateButton.addEventListener('click', function() {
                        const tanggalField = document.getElementById('tanggal_laporan');
                        if (!tanggalField || !tanggalField.value) {
                            alert('Isi tanggal laporan terlebih dahulu.');
                            return;
                        }

                        // Check if textarea has content
                        const hasExistingContent = catatanField && catatanField.value.trim().length > 0;

                        if (hasExistingContent) {
                            // Show confirmation popup
                            Swal.fire({
                                title: 'Ganti Isi Laporan?',
                                text: 'Inputan laporan sudah ada isinya. Apakah Anda ingin menggantinya dengan laporan yang di-generate?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ya, Ganti',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    performGeneration();
                                }
                            });
                        } else {
                            // No existing content, generate directly
                            performGeneration();
                        }

                        function performGeneration() {
                            const params = new URLSearchParams({ tanggal: tanggalField.value });
                            const originalLabel = generateButton.innerHTML;
                            generateButton.disabled = true;
                            generateButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Generating...';
                            generateButton.classList.add('generating');

                            fetch(`${generateUrl}?${params.toString()}`, {
                                headers: { 'Accept': 'application/json' }
                            })
                            .then(async response => {
                                const payload = await response.json().catch(() => ({}));
                                if (!response.ok) {
                                    throw new Error(payload.message || 'Gagal mengenerate catatan.');
                                }
                                return payload;
                            })
                            .then(data => {
                                if (catatanField) {
                                    // Ensure complete replacement of the textarea content
                                    catatanField.value = data.summary || '';
                                    catatanField.dispatchEvent(new Event('input'));
                                }
                            })
                            .catch(error => {
                                alert(error.message);
                            })
                            .finally(() => {
                                generateButton.disabled = false;
                                generateButton.innerHTML = originalLabel;
                                generateButton.classList.remove('generating');
                            });
                        }
                    });
                }

                // Form submission handler with duplicate-date confirmation
                if (form) {
                    form.addEventListener('submit', function(e) {
                        if (activeTabInput) {
                            activeTabInput.value = currentTabId;
                        }

                        const tanggalUmum = document.getElementById('tanggal');
                        if (tanggalUmum) {
                            syncAllDateInputs(tanggalUmum.value);
                        }

                        if (bypassDuplicateCheck) {
                            bypassDuplicateCheck = false;
                            return;
                        }

                        const dateFieldId = dateFieldByTab[currentTabId];
                        const dateField = dateFieldId ? document.getElementById(dateFieldId) : null;
                        const selectedDate = dateField?.value;
                        const tabEntries = (existingEntriesByTab && existingEntriesByTab[currentTabId]) || {};
                        const alreadyRecorded = selectedDate && tabEntries[selectedDate];

                        if (alreadyRecorded) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Tambah Catatan Lagi?',
                                text: `${tabLabels[currentTabId] || 'Pencatatan'} untuk tanggal ${selectedDate} sudah tersimpan. Lanjutkan menambahkan data baru?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#0d6efd',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, Tambahkan',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then(result => {
                                if (result.isConfirmed) {
                                    bypassDuplicateCheck = true;
                                    form.submit();
                                }
                            });
                        }
                    });
                }
            });
        </script>
    </div>
</div>
