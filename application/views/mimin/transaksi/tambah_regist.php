<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backoffice/in_regist'); ?>
            <div class="card-body">

                <h4 class="mt-0 header-title">Biaya Registrasi</h4>
                <p class="text-muted m-b-30">
                    Tambah data biaya registrasi
                </p>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Club</label>
                            <select id="club" class="form-control select2" name="club">
                                <option value="">Pilih Club</option>
                                <option value="" disabled>-------------------</option>
                                <?php
                                foreach ($club as $r) {
                                ?>
                                    <option value="<?= $r->id_mnl_club ?>"><?= ucwords($r->club); ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lokasi / Tempat</label>
                            <select id="lokasi" class="form-control select2" name="lokasi">
                                <option value="">Pilih Lokasi / Tempat</option>
                                <option value="" disabled>-------------------</option>
                                <?php
                                foreach ($lokasi as $r) {
                                ?>
                                    <option value="<?= $r->id_mnl_lokasi ?>"><?= ucwords($r->lokasi); ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kelas</label>
                            <select id="kelas" class="form-control select2" name="kelas">
                                <option value="">Pilih Kelas</option>
                                <option value="" disabled>-------------------</option>
                                <?php
                                if (!empty($kelas)) {
                                    foreach ($kelas as $r) {
                                ?>
                                        <option value="<?= $r->id_mnl_kelas; ?>"><?= ucwords($r->kelas); ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nominal</label>
                            <input class="form-control" onkeyup="convertToRupiah(this);" name="nominal" type="text">
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input class="form-control" name="nama" type="text">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->