<!-- Page Header -->
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-edit"></i>
                    Edit Penetasan
                </h1>
                <p class="page-subtitle">Edit data penetasan telur</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('penetasan') ?>">Penetasan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Form Content -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-form me-2"></i>
                        Form Edit Penetasan: <?= isset($penetasan) ? htmlspecialchars($penetasan['batch']) : '' ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($penetasan)): ?>
                        <form method="post" action="<?= base_url('penetasan/edit/' . $penetasan['id_penetasan']) ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Batch Code</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="batch" 
                                               value="<?= htmlspecialchars($penetasan['batch']) ?>"
                                               style="background: #f8f9fa; color: #6c757d;"
                                               readonly>
                                        <small class="text-muted">Kode batch tidak dapat diubah</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" 
                                               class="form-control" 
                                               name="tanggal_mulai" 
                                               value="<?= $penetasan['tanggal_mulai'] ?>"
                                               required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Mesin Penetasan</label>
                                        <select class="form-control" name="id_mesin">
                                            <option value="">-- Pilih Mesin --</option>
                                            <?php if (isset($mesin) && !empty($mesin)): ?>
                                                <?php foreach ($mesin as $m): ?>
                                                    <option value="<?= $m['id_mesin'] ?>" 
                                                            <?= ($penetasan['id_mesin'] == $m['id_mesin']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($m['nama_mesin']) ?> - Kapasitas: <?= number_format($m['kapasitas']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Jumlah Telur</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="jumlah_telur" 
                                               value="<?= $penetasan['jumlah_telur'] ?>"
                                               min="1"
                                               required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Suhu Rata-rata (°C)</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="suhu_rata" 
                                               value="<?= $penetasan['suhu_rata'] ?>"
                                               step="0.1"
                                               min="35"
                                               max="40"
                                               required>
                                        <small class="text-muted">Suhu optimal: 37.5°C</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Kelembaban Rata-rata (%)</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="kelembaban_rata" 
                                               value="<?= $penetasan['kelembaban_rata'] ?>"
                                               step="0.1"
                                               min="40"
                                               max="80"
                                               required>
                                        <small class="text-muted">Kelembaban optimal: 60%</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Status Penetasan</label>
                                        <select class="form-control" name="status" required>
                                            <option value="persiapan" <?= ($penetasan['status'] == 'persiapan') ? 'selected' : '' ?>>Persiapan</option>
                                            <option value="proses" <?= ($penetasan['status'] == 'proses') ? 'selected' : '' ?>>Proses</option>
                                            <option value="selesai" <?= ($penetasan['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                                            <option value="gagal" <?= ($penetasan['status'] == 'gagal') ? 'selected' : '' ?>>Gagal</option>
                                        </select>
                                        <small class="text-muted">Status dapat diubah sesuai kondisi aktual</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <strong>Status Saat Ini:</strong><br>
                                        <span class="badge badge-<?= $penetasan['status'] == 'proses' ? 'warning' : ($penetasan['status'] == 'selesai' ? 'success' : 'danger') ?>">
                                            <?= ucfirst($penetasan['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" 
                                          name="catatan" 
                                          rows="3" 
                                          placeholder="Tambahkan catatan atau keterangan..."><?= htmlspecialchars($penetasan['catatan']) ?></textarea>
                            </div>
                            
                            <!-- Info Status -->
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Status:</strong><br>
                                        <span class="badge badge-<?= $penetasan['status'] == 'proses' ? 'warning' : ($penetasan['status'] == 'selesai' ? 'success' : 'danger') ?>">
                                            <?= ucfirst($penetasan['status']) ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Tanggal Selesai:</strong><br>
                                        <?= $penetasan['tanggal_selesai'] ? date('d/m/Y', strtotime($penetasan['tanggal_selesai'])) : 'Belum selesai' ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Hasil Menetas:</strong><br>
                                        <?= number_format($penetasan['hasil_menetas'] ?? 0) ?> ekor
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Persentase:</strong><br>
                                        <?= number_format($penetasan['persentase_menetas'] ?? 0, 1) ?>%
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('penetasan') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Update Penetasan
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Data penetasan tidak ditemukan!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const jumlahTelur = parseInt(document.querySelector('input[name="jumlah_telur"]').value) || 0;
            const suhu = parseFloat(document.querySelector('input[name="suhu_rata"]').value) || 0;
            const kelembaban = parseFloat(document.querySelector('input[name="kelembaban_rata"]').value) || 0;
            
            if (jumlahTelur <= 0) {
                e.preventDefault();
                alert('Jumlah telur harus lebih dari 0');
                return false;
            }
            
            if (suhu < 35 || suhu > 40) {
                e.preventDefault();
                alert('Suhu harus antara 35-40°C');
                return false;
            }
            
            if (kelembaban < 40 || kelembaban > 80) {
                e.preventDefault();
                alert('Kelembaban harus antara 40-80%');
                return false;
            }
        });
    }
});
</script>
