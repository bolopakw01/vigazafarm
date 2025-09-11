<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="<?= base_url('assets/'); ?>back/images/fav.png" type="image/x-icon" />
    <title>Vigaza Farm</title>

    <!-- Bootstrap Core and vandor -->
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/bootstrap/css/bootstrap.min.css" />
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <!-- Plugins css -->
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/summernote/dist/summernote.css" />
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>plugins/charts-c3/c3.min.css"/>
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>back/plugins/sweet-alert2/sweetalert2.min.css"/>


    <!-- Core css -->
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>css/style.css" />
    
    <!-- Custom CSS for Vigaza Theme -->
    <style>
        /* Vigaza Color Palette */
        .bg-vigaza {
            background-color: #0eaab4 !important;
        }

        .text-vigaza {
            color: #0eaab4 !important;
        }

        .btn-vigaza {
            background-color: #0eaab4;
            border-color: #0eaab4;
            color: #fff;
        }

        .btn-vigaza:hover {
            background-color: #0c9499;
            border-color: #0c9499;
            color: #fff;
        }

        .btn-outline-vigaza {
            color: #0eaab4;
            border-color: #0eaab4;
            background-color: transparent;
        }

        .btn-outline-vigaza:hover {
            background-color: #0eaab4;
            border-color: #0eaab4;
            color: #fff;
        }

        .badge-vigaza {
            background-color: #0eaab4;
            color: #fff;
        }

        .progress-bar.bg-vigaza {
            background-color: #0eaab4 !important;
        }

        /* Custom Styles for Better UX */
        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px;
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .section-body {
            padding: 20px 0;
        }

        .form-control:focus {
            border-color: #0eaab4;
            box-shadow: 0 0 0 0.2rem rgba(14, 170, 180, 0.25);
        }

        .icon-in-bg {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Sidebar compatibility */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .header-action {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .header-action .btn {
                margin-top: 10px;
            }

            .page-title {
                font-size: 24px;
            }
        }

        /* Loading animations */
        .count-to {
            font-size: 24px;
            font-weight: bold;
        }

        /* Table improvements */
        .table th {
            border-top: none;
            font-weight: 600;
            color: #555;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(14, 170, 180, 0.05);
        }

        /* Progress bars */
        .progress {
            border-radius: 10px;
            height: 8px;
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* Alert improvements */
        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #155724;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #721c24;
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #856404;
        }

        .alert-info {
            background-color: rgba(14, 170, 180, 0.1);
            color: #0c5460;
        }

        /* Button group improvements */
        .btn-group .btn {
            border-radius: 4px;
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        /* Badge improvements */
        .badge {
            font-size: 0.75em;
            font-weight: 600;
            padding: 0.375rem 0.5rem;
            border-radius: 0.375rem;
        }

        /* Input group improvements */
        .input-group .form-control:not(:last-child) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
    </style>
    
    <!-- Custom CSS for Dashboard -->
    <style>
        .timeline {
            position: relative;
            padding-left: 20px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 15px;
            padding-left: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -9px;
            top: 3px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #007bff;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }
        
        .my_sort_cut {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: inherit;
        }
        
        .my_sort_cut:hover {
            text-decoration: none;
            color: inherit;
            opacity: 0.8;
        }
        
        .icon-in-bg {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Hide demo content in sidebar */
        .user_div {
            display: none !important;
        }
    </style>
</head>

<body class="font-muli theme-cyan gradient">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
        </div>
    </div>

    <div id="main_content">