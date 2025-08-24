<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backoffice/up_regist'); ?>
            <div class="card-body">
                <h4 class="mt-0 header-title">Biaya Registrasi</h4>
                <p class="text-muted m-b-30">
                    Ubah data biaya registrasi
                </p>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Club</label>
                            <select id="club" class="form-control select2" name="club">
                                <option value="<?= $data['id_mnl_club']; ?>"><?= ucwords($data['club']); ?></option>
                                <option value="" disabled>-------------------</option>
                                <?php
                                if (!empty($club)) {
                                    foreach ($club as $r) {
                                ?>
                                        <option value="<?= $r->id_mnl_club; ?>"><?= ucwords($r->club); ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lokasi / Tempat</label>
                            <select id="lokasi" class="form-control select2" name="lokasi">
                                <option value="<?= $data['id_mnl_lokasi']; ?>"><?= ucwords($data['lokasi']); ?></option>
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
                                <option value="<?= $data['id_mnl_kelas']; ?>"><?= ucwords($data['kelas']); ?></option>
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
                            <input type="hidden" name="id" value="<?= $data['id_mnl_regist']; ?>">
                            <input class="form-control" onkeyup="convertToRupiah(this);" name="nominal" type="text" value="<?= $data['nominal']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input class="form-control" name="nama" type="text" value="<?= $data['nama']; ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->