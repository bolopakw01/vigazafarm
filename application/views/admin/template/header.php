<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>MATARAM</title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="ThemeDesign" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="<?php echo base_url('assets/front/images/base/'); ?>fav.png">

    <!-- morris css -->
    <link rel="stylesheet" href="<?php echo base_url('assets/back/'); ?>plugins/morris/morris.css">

    <!-- Plugins css -->
    <link href="<?php echo base_url('assets/back/'); ?>plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/back/'); ?>plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/back/'); ?>plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css" rel="stylesheet" />

    <!-- DataTables -->
    <link href="<?php echo base_url('assets/back/'); ?>plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/back/'); ?>plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="<?php echo base_url('assets/back/'); ?>plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo base_url('assets/back/'); ?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url('assets/back/'); ?>css/icons.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url('assets/back/'); ?>css/style.css" rel="stylesheet" type="text/css">

    <!-- jQuery  -->
    <script src="<?php echo base_url('assets/back/'); ?>js/jquery.min.js"></script>

    <!-- <link rel="stylesheet" href="<?php echo base_url('assets/back/'); ?>plugins/selec2/select2.min.css">
    <script src="<?php echo base_url('assets/back/'); ?>plugins/select2/select2.min.js"></script> -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-selection__rendered {
            line-height: 35px !important;
        }

        .select2-container .select2-selection--single {
            height: 35px !important;
        }

        .select2-selection__arrow {
            height: 33px !important;
        }

        .select2-container--default .select2-selection--single {
            border-color: #CED4DA;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#loading').hide();

            $('form').submit(function() {
                $('#loading').show();
                $('#generate').hide();
            })
        })
    </script>

</head>


<body class="fixed-left">

    <?php
    function rupiah($angka)
    {

        $hasil_rupiah = "Rp. " . number_format($angka, 0, ',', '.');
        return $hasil_rupiah;
    }

    function selisih_tgl($tgl1, $tgl2)
    {
        $tanggal1 = new DateTime($tgl1);
        $tanggal2 = new DateTime($tgl2);
        $hasil_selisih = $tanggal2->diff($tanggal1)->days + 1;
        return $hasil_selisih;
    }

    function limit_word($string, $word_limit)
    {
        $words = explode(" ", $string);
        return implode(" ", array_splice($words, 0, $word_limit));
    }

    function limit_char($x, $length)
    {
        if (strlen($x) <= $length) {
            echo $x;
        } else {
            $y = substr($x, 0, $length) . '...';
            echo $y;
        }
    }

    function tanggal_indo($tanggal)
    {

        $bulan = array(
            1 =>   'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        );
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }

    function month($month)
    {
        if ($month == '01') {
            $bulan = 'Januari';
        } else if ($month == '02') {
            $bulan = 'Februari';
        } else if ($month == '03') {
            $bulan = 'Maret';
        } else if ($month == '04') {
            $bulan = 'April';
        } else if ($month == '05') {
            $bulan = 'Mei';
        } else if ($month == '06') {
            $bulan = 'Juni';
        } else if ($month == '07') {
            $bulan = 'Juli';
        } else if ($month == '08') {
            $bulan = 'Agustus';
        } else if ($month == '09') {
            $bulan = 'September';
        } else if ($month == '10') {
            $bulan = 'Oktober';
        } else if ($month == '11') {
            $bulan = 'November';
        } else if ($month == '12') {
            $bulan = 'Desember';
        }
        return $bulan;
    }
    ?>

    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="wrapper">