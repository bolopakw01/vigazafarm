<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">
                    <i class="fa fa-eye"></i> Detail Penetasan - <?= htmlspecialchars($penetasan->batch) ?>
                </h4>
                <div>
                    <?php if ($penetasan->status == 'proses'): ?>
                    <a href="<?= base_url('penetasan/edit/' . $penetasan->id_penetasan) ?>" class="btn btn-warning">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <?php endif; ?>
                    <a href="<?= base_url('penetasan') ?>" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Informasi Utama -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informasi Penetasan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="150"><strong>Batch:</strong></td>
                                                <td><?= htmlspecialchars($penetasan->batch) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Jumlah Telur:</strong></td>
                                                <td><?= number_format($penetasan->jumlah_telur) ?> telur</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tanggal Mulai:</strong></td>
                                                <td><?= date('d F Y', strtotime($penetasan->tanggal_mulai)) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Lama Penetasan:</strong></td>
                                                <td><?= $penetasan->lama_penetasan ?> hari</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Target Selesai:</strong></td>
                                                <td>
                                                    <?php 
                                                    $target_selesai = date('Y-m-d', strtotime($penetasan->tanggal_mulai . ' + ' . $penetasan->lama_penetasan . ' days'));
                                                    echo date('d F Y', strtotime($target_selesai));
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="150"><strong>Mesin:</strong></td>
                                                <td>
                                                    <?php if (isset($penetasan->nama_mesin)): ?>
                                                        <?= htmlspecialchars($penetasan->nama_mesin) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak menggunakan mesin</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Suhu:</strong></td>
                                                <td>
                                                    <?php if ($penetasan->suhu): ?>
                                                        <?= $penetasan->suhu ?>°C
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kelembaban:</strong></td>
                                                <td>
                                                    <?php if ($penetasan->kelembaban): ?>
                                                        <?= $penetasan->kelembaban ?>%
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    <?php 
                                                    $status_class = '';
                                                    switch($penetasan->status) {
                                                        case 'proses':
                                                            $status_class = 'bg-warning';
                                                            break;
                                                        case 'selesai':
                                                            $status_class = 'bg-success';
                                                            break;
                                                        case 'gagal':
                                                            $status_class = 'bg-danger';
                                                            break;
                                                        default:
                                                            $status_class = 'bg-secondary';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $status_class ?> fs-6"><?= ucfirst($penetasan->status) ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Dibuat:</strong></td>
                                                <td><?= date('d/m/Y H:i', strtotime($penetasan->tanggal . ' ' . $penetasan->waktu)) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Timeline -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Progress Penetasan</h5>
                            </div>
                            <div class="card-body">
                                <?php 
                                $hari_berlalu = floor((strtotime(date('Y-m-d')) - strtotime($penetasan->tanggal_mulai)) / (60*60*24));
                                $progress = ($hari_berlalu / $penetasan->lama_penetasan) * 100;
                                $progress = min(100, max(0, $progress));
                                ?>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><strong>Hari ke-<?= $hari_berlalu ?></strong> dari <?= $penetasan->lama_penetasan ?> hari</span>
                                    <span><strong><?= round($progress) ?>%</strong></span>
                                </div>
                                
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar <?= $progress >= 100 ? 'bg-success' : ($progress >= 75 ? 'bg-warning' : 'bg-info') ?>" 
                                         role="progressbar" 
                                         style="width: <?= $progress ?>%"
                                         aria-valuenow="<?= $progress ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?= round($progress) ?>%
                                    </div>
                                </div>

                                <!-- Timeline Milestones -->
                                <div class="row">
                                    <?php 
                                    $milestones = [
                                        ['day' => 1, 'title' => 'Mulai Penetasan', 'desc' => 'Telur masuk inkubator'],
                                        ['day' => 7, 'title' => 'Minggu 1', 'desc' => 'Pemeriksaan candling pertama'],
                                        ['day' => 14, 'title' => 'Minggu 2', 'desc' => 'Pemeriksaan candling kedua'],
                                        ['day' => intval($penetasan->lama_penetasan), 'title' => 'Hari Menetas', 'desc' => 'Target penetasan selesai']
                                    ];
                                    
                                    foreach ($milestones as $milestone):
                                        $is_passed = $hari_berlalu >= $milestone['day'];
                                        $is_current = $hari_berlalu == $milestone['day'];
                                    ?>
                                    <div class="col-md-3">
                                        <div class="timeline-item text-center">
                                            <div class="timeline-marker <?= $is_passed ? 'bg-success' : ($is_current ? 'bg-warning' : 'bg-light') ?> rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                                                <i class="fa <?= $is_passed ? 'fa-check' : ($is_current ? 'fa-clock' : 'fa-circle') ?> text-white"></i>
                                            </div>
                                            <h6 class="<?= $is_passed ? 'text-success' : ($is_current ? 'text-warning' : 'text-muted') ?>">
                                                <?= $milestone['title'] ?>
                                            </h6>
                                            <small class="text-muted"><?= $milestone['desc'] ?></small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Hasil Penetasan (jika sudah selesai) -->
                        <?php if ($penetasan->status == 'selesai'): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Hasil Penetasan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-success"><?= number_format($penetasan->hasil_menetas ?? 0) ?></h3>
                                            <p class="text-muted">Telur Menetas</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-danger"><?= number_format($penetasan->telur_gagal ?? 0) ?></h3>
                                            <p class="text-muted">Telur Gagal</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <?php $persentase = $penetasan->jumlah_telur > 0 ? round((($penetasan->hasil_menetas ?? 0) / $penetasan->jumlah_telur) * 100, 1) : 0; ?>
                                            <h3 class="text-info"><?= $persentase ?>%</h3>
                                            <p class="text-muted">Tingkat Keberhasilan</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-warning"><?= ucfirst($penetasan->kualitas ?? 'Baik') ?></h3>
                                            <p class="text-muted">Kualitas</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (!empty($penetasan->catatan)): ?>
                                <div class="mt-3">
                                    <h6>Catatan:</h6>
                                    <p class="text-muted"><?= nl2br(htmlspecialchars($penetasan->catatan)) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar Info -->
                    <div class="col-md-4">
                        <!-- Status Card -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Status Penetasan</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fa fa-egg fa-3x <?= $penetasan->status == 'selesai' ? 'text-success' : ($penetasan->status == 'proses' ? 'text-warning' : 'text-danger') ?>"></i>
                                </div>
                                <h4 class="<?= $penetasan->status == 'selesai' ? 'text-success' : ($penetasan->status == 'proses' ? 'text-warning' : 'text-danger') ?>">
                                    <?= ucfirst($penetasan->status) ?>
                                </h4>
                                
                                <?php if ($penetasan->status == 'proses'): ?>
                                <div class="mt-3">
                                    <button class="btn btn-success btn-sm" onclick="updateStatus('selesai')">
                                        <i class="fa fa-check"></i> Tandai Selesai
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="updateStatus('gagal')">
                                        <i class="fa fa-times"></i> Tandai Gagal
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Statistik Cepat</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Sisa Hari:</span>
                                    <strong class="text-info">
                                        <?= max(0, $penetasan->lama_penetasan - $hari_berlalu) ?> hari
                                    </strong>
                                </div>
                                
                                <?php if ($penetasan->status == 'selesai' && isset($penetasan->hasil_menetas)): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Hasil:</span>
                                    <strong class="text-success">
                                        <?= number_format($penetasan->hasil_menetas) ?>/<?= number_format($penetasan->jumlah_telur) ?>
                                    </strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Success Rate:</span>
                                    <strong class="text-info">
                                        <?= $persentase ?? 0 ?>%
                                    </strong>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Aksi -->
                        <?php if ($penetasan->status == 'proses'): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Aksi</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-warning" onclick="window.location.href='<?= base_url('penetasan/edit/' . $penetasan->id_penetasan) ?>'">
                                        <i class="fa fa-edit"></i> Edit Data
                                    </button>
                                    <button class="btn btn-info" onclick="inputHasil()">
                                        <i class="fa fa-clipboard-check"></i> Input Hasil
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    const statusText = status === 'selesai' ? 'selesai' : 'gagal';
    
    Swal.fire({
        title: `Ubah Status ke ${statusText.toUpperCase()}?`,
        text: `Apakah Anda yakin ingin mengubah status penetasan menjadi ${statusText}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: status === 'selesai' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('penetasan/update_status') ?>',
                type: 'POST',
                data: {
                    id: <?= $penetasan->id_penetasan ?>,
                    status: status
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengupdate status'
                    });
                }
            });
        }
    });
}

function inputHasil() {
    Swal.fire({
        title: 'Input Hasil Penetasan',
        html: `
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="hasil_menetas" class="form-label">Jumlah Telur Menetas:</label>
                    <input type="number" id="hasil_menetas" class="form-control" max="<?= $penetasan->jumlah_telur ?>" min="0">
                </div>
                <div class="col-12 mb-3">
                    <label for="telur_gagal" class="form-label">Jumlah Telur Gagal:</label>
                    <input type="number" id="telur_gagal" class="form-control" max="<?= $penetasan->jumlah_telur ?>" min="0">
                </div>
                <div class="col-12 mb-3">
                    <label for="kualitas" class="form-label">Kualitas:</label>
                    <select id="kualitas" class="form-select">
                        <option value="baik">Baik</option>
                        <option value="sedang">Sedang</option>
                        <option value="kurang">Kurang</option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="catatan" class="form-label">Catatan:</label>
                    <textarea id="catatan" class="form-control" rows="3"></textarea>
                </div>
            </div>
        `,
        width: 500,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const hasilMenetas = parseInt(document.getElementById('hasil_menetas').value) || 0;
            const telurGagal = parseInt(document.getElementById('telur_gagal').value) || 0;
            const kualitas = document.getElementById('kualitas').value;
            const catatan = document.getElementById('catatan').value;
            
            if (hasilMenetas + telurGagal > <?= $penetasan->jumlah_telur ?>) {
                Swal.showValidationMessage('Total hasil tidak boleh melebihi jumlah telur');
                return false;
            }
            
            return {
                hasil_menetas: hasilMenetas,
                telur_gagal: telurGagal,
                kualitas: kualitas,
                catatan: catatan
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('penetasan/update_hasil') ?>',
                type: 'POST',
                data: {
                    id_penetasan: <?= $penetasan->id_penetasan ?>,
                    ...result.value
                },
                success: function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Hasil penetasan berhasil disimpan',
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menyimpan hasil'
                    });
                }
            });
        }
    });
}
</script>

<style>
.timeline-item {
    position: relative;
}

.timeline-marker {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
