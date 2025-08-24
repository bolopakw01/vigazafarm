<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
        <i class="mdi mdi-close"></i>
    </button>

    <div class="left-side-logo d-block d-lg-none">
        <div class="text-center">

            <a href="<?= base_url('backuser'); ?>" class="logo"><span style="color: #000;font-size: 20px;letter-spacing: 2px;font-weight: bold;">MATARAM</span></a>
        </div>
    </div>

    <div class="sidebar-inner slimscrollleft">

        <div id="sidebar-menu">
            <ul>
                <li class="menu-title">Main</li>

                <li <?php if ($thisPage == "dashboard") echo "class='active nav-active'"; ?>>
                    <a href="<?php echo base_url('backuser'); ?>" class="waves-effect">
                        <i class="dripicons-home"></i>
                        <span> Dashboard</span>
                    </a>
                </li>

                <li <?php if ($thisPage == "invoice") echo "class='active nav-active'"; ?>>
                    <a href="<?php echo base_url('backuser/invoice'); ?>" class="waves-effect">
                        <i class="fas fa-file-invoice"></i>
                        <span> Invoice</span>
                    </a>
                </li>

                <li <?php if ($thisPage == "history") echo "class='active nav-active'"; ?>>
                    <a href="<?php echo base_url('backuser/history'); ?>" class="waves-effect">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span> Payment History</span>
                    </a>
                </li>



                <!-- <li <?php if ($thisPage == "campaign") echo "class='active nav-active'"; ?>>
                    <a href="<?php echo base_url('backuser/campaign'); ?>" class="waves-effect">
                        <i class="dripicons-broadcast"></i>
                        <span> Campaign</span>
                    </a>
                </li>

                <li <?php if ($thisPage == "donor") echo "class='active nav-active'"; ?>>
                    <a href="<?php echo base_url('backuser/donor'); ?>" class="waves-effect">
                        <i class="dripicons-user"></i>
                        <span> Donor</span>
                    </a>
                </li>

                <li <?php if ($thisPage == "donation") echo "class='active nav-active'"; ?>>
                    <a href="<?php echo base_url('backuser/donation'); ?>" class="waves-effect">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span> Donation</span>
                    </a>
                </li> -->

                <!-- <li class="has_sub <?php if ($thisPage == 'campaign') : ?>active nav-active <?php endif; ?>">
                    <a href="javascript:void(0);" class="waves-effect <?php if ($thisPage == 'campaign') : ?>active<?php endif; ?>"><i class="dripicons-broadcast"></i> <span> Campaign </span> <span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?php if ($thisPg == 'donation') : ?>active<?php endif; ?>"><a href="<?php echo base_url('backuser/donation'); ?>" class="<?php if ($thisPg == 'donation') : ?>active<?php endif; ?>">Donation</a></li>
                    </ul>
                </li> -->

                <!-- <li class="menu-title">backuser</li>

                <li class="has_sub <?php if ($thisPage == 'settings') : ?>active nav-active <?php endif; ?>">
                    <a href="javascript:void(0);" class="waves-effect <?php if ($thisPage == 'settings') : ?>active<?php endif; ?>"><i class="dripicons-gear"></i> <span> Settings </span> <span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?php if ($thisPg == 'category') : ?>active<?php endif; ?>"><a href="<?= base_url('backuser/category'); ?>" class="<?php if ($thisPg == 'category') : ?>active<?php endif; ?>">Category</a></li>
                        <li class="<?php if ($thisPg == 'image') : ?>active<?php endif; ?>"><a href="<?= base_url('backuser/image'); ?>" class="<?php if ($thisPg == 'image') : ?>active<?php endif; ?>">Image</a></li>
                        <li class="<?php if ($thisPg == 'information') : ?>active<?php endif; ?>"><a href="<?php echo base_url('backuser/information'); ?>" class="<?php if ($thisPg == 'information') : ?>active<?php endif; ?>">Information</a></li>
                        <li class="<?php if ($thisPg == 'log') : ?>active<?php endif; ?>"><a href="<?= base_url('backuser/log'); ?>" class="<?php if ($thisPg == 'log') : ?>active<?php endif; ?>">App Logs</a></li>
                    </ul>
                </li>

                <li <?php if ($thisPage == "newsletter") echo "class='active nav-active'"; ?>>
                    <a href="<?php echo base_url('backuser/newsletter'); ?>" class="waves-effect">
                        <i class="far fa-envelope"></i>
                        <span> Newsletter</span>
                    </a>
                </li> -->

            </ul>
        </div>
        <div class="clearfix"></div>
    </div> <!-- end sidebarinner -->
</div>
<!-- Left Sidebar End -->