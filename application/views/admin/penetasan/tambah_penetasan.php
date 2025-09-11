<!-- Page Header -->
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Penetasan Baru
                </h1>
                <p class="page-subtitle">Buat batch penetasan baru dengan data yang lengkap</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('penetasan') ?>">Penetasan</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Form Section -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-vigaza text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-seedling me-2"></i>
                        Form Penetasan Baru
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= $this->session->flashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form id="formTambahPenetasan" action="<?= base_url('penetasan/tambah') ?>" method="POST">
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="batch" class="form-label required">Kode Batch</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="batch" name="batch" 
                                               readonly required value="<?= isset($next_batch) ? $next_batch : 'P001' ?>">
                                        <button type="button" class="btn btn-outline-secondary" id="btnGenerateBatch">
                                            <i class="fa fa-sync-alt"></i> Generate
                                        </button>
                                    </div>
                                    <div class="form-text">Kode batch di-generate otomatis</div>
                                </div>

                                <div class="mb-3">
                                    <label for="id_mesin" class="form-label required">Mesin Penetasan</label>
                                    <select class="form-select" id="id_mesin" name="id_mesin" required>
                                        <option value="">Pilih Mesin</option>
                                        <?php if (isset($mesin_options) && !empty($mesin_options)): ?>
                                            <?php foreach ($mesin_options as $mesin): ?>
                                                <option value="<?= $mesin['id_mesin'] ?>" 
                                                        data-kapasitas="<?= $mesin['kapasitas'] ?>">
                                                    <?= $mesin['nama_mesin'] ?> (Kapasitas: <?= number_format($mesin['kapasitas']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-text">Pilih mesin yang akan digunakan untuk penetasan</div>
                                </div>

                                <div class="mb-3">
                                    <label for="tanggal_mulai" class="form-label required">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                                    <div class="form-text">Tanggal dimulainya proses penetasan</div>
                                </div>

                                <div class="mb-3">
                                    <label for="jumlah_telur" class="form-label required">Jumlah Telur</label>
                                    <input type="number" class="form-control" id="jumlah_telur" name="jumlah_telur" 
                                           min="1" required>
                                    <div class="form-text">Jumlah telur yang akan ditetaskan</div>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lama_penetasan" class="form-label required">Lama Penetasan (Hari)</label>
                                    <input type="number" class="form-control" id="lama_penetasan" name="lama_penetasan" 
                                           value="21" min="1" max="30" required>
                                    <div class="form-text">Durasi penetasan dalam hari (biasanya 21 hari)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="suhu_rata" class="form-label required">Suhu Rata-rata (°C)</label>
                                    <input type="number" class="form-control" id="suhu_rata" name="suhu_rata" 
                                           value="37.5" min="35" max="40" step="0.1" required>
                                    <div class="form-text">Suhu target untuk penetasan (biasanya 37.5°C)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="kelembaban_rata" class="form-label required">Kelembaban Rata-rata (%)</label>
                                    <input type="number" class="form-control" id="kelembaban_rata" name="kelembaban_rata" 
                                           value="60" min="40" max="80" required>
                                    <div class="form-text">Kelembaban target untuk penetasan (biasanya 60%)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="asal_telur" class="form-label">Asal Telur</label>
                                    <input type="text" class="form-control" id="asal_telur" name="asal_telur" 
                                           placeholder="Contoh: Kandang A, Supplier XYZ">
                                    <div class="form-text">Sumber telur yang akan ditetaskan</div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="catatan" class="form-label">Catatan</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                              placeholder="Catatan tambahan tentang batch penetasan ini..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('penetasan') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-undo me-2"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-vigaza">
                                            <i class="fas fa-save me-2"></i>Simpan Penetasan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-vigaza text-white">
                <h5 class="modal-title">Konfirmasi Simpan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyimpan data penetasan ini?</p>
                <div class="alert alert-info">
                    <small>
                        <strong>Info:</strong> Setelah disimpan, batch penetasan akan memulai proses monitoring harian.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-vigaza" id="btnKonfirmasiSimpan">Ya, Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto fill batch when page loads
    if ($('#batch').val() === '') {
        generateBatch();
    }
    
    // Generate batch button
    $('#btnGenerateBatch').click(function() {
        generateBatch();
    });
    
    function generateBatch() {
        $.ajax({
            url: '<?= base_url('penetasan/generate_batch') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#batch').val(response.batch);
                } else {
                    // Fallback if AJAX fails - use same format as database
                    var now = new Date();
                    var year = now.getFullYear();
                    var month = String(now.getMonth() + 1).padStart(2, '0');
                    var day = String(now.getDate()).padStart(2, '0');
                    var hour = String(now.getHours()).padStart(2, '0');
                    var minute = String(now.getMinutes()).padStart(2, '0');
                    var batch = 'BATCH-' + year + '-' + month + '-' + hour + minute;
                    $('#batch').val(batch);
                }
            },
            error: function() {
                // Fallback if AJAX fails - use same format as database
                var now = new Date();
                var year = now.getFullYear();
                var month = String(now.getMonth() + 1).padStart(2, '0');
                var timestamp = String(now.getHours()).padStart(2, '0') + String(now.getMinutes()).padStart(2, '0');
                var batch = 'BATCH-' + year + '-' + month + '-' + timestamp;
                $('#batch').val(batch);
            }
        });
    }
    
    // Auto set default date to today
    if ($('#tanggal_mulai').val() === '') {
        var today = new Date().toISOString().split('T')[0];
        $('#tanggal_mulai').val(today);
    }
    
    // Form validation
    $('#formTambahPenetasan').on('submit', function(e) {
        var batch = $('#batch').val().trim();
        var jumlahTelur = $('#jumlah_telur').val();
        var suhuRata = $('#suhu_rata').val();
        var kelembaban = $('#kelembaban_rata').val();
        var lamaPenetasan = $('#lama_penetasan').val();
        
        if (!batch) {
            e.preventDefault();
            alert('Batch harus diisi! Klik tombol Generate untuk membuat batch baru.');
            return false;
        }
        
        if (!jumlahTelur || parseInt(jumlahTelur) <= 0) {
            e.preventDefault();
            alert('Jumlah telur harus diisi dan lebih dari 0!');
            $('#jumlah_telur').focus();
            return false;
        }
        
        if (!suhuRata) {
            e.preventDefault();
            alert('Suhu rata-rata harus diisi!');
            $('#suhu_rata').focus();
            return false;
        }
        
        if (!kelembaban) {
            e.preventDefault();
            alert('Kelembaban rata-rata harus diisi!');
            $('#kelembaban_rata').focus();
            return false;
        }
        
        if (!lamaPenetasan || parseInt(lamaPenetasan) <= 0) {
            e.preventDefault();
            alert('Lama penetasan harus diisi dan lebih dari 0!');
            $('#lama_penetasan').focus();
            return false;
        }
        
        return true;
    });
});
</script>
