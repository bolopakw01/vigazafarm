<!-- Start Page title and tab -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="header-action">
                <h1 class="page-title">Pembesaran</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Vigaza Farm</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pembesaran</li>
                </ol>
            </div>
            <!-- <ul class="nav nav-tabs page-header-tab">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Library-all">List View</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Library-add">Add</a></li>
                <li class="nav-item"><a class="nav-link" id="Library-tab-Boot" data-toggle="tab" href="#Library-add-Boot">Add Bootstrap Style</a></li>
            </ul> -->
        </div>
    </div>
</div>
<div class="section-body mt-4">
    <div class="container-fluid">
        <div class="tab-content">
            <div class="tab-pane active" id="Library-all">
                <div class="card">
                    <div class="card-body">
                        <a href="javascript:void();" data-toggle="modal" data-target="#modal-insert"><button type="button" class="btn bcg-biru pull-right"><i class="fa fa-plus"></i></button></a>
                        <div class="table-responsive">
                            <br />
                            <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Periode</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Populasi Awal</th>
                                        <th>Kandang</th>
                                        <th>Status</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($data)) {
                                        $no = 1;
                                        foreach ($data as $row) {
                                    ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= $row->periode; ?></td>
                                                <td><strong><?= $row->tgl_masuk; ?></strong></td>
                                                <td><?= $row->populasi; ?></td>
                                                <td><?= $row->kandang; ?></td>

                                                <td><span class="tag tag-success"><?= $row->status; ?></span></td>
                                                <td>
                                                    <a href="<?=base_url('backoffice/detail_pembesaran');?>"><button type="button" class="btn btn-icon btn-sm" title="View"><i class="fa fa-eye"></i></button></a>
                                                    <button type="button" class="btn btn-icon btn-sm" title="Edit"><i class="fa fa-edit"></i></button>
                                                    <button type="button" class="btn btn-icon btn-sm js-sweetalert" title="Delete" data-type="confirm"><i class="fa fa-trash-o text-danger"></i></button>
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
            </div>
            <div class="tab-pane" id="Library-add">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Library</h3>
                        <div class="card-options ">
                            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="text" value="" placeholder="Enter Title" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="text" value="" placeholder="Enter Subject" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="text" value="" placeholder="Department" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="text" value="" placeholder="Enter Type" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="text" value="" placeholder="Enter Year" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="text" value="" placeholder="Enter Status" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button class="btn btn-primary btn-lg btn-simple">Add Library</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="Library-add-Boot">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Library</h3>
                        <div class="card-options ">
                            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
                        </div>
                    </div>
                    <form class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Title <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Subject <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <select class="form-control input-height" name="sub">
                                    <option value="">Select...</option>
                                    <option value="Category 1">Mathematics</option>
                                    <option value="Category 2">Science</option>
                                    <option value="Category 3">Software</option>
                                    <option value="Category 3">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Purchase Date <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <input data-provide="datepicker" data-date-autoclose="true" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Auther Name <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Publisher <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Price <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Department <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <select class="form-control input-height" name="departmnt">
                                    <option value="">Select...</option>
                                    <option value="Category 1">Mathematics</option>
                                    <option value="Category 2">Engineering</option>
                                    <option value="Category 3">Science</option>
                                    <option value="Category 3">M.B.A.</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Asset Type <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <select class="form-control input-height" name="assttype">
                                    <option value="">Select...</option>
                                    <option value="Category 1">Book</option>
                                    <option value="Category 2">CD</option>
                                    <option value="Category 3">DVD</option>
                                    <option value="Category 3">NewsPaper</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Asset Details <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <textarea rows="4" class="form-control no-resize" placeholder="Please type what you want..."></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"></label>
                            <div class="col-md-7">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="submit" class="btn btn-outline-secondary">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL INSERT OPEN -->
<div id="modal-insert" class="modal fade" style="width: 100%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    Tambah Periode Pembesaran
                </h6>
            </div>
            <form method="post" enctype="multipart/form-data" action="<?= base_url('backoffice/in_pembesaran'); ?>">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6">
                            <input type="hidden" name="uriseg" value="<?= $this->uri->segment(3); ?>">
                            <!-- <div class="form-group">
                                            <input name="user" id="user2" type="radio" class="user" value="baru" checked="checked" />
                                            <label for="user2">Penghuni Baru</label>&nbsp;&nbsp;&nbsp;
                                            <input name="user" id="user" type="radio" class="user" value="lama" />
                                            <label for="user">Penghuni Lama</label>
                                            <div id="lama">
                                                <select class="form-control select2" name="penghunilama" id="dtNyad" onchange="changeValued(this.value)">
                                                    <option value="">Pilih Penghuni</option>
                                                    <option value="" disabled>-------------------</option>
                                                    <?php
                                                    $jsArrayd = "var dtItemd = new Array();\n";
                                                    foreach ($penghunilama as $ro) {
                                                    ?>
                                                        <option value="<?= $ro->id_penghuni ?>"><?= ucwords($ro->identitas) . ' - ' . ucwords($ro->nama) . ' - ' . ucwords($ro->hp) . ''; ?></option>
                                                    <?php
                                                        $jsArrayd .= "dtItemd['" . $ro->id_penghuni . "'] = 
                                                        {
                                                            lama_nama:'" . addslashes($ro->nama) . "',
                                                            lama_identitas:'" . addslashes($ro->identitas) . "',
                                                            lama_tgl_lahir:'" . addslashes($ro->tgl_lahir) . "',
                                                            lama_email:'" . addslashes($ro->email) . "',
                                                            lama_hp:'" . addslashes($ro->hp) . "',
                                                            lama_tgl_masuk:'" . addslashes($ro->tgl_masuk) . "',
                                                            lama_darurat:'" . addslashes($ro->darurat) . "',
                                                            lama_pekerjaan:'" . addslashes($ro->pekerjaan) . "',
                                                            lama_instansi:'" . addslashes($ro->instansi) . "',
                                                            lama_alamat:'" . addslashes($ro->alamat) . "',
                                                            lama_kendaraan:'" . addslashes($ro->kendaraan) . "',
                                                            lama_plat:'" . addslashes($ro->plat) . "',
                                                            lama_los:'" . addslashes($ro->los) . "',
                                                            lama_deposit:'" . addslashes($ro->deposit) . "',
                                                            lama_ket:'" . addslashes($ro->ket) . "',
                                                        };\n";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div> -->
                            <div class="form-group">
                                <input type="text" id="lama_nama" class="form-control" name="nama_periode" placeholder="Nama Periode" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Masuk</label>
                                <input type="date" id="lama_tgl_lahir" class="form-control" name="tgl_masuk" placeholder="Tanggal Masuk" required>
                            </div>
                            <!-- <div class="form-group">
                                <input type="text" id="lama_nama" class="form-control" name="nama_pic" placeholder="Nama PIC" required>
                            </div>
                            <div class="form-group">
                                <input type="text" id="lama_identitas" class="form-control" name="hp_pic" placeholder="No. HP PIC" required>
                                <label style="color:red; font-size:10px;">Masukkan No. Passport - Masa Berlaku Visa jika WNA</label>
                            </div> -->

                            <!-- <div class="form-group">
                                            <input type="email" id="lama_email" class="form-control" name="email" placeholder="Email" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" id="lama_hp" class="form-control" name="hp" placeholder="No. HP / WhatsApp" required>
                                        </div> -->
                            <!-- <div class="form-group">
                                <input type="text" id="lama_darurat" class="form-control" name="darurat" placeholder="No. HP / WhatsApp Darurat" required>
                                <textarea id="lama_darurat" class="form-control" name="darurat" placeholder="No. HP / WhatsApp Darurat" required></textarea>
                                <label style="color:red; font-size:10px;">Ex : 081912718271 / Ibu / Winarni / Jl. Banyuwangi No. 01 </label>
                            </div>
                            <div class="form-group">
                                <input id="lama_diskon" type="number" class="form-control" name="diskon" placeholder="Diskon" required>
                                <label style="color:red; font-size:10px;">Satuan dalam % (Presentase)</label>
                            </div> -->
                            <div class="form-group">
                                <select class="form-control select2" name="kandang" id="dtNya" onchange="changeValue(this.value)" required>
                                    <option value="">Pilih Kandang</option>
                                    <option value="" disabled>-------------------</option>
                                    <?php
                                    $jsArray = "var dtItem = new Array();\n";
                                    foreach ($kandang as $r) {
                                    ?>
                                        <option value="<?= $r->id_kandang ?>"><?= ucwords($r->nama); ?></option>
                                    <?php
                                        $jsArray .= "dtItem['" . $r->id_kandang . "'] = {id_cabangnya:'" . addslashes($r->id_cabang) . "',cabangnya:'" . addslashes($r->cabang) . "',id_jenisnya:'" . addslashes($r->id_jenis_kamar) . "'};\n";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- <div class="form-group">
                                <input id="id_cabangnya" name="cabang" type="hidden">
                                <input id="id_jenisnya" name="id_jenis_kamar" type="hidden">
                                <input id="cabangnya" type="text" class="form-control" placeholder="Cabang" readonly="readonly">
                            </div> -->
                            <script type="text/javascript">
                                <?php echo $jsArray; ?>

                                function changeValue(dtNya) {
                                    document.getElementById('id_cabangnya').value = dtItem[dtNya].id_cabangnya;
                                    document.getElementById('id_jenisnya').value = dtItem[dtNya].id_jenisnya;
                                    document.getElementById('cabangnya').value = dtItem[dtNya].cabangnya;
                                };
                            </script>
                            <!-- </div>
                                    <div class="col-md-6"> -->
                            <!-- <div class="form-group">
                                            <label>Tanggal Masuk</label>
                                            <input type="date" id="lama_tgl_masuk" class="form-control" name="tgl_masuk" placeholder="Tanggal Masuk" required>
                                        </div>
                                        <div class="form-group">
                                            <select id="lama_pekerjaan" class="form-control select2" name="pekerjaan">
                                                <option value="">Pilih Pekerjaan</option>
                                                <option value="" disabled>-------------------</option>
                                                <option value="karyawan">Karyawan</option>
                                                <option value="pelajar">Pelajar / Mahasiswa</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="lama_instansi" class="form-control" name="instansi" placeholder="Nama Instansi" required>
                                        </div>
                                        <div class="form-group">
                                            <textarea id="lama_alamat" class="form-control" name="alamat" placeholder="Alamat Instansi"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="lama_kendaraan" class="form-control" name="kendaraan" placeholder="Kendaraan" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="lama_plat" class="form-control" name="plat" placeholder="Plat Kendaraan" required>
                                        </div>
                                        <div class="form-group">
                                            <select id="lama_los" class="form-control select2" name="los">
                                                <option value="">Pilih Lama Menginap</option>
                                                <option value="" disabled>-------------------</option>
                                                <option value="harian">Harian</option>
                                                <option value="mingguan">Mingguan</option>
                                                <option value="bulanan">Bulanan</option>
                                                <option value="tahunan">Tahunan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input id="lama_deposit" type="text" onkeyup="convertToRupiah(this);" class="form-control" name="deposit" placeholder="Deposit" required>
                                        </div> -->
                            <!-- <div class="form-group">
                                <textarea id="lama_ket" class="form-control" name="alamat" placeholder="Alamat"></textarea>
                            </div> -->

                            <!-- <script type="text/javascript">
                                            <?php echo $jsArrayd; ?>

                                            function changeValued(dtNyad) {
                                                document.getElementById('lama_nama').value = dtItemd[dtNyad].lama_nama;
                                                document.getElementById('lama_identitas').value = dtItemd[dtNyad].lama_identitas;
                                                document.getElementById('lama_tgl_lahir').value = dtItemd[dtNyad].lama_tgl_lahir;
                                                document.getElementById('lama_email').value = dtItemd[dtNyad].lama_email;
                                                document.getElementById('lama_hp').value = dtItemd[dtNyad].lama_hp;
                                                document.getElementById('lama_tgl_masuk').value = dtItemd[dtNyad].lama_tgl_masuk;
                                                document.getElementById('lama_darurat').value = dtItemd[dtNyad].lama_darurat;
                                                document.getElementById('lama_pekerjaan').value = dtItemd[dtNyad].lama_pekerjaan;
                                                document.getElementById('lama_instansi').value = dtItemd[dtNyad].lama_instansi;
                                                document.getElementById('lama_alamat').value = dtItemd[dtNyad].lama_alamat;
                                                document.getElementById('lama_kendaraan').value = dtItemd[dtNyad].lama_kendaraan;
                                                document.getElementById('lama_plat').value = dtItemd[dtNyad].lama_plat;
                                                document.getElementById('lama_los').value = dtItemd[dtNyad].lama_los;
                                                document.getElementById('lama_deposit').value = dtItemd[dtNyad].lama_deposit;
                                                document.getElementById('lama_ket').value = dtItemd[dtNyad].lama_ket;
                                            };
                                        </script> -->
                        </div>
                        <div class="col-md-6">
                            <input type="hidden" name="uriseg" value="<?= $this->uri->segment(3); ?>">
                            <div class="form-group">
                                <input type="text" id="lama_nama" class="form-control" name="harga_doq" placeholder="Harga DOQ" required>
                            </div>
                            <div class="form-group">
                                <input type="text" id="lama_nama" class="form-control" name="populasi_awal" placeholder="Populasi Awal" required>
                            </div>
                            <div class="form-group">
                                <textarea id="lama_ket" class="form-control" name="ket" placeholder="Keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn bcg-hijau">Submit</button>
                    <button type="button" class="btn bcg-merah" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- MODAL INSERT CLOSE -->