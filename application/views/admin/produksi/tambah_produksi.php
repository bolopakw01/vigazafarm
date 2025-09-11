<!-- Load SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page Title -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-action">
                <h1 class="page-title">Tambah Data Produksi</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Vigaza Farm</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('produksi') ?>">Produksi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
                </ol>
            </div>
            <div class="header-action">
                <a href="<?= base_url('produksi') ?>" class="btn btn-outline-vigaza">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="section-body mt-4">
    <div class="container-fluid">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '<?= $this->session->flashdata('error') ?>',
                        confirmButtonColor: '#0eaab4'
                    });
                });
            </script>
        <?php endif; ?>

        <!-- Main Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-plus"></i> Form Input Produksi
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('produksi/proses_tambah') ?>" method="post" id="produksiForm">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-info-circle mr-2"></i>Informasi Dasar
                        </h6>
                        <hr class="mb-4">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kandang <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_kandang" required>
                                    <option value="">Pilih Kandang</option>
                                    <?php if (isset($kandang) && !empty($kandang)): ?>
                                        <?php foreach ($kandang as $k): ?>
                                            <option value="<?= $k->id_kandang ?>" <?= set_select('id_kandang', $k->id_kandang) ?>>
                                                <?= $k->nama ?> (Kapasitas: <?= $k->kapasitas ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">Pilih kandang yang akan digunakan untuk produksi</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Produksi <span class="text-danger">*</span></label>
                                <select class="form-control" name="jenis_produksi" required onchange="updateSatuan()">
                                    <option value="">Pilih Jenis Produksi</option>
                                    <option value="telur" <?= set_select('jenis_produksi', 'telur') ?>>Telur</option>
                                    <option value="daging" <?= set_select('jenis_produksi', 'daging') ?>>Daging</option>
                                    <option value="ayam_hidup" <?= set_select('jenis_produksi', 'ayam_hidup') ?>>Ayam Hidup</option>
                                </select>
                                <small class="form-text text-muted">Tentukan jenis produksi yang akan dicatat</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Produksi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                                <small class="form-text text-muted">Tanggal pencatatan produksi</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fase Produksi</label>
                                <select class="form-control" name="fase_produksi">
                                    <option value="awal" <?= set_select('fase_produksi', 'awal') ?>>Awal</option>
                                    <option value="puncak" <?= set_select('fase_produksi', 'puncak') ?>>Puncak</option>
                                    <option value="akhir" <?= set_select('fase_produksi', 'akhir') ?>>Akhir</option>
                                </select>
                                <small class="form-text text-muted">Fase produksi saat ini</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kualitas Produk</label>
                                <select class="form-control" name="kualitas">
                                    <option value="A" <?= set_select('kualitas', 'A') ?>>Grade A</option>
                                    <option value="B" <?= set_select('kualitas', 'B') ?>>Grade B</option>
                                    <option value="C" <?= set_select('kualitas', 'C') ?>>Grade C</option>
                                    <option value="Reject" <?= set_select('kualitas', 'Reject') ?>>Reject</option>
                                </select>
                                <small class="form-text text-muted">Tingkat kualitas produk</small>
                            </div>
                        </div>
                    </div>

                    <!-- Production Details -->
                    <div class="form-section mt-4">
                        <h6 class="section-title">
                            <i class="fas fa-chart-line mr-2"></i>Detail Produksi
                        </h6>
                        <hr class="mb-4">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="jumlah" 
                                           min="0" step="1" required onchange="hitungTotal()">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="satuanJumlah">pcs</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Jumlah produk yang dihasilkan</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Berat</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="berat" 
                                           min="0" step="0.01" onchange="hitungTotal()">
                                    <div class="input-group-append">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Berat total produk (opsional)</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga Satuan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" class="form-control" name="harga_satuan" 
                                           min="0" step="100" onchange="hitungTotal()">
                                </div>
                                <small class="form-text text-muted">Harga per satuan produk</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Nilai</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" class="form-control" name="total_nilai" readonly>
                                </div>
                                <small class="form-text text-muted">Nilai total otomatis dihitung</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status">
                                    <option value="persiapan" <?= set_select('status', 'persiapan') ?>>Persiapan</option>
                                    <option value="aktif" <?= set_select('status', 'aktif') ?>>Aktif</option>
                                    <option value="selesai" <?= set_select('status', 'selesai') ?>>Selesai</option>
                                </select>
                                <small class="form-text text-muted">Status produksi saat ini</small>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-section mt-4">
                        <h6 class="section-title">
                            <i class="fas fa-clipboard mr-2"></i>Informasi Tambahan
                        </h6>
                        <hr class="mb-4">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" name="catatan" rows="3" 
                                          placeholder="Catatan tambahan mengenai produksi ini..."><?= set_value('catatan') ?></textarea>
                                <small class="form-text text-muted">Informasi tambahan tentang produksi (opsional)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-section mt-4 pt-3 border-top">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('produksi') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-warning mr-2">
                                            <i class="fas fa-undo mr-2"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>Simpan Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
function updateSatuan() {
    const jenis = document.querySelector('[name="jenis_produksi"]').value;
    const satuanElement = document.getElementById('satuanJumlah');
    
    switch(jenis) {
        case 'telur':
            satuanElement.textContent = 'butir';
            break;
        case 'daging':
            satuanElement.textContent = 'kg';
            break;
        case 'ayam_hidup':
            satuanElement.textContent = 'ekor';
            break;
        default:
            satuanElement.textContent = 'pcs';
    }
}

function hitungTotal() {
    const jumlah = parseFloat(document.querySelector('[name="jumlah"]').value) || 0;
    const harga = parseFloat(document.querySelector('[name="harga_satuan"]').value) || 0;
    const total = jumlah * harga;
    
    document.querySelector('[name="total_nilai"]').value = total;
}

$(document).ready(function() {
    // Form validation
    $('#produksiForm').on('submit', function(e) {
        const requiredFields = ['id_kandang', 'jenis_produksi', 'tanggal', 'jumlah'];
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            const input = document.querySelector('[name="' + field + '"]');
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
        }
    });
    
    // Initialize values
    updateSatuan();
    hitungTotal();
});
</script>

