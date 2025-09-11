<!-- Page Title -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-action">
                <h1 class="page-title">Daftar Penetasan</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Vigaza Farm</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('penetasan') ?>">Penetasan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Daftar</li>
                </ol>
            </div>
            <div class="header-action">
                <a href="<?= base_url('penetasan/tambah') ?>" class="btn btn-vigaza">
                    <i class="fa fa-plus"></i> Tambah Penetasan
                </a>
                <a href="<?= base_url('penetasan') ?>" class="btn btn-outline-vigaza">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="section-body mt-4">
    <div class="container-fluid">
        <!-- Alert Messages -->
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>

        <div class="row clearfix">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-list text-vigaza"></i> 
                            Data Penetasan Lengkap
                        </h3>
                        <div class="card-options">
                            <span class="badge badge-vigaza"><?= count($penetasan) ?> Data</span>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tabel-penetasan">
                                <thead class="bg-vigaza text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Batch</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Jumlah Telur</th>
                                        <th>Lama Penetasan</th>
                                        <th>Target Selesai</th>
                                        <th>Mesin</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($penetasan)): ?>
                                        <?php foreach ($penetasan as $index => $row): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong class="text-vigaza"><?= htmlspecialchars($row->batch) ?></strong>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($row->tanggal_mulai)) ?></td>
                                            <td>
                                                <span class="badge badge-secondary"><?= number_format($row->jumlah_telur) ?></span>
                                            </td>
                                            <td><?= $row->lama_penetasan ?> hari</td>
                                            <td>
                                                <?php 
                                                $target_selesai = date('Y-m-d', strtotime($row->tanggal_mulai . ' + ' . $row->lama_penetasan . ' days'));
                                                echo date('d/m/Y', strtotime($target_selesai));
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($row->nama_mesin)): ?>
                                                    <span class="badge badge-vigaza"><?= htmlspecialchars($row->nama_mesin) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $status_class = '';
                                                switch($row->status) {
                                                    case 'proses':
                                                        $status_class = 'badge-warning';
                                                        break;
                                                    case 'selesai':
                                                        $status_class = 'badge-success';
                                                        break;
                                                    case 'gagal':
                                                        $status_class = 'badge-danger';
                                                        break;
                                                    default:
                                                        $status_class = 'badge-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $status_class ?>"><?= ucfirst($row->status) ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $hari_berlalu = floor((strtotime(date('Y-m-d')) - strtotime($row->tanggal_mulai)) / (60*60*24));
                                                $progress = ($hari_berlalu / $row->lama_penetasan) * 100;
                                                $progress = min(100, max(0, $progress));
                                                ?>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar <?= $progress >= 100 ? 'bg-success' : ($progress >= 75 ? 'bg-warning' : 'bg-vigaza') ?>" 
                                                         role="progressbar" 
                                                         style="width: <?= $progress ?>%"
                                                         aria-valuenow="<?= $progress ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?= round($progress) ?>%
                                                    </div>
                                                </div>
                                                <small class="text-muted">Hari ke-<?= $hari_berlalu ?> dari <?= $row->lama_penetasan ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('penetasan/detail/' . $row->id_penetasan) ?>" 
                                                       class="btn btn-sm btn-info" title="Detail">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if ($row->status == 'proses'): ?>
                                                    <a href="<?= base_url('penetasan/edit/' . $row->id_penetasan) ?>" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="hapusPenetasan(<?= $row->id_penetasan ?>, '<?= $row->batch ?>')" 
                                                            title="Hapus">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                <div class="py-4">
                                                    <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Belum ada data penetasan</p>
                                                    <a href="<?= base_url('penetasan/tambah') ?>" class="btn btn-vigaza">
                                                        <i class="fa fa-plus"></i> Tambah Penetasan Pertama
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
.bg-vigaza {
    background-color: #0eaab4 !important;
}

.text-vigaza {
    color: #0eaab4 !important;
}

.btn-vigaza {
    background-color: #0eaab4;
    border-color: #0eaab4;
    color: #fff;
}

.btn-vigaza:hover {
    background-color: #0c9499;
    border-color: #0c9499;
    color: #fff;
}

.btn-outline-vigaza {
    color: #0eaab4;
    border-color: #0eaab4;
}

.btn-outline-vigaza:hover {
    background-color: #0eaab4;
    border-color: #0eaab4;
    color: #fff;
}

.badge-vigaza {
    background-color: #0eaab4;
    color: #fff;
}

.progress-bar.bg-vigaza {
    background-color: #0eaab4 !important;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background-color: transparent;
    border-bottom: 1px solid #f0f0f0;
    padding: 20px;
}

.card-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.breadcrumb {
    margin: 0;
    background: transparent;
    padding: 0;
}

.section-body {
    padding: 20px 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #fff;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

@media (max-width: 768px) {
    .header-action {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .header-action .btn {
        margin-top: 10px;
    }
}
</style>

<!-- DataTables & Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#tabel-penetasan').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[ 2, "desc" ]],
        "pageLength": 25,
        "responsive": true,
        "columnDefs": [
            { "orderable": false, "targets": [8, 9] }
        ]
    });
});

function hapusPenetasan(id, batch) {
    Swal.fire({
        title: 'Hapus Penetasan?',
        text: `Apakah Anda yakin ingin menghapus penetasan batch "${batch}"? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#0eaab4',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= base_url('penetasan/hapus/') ?>${id}`;
        }
    });
}
</script>
