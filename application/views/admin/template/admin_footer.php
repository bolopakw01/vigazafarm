    </div>
    <!-- End Page Wrapper -->

    <!-- Core SCRIPTS -->
    <script src="<?= base_url('assets/'); ?>bundles/lib.vendor.bundle.js"></script>
    <script src="<?= base_url('assets/'); ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url('assets/'); ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Plugins JS -->
    <script src="<?= base_url('assets/'); ?>plugins/datatable/jquery.dataTables.min.js"></script>
    <script src="<?= base_url('assets/'); ?>plugins/datatable/dataTables.bootstrap4.min.js"></script>
    <script src="<?= base_url('assets/'); ?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= base_url('assets/'); ?>plugins/bootstrap-datepicker/locales/bootstrap-datepicker.id.min.js"></script>
    <script src="<?= base_url('assets/'); ?>plugins/summernote/dist/summernote.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Core JS -->
    <script src="<?= base_url('assets/'); ?>js/core.js"></script>
    
    <!-- Custom Admin Dashboard JS -->
    <script src="<?= base_url('assets/bolopa/admin-dashboard.js'); ?>"></script>
    
    <!-- Module Specific JS -->
    <?php if (isset($thisPg)): ?>
        <?php if (file_exists(FCPATH . 'assets/bolopa/' . $thisPg . '.js')): ?>
            <script src="<?= base_url('assets/bolopa/' . $thisPg . '.js'); ?>"></script>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Page specific scripts -->
    <?php if (isset($custom_js)): ?>
        <script>
            <?= $custom_js ?>
        </script>
    <?php endif; ?>
    
    <?php if (isset($js_files)): ?>
        <?php foreach ($js_files as $js_file): ?>
            <script src="<?= base_url($js_file); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Show Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <script>
            VigzaAdmin.swal.success('Berhasil!', '<?= $this->session->flashdata('success') ?>');
        </script>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('error')): ?>
        <script>
            VigzaAdmin.swal.error('Error!', '<?= $this->session->flashdata('error') ?>');
        </script>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('warning')): ?>
        <script>
            VigzaAdmin.swal.warning('Peringatan!', '<?= $this->session->flashdata('warning') ?>');
        </script>
    <?php endif; ?>

</body>
</html>
