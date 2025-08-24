<div class="row">
    <div class="col-lg-8">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Kamar</h4>
                    <p class="text-muted m-b-30">Manajemen kamar
                        <span class="float-right d-md-block"><a href="<?php echo base_url('backoffice/tambah_kamar'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span>
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Jenis Kamar</th>
                                <th>Nomor</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($kamar)) {
                                $no = 1;
                                foreach ($kamar as $row) {
                            ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <td><?= $row->jenis; ?></td>
                                        <td><?= $row->nomor; ?></td>
                                        <td>
                                            <?php if ($row->status == 'terisi') { ?>
                                                <a href="<?= base_url('backoffice/status_kamar/' . $row->id_kamar . '/' . $row->status . ''); ?>"><button type="button" class="btn btn-success btn-sm" onclick="return kosong();">TERISI</button></a>
                                            <?php } else { ?>
                                                <a href="<?= base_url('backoffice/status_kamar/' . $row->id_kamar . '/' . $row->status . ''); ?>"><button type="button" class="btn btn-danger btn-sm" onclick="return terisi();">BELUM TERISI</button></a>
                                            <?php } ?>
                                            <a href="<?php echo base_url('backoffice/edit_kamar/' . $row->id_kamar . ''); ?>"><button class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></button></a>
                                            <a href="<?php echo base_url('backoffice/hapus_kamar/' . $row->id_kamar . ''); ?>"><button class="btn btn-sm btn-danger" onclick="return doConfirm();"><i class="fa fa-trash"></i></button></a>
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