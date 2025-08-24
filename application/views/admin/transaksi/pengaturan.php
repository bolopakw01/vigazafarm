<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Pengaturan</h4>
                    <p class="text-muted m-b-30">Manajemen data pengaturan sistem
                        <!-- <span class="float-right d-md-block"><a href="<?php echo base_url('backadmin/tambah_campaign'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span> -->
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table class="table table-borderless">
                        <!-- <tr>
                            <td>Nominal SPP Per-Bulan</td>
                            <td>
                                <?= form_open('backadmin/up_nominal'); ?>
                                <div class="input-group mb-3">
                                    <input type="text" name="nominal" onkeyup="convertToRupiah(this);" class="form-control" aria-describedby="basic-addon2" value="<?= $data['nominal']; ?>">
                                    <div class="input-group-append">
                                        <button class="input-group-text btn-sm btn-info" type="submit" style="margin-left: 25px; border-radius: 2px;" id="basic-addon2">Update</button>
                                    </div>
                                </div>
                                <?= form_close(); ?>
                            </td>
                        </tr> -->
                        <tr>
                            <?php //= form_open('backadmin/generate', array('id' => 'tes')); 
                            ?>
                            <?= form_open('backadmin/generate'); ?>
                            <td>Generate Data Siswa Bulan Ini</td>
                            <td>
                                <? //php if ($generate == true) { 
                                ?>
                                <!-- <button id="generate" type="button" class="btn btn-sm btn-success disabled">SUDAH DI GENERATE UNTUK BULAN INI</button> -->
                                <? //php } else { 
                                ?>
                                <button id="generate" type="submit" class="btn btn-sm btn-success">GENERATE</button>
                                <button id="loading" class="btn btn-success btn-sm disabled" type="button"><i class=" fa fa-spinner fa-spin"></i> &nbsp;LOADING</button>
                                <? //php } 
                                ?>
                            </td>
                            <?= form_close(); ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->