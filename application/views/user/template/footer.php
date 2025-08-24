</div><!-- container fluid -->

</div> <!-- Page content Wrapper -->

</div> <!-- content -->

<footer class="footer">
    © Copyright 2021 MATARAM
</footer>

</div>
<!-- End Right content here -->

</div>
<!-- END wrapper -->

<script>
    function doConfirm() {
        job = confirm("Anda yakin menghapus data ini ?");
        if (job != true) {
            return false;
        }
    }

    function mendesak() {
        job = confirm("Anda yakin merubah data ini menjadi donasi mendesak (akan direview oleh tim admin) ?");
        if (job != true) {
            return false;
        }
    }

    function publish() {
        job = confirm("Anda yakin menerbitkan data ini ke publik (akan direview oleh tim admin) ?");
        if (job != true) {
            return false;
        }
    }

    function batalkan() {
        job = confirm("Anda yakin membatalkan permintaan publish data ini ke publik ?");
        if (job != true) {
            return false;
        }
    }

    function batalmendesak() {
        job = confirm("Anda yakin membatalkan permintaan mendesak untuk data ini ?");
        if (job != true) {
            return false;
        }
    }

    function nopublish() {
        job = confirm("Anda yakin sembunyikan data ini dari publik ?");
        if (job != true) {
            return false;
        }
    }

    function update_data() {
        alert("Silakan update gambar terlebih dahulu !");
    }
</script>
<script src="<?php echo base_url('assets/back/'); ?>js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>js/modernizr.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>js/detect.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>js/fastclick.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>js/jquery.slimscroll.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>js/jquery.blockUI.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>js/waves.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>js/jquery.nicescroll.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>js/jquery.scrollTo.min.js"></script>

<!--Morris Chart-->
<script src="<?php echo base_url('assets/back/'); ?>plugins/morris/morris.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/raphael/raphael.min.js"></script>

<!-- Plugins js -->
<script src="<?php echo base_url('assets/back/'); ?>plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js"></script>

<!-- Required datatable js -->
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Buttons examples -->
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/vfs_fonts.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/buttons.colVis.min.js"></script>
<!-- Responsive examples -->
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url('assets/back/'); ?>plugins/datatables/responsive.bootstrap4.min.js"></script>

<!-- Datatable init js -->
<script src="<?php echo base_url('assets/back/'); ?>pages/datatables.init.js"></script>

<!-- Plugins Init js -->
<script src="<?php echo base_url('assets/back/'); ?>pages/form-advanced.js"></script>

<!-- dashboard js -->
<script src="<?php echo base_url('assets/back/'); ?>pages/dashboard.int.js"></script>

<!-- App js -->
<script src="<?php echo base_url('assets/back/'); ?>js/app.js"></script>

<script>
    setTimeout(function() {
        $('#gone').hide('fast');
    }, 5000);

    $('#datatable').dataTable({
        "bSort": false
    });

    $(function() {
        $('.select2').css('width', '100%');
        $(".select2").select2();
        // $(".select2").select2({
        //     dropdownParent: $('#largeModal')
        // });

        $('.select3').css('width', '100%');
        $(".select3").select2();

        $('.select4').css('width', '100%');
        $(".select4").select2();
    });
</script>



</body>

</html>