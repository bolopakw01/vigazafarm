<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backadmin/up_siswa'); ?>
            <div class="card-body">
                <h4 class="mt-0 header-title">Siswa</h4>
                <p class="text-muted m-b-30">
                    Ubah data siswa
                </p>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="hidden" name="id" value="<?= $data['id_mnl_siswa']; ?>">
                            <input type="hidden" name="pass_db" value="<?= $data['password']; ?>" />
                            <input class="form-control" name="nama" type="text" value="<?= $data['nama']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" name="email" type="text" value="<?= $data['email']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input class="form-control" name="password" type="password" value="<?= $data['password']; ?>">
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
                            <label>Biaya Registrasi</label>
                            <select id="regist" class="form-control select2" name="regist">
                                <option value="<?= $data['id_mnl_regist']; ?>"><?= ucwords($data['regist']); ?></option>
                                <option value="" disabled>-------------------</option>
                                <?php
                                foreach ($regist as $r) {
                                ?>
                                    <option value="<?= $r->id_mnl_regist ?>"><?= ucwords($r->nama); ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea rows="3" class="form-control" name="alamat"><?= $data['alamat']; ?></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Nama Orang Tua</label>
                            <input class="form-control" name="ortu" type="text" value="<?= $data['ortu']; ?>">
                        </div>
                        <div class="form-group">
                            <label>No. HP</label>
                            <input class="form-control" name="no_hp" type="text" value="<?= $data['no_hp']; ?>" readonly="readonly">
                        </div>
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
                            <label>Biaya SPP</label>
                            <select id="spp" class="form-control select2" name="spp">
                                <option value="<?= $data['id_mnl_spp']; ?>"><?= ucwords($data['spp']); ?></option>
                                <option value="" disabled>-------------------</option>
                                <?php
                                foreach ($spp as $r) {
                                ?>
                                    <option value="<?= $r->id_mnl_spp ?>"><?= ucwords($r->nama); ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input class="form-control" name="tgl_lahir" id="tgl_lahir" type="date" onchange="getAge();" value="<?= $data['tgl_lahir']; ?>">
                            <label id="umur" style="color:blue;font-weight:bold;margin-top:5px;font-size:15px;"></label>
                        </div>

                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->

<script>
    function getAge() {
        var date = document.getElementById('tgl_lahir').value;

        if (date === "") {
            alert("Please complete the required field!");
        } else {
            var today = new Date();
            var birthday = new Date(date);
            var year = 0;
            if (today.getMonth() < birthday.getMonth()) {
                year = 1;
            } else if ((today.getMonth() == birthday.getMonth()) && today.getDate() < birthday.getDate()) {
                year = 1;
            }

            var age = today.getFullYear() - birthday.getFullYear() - year;
            var bulan = today.getMonth() - birthday.getMonth();

            if (age < 0) {
                age = 0;
            }

            if (bulan < 0) {
                bulan = 0;
            }
            document.getElementById('umur').innerHTML = age + ' tahun ' + bulan + ' bulan';
        }
    }
</script>