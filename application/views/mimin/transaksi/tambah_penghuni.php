<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backoffice/in_penghuni'); ?>
            <div class="card-body">

                <h4 class="mt-0 header-title">Penghuni</h4>
                <p class="text-muted m-b-30">
                    Tambah data penghuni
                </p>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Nama</label>
                            <input class="form-control" name="nama" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>No. Whatsapp (ex : <b style="color:red;">62</b>81917892)</label>
                            <input class="form-control" name="hp" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" name="email" type="text">
                        </div>
                        <div class="form-group">
                            <label>No. Darurat</label>
                            <textarea class="form-control" name="darurat"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Kamar</label>
                            <select id="kamar" class="form-control select2" name="kamar" required>
                                <option value="">Pilih Kamar</option>
                                <option value="" disabled>-------------------</option>
                                <?php
                                foreach ($kamar as $r) {
                                ?>
                                    <option value="<?= $r->id_kamar ?>"><?= $r->jenis; ?> - <?= $r->nomor; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Kendaraan</label>
                            <input class="form-control" name="kendaraan" type="text">
                        </div>
                        <div class="form-group">
                            <label>No. Plat</label>
                            <input class="form-control" name="plat" type="text">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Masuk</label>
                            <input class="form-control" name="tgl_masuk" id="tgl_lahir" type="date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea class="form-control" name="ket"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Foto KTP</label>
                            <input class="form-control" name="fotopost" type="file">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->

<script>
    // function getAge() {
    //     var date = document.getElementById('tgl_lahir').value;

    //     if (date === "") {
    //         alert("Please complete the required field!");
    //     } else {
    //         var today = new Date();
    //         var birthday = new Date(date);
    //         var year = 0;
    //         if (today.getMonth() < birthday.getMonth()) {
    //             year = 1;
    //         } else if ((today.getMonth() == birthday.getMonth()) && today.getDate() < birthday.getDate()) {
    //             year = 1;
    //         }

    //         var age = today.getFullYear() - birthday.getFullYear() - year;
    //         var bulan = today.getMonth() - birthday.getMonth();

    //         if (age < 0) {
    //             age = 0;
    //         }

    //         if (bulan < 0) {
    //             bulan = 0;
    //         }
    //         document.getElementById('umur').innerHTML = age + ' tahun ' + bulan + ' bulan';
    //     }
    // }
</script>