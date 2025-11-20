<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.produksi.laporan.store', $produksi) }}" method="POST" id="pencatatanForm" data-generate-url="{{ route('admin.produksi.laporan.generate-summary', $produksi) }}">
            @csrf
            <input type="hidden" name="active_tab" id="activeTabInput" value="telur">

            <ul class="nav nav-tabs mb-3" id="pencatatanTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="telur-tab" data-bs-toggle="tab" data-bs-target="#telur" type="button" role="tab" aria-controls="telur" aria-selected="true">
                        Telur
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pakan-tab" data-bs-toggle="tab" data-bs-target="#pakan" type="button" role="tab" aria-controls="pakan" aria-selected="false">
                        Pakan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vitamin-tab" data-bs-toggle="tab" data-bs-target="#vitamin" type="button" role="tab" aria-controls="vitamin" aria-selected="false">
                        Vitamin
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="kematian-tab" data-bs-toggle="tab" data-bs-target="#kematian" type="button" role="tab" aria-controls="kematian" aria-selected="false">
                        Kematian
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="laporan-tab" data-bs-toggle="tab" data-bs-target="#laporan" type="button" role="tab" aria-controls="laporan" aria-selected="false">
                        Laporan
                    </button>
                </li>
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

        <style>
            .btn.generating {
                animation: ai-generate 1.5s ease-in-out infinite;
                box-shadow: 0 0 20px rgba(0, 123, 255, 0.5);
            }

            @keyframes ai-generate {
                0%, 100% {
                    transform: scale(1);
                    opacity: 1;
                }
                50% {
                    transform: scale(1.05);
                    opacity: 0.8;
                }
            }
        </style>

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

                const tabLabels = {
                    telur: 'Telur',
                    pakan: 'Pakan',
                    vitamin: 'Vitamin',
                    kematian: 'Kematian',
                    laporan: 'Laporan'
                };

                const dateFieldByTab = {
                    telur: 'tanggal',
                    pakan: 'tanggal_pakan',
                    vitamin: 'tanggal_vitamin',
                    kematian: 'tanggal_kematian',
                    laporan: 'tanggal_laporan'
                };

                let bypassDuplicateCheck = false;

                // Date input elements
                const dateInputs = [
                    document.getElementById('tanggal'), // telur tab
                    document.getElementById('tanggal_pakan'), // pakan tab
                    document.getElementById('tanggal_vitamin'), // vitamin tab
                    document.getElementById('tanggal_kematian'), // kematian tab
                    document.getElementById('tanggal_laporan') // laporan tab
                ];

                function updateButtonColor(activeTabId) {
                    // Remove all color classes
                    saveButton.classList.remove('btn-telur', 'btn-pakan', 'btn-vitamin', 'btn-kematian', 'btn-laporan');

                    // Add the appropriate color class based on active tab
                    switch(activeTabId) {
                        case 'telur':
                            saveButton.classList.add('btn-telur');
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
                    if (activeTabId === 'laporan') {
                        const hasCatatan = catatanField && catatanField.value.trim().length > 0;
                        saveButton.disabled = !hasCatatan;
                    } else {
                        saveButton.disabled = false;
                    }
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
