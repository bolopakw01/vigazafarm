<!-- Load SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page Title -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-action">
                <h1 class="page-title">Detail Pembesaran</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Vigaza Farm</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pembesaran') ?>">Pembesaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </div>
            <div class="header-action">
                <?php if (isset($pembesaran) && $pembesaran->status == 'aktif'): ?>
                <button class="btn btn-success" onclick="panenPembesaran('<?= $pembesaran->id_pembesaran ?>', '<?= $pembesaran->periode ?>')">
                    <i class="fa fa-check"></i> Selesai Panen
                </button>
                <a href="<?= base_url('pembesaran/edit/' . $pembesaran->id_pembesaran) ?>" class="btn btn-warning">
                    <i class="fa fa-edit"></i> Edit
                </a>
                <?php endif; ?>
                <a href="<?= base_url('pembesaran/daftar') ?>" class="btn btn-outline-vigaza">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="section-body mt-4">
    <div class="container-fluid">
        <?php if (isset($pembesaran)): ?>
        
        <!-- Header Info -->
        <div class="row clearfix">
            <div class="col-12">
                <div class="card bg-vigaza text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="text-white mb-1">
                                    <i class="fa fa-tag"></i> <?= $pembesaran->periode ?>
                                </h3>
                                <p class="mb-0">
                                    <i class="fa fa-home"></i> Kandang: <strong><?= $pembesaran->nama_kandang ?></strong> | 
                                    <i class="fa fa-calendar"></i> Mulai: <strong><?= date('d/m/Y', strtotime($pembesaran->tgl_masuk)) ?></strong> |
                                    <i class="fa fa-users"></i> Populasi: <strong><?= number_format($pembesaran->jml_saat_ini ?? $pembesaran->jml_awal) ?> ekor</strong>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php
                                $status_class = '';
                                switch ($pembesaran->status) {
                                    case 'aktif': $status_class = 'badge-warning'; break;
                                    case 'selesai': $status_class = 'badge-success'; break;
                                    case 'panen': $status_class = 'badge-success'; break;
                                    default: $status_class = 'badge-secondary';
                                }
                                ?>
                                <span class="badge <?= $status_class ?> badge-lg">
                                    <?= ucfirst($pembesaran->status) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress & Stats -->
        <div class="row clearfix">
            <!-- Progress Card -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-line-chart text-vigaza"></i> 
                            Progress Pembesaran
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php 
                            $umur_hari = floor((strtotime(date('Y-m-d')) - strtotime($pembesaran->tgl_masuk)) / (60*60*24));
                            $target_hari = floor((strtotime($pembesaran->target_panen) - strtotime($pembesaran->tgl_masuk)) / (60*60*24));
                            $progress = $target_hari > 0 ? round(($umur_hari / $target_hari) * 100, 2) : 0;
                            $progress = min($progress, 100);
                            
                            if ($pembesaran->status == 'selesai' || $pembesaran->status == 'panen') {
                                $progress = 100;
                                $progress_class = 'bg-success';
                                $progress_text = 'Selesai';
                            } else if ($progress >= 90) {
                                $progress_class = 'bg-warning';
                                $progress_text = 'Siap Panen';
                            } else if ($progress >= 70) {
                                $progress_class = 'bg-info';
                                $progress_text = 'Tahap Akhir';
                            } else {
                                $progress_class = 'bg-vigaza';
                                $progress_text = 'Dalam Proses';
                            }
                        ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-vigaza">Progress Pembesaran</h6>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar <?= $progress_class ?> progress-bar-striped progress-bar-animated" 
                                         role="progressbar" style="width: <?= $progress ?>%" 
                                         aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                        <strong><?= $progress ?>%</strong>
                                    </div>
                                </div>
                                <p class="text-muted mb-0">
                                    <i class="fa fa-info-circle"></i> Status: <?= $progress_text ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-vigaza"><?= $umur_hari ?></h4>
                                            <p class="text-muted">Hari</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-success"><?= $target_hari ?></h4>
                                            <p class="text-muted">Target</p>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($pembesaran->status == 'aktif'): ?>
                                    <?php 
                                    $hari_tersisa = floor((strtotime($pembesaran->target_panen) - strtotime(date('Y-m-d'))) / (60*60*24));
                                    ?>
                                    <div class="text-center mt-2">
                                        <?php if ($hari_tersisa < 0): ?>
                                            <span class="badge badge-danger">Terlambat <?= abs($hari_tersisa) ?> hari</span>
                                        <?php elseif ($hari_tersisa == 0): ?>
                                            <span class="badge badge-warning">Target hari ini</span>
                                        <?php else: ?>
                                            <span class="badge badge-info"><?= $hari_tersisa ?> hari lagi</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-chart-pie text-vigaza"></i> 
                            Statistik Cepat
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Populasi Saat Ini:</span>
                                    <strong class="text-vigaza"><?= number_format($pembesaran->jml_saat_ini ?? $pembesaran->jml_awal) ?></strong>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Populasi Awal:</span>
                                    <strong><?= number_format($pembesaran->jml_awal) ?></strong>
                                </div>
                            </div>
                            <?php if (isset($pembesaran->jml_saat_ini) && $pembesaran->jml_saat_ini < $pembesaran->jml_awal): ?>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Mortalitas:</span>
                                    <?php $mortalitas = round((($pembesaran->jml_awal - $pembesaran->jml_saat_ini) / $pembesaran->jml_awal) * 100, 2); ?>
                                    <strong class="text-danger"><?= $mortalitas ?>%</strong>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Target Panen:</span>
                                    <strong class="text-success"><?= date('d/m/Y', strtotime($pembesaran->target_panen)) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information -->
        <div class="row clearfix">
            <!-- Informasi Detail -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-info-circle text-vigaza"></i> 
                            Informasi Detail
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>ID Pembesaran:</strong></td>
                                <td><?= $pembesaran->id_pembesaran ?></td>
                            </tr>
                            <tr>
                                <td><strong>Periode/Batch:</strong></td>
                                <td><span class="badge badge-vigaza"><?= $pembesaran->periode ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Kandang:</strong></td>
                                <td><?= $pembesaran->nama_kandang ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Masuk:</strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($pembesaran->tgl_masuk)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah Awal:</strong></td>
                                <td><?= number_format($pembesaran->jml_awal) ?> ekor</td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah Saat Ini:</strong></td>
                                <td><?= number_format($pembesaran->jml_saat_ini ?? $pembesaran->jml_awal) ?> ekor</td>
                            </tr>
                            <tr>
                                <td><strong>Target Panen:</strong></td>
                                <td><?= date('d/m/Y', strtotime($pembesaran->target_panen)) ?></td>
                            </tr>
                            <?php if (!empty($pembesaran->berat_awal)): ?>
                            <tr>
                                <td><strong>Berat Awal:</strong></td>
                                <td><?= $pembesaran->berat_awal ?> gram/ekor</td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($pembesaran->target_berat)): ?>
                            <tr>
                                <td><strong>Target Berat:</strong></td>
                                <td><?= $pembesaran->target_berat ?> gram/ekor</td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge <?= $status_class ?>"><?= ucfirst($pembesaran->status) ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Timeline & Aktivitas -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-clock-o text-vigaza"></i> 
                            Timeline Pembesaran
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <!-- Mulai Pembesaran -->
                            <div class="timeline-item">
                                <div class="timeline-marker bg-vigaza"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Pembesaran Dimulai</h6>
                                    <p class="timeline-text">
                                        DOC sebanyak <?= number_format($pembesaran->jml_awal) ?> ekor masuk ke kandang <?= $pembesaran->nama_kandang ?>
                                    </p>
                                    <span class="timeline-date"><?= date('d M Y', strtotime($pembesaran->tgl_masuk)) ?></span>
                                </div>
                            </div>

                            <!-- Progress Check -->
                            <?php if ($umur_hari >= 7): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Minggu Pertama</h6>
                                    <p class="timeline-text">Periode adaptasi dan monitoring awal</p>
                                    <span class="timeline-date"><?= date('d M Y', strtotime($pembesaran->tgl_masuk . ' +7 days')) ?></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($umur_hari >= 14): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Minggu Kedua</h6>
                                    <p class="timeline-text">Fase pertumbuhan aktif</p>
                                    <span class="timeline-date"><?= date('d M Y', strtotime($pembesaran->tgl_masuk . ' +14 days')) ?></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($umur_hari >= 21): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Minggu Ketiga</h6>
                                    <p class="timeline-text">Monitoring intensif dan evaluasi pertumbuhan</p>
                                    <span class="timeline-date"><?= date('d M Y', strtotime($pembesaran->tgl_masuk . ' +21 days')) ?></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Target Panen -->
                            <div class="timeline-item">
                                <div class="timeline-marker <?= $pembesaran->status == 'selesai' ? 'bg-success' : 'bg-secondary' ?>"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">
                                        <?= $pembesaran->status == 'selesai' ? 'Panen Selesai' : 'Target Panen' ?>
                                    </h6>
                                    <p class="timeline-text">
                                        <?= $pembesaran->status == 'selesai' ? 'Pembesaran telah selesai dan siap dipanen' : 'Estimasi waktu panen' ?>
                                    </p>
                                    <span class="timeline-date"><?= date('d M Y', strtotime($pembesaran->target_panen)) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <?php if (!empty($pembesaran->catatan)): ?>
        <div class="row clearfix">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-sticky-note text-vigaza"></i> 
                            Catatan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-vigaza-light">
                            <p class="mb-0"><?= nl2br(htmlspecialchars($pembesaran->catatan)) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- Data Not Found -->
        <div class="row clearfix">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                            <h4>Data Tidak Ditemukan</h4>
                            <p>Data pembesaran yang Anda cari tidak ditemukan atau telah dihapus.</p>
                            <a href="<?= base_url('pembesaran') ?>" class="btn btn-vigaza">
                                <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript -->
<script>
function panenPembesaran(id, periode) {
    Swal.fire({
        title: 'Selesaikan Pembesaran?',
        text: `Apakah Anda yakin ingin menyelesaikan periode "${periode}"? Status akan diubah menjadi "Panen".`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#0eaab4',
        confirmButtonText: 'Ya, Selesai!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= base_url('pembesaran/panen/') ?>${id}`;
        }
    });
}

// Auto refresh untuk data aktif
<?php if (isset($pembesaran) && $pembesaran->status == 'aktif'): ?>
setTimeout(function() {
    location.reload();
}, 300000); // Refresh setiap 5 menit
<?php endif; ?>
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline:before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #0eaab4;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #0eaab4;
}

.timeline-title {
    margin-bottom: 5px;
    color: #0eaab4;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    color: #666;
}

.timeline-date {
    font-size: 12px;
    color: #999;
}

.badge-lg {
    font-size: 14px;
    padding: 8px 16px;
}
</style>
