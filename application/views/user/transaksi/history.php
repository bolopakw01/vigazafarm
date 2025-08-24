<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Payment History</h4>
                    <p class="text-muted m-b-30">Daftar data pembayaran
                        <!-- <span class="float-right d-md-block"><a href="<?php echo base_url('backoffice/tambah_campaign'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span> -->
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Tanggal Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($data)) {
                                $no = 1;
                                foreach ($data as $row) {
                                    $stat = $row->status;
                            ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <td><?= $row->nama; ?></td>
                                        <td><?= month($row->bulan); ?></td>
                                        <td><?= $row->tahun; ?></td>
                                        <td><?= $row->tanggal; ?> || <?= $row->waktu; ?></td>
                                        <td>
                                            <a href="javascript:void();" data-toggle="modal" data-target="#modal-bukti"><button data-bukti="<?= $row->bukti; ?>" class="btn btn-sm btn-success sb" style="font-weight:bold;">Sudah Bayar</button></a>
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

<div id="modal-bukti" class="modal fade" style="width: 100%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    Bukti Transfer
                </h6>
            </div>
            <div class="modal-body">
                <img id="buktitf" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(".sb").click(function() {
        var buktitrf = $(this).attr('data-bukti');
        document.getElementById('buktitf').src = "<?= base_url('assets/back/images/bukti/'); ?>" + buktitrf;
        document.getElementById('buktitf').style.width = '100%';
        document.getElementById('buktitf').style.height = 'auto';
    });
</script>