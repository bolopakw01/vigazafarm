<!-- Load SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page Title -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-action">
                <h1 class="page-title">Tambah Periode Pembesaran</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Vigaza Farm</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pembesaran') ?>">Pembesaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Periode</li>
                </ol>
            </div>
            <div class="header-action">
                <a href="<?= base_url('pembesaran') ?>" class="btn btn-outline-vigaza">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="section-body mt-4">
    <div class="container-fluid">
        <!-- Form Card -->
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-plus-circle text-vigaza"></i> 
                            Form Tambah Periode Pembesaran
                        </h3>
                        <div class="card-options">
                            <span class="badge badge-vigaza">
                                <i class="fa fa-info-circle"></i> Form Input
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?= form_open('pembesaran/simpan', array('class' => 'form-horizontal', 'id' => 'form-pembesaran')) ?>
                        
                        <div class="row">
                            <!-- Informasi Dasar -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-tag"></i> Periode/Batch <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-vigaza text-white">
                                                <i class="fa fa-tag"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" name="periode" id="periode" 
                                               placeholder="Masukkan nama periode/batch" required
                                               value="PB-<?= date('Ymd') ?>-001">
                                    </div>
                                    <small class="form-hint">Contoh: PB-20231201-001</small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-home"></i> Kandang <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-vigaza text-white">
                                                <i class="fa fa-home"></i>
                                            </span>
                                        </div>
                                        <select class="form-control" name="id_kandang" id="id_kandang" required>
                                            <option value="">-- Pilih Kandang --</option>
                                            <?php if (!empty($kandang)): ?>
                                                <?php foreach ($kandang as $k): ?>
                                                <option value="<?= $k->id_kandang ?>" data-kapasitas="<?= $k->kapasitas ?>">
                                                    <?= $k->nama_kandang ?> (Kapasitas: <?= number_format($k->kapasitas) ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <small class="form-hint text-info" id="kapasitas-info"></small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-calendar"></i> Tanggal Masuk <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-vigaza text-white">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="tgl_masuk" id="tgl_masuk" 
                                               value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-users"></i> Jumlah Awal DOC <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-vigaza text-white">
                                                <i class="fa fa-users"></i>
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" name="jml_awal" id="jml_awal" 
                                               min="1" step="1" placeholder="Contoh: 1000" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Ekor</span>
                                        </div>
                                    </div>
                                    <small class="form-hint text-warning" id="validasi-kapasitas"></small>
                                </div>
                            </div>

                            <!-- Target dan Informasi Lanjutan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-weight"></i> Berat Awal (gram/ekor)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-vigaza text-white">
                                                <i class="fa fa-weight"></i>
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" name="berat_awal" id="berat_awal" 
                                               min="30" step="0.1" placeholder="Contoh: 40" value="40">
                                        <div class="input-group-append">
                                            <span class="input-group-text">gram</span>
                                        </div>
                                    </div>
                                    <small class="form-hint">Biasanya sekitar 40-45 gram untuk DOC</small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-clock-o"></i> Durasi Pembesaran <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-vigaza text-white">
                                                <i class="fa fa-clock-o"></i>
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" name="durasi_hari" id="durasi_hari" 
                                               min="21" max="60" step="1" placeholder="Contoh: 35" value="35" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Hari</span>
                                        </div>
                                    </div>
                                    <small class="form-hint">Range: 21-60 hari (biasanya 35 hari)</small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-calendar-check-o"></i> Target Panen
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-success text-white">
                                                <i class="fa fa-calendar-check-o"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="target_panen" id="target_panen" readonly>
                                    </div>
                                    <small class="form-hint text-success">Otomatis dihitung dari tanggal masuk + durasi</small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-weight"></i> Target Berat Panen (gram/ekor)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-vigaza text-white">
                                                <i class="fa fa-weight"></i>
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" name="target_berat" id="target_berat" 
                                               min="1000" step="50" placeholder="Contoh: 1800" value="1800">
                                        <div class="input-group-append">
                                            <span class="input-group-text">gram</span>
                                        </div>
                                    </div>
                                    <small class="form-hint">Target berat ayam saat panen</small>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-sticky-note"></i> Catatan
                                    </label>
                                    <textarea class="form-control" name="catatan" id="catatan" rows="3" 
                                              placeholder="Catatan tambahan mengenai periode pembesaran ini..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Otomatis -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-vigaza-light" id="ringkasan" style="display: none;">
                                    <h6 class="text-vigaza"><i class="fa fa-info-circle"></i> Ringkasan Periode</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Periode:</strong><br>
                                            <span id="ringkasan-periode">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Kandang:</strong><br>
                                            <span id="ringkasan-kandang">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Durasi:</strong><br>
                                            <span id="ringkasan-durasi">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Populasi:</strong><br>
                                            <span id="ringkasan-populasi">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-footer text-center">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                        <i class="fa fa-times"></i> Batal
                                    </button>
                                    <button type="reset" class="btn btn-warning" id="btn-reset">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-vigaza" id="btn-simpan">
                                        <i class="fa fa-save"></i> Simpan Periode
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Auto generate periode
    function generatePeriode() {
        var today = new Date();
        var dateStr = today.getFullYear() + 
                     String(today.getMonth() + 1).padStart(2, '0') + 
                     String(today.getDate()).padStart(2, '0');
        
        // Get next number (you can enhance this to check existing periods)
        var nextNum = String(Math.floor(Math.random() * 999) + 1).padStart(3, '0');
        $('#periode').val('PB-' + dateStr + '-' + nextNum);
    }

    // Hitung target panen otomatis
    function hitungTargetPanen() {
        var tglMasuk = $('#tgl_masuk').val();
        var durasi = parseInt($('#durasi_hari').val()) || 0;
        
        if (tglMasuk && durasi > 0) {
            var tanggal = new Date(tglMasuk);
            tanggal.setDate(tanggal.getDate() + durasi);
            
            var year = tanggal.getFullYear();
            var month = String(tanggal.getMonth() + 1).padStart(2, '0');
            var day = String(tanggal.getDate()).padStart(2, '0');
            
            $('#target_panen').val(year + '-' + month + '-' + day);
        }
    }

    // Validasi kapasitas kandang
    function validasiKapasitas() {
        var kandang = $('#id_kandang option:selected');
        var kapasitas = parseInt(kandang.data('kapasitas')) || 0;
        var jumlah = parseInt($('#jml_awal').val()) || 0;
        
        if (kapasitas > 0) {
            $('#kapasitas-info').text('Kapasitas maksimal: ' + kapasitas.toLocaleString() + ' ekor');
            
            if (jumlah > 0) {
                if (jumlah > kapasitas) {
                    $('#validasi-kapasitas').text('⚠️ Melebihi kapasitas kandang!')
                                           .removeClass('text-success')
                                           .addClass('text-danger');
                } else {
                    var persentase = Math.round((jumlah / kapasitas) * 100);
                    $('#validasi-kapasitas').text('✓ Kapasitas terpakai: ' + persentase + '%')
                                           .removeClass('text-danger')
                                           .addClass('text-success');
                }
            }
        }
    }

    // Update ringkasan
    function updateRingkasan() {
        var periode = $('#periode').val();
        var kandang = $('#id_kandang option:selected').text();
        var durasi = $('#durasi_hari').val();
        var jumlah = $('#jml_awal').val();
        
        if (periode && kandang && durasi && jumlah) {
            $('#ringkasan-periode').text(periode);
            $('#ringkasan-kandang').text(kandang.split('(')[0].trim());
            $('#ringkasan-durasi').text(durasi + ' hari');
            $('#ringkasan-populasi').text(parseInt(jumlah).toLocaleString() + ' ekor');
            $('#ringkasan').show();
        } else {
            $('#ringkasan').hide();
        }
    }

    // Event listeners
    $('#tgl_masuk, #durasi_hari').on('change', function() {
        hitungTargetPanen();
        updateRingkasan();
    });

    $('#id_kandang').on('change', function() {
        validasiKapasitas();
        updateRingkasan();
    });

    $('#jml_awal').on('input', function() {
        validasiKapasitas();
        updateRingkasan();
    });

    $('#periode').on('input', updateRingkasan);

    // Form validation
    $('#form-pembesaran').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var kandang = $('#id_kandang option:selected');
        var kapasitas = parseInt(kandang.data('kapasitas')) || 0;
        var jumlah = parseInt($('#jml_awal').val()) || 0;
        
        // Validasi kapasitas
        if (jumlah > kapasitas) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Jumlah DOC melebihi kapasitas kandang. Lanjutkan?',
                showCancelButton: true,
                confirmButtonColor: '#0eaab4',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
        } else {
            submitForm();
        }
        
        function submitForm() {
            $('#btn-simpan').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
            
            // Submit form
            setTimeout(function() {
                form.unbind('submit').submit();
            }, 500);
        }
    });

    // Reset form
    $('#btn-reset').on('click', function() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua data yang sudah diisi akan dihapus.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0eaab4',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form-pembesaran')[0].reset();
                generatePeriode();
                $('#ringkasan').hide();
                $('#kapasitas-info').text('');
                $('#validasi-kapasitas').text('');
            }
        });
    });

    // Initialize
    generatePeriode();
    hitungTargetPanen();
});
</script>
