<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
        <i class="mdi mdi-close"></i>
    </button>

    <div class="left-side-logo d-block d-lg-none">
        <div class="text-center">

            <a href="<?= base_url('backadmin'); ?>" class="logo"><img src="<?php echo base_url('assets/back/'); ?>images/logo.png" height="20" alt="logo"></a>
        </div>
    </div>

    <div class="sidebar-inner slimscrollleft">

        <div id="sidebar-menu">
            <ul>
                <li class="menu-title">Main</li>

                <li <?php if ($thisPage == "dashboard") echo "class='active nav-active'"; ?>>
                    <a href="<?php echo base_url('backadmin'); ?>" class="waves-effect">
                        <i class="dripicons-home"></i>
                        <span> Dashboard</span>
                    </a>
                </li>

                <li class="has_sub <?php if ($thisPage == 'registrasi') : ?>active nav-active <?php endif; ?>">
                    <a href="javascript:void(0);" class="waves-effect <?php if ($thisPage == 'registrasi') : ?>active<?php endif; ?>"><i class="far fa-money-bill-alt"></i> <span> Registrasi </span> <span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?php if ($thisPg == 'semua') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pembayaran_regist/all'); ?>" class="<?php if ($thisPg == 'semua') : ?>active<?php endif; ?>">Semua</a></li>
                        <li class="<?php if ($thisPg == 'butuh approval') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pembayaran_regist/ba'); ?>" class="<?php if ($thisPg == 'butuh approval') : ?>active<?php endif; ?>">Butuh Approval</a></li>
                        <li class="<?php if ($thisPg == 'belum bayar') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pembayaran_regist/bb'); ?>" class="<?php if ($thisPg == 'belum bayar') : ?>active<?php endif; ?>">Belum Bayar</a></li>
                        <li class="<?php if ($thisPg == 'sudah bayar') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pembayaran_regist/sb'); ?>" class="<?php if ($thisPg == 'sudah bayar') : ?>active<?php endif; ?>">Sudah Bayar</a></li>
                    </ul>
                </li>

                <li class="has_sub <?php if ($thisPage == 'pembayaran') : ?>active nav-active <?php endif; ?>">
                    <a href="javascript:void(0);" class="waves-effect <?php if ($thisPage == 'pembayaran') : ?>active<?php endif; ?>"><i class="fas fa-dollar-sign"></i> <span> Pembayaran </span> <span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?php if ($thisPg == 'semua') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pembayaran/all'); ?>" class="<?php if ($thisPg == 'semua') : ?>active<?php endif; ?>">Semua</a></li>
                        <li class="<?php if ($thisPg == 'butuh approval') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pembayaran/ba'); ?>" class="<?php if ($thisPg == 'butuh approval') : ?>active<?php endif; ?>">Butuh Approval</a></li>
                        <li class="<?php if ($thisPg == 'belum bayar') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pembayaran/bb'); ?>" class="<?php if ($thisPg == 'belum bayar') : ?>active<?php endif; ?>">Belum Bayar</a></li>
                        <li class="<?php if ($thisPg == 'sudah bayar') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pembayaran/sb'); ?>" class="<?php if ($thisPg == 'sudah bayar') : ?>active<?php endif; ?>">Sudah Bayar</a></li>
                    </ul>
                </li>

                <li class="menu-title">Backoffice</li>

                <li class="has_sub <?php if ($thisPage == 'master') : ?>active nav-active <?php endif; ?>">
                    <a href="javascript:void(0);" class="waves-effect <?php if ($thisPage == 'master') : ?>active<?php endif; ?>"><i class="dripicons-gear"></i> <span> Data Master </span> <span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?php if ($thisPg == 'pengaturan') : ?>active<?php endif; ?>"><a href="<?= base_url('backadmin/pengaturan'); ?>" class="<?php if ($thisPg == 'pengaturan') : ?>active<?php endif; ?>">Pengaturan</a></li>
                </li>

            </ul>
        </div>
        <div class="clearfix"></div>
    </div> <!-- end sidebarinner -->
</div>
<!-- Left Sidebar End -->