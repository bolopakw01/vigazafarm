<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backoffice/in_kamar'); ?>
            <div class="card-body">
                <h4 class="mt-0 header-title">Lokasi</h4>
                <p class="text-muted m-b-30">
                    Tambah data kamar
                </p>
                <div class="form-group">
                    <label>Jenis Kamar</label>
                    <select id="jkamar" class="form-control select2" name="jkamar" required>
                        <option value="">Pilih Jenis Kamar</option>
                        <option value="" disabled>-------------------</option>
                        <?php
                        foreach ($jkamar as $r) {
                        ?>
                            <option value="<?= $r->id_jenis_kamar ?>"><?= ucwords($r->jenis); ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor</label>
                    <input class="form-control" name="nomor" type="text">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->