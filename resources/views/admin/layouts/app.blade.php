<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vigaza Farm') | Vigaza Farm</title>
    <link rel="icon" type="image/png" href="{{ asset('bolopa/img/icon.png') }}">
    <link rel="stylesheet" href="{{ asset('bolopa/plugin/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bolopa/css/bootstrap.min.css') }}" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('bolopa/plugin/sweetalert2/sweetalert2.min.css') }}">

    <!-- Load SweetAlert2 in head to ensure it's available for inline scripts -->
    <script src="{{ asset('bolopa/plugin/sweetalert2/sweetalert2.all.min.js') }}"></script>
    
    @stack('styles')
    <style>
        /* Load Local Poppins Font */
        @font-face {
            font-family: 'Poppins';
            src: url('{{ asset("bolopa/font/Poppins-Regular.ttf") }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --bolopa-header-height: 72px; /* approximate header height (matches header padding + content) */
            --bolopa-footer-height: 48px; /* approximate footer height */
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .home-section {
            position: relative;
            background: #E4E9F7;
            flex: 1 1 auto;
            min-height: 100vh;
            left: 78px;
            width: calc(100% - 78px);
            transition: all 0.5s ease;
            z-index: 2;
            display: flex;
            flex-direction: column;
        }

        .bolopa-sidebar-vigazafarm.open ~ .home-section {
            left: 250px;
            width: calc(100% - 250px);
        }

        .page-content {
            flex: 1;
            padding: 12px 16px;
            min-height: 0;
            padding-top: var(--bolopa-header-height);
            padding-bottom: calc(var(--bolopa-footer-height) + 4px);
        }

        table {
            border: 1px solid #d5dae4;
            border-collapse: collapse;
            width: 100%;
        }

        table th,
        table td {
            border: 1px solid #d5dae4;
            vertical-align: middle;
        }

        table thead th {
            position: relative;
            padding-right: 1.5rem;
            text-align: center !important;
        }

        table thead th .bolopa-tabel-sort-wrap,
        table thead th .filter-icon,
        table thead th .filter-trigger,
        table thead th .filter-toggle,
        table thead th .filter-action {
            position: absolute;
            right: 0.35rem;
            top: 50%;
            transform: translateY(-50%);
        }

        table thead th .bolopa-tabel-sort-wrap {
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
        }

        .bolopa-tabel-text-left,
        .table-text-left,
        .text-left-column {
            text-align: left !important;
        }

        .bolopa-tabel-text-right,
        .table-text-right,
        .text-right-column {
            text-align: right !important;
        }

        .bolopa-tabel-text-center,
        .table-text-center,
        .text-center-column {
            text-align: center !important;
        }

        @media (max-width: 420px) {
            .home-section {
                left: 78px;
                width: calc(100% - 78px);
            }

            .bolopa-sidebar-vigazafarm.open ~ .home-section {
                left: 250px;
                width: calc(100% - 250px);
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <section class="home-section">
        @php
            $title = $title ?? 'Dashboard';
        @endphp
        @include('admin.partials.header')

        <div class="page-content">
            @yield('content')
        </div>

        @include('admin.partials.footer')
    </section>

    <!-- Bootstrap JS for tabs and components -->
    <script src="{{ asset('bolopa/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('bolopa/plugin/apexcharts/apexcharts.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
