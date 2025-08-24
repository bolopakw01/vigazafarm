<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Penghuni</h4>
                    <p class="text-muted m-b-30">Manajemen data penghuni
                        <span class="float-right d-md-block"><a href="<?php echo base_url('backoffice/tambah_penghuni'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span>
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kamar</th>
                                <th>Nama</th>
                                <th>WhatsApp</th>
                                <th>Tgl Masuk</th>
                                <th>Status</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($data)) {
                                $no = 1;
                                foreach ($data as $row) {
                                    $tanggal = date('Y-m-d', strtotime($row->tgl_masuk));
                            ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <td><?= ucwords($row->jenis); ?> - <?= $row->nomor; ?></td>
                                        <td><?= ucwords($row->nama); ?></td>
                                        <td><?= $row->hp; ?></td>
                                        <td><?= tanggal_indo($tanggal); ?></td>
                                        <td>
                                            <?php if ($row->statusnya == 'aktif') { ?>
                                                <a href="<?= base_url('backoffice/status_penghuni/' . $row->id_penghuni . '/' . $row->statusnya . '/' . $row->id_kamar . ''); ?>"><button type="button" class="btn btn-success btn-sm" onclick="return nonaktif();">AKTIF</button></a>
                                            <?php } else { ?>
                                                <a href="<?= base_url('backoffice/status_penghuni/' . $row->id_penghuni . '/' . $row->statusnya . '/' . $row->id_kamar . ''); ?>"><button type="button" class="btn btn-danger btn-sm" onclick="return aktif();">TIDAK AKTIF</button></a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('backoffice/edit_penghuni/' . $row->id_penghuni . ''); ?>"><button class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></button></a>
                                            <!-- <a href="<?php echo base_url('backoffice/hapus_penghuni/' . $row->id_penghuni . ''); ?>"><button class="btn btn-sm btn-danger" onClick="return doConfirm();"><i class="fa fa-trash"></i></button></a> -->
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