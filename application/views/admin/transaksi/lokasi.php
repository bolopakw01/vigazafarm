<div class="row">
    <div class="col-lg-8">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Lokasi / Tempat</h4>
                    <p class="text-muted m-b-30">Manajemen lokasi / tempat
                        <span class="float-right d-md-block"><a href="<?php echo base_url('backadmin/tambah_lokasi'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span>
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Lokasi / Tempat</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($lokasi)) {
                                $no = 1;
                                foreach ($lokasi as $row) {
                            ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <td><?= ucwords($row->lokasi); ?></td>
                                        <td>
                                            <a href="<?php echo base_url('backadmin/edit_lokasi/' . $row->id_mnl_lokasi . ''); ?>"><button class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></button></a>
                                            <a href="<?php echo base_url('backadmin/hapus_lokasi/' . $row->id_mnl_lokasi . ''); ?>"><button class="btn btn-sm btn-danger" onclick="return doConfirm();"><i class="fa fa-trash"></i></button></a>
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