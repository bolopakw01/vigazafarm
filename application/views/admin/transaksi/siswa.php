<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Siswa</h4>
                    <p class="text-muted m-b-30">Manajemen data siswa
                        <span class="float-right d-md-block"><a href="<?php echo base_url('backadmin/tambah_siswa'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span>
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Registrasi</th>
                                <th>Nama</th>
                                <th>Club</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($data)) {
                                $no = 1;
                                foreach ($data as $row) {
                            ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <!-- <td>
                                            <?php
                                            if ($row->status == 'belum') {
                                            ?>
                                                <a href="<?= base_url('backadmin/status_regist/' . $row->id_mnl_siswa . '/' . $row->status . ''); ?>"><button type="button" class="btn btn-sm btn-danger" onclick="return sudah();">BELUM</button></a>
                                            <?php } else if ($row->status == 'proses') { ?>
                                                <a href="<?= base_url('backadmin/status_regist/' . $row->id_mnl_siswa . '/' . $row->status . ''); ?>"><button type="button" class="btn btn-sm btn-warning" onclick="return belum();">PROSES</button></a>
                                            <?php } else { ?>
                                                <a href="<?= base_url('backadmin/status_regist/' . $row->id_mnl_siswa . '/' . $row->status . ''); ?>"><button type="button" class="btn btn-sm btn-info" onclick="return belum();">SUDAH</button></a>
                                            <?php
                                            }
                                            ?>
                                        </td> -->
                                        <td>
                                            <?php
                                            if ($row->status == 'belum') {
                                            ?>
                                                <button type="button" class="btn btn-sm btn-danger">BELUM</button>
                                            <?php } else if ($row->status == 'proses') { ?>
                                                <button type="button" class="btn btn-sm btn-warning">PROSES</button>
                                            <?php } else { ?>
                                                <button type="button" class="btn btn-sm btn-info">SUDAH</button>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                        <td><?= ucwords($row->nama); ?></td>
                                        <td><?= $row->club; ?></td>
                                        <td><?= $row->no_hp; ?></td>
                                        <td>
                                            <?php if ($row->status == 'aktif') { ?>
                                                <a href="<?= base_url('backadmin/status_siswa/' . $row->id_mnl_siswa . '/' . $row->status . ''); ?>"><button type="button" class="btn btn-success btn-sm" onclick="return nonaktif();">AKTIF</button></a>
                                            <?php } else { ?>
                                                <a href="<?= base_url('backadmin/status_siswa/' . $row->id_mnl_siswa . '/' . $row->status . ''); ?>"><button type="button" class="btn btn-danger btn-sm" onclick="return aktif();">TIDAK AKTIF</button></a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('backadmin/edit_siswa/' . $row->id_mnl_siswa . ''); ?>"><button class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></button></a>
                                            <!-- <a href="<?php echo base_url('backadmin/hapus_siswa/' . $row->id_mnl_siswa . ''); ?>"><button class="btn btn-sm btn-danger" onClick="return doConfirm();"><i class="fa fa-trash"></i></button></a> -->
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->