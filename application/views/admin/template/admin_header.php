<!doctype html>
<html lang="id" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="<?= base_url('assets/'); ?>images/favicon.ico" type="image/x-icon" />
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Vigaza Farm</title>

    <!-- Bootstrap Core and vendor -->
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/bootstrap/css/bootstrap.min.css" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/font-awesome/css/fontawesome-all.min.css" />
    
    <!-- Plugins css -->
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/summernote/dist/summernote.css" />
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Core css -->
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>css/style.css" />
    
    <!-- Custom Admin Dashboard CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/bolopa/admin-dashboard.css'); ?>" />
    
    <!-- Module Specific CSS -->
    <?php if (isset($thisPg)): ?>
        <?php if (file_exists(FCPATH . 'assets/bolopa/' . $thisPg . '.css')): ?>
            <link rel="stylesheet" href="<?= base_url('assets/bolopa/' . $thisPg . '.css'); ?>" />
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Page specific styles can be added here */
        <?php if (isset($custom_css)): ?>
            <?= $custom_css ?>
        <?php endif; ?>
    </style>
</head>

<body class="dashboard-container">
    <div id="wrapper">
        <!-- Page Wrapper -->