<style>
.form-section {
    margin-bottom: 2rem;
}

.section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.is-invalid {
    border-color: #dc3545;
}

.card-hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transition: box-shadow 0.3s ease-in-out;
}
</style>
                            <div class="form-group">
                                <label>Jumlah <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="jumlah" required 
                                       min="0" step="0.1" placeholder="Masukkan jumlah produksi" value="<?= set_value('jumlah') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Satuan <span class="text-danger">*</span></label>
                                <select class="form-control" name="satuan" required>
                                    <option value="">Pilih Satuan</option>
                                    <option value="butir" <?= set_select('satuan', 'butir') ?>>Butir</option>
                                    <option value="kg" <?= set_select('satuan', 'kg') ?>>Kilogram (kg)</option>
                                    <option value="ekor" <?= set_select('satuan', 'ekor') ?>>Ekor</option>
                                    <option value="gram" <?= set_select('satuan', 'gram') ?>>Gram</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal" required 
                                       value="<?= set_value('tanggal', date('Y-m-d')) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Waktu</label>
                                <input type="time" class="form-control" name="waktu" 
                                       value="<?= set_value('waktu', date('H:i')) ?>">
                                <small class="form-text text-muted">Kosongkan untuk menggunakan waktu saat ini</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kualitas</label>
                                <select class="form-control" name="kualitas">
                                    <option value="">Pilih Kualitas</option>
                                    <option value="A" <?= set_select('kualitas', 'A') ?>>Grade A (Premium)</option>
                                    <option value="B" <?= set_select('kualitas', 'B') ?>>Grade B (Sedang)</option>
                                    <option value="C" <?= set_select('kualitas', 'C') ?>>Grade C (Rendah)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea class="form-control" name="catatan" rows="3" 
                                          placeholder="Catatan tambahan tentang produksi ini"><?= set_value('catatan') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="alert alert-info">
                        <h6><i class="zmdi zmdi-info"></i> Panduan Input:</h6>
                        <ul class="mb-0">
                            <li><strong>Telur:</strong> Gunakan satuan "butir" untuk menghitung jumlah telur yang diproduksi</li>
                            <li><strong>Daging:</strong> Gunakan satuan "kg" atau "gram" untuk berat daging yang dihasilkan</li>
                            <li><strong>Ayam Hidup:</strong> Gunakan satuan "ekor" untuk jumlah ayam yang siap jual</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="zmdi zmdi-save"></i> Simpan Data
                        </button>
                        <a href="<?= base_url('produksi') ?>" class="btn btn-secondary">
                            <i class="zmdi zmdi-close"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateSatuan() {
    var jenis = $('select[name="jenis_produksi"]').val();
    var satuanSelect = $('select[name="satuan"]');
    
    // Reset options
    satuanSelect.html('<option value="">Pilih Satuan</option>');
    
    if (jenis == 'telur') {
        satuanSelect.append('<option value="butir">Butir</option>');
        satuanSelect.val('butir');
    } else if (jenis == 'daging') {
        satuanSelect.append('<option value="kg">Kilogram (kg)</option>');
        satuanSelect.append('<option value="gram">Gram</option>');
        satuanSelect.val('kg');
    } else if (jenis == 'ayam_hidup') {
        satuanSelect.append('<option value="ekor">Ekor</option>');
        satuanSelect.val('ekor');
    }
}

// Auto update satuan when page loads if jenis_produksi already selected
$(document).ready(function() {
    if ($('select[name="jenis_produksi"]').val() != '') {
        updateSatuan();
    }
});
</script>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
