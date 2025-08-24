<div class="row">
    <div class="col-lg-8">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Jenis Kamar</h4>
                    <p class="text-muted m-b-30">Manajemen jenis kamar
                        <span class="float-right d-md-block"><a href="<?php echo base_url('backoffice/tambah_jkamar'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span>
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Jenis Kamar</th>
                                <th>Harga Kamar</th>
                                <th>Tahun</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($jkamar)) {
                                $no = 1;
                                foreach ($jkamar as $row) {
                            ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <td><?= ucwords($row->jenis); ?></td>
                                        <td><?= rupiah($row->harga); ?></td>
                                        <td><?= $row->tahun; ?></td>
                                        <td>
                                            <a href="<?php echo base_url('backoffice/edit_jkamar/' . $row->id_jenis_kamar . ''); ?>"><button class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></button></a>
                                            <a href="<?php echo base_url('backoffice/hapus_jkamar/' . $row->id_jenis_kamar . ''); ?>"><button class="btn btn-sm btn-danger" onclick="return doConfirm();"><i class="fa fa-trash"></i></button></a>
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