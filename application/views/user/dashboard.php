<div class="row">
    <div class="col-xl-6 col-md-6">
        <div class="card bg-danger mini-stat text-white">
            <div class="p-3 mini-stat-desc">
                <div class="clearfix">
                    <h6 class="text-uppercase mt-0 float-left text-white-50">Belum Bayar Registrasi</h6>
                    <h4 class="mb-3 mt-0 float-right"><?= rupiah($jml_bb_regist); ?></h4>
                </div>
                <!-- <div>
                    <span class="badge badge-light text-info"> +11% </span> <span class="ml-2">From previous period</span>
                </div> -->

            </div>
            <div class="p-3">
                <div class="float-right">
                    <b class="text-white-50"><i class="far fa-money-bill-alt h3"></i></b>
                </div>
                <p class="font-14 m-0">Jumlah : <b><?= $bb_regist; ?></b> Anak</p>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6">
        <div class="card bg-pink mini-stat text-white">
            <div class="p-3 mini-stat-desc">
                <div class="clearfix">
                    <h6 class="text-uppercase mt-0 float-left text-white-50">BELUM BAYAR SPP</h6>
                    <h4 class="mb-3 mt-0 float-right"><?= rupiah($jml_bb_spp); ?></h4>
                </div>
                <!-- <div>
                    <span class="badge badge-light text-danger"> -29% </span> <span class="ml-2">From previous period</span>
                </div> -->
            </div>
            <div class="p-3">
                <div class="float-right">
                    <b class="text-white-50"><i class="fas fa-dollar-sign h3"></i></b>
                </div>
                <p class="font-14 m-0">Jumlah : <b><?= $bb_spp; ?></b> Anak</p>
            </div>
        </div>
    </div>
</div>
<!-- end row -->