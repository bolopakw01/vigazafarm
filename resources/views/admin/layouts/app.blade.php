<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vigaza Farm') | Vigaza Farm</title>
    <link rel="icon" type="image/png" href="{{ asset('bolopa/img/icon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap temporarily disabled - causing sidebar style conflicts -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Load SweetAlert2 in head to ensure it's available for inline scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        }

        .home-section {
            position: relative;
            background: #E4E9F7;
            /* make the home-section fill the viewport so header/footer can stay put */
            flex: 1;
            height: 100vh;
            left: 78px;
            width: calc(100% - 78px);
            transition: all 0.5s ease;
            z-index: 2;
            display: flex;
            flex-direction: column;
            /* prevent page itself from scrolling so the inner page-content can scroll */
            overflow: hidden;
        }

        .bolopa-sidebar-vigazafarm.open ~ .home-section {
            left: 250px;
            width: calc(100% - 250px);
        }

        .page-content {
            /* this area will handle scrolling so header/footer remain visible */
            flex: 1;
            padding: 12px 16px;
            overflow: auto;
            /* allow the flex child to shrink below its content for correct scrolling */
            min-height: 0;
            /* ensure content is not hidden behind sticky header/footer */
            padding-top: calc(var(--bolopa-header-height) + -30px);
            padding-bottom: calc(var(--bolopa-footer-height) + 8px);
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

        /* Minimal Bootstrap Alert Styles (to replace disabled Bootstrap CSS) */
        .alert {
            position: relative;
            padding: 12px 20px;
            margin-bottom: 16px;
            border: 1px solid transparent;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.5;
        }
        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }
        .alert-warning {
            color: #664d03;
            background-color: #fff3cd;
            border-color: #ffecb5;
        }
        .alert-info {
            color: #055160;
            background-color: #cff4fc;
            border-color: #b6effb;
        }
        .alert-secondary {
            color: #41464b;
            background-color: #e2e3e5;
            border-color: #d3d6d8;
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

    <!-- Bootstrap JS temporarily disabled - not needed for current implementation -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('scripts')
</body>
</html>
