<!-- Load SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page Title -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-action">
                <h1 class="page-title">Edit Pembesaran</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Vigaza Farm</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pembesaran') ?>">Pembesaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </div>
            <div class="header-action">
                <a href="<?= base_url('pembesaran/detail/' . $pembesaran->id_pembesaran) ?>" class="btn btn-info">
                    <i class="fa fa-eye"></i> Lihat Detail
                </a>
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
        <!-- Header Info -->
        <div class="row clearfix">
            <div class="col-12">
                <div class="card bg-vigaza text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="text-white mb-1">
                                    <i class="fa fa-edit"></i> Edit Periode: <?= $pembesaran->periode ?>
                                </h4>
                                <p class="mb-0">
                                    <i class="fa fa-home"></i> Kandang: <strong><?= $pembesaran->nama_kandang ?></strong> | 
                                    <i class="fa fa-calendar"></i> Mulai: <strong><?= date('d/m/Y', strtotime($pembesaran->tgl_masuk)) ?></strong>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="badge badge-warning badge-lg">
                                    <?= ucfirst($pembesaran->status) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Edit -->
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-edit text-vigaza"></i> 
                            Form Edit Data Pembesaran
                        </h3>
                        <div class="card-options">
                            <span class="badge badge-warning">
                                <i class="fa fa-exclamation-triangle"></i> Mode Edit
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?= form_open('pembesaran/update', array('class' => 'form-horizontal', 'id' => 'form-edit-pembesaran')) ?>
                        
                        <input type="hidden" name="id_pembesaran" value="<?= $pembesaran->id_pembesaran ?>">
                        
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
                                               value="<?= $pembesaran->periode ?>" required>
                                    </div>
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
                                            <?php if (!empty($kandang)): ?>
                                                <?php foreach ($kandang as $k): ?>
                                                <option value="<?= $k->id_kandang ?>" <?= ($k->id_kandang == $pembesaran->id_kandang) ? 'selected' : '' ?>>
                                                    <?= $k->nama_kandang ?> (Kapasitas: <?= number_format($k->kapasitas) ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-calendar"></i> Tanggal Masuk
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-secondary text-white">
                                                <i class="fa fa-lock"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" value="<?= $pembesaran->tgl_masuk ?>" readonly>
                                    </div>
                                    <small class="form-hint text-muted">Tanggal masuk tidak dapat diubah</small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-users"></i> Jumlah Awal DOC
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-secondary text-white">
                                                <i class="fa fa-lock"></i>
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" value="<?= number_format($pembesaran->jml_awal) ?>" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Ekor</span>
                                        </div>
                                    </div>
                                    <small class="form-hint text-muted">Jumlah awal tidak dapat diubah</small>
                                </div>
                            </div>

                            <!-- Data Yang Dapat Diubah -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-users"></i> Jumlah Saat Ini <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-warning text-white">
                                                <i class="fa fa-users"></i>
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" name="jml_saat_ini" id="jml_saat_ini" 
                                               min="0" max="<?= $pembesaran->jml_awal ?>" step="1" 
                                               value="<?= $pembesaran->jml_saat_ini ?? $pembesaran->jml_awal ?>" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Ekor</span>
                                        </div>
                                    </div>
                                    <small class="form-hint text-info" id="mortalitas-info"></small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-calendar-check-o"></i> Target Panen <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-success text-white">
                                                <i class="fa fa-calendar-check-o"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="target_panen" id="target_panen" 
                                               value="<?= $pembesaran->target_panen ?>" required>
                                    </div>
                                    <small class="form-hint" id="target-info"></small>
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
                                               min="1000" step="50" 
                                               value="<?= $pembesaran->target_berat ?? 1800 ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">gram</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-vigaza">
                                        <i class="fa fa-info-circle"></i> Status
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-secondary text-white">
                                                <i class="fa fa-lock"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" value="<?= ucfirst($pembesaran->status) ?>" readonly>
                                    </div>
                                    <small class="form-hint text-muted">Status diubah melalui tombol aksi khusus</small>
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
                                    <textarea class="form-control" name="catatan" id="catatan" rows="4" 
                                              placeholder="Update catatan mengenai periode pembesaran ini..."><?= $pembesaran->catatan ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Info -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-vigaza-light">
                                    <h6 class="text-vigaza"><i class="fa fa-info-circle"></i> Informasi Progress</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Umur Saat Ini:</strong><br>
                                            <?php 
                                            $umur_hari = floor((strtotime(date('Y-m-d')) - strtotime($pembesaran->tgl_masuk)) / (60*60*24));
                                            echo $umur_hari . ' hari';
                                            ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Durasi Target:</strong><br>
                                            <?php 
                                            $target_hari = floor((strtotime($pembesaran->target_panen) - strtotime($pembesaran->tgl_masuk)) / (60*60*24));
                                            echo $target_hari . ' hari';
                                            ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Progress:</strong><br>
                                            <?php 
                                            $progress = $target_hari > 0 ? round(($umur_hari / $target_hari) * 100, 1) : 0;
                                            $progress = min($progress, 100);
                                            echo $progress . '%';
                                            ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Estimasi Sisa:</strong><br>
                                            <?php 
                                            $hari_tersisa = floor((strtotime($pembesaran->target_panen) - strtotime(date('Y-m-d'))) / (60*60*24));
                                            if ($hari_tersisa < 0) {
                                                echo '<span class="text-danger">Terlambat ' . abs($hari_tersisa) . ' hari</span>';
                                            } elseif ($hari_tersisa == 0) {
                                                echo '<span class="text-warning">Hari ini</span>';
                                            } else {
                                                echo '<span class="text-success">' . $hari_tersisa . ' hari</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-footer text-center">
                                    <a href="<?= base_url('pembesaran/detail/' . $pembesaran->id_pembesaran) ?>" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Batal
                                    </a>
                                    <button type="reset" class="btn btn-warning" id="btn-reset">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-vigaza" id="btn-update">
                                        <i class="fa fa-save"></i> Update Data
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
    // Hitung mortalitas
    function hitungMortalitas() {
        var jmlAwal = <?= $pembesaran->jml_awal ?>;
        var jmlSaatIni = parseInt($('#jml_saat_ini').val()) || 0;
        
        if (jmlSaatIni < jmlAwal) {
            var mortalitas = ((jmlAwal - jmlSaatIni) / jmlAwal * 100).toFixed(2);
            var mati = jmlAwal - jmlSaatIni;
            $('#mortalitas-info').html(`<i class="fa fa-exclamation-triangle text-warning"></i> Mortalitas: ${mortalitas}% (${mati} ekor)`);
        } else {
            $('#mortalitas-info').html('<i class="fa fa-check text-success"></i> Tidak ada mortalitas');
        }
    }

    // Update target info
    function updateTargetInfo() {
        var targetDate = new Date($('#target_panen').val());
        var today = new Date();
        var mulaiDate = new Date('<?= $pembesaran->tgl_masuk ?>');
        
        if (targetDate) {
            var diffTime = targetDate - today;
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            var totalDays = Math.ceil((targetDate - mulaiDate) / (1000 * 60 * 60 * 24));
            
            var infoText = '';
            if (diffDays < 0) {
                infoText = `<i class="fa fa-exclamation-triangle text-danger"></i> Terlambat ${Math.abs(diffDays)} hari (Total durasi: ${totalDays} hari)`;
            } else if (diffDays == 0) {
                infoText = `<i class="fa fa-clock-o text-warning"></i> Target hari ini (Total durasi: ${totalDays} hari)`;
            } else {
                infoText = `<i class="fa fa-check text-success"></i> ${diffDays} hari lagi (Total durasi: ${totalDays} hari)`;
            }
            
            $('#target-info').html(infoText);
        }
    }

    // Event listeners
    $('#jml_saat_ini').on('input', hitungMortalitas);
    $('#target_panen').on('change', updateTargetInfo);

    // Form validation
    $('#form-edit-pembesaran').on('submit', function(e) {
        e.preventDefault();
        
        var jmlSaatIni = parseInt($('#jml_saat_ini').val());
        var jmlAwal = <?= $pembesaran->jml_awal ?>;
        
        if (jmlSaatIni > jmlAwal) {
            Swal.fire({
                icon: 'error',
                title: 'Input Tidak Valid!',
                text: 'Jumlah saat ini tidak boleh lebih dari jumlah awal',
                confirmButtonColor: '#0eaab4'
            });
            return false;
        }
        
        var form = $(this);
        $('#btn-update').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        
        // Submit form
        setTimeout(function() {
            form.unbind('submit').submit();
        }, 500);
    });

    // Reset form
    $('#btn-reset').on('click', function() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua perubahan akan dikembalikan ke data asli.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0eaab4',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    });

    // Initialize
    hitungMortalitas();
    updateTargetInfo();
});
</script>
