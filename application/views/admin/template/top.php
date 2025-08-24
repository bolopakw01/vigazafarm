<!-- Start right Content here -->

<div class="content-page">
    <!-- Start content -->
    <div class="content">

        <!-- Top Bar Start -->
        <div class="topbar">

            <div class="topbar-left	d-none d-lg-block">
                <div class="text-center">
                    <a href="<?= base_url('backadmin'); ?>" class="logo"><span style="color: #fff;font-size: 20px;letter-spacing: 2px;font-weight: bold;">MATARAM</span></a>
                </div>
            </div>

            <nav class="navbar-custom">

                <!-- Search input -->
                <div class="search-wrap" id="search-wrap">
                    <div class="search-bar">
                        <input class="search-input" type="search" placeholder="Search" />
                        <a href="#" class="close-search toggle-search" data-target="#search-wrap">
                            <i class="mdi mdi-close-circle"></i>
                        </a>
                    </div>
                </div>

                <ul class="list-inline float-right mb-0">
                    <!-- <li class="list-inline-item dropdown notification-list">
                        <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="mdi mdi-bell-outline noti-icon"></i>
                            <!-- <span class="badge badge-danger badge-pill noti-icon-badge">3</span> --
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg dropdown-menu-animated">
                            <!-- item--
                            <div class="dropdown-item noti-title">
                                <h5>Notification</h5>
                            </div>

                            <div class="slimscroll-noti">
                                <!-- item--
                                <?php foreach ($record->result() as $r) { ?>
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <div class="notify-icon bg-success"><i class="mdi mdi-message-text-outline"></i></div>
                                        <p class="notify-details">
                                            <b><b style="color:red;font-weight:bold;"><?= $r->nama; ?></b> <?= substr($r->aksi, 0, 25); ?>...</b>
                                            <span style="color:#000;"><?= $r->tanggal; ?></span>
                                        </p>
                                    </a>
                                <?php } ?>
                            </div>


                            <!-- All--
                            <a href="<?php echo base_url('backadmin/log'); ?>" class="dropdown-item notify-all">
                                View All
                            </a>

                        </div>
                    </li> -->


                    <li class="list-inline-item dropdown notification-list nav-user">
                        <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="<?php echo base_url('assets/back/'); ?>images/users/user.png" alt="user" class="rounded-circle">
                            <span class="d-none d-md-inline-block ml-1"><?= $profil['nama']; ?> (<?= $profil['jabatan']; ?>) &nbsp;<i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated profile-dropdown" style="color:#fff">
                            <a class="dropdown-item" href="<?php echo base_url('backadmin/profil'); ?>"><i class="dripicons-user"></i> Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo base_url('siadmin/logout'); ?>"><i class="dripicons-exit"></i> Logout</a>
                        </div>
                    </li>

                </ul>

                <ul class="list-inline menu-left mb-0">
                    <li class="list-inline-item">
                        <button type="button" class="button-menu-mobile open-left waves-effect">
                            <i class="mdi mdi-menu"></i>
                        </button>
                    </li>
                </ul>


            </nav>

        </div>
        <!-- Top Bar End -->

        <div class="page-content-wrapper ">

            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="page-title m-0"><?= $thisPg; ?></h4>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end page-title-box -->
                    </div>
                </div>
                <!-- end page title -->